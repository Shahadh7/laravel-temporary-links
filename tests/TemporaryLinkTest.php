<?php

namespace Shahadh\TemporaryLinks\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase;
use Shahadh\TemporaryLinks\TemporaryLinksServiceProvider;
use Shahadh\TemporaryLinks\Models\TemporaryLink;
use Shahadh\TemporaryLinks\Services\TemporaryLinkService;

class TemporaryLinkTest extends TestCase
{
    use RefreshDatabase;

    protected function getPackageProviders($app)
    {
        return [TemporaryLinksServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure migrations are loaded
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Run Laravel's default migrations if needed
        $this->artisan('migrate');
    }

    /** @test */
    public function it_can_create_a_temporary_link()
    {
        $service = new TemporaryLinkService();
        $link = $service->create(null, ['path' => '/test/path']);

        $this->assertNotNull($link);
        $this->assertInstanceOf(TemporaryLink::class, $link);
        $this->assertEquals('/test/path', $link->path);
    }

    /** @test */
    public function it_can_validate_expiration()
    {
        $service = new TemporaryLinkService();
        
        // Create an expired link
        $link = $service->create(null, [
            'path' => '/test/path',
            'expires_at' => now()->subHour(),
        ]);

        $this->assertTrue($link->isExpired());
    }

    /** @test */
    public function it_can_validate_single_use()
    {
        $service = new TemporaryLinkService();
        
        // Create a single-use link
        $link = $service->create(null, [
            'path' => '/test/path',
            'single_use' => true,
        ]);

        $this->assertFalse($link->isUsed());
        
        $link->markAsUsed();
        
        $this->assertTrue($link->isUsed());
    }
}
