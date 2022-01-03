<?php

namespace Antoniputra\Reviz\Tests;

use Antoniputra\Reviz\RevizStoreEvent;
use Antoniputra\Reviz\Tests\Fixtures\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdminTest extends TestCase
{
    use DatabaseTransactions;

    public function testAnonymousVisitAdminPanel_WhenAuthorizedEmailsFilled_ShouldForbidden()
    {
        config(['reviz.ui.authorized_emails' => ['akiddcode@gmail.com']]);
        $response = $this->get('/reviz-panel');
        $response->assertStatus(403);
    }
    
    public function testCorrectUserVisitAdminPanel_WhenAuthorizedEmailsFilled_ShouldForbidden()
    {
        $adminEmail = 'admin@prend.com';
        config(['reviz.ui.authorized_emails' => [$adminEmail]]);
        $this->createAuth($adminEmail);
        $response = $this->get('/reviz-panel');
        $response->assertOk();
    }

    public function testVisitRevisionListPage_ShouldSuccess()
    {
        $response = $this->get('/reviz-panel');
        $response->assertOk();
    }
    
    public function testVisitRevisionDetailPage_ShouldSuccess()
    {
        $user = $this->helperUpdateUser();
        $this->helperUpdatePost($user);
        event(new RevizStoreEvent('command'));

        $response = $this->get('/reviz-panel/1/show');
        $response->assertOk();
    }
    
    public function testVisitRevisionDetailPage_ShouldNotFound()
    {
        $response = $this->get('/reviz-panel/1/show');
        $response->assertStatus(404);
    }
}
