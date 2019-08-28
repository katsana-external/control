<?php

namespace Orchestra\Control\TestCase\Commands;

use Mockery as m;
use Orchestra\Model\Role;
use PHPUnit\Framework\TestCase;
use Orchestra\Control\Commands\Synchronizer;
use Illuminate\Contracts\Foundation\Application;
use Orchestra\Contracts\Authorization\Authorization;
use Orchestra\Control\Contracts\Commands\Synchronizer as SynchronizerContract;

class SynchronizerTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

    public function testItIsInitializable()
    {
        $app = m::mock(Application::class);
        $acl = m::mock(Authorization::class);

        $stub = new Synchronizer($app, $acl);

        $this->assertInstanceOf(Synchronizer::class, $stub);
        $this->assertInstanceOf(SynchronizerContract::class, $stub);
    }

    public function testItCanBeSynced()
    {
        $app = m::mock(Application::class);
        $acl = m::mock(Authorization::class);
        $role = m::mock(Role::class);

        $stub = new Synchronizer($app, $acl);

        $admin = (object) ['id' => 1, 'name' => 'Administrator'];

        $app->shouldReceive('make')->once()->with('orchestra.role')->andReturn($role);
        $role->shouldReceive('admin')->once()->andReturn($admin);

        $acl->shouldReceive('allow')->once()
            ->with('Administrator', ['Manage Users', 'Manage Orchestra', 'Manage Roles', 'Manage Acl'])
            ->andReturnNull();

        $this->assertNull($stub->handle());
    }
}