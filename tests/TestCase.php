<?php

namespace Antoniputra\Reviz\Tests;

use Antoniputra\Reviz\RevizServiceProvider;
use Antoniputra\Reviz\Tests\Fixtures\Models\Post;
use Antoniputra\Reviz\Tests\Fixtures\Models\User;
use Illuminate\Support\Facades\Hash;
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
        $app['config']->set('app.key', 'base64:UTyp33UhGolgzCK5CJmT+hNHcA+dJyp3+oINtX+VoPI=');
        $app['config']->set('auth.providers.users.model', User::class);

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

    protected function createAuth($email = null)
    {
        $user = User::create([
            'name' => 'mas Admin',
            'email' => $email ?? 'admin@admin.test',
            'password' => \Hash::make('456')
        ]);

        $this->actingAs($user);
        return $user;
    }

    /**
     * Define routes setup.
     *
     * @param  \Illuminate\Routing\Router  $router
     *
     * @return void
     */
    protected function defineRoutes($router)
    {
        $router->put('/endpoint-update', function () {
            $this->sampleUpdateProcess();
        });
    }

    /**
     * Updating User and Post at once for testing purpose.
     * 
     * @return \Illuminate\Http\Response
     */
    protected function sampleUpdateProcess()
    {
        $user = $this->helperUpdateUser();
        $post = $this->helperUpdatePost($user);

        return 'success';
    }

    protected function helperUpdateUser()
    {
        $user = User::create([
            'name' => 'Antoni',
            'email' => 'me@antoniputra.com',
            'password' => Hash::make('456'),
        ]);
        $user->name = 'Antoni changed';
        $user->save();
        return $user;
    }

    protected function helperUpdatePost($user)
    {
        $post = $user->posts()->create([
            'title' => 'hello world',
            'content' => 'lorem ipsum'
        ]);
        $post->update([
            'title' => 'hello world updated',
            'content' => 'lorem ipsum updated',
        ]);
        return $post;
    }
}
