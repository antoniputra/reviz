<?php

namespace Antoniputra\Reviz\Tests;

use Antoniputra\Reviz\Facades\Reviz;
use Antoniputra\Reviz\RevizEloquent;
use Antoniputra\Reviz\RevizStoreEvent;
use Antoniputra\Reviz\Tests\Fixtures\Models\Post;
use Antoniputra\Reviz\Tests\Fixtures\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class FeatureTest extends TestCase
{
    use DatabaseTransactions;

    public function testPackage_QueryInsert_ShouldOneTime()
    {
        $revizSqls = [];
        DB::listen(function ($query) use (&$revizSqls) {
            if (Str::contains($query->sql, 'insert into "reviz"')) {
                $revizSqls[] = $query->sql;
            }
        });

        $auth = $this->createAuth();
        $response = $this->put('/endpoint-update');
        $response->assertOk();
        $this->assertEquals(1, count($revizSqls));
    }

    public function testPackage_AbleToLogChangesCorrectly_WhenEntityUpdated()
    {
        $auth = $this->createAuth();
        $response = $this->put('/endpoint-update');
        $response->assertOk();

        $revizTable = (new RevizEloquent)->getTable();

        $this->assertDatabaseHas($revizTable, [
            'user_id'   => $auth->id,
            'old_value' => json_encode(['name' => 'Antoni']),
            'new_value' => json_encode(['name' => 'Antoni changed']),
        ]);

        $this->assertDatabaseHas($revizTable, [
            'user_id'   => $auth->id,
            'old_value' => json_encode(['title' => 'hello world', 'content' => 'lorem ipsum']),
            'new_value' => json_encode(['title' => 'hello world updated', 'content' => 'lorem ipsum updated']),
        ]);
    }

    public function testPackage_LogNothing_WhenEntityUpdatesSameData()
    {
        $user = User::create([
            'name' => 'Antoni',
            'email' => 'me@antoniputra.com',
            'password' => Hash::make('456'),
        ]);
        $user->name = 'Antoni';
        $user->save();
        event(new RevizStoreEvent('command'));

        $this->assertEquals(0, RevizEloquent::count());
    }

    public function testPackage_AbleToLogChanges_WithResponsibleUser()
    {
        $auth = $this->createAuth();
        $response = $this->put('/endpoint-update');
        $response->assertOk();

        $revizTable = (new RevizEloquent)->getTable();

        $this->assertDatabaseHas($revizTable, [
            'user_id'   => $auth->id,
            'old_value' => json_encode(['name' => 'Antoni']),
            'new_value' => json_encode(['name' => 'Antoni changed']),
        ]);

        $this->assertDatabaseHas($revizTable, [
            'user_id'   => $auth->id,
            'old_value' => json_encode(['title' => 'hello world', 'content' => 'lorem ipsum']),
            'new_value' => json_encode(['title' => 'hello world updated', 'content' => 'lorem ipsum updated']),
        ]);
    }

    public function testPackage_AbleToLogChanges_WithoutResponsibleUser()
    {
        $response = $this->put('/endpoint-update');
        $response->assertOk();

        $revizTable = (new RevizEloquent)->getTable();

        $this->assertDatabaseHas($revizTable, [
            'user_id'   => null,
            'old_value' => json_encode(['name' => 'Antoni']),
            'new_value' => json_encode(['name' => 'Antoni changed']),
        ]);

        $this->assertDatabaseHas($revizTable, [
            'user_id'   => null,
            'old_value' => json_encode(['title' => 'hello world', 'content' => 'lorem ipsum']),
            'new_value' => json_encode(['title' => 'hello world updated', 'content' => 'lorem ipsum updated']),
        ]);
    }

    public function testPackage_AbleToLogChanges_WithCustomAuthDriver()
    {
        $auth = $this->createAuth();
        Reviz::setUser(function () use ($auth) {
            return $auth;
        });
        $response = $this->put('/endpoint-update');
        $response->assertOk();

        $revizTable = (new RevizEloquent)->getTable();

        $this->assertDatabaseHas($revizTable, [
            'user_id'   => $auth->id,
            'old_value' => json_encode(['name' => 'Antoni']),
            'new_value' => json_encode(['name' => 'Antoni changed']),
        ]);

        $this->assertDatabaseHas($revizTable, [
            'user_id'   => $auth->id,
            'old_value' => json_encode(['title' => 'hello world', 'content' => 'lorem ipsum']),
            'new_value' => json_encode(['title' => 'hello world updated', 'content' => 'lorem ipsum updated']),
        ]);
    }

    public function testPackage_AbleToGetCustomMorphMap_FromGlobalConfig()
    {
        config([
            'reviz.morphMap' => [
                'users' => User::class,
                'posts' => Post::class,
            ]
        ]);

        $response = $this->put('/endpoint-update');
        $response->assertOk();

        $revizTable = (new RevizEloquent)->getTable();
        $this->assertDatabaseHas($revizTable, [
            'revizable_type' => 'users',
        ]);
        $this->assertDatabaseHas($revizTable, [
            'revizable_type' => 'posts',
        ]);
    }

    public function testPackage_AbleToIgnoreFieldToBeLogged_FromGlobalConfig()
    {
        config(['reviz.ignore_fields' => ['updated_at', 'content']]);
        $response = $this->put('/endpoint-update');
        $response->assertOk();

        $revizTable = (new RevizEloquent)->getTable();

        $this->assertDatabaseHas($revizTable, [
            'old_value' => json_encode(['name' => 'Antoni']),
            'new_value' => json_encode(['name' => 'Antoni changed']),
        ]);

        $this->assertDatabaseHas($revizTable, [
            'old_value' => json_encode(['title' => 'hello world']),
            'new_value' => json_encode(['title' => 'hello world updated']),
        ]);
    }

    public function testPackage_AbleToIgnoreFieldToBeLogged_FromModelPropertyConfig()
    {
        $post = Post::create([
            'title' => 'one',
            'content' => 'lorem',
        ]);
        $post->revizIgnoreFields = ['content'];
        $post->update([
            'title' => 'one updated',
            'content' => 'lorem updated'
        ]);
        event(new RevizStoreEvent('command', [
            'command' => 'DummyCommand'
        ]));

        $revizTable = (new RevizEloquent)->getTable();
        $this->assertDatabaseHas($revizTable, [
            'old_value' => json_encode(['title' => 'one']),
            'new_value' => json_encode(['title' => 'one updated']),
        ]);
    }

    public function testPackage_AbleToDisableLog_WhenEntityUpdated()
    {
        Reviz::disable();
        $response = $this->put('/endpoint-update');
        $response->assertOk();

        $this->assertEquals(0, RevizEloquent::count());
    }
    
    public function testPackage_AbleToDisableLogAtSpecificScope_WhenEntityUpdated()
    {
        Reviz::disable();
        $user = $this->helperUpdateUser();

        Reviz::enable();
        $post = $this->helperUpdatePost($user);

        event(new RevizStoreEvent('manual'));

        $this->assertEquals(1, RevizEloquent::count());
        
        $revizTable = (new RevizEloquent)->getTable();
        $this->assertDatabaseHas($revizTable, [
            'old_value' => json_encode(['title' => 'hello world', 'content' => 'lorem ipsum']),
            'new_value' => json_encode(['title' => 'hello world updated', 'content' => 'lorem ipsum updated']),
        ]);
    }
    
    public function testPackage_TargetModel_AbleToUtilizeRevizRelations()
    {
        $user = $this->helperUpdateUser();
        event(new RevizStoreEvent('manual'));

        $this->assertInstanceOf(Collection::class, $user->reviz);
        $this->assertInstanceOf(Collection::class, $user->revisionList);
        $this->assertInstanceOf(Collection::class, $user->rollbackedList);
    }

    public function testPackage_RollbackShouldReturnNull_WhenThereIsNoRevisionsData()
    {
        $user = User::create([
            'name' => 'Antoni',
            'email' => 'me@antoniputra.com',
            'password' => Hash::make('456'),
        ]);

        $this->assertNull($user->rollback());
    }

    public function testPackage_AbleTo_SingleRollback()
    {
        // Create changes
        $user = $this->helperUpdateUser();
        event(new RevizStoreEvent('manual'));

        // Rollback Changes
        $user->rollback();

        // Database should updated
        $this->assertDatabaseHas($user->getTable(), [
            'name' => 'Antoni'
        ]);
        // Model should got fresh attribute
        $this->assertEquals('Antoni', $user->name);
        // Model should have 1 rollbacked items
        $this->assertEquals(1, $user->rollbackedList()->count());
    }
    
    public function testPackage_AbleTo_SingleRollback_ToSpecificId()
    {
        // Create & Make Changes #1
        $user = $this->helperUpdateUser();

        // Make Changes #2
        $user->name = 'Rodriguez';
        $user->save();

        // Make Changes #3
        $user->name = 'Sobirin';
        $user->save();

        // Make Changes #4
        $user->name = 'Bejo';
        $user->save();

        event(new RevizStoreEvent('manual'));

        // Rollback to Changes #3
        $changesThree = $user->revisionList->skip(2)->take(1)->first();
        $user->rollback($changesThree->id);

        // Database should updated
        $this->assertDatabaseHas($user->getTable(), [
            'name' => 'Rodriguez'
        ]);
        // Model should got fresh attribute
        $this->assertEquals('Rodriguez', $user->name);
        // Model should have 1 rollbacked items
        $this->assertEquals(3, $user->rollbackedList()->count());
    }

    public function testPackage_AbleTo_BatchRollback()
    {
        // Initiate & Changes Batch #1
        $user = $this->helperUpdateUser();
        $post = $this->helperUpdatePost($user);
        $post->title = 'Dummy Updated';
        $post->save();
        $user->save();
        event(new RevizStoreEvent);
        
        // Changes Batch #2
        $user->name = 'Antoni';
        $user->save();
        $post->title = 'Reviz Blog';
        $post->save();
        event(new RevizStoreEvent);
        
        // Changes Batch #3
        $user->name = 'Sobirin';
        $user->save();
        $post->title = 'abc';
        $post->save();
        event(new RevizStoreEvent);

        Reviz::batchRollback(3);

        // We should expect our model values should be in Batch #2
        $this->assertDatabaseHas($user->getTable(), [
            'name' => 'Antoni'
        ]);
        $this->assertDatabaseHas($post->getTable(), [
            'title' => 'Reviz Blog'
        ]);
    }
}
