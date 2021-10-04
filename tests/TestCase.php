<?php

namespace Antoniputra\Reviz\Tests;

use Antoniputra\Reviz\RevizServiceProvider;
use Antoniputra\Reviz\Tests\Fixtures\Models\User;
use Orchestra\Testbench\TestCase as TestbenchCase;

class TestCase extends TestbenchCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadLaravelMigrations(['--database' => 'testbench']);

        // call migrations specific to our tests, e.g. to seed the db
        // the path option should be an absolute path.
        $this->loadMigrationsFrom([
            '--database' => 'testbench',
            '--path' => realpath(__DIR__.'/../../database/migrations'),
        ]);
        $this->loadMigrationsFrom([
            '--database' => 'testbench',
            '--path' => realpath(__DIR__.'/Fixtures/Migrations'),
        ]);
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function defineEnvironment($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    /**
    * Get package providers.
    *
    * @param  \Illuminate\Foundation\Application  $app
    *
    * @return array
    */
    protected function getPackageProviders($app)
    {
        return [
            RevizServiceProvider::class,
        ];
    }

    protected function createAuth()
    {
        config(['auth.providers.users.model' => User::class]);
        $user = User::create([
            'name' => 'mas Admin',
            'email' => 'admin@admin.test',
            'password' => \Hash::make('456')
        ]);

        $this->actingAs($user);
        return $user;
    }
}
