<?php

namespace Antoniputra\Reviz\Tests;

use Antoniputra\Reviz\RevizEloquent;
use Antoniputra\Reviz\RevizRepository;
use Antoniputra\Reviz\RevizStoreEvent;
use Antoniputra\Reviz\Tests\Fixtures\Models\Post;
use Antoniputra\Reviz\Tests\Fixtures\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RevizRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repo = $this->app->make(RevizRepository::class);
    }

    public function testGetListForRollback_ShouldSuccess()
    {
        $this->assertInstanceOf(LengthAwarePaginator::class, $this->repo->getListForRollback(5));
    }

    public function testGetById_ShouldSuccess()
    {
        $this->assertNull($this->repo->getById(1));

        $user = $this->helperUpdateUser();
        $post = $this->helperUpdatePost($user);
        event(new RevizStoreEvent('command'));

        $this->assertInstanceOf(RevizEloquent::class, $this->repo->getById(RevizEloquent::first()->id));
    }
}
