<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Setting;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Bypass EnsureAppInstalled middleware by creating the lock file
        $installedPath = storage_path('installed');
        if (!file_exists(dirname($installedPath))) {
            mkdir(dirname($installedPath), 0755, true);
        }
        file_put_contents($installedPath, 'mock-installed');

        // Seed some essential settings
        Setting::create(['key' => 'store_name', 'value' => 'ShopHub']);
        Setting::create(['key' => 'store_slogan', 'value' => 'Express dynamic storefront']);
        Setting::create(['key' => 'installer_completed', 'value' => 'true']);

        // Touch the storefront blade template so '/' can render it
        $viewPath = resource_path('views/storefront.blade.php');
        if (!file_exists(dirname($viewPath))) {
            mkdir(dirname($viewPath), 0755, true);
        }
        if (!file_exists($viewPath)) {
            file_put_contents($viewPath, '<!-- Mocked SPA Shell -->');
        }
    }

    protected function tearDown(): void
    {
        @unlink(storage_path('installed'));
        parent::tearDown();
    }

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
