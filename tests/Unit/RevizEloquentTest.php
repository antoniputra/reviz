<?php

namespace Antoniputra\Reviz\Tests;

use Antoniputra\Reviz\RevizEloquent;
use Antoniputra\Reviz\RevizStoreEvent;
use Antoniputra\Reviz\Tests\Fixtures\Models\Post;
use Antoniputra\Reviz\Tests\Fixtures\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RevizEloquentTest extends TestCase
{
    use DatabaseTransactions;

    public function testGetUserData_ShouldBasedFromConfig()
    {
        extract($this->scenarioUpdatePost());
        $reviz = $post->revisionList->first();

        $this->assertIsString($reviz->getUserGravatar());
        $this->assertIsString($reviz->getUserEmail());
        $this->assertIsString($reviz->getUserName());

        config([
            'reviz.ui.user_name' => 'wrong_field_name',
            'reviz.ui.user_email' => 'wrong_field_email',
        ]);
        $this->assertNull($reviz->getUserName());
        $this->assertNull($reviz->getUserEmail());
        $this->assertNull($reviz->getUserGravatar());
    }

    public function testUser_ShouldGetInstance_BasedOnLaravelAuthConfig()
    {
        extract($this->scenarioUpdatePost());

        $userRelation = $post->reviz->first()->user;

        $this->assertInstanceOf(User::class, $userRelation);
    }

    public function testSomeFields_ShouldMutatedAsArray()
    {
        extract($this->scenarioUpdatePost());

        $reviz = RevizEloquent::first();

        $this->assertIsArray($reviz->old_value);
        $this->assertIsArray($reviz->new_value);
    }

    public function testCreatedAt_ShouldAutomaticallyFilled_WhenCreateRowViaEloquent()
    {
        $row = RevizEloquent::create([
            'revizable_type' => 'dummy',
            'revizable_id' => 1,
            'user_id' => 1,
            'old_value' => json_encode(['field' => 'value']),
            'new_value' => json_encode(['field' => 'value updated']),
            'batch' => 1,
        ]);

        $this->assertInstanceOf(RevizEloquent::class, $row);
        $this->assertNotNull($row->created_at);
        $this->assertEquals(0, $row->is_rollbacked);
    }

    private function scenarioUpdatePost()
    {
        $auth = $this->createAuth();
        $user = User::create([
            'name' => 'Antoni',
            'email' => 'me@antoniputra.com',
            'password' => \Hash::make('456'),
        ]);
        $post = $user->posts()->create([
            'title' => 'hello world',
            'content' => 'lorem ipsum'
        ]);

        $post->update([
            'title' => 'hello world updated',
            'content' => 'lorem ipsum updated'
        ]);
        event(new RevizStoreEvent('manual', ['class' => 'App\TestClass']));

        return [
            'auth' => $auth,
            'user' => $user,
            'post' => $post,
        ];
    }
}
