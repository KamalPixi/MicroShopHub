<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Discount;
use App\Models\Country;
use App\Models\ShippingMethod;
use App\Models\ShippingZone;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StorefrontApiTest extends TestCase
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

        // Seed currencies
        $this->seed(\Database\Seeders\CurrencySeeder::class);
    }

    protected function tearDown(): void
    {
        @unlink(storage_path('installed'));
        parent::tearDown();
    }

    public function test_homepage_api_returns_correct_json_schema(): void
    {
        $response = $this->getJson('/api/homepage');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'settings',
                     'banners',
                     'categories',
                     'featured_products',
                     'new_arrivals',
                     'flash_sale',
                     'currency' => ['code', 'symbol', 'exchange_rate']
                 ]);
    }

    public function test_categories_api_returns_hierarchical_tree(): void
    {
        $parent = Category::create([
            'name' => 'Home Electronics',
            'slug' => 'home-electronics',
            'thumbnail' => 'categories/thumb.jpg',
            'show_on_homepage' => true,
        ]);

        $child = Category::create([
            'name' => 'Smartphones',
            'slug' => 'smartphones',
            'parent_id' => $parent->id,
            'thumbnail' => 'categories/sub.jpg',
        ]);

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'id' => $parent->id,
                     'name' => 'Home Electronics',
                 ])
                 ->assertJsonFragment([
                     'id' => $child->id,
                     'name' => 'Smartphones',
                 ]);
    }

    public function test_products_api_handles_search_and_pagination(): void
    {
        $product = Product::create([
            'name' => 'Super Premium Smartphone',
            'slug' => 'super-premium-smartphone',
            'sku' => 'PHONE-PREM',
            'price' => 999.99,
            'stock' => 10,
            'status' => 1,
            'thumbnail' => 'products/iphone.jpg',
        ]);

        $response = $this->getJson('/api/products?query=Premium');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'products',
                     'pagination' => ['current_page', 'last_page', 'per_page', 'total'],
                     'categories',
                 ])
                 ->assertJsonFragment([
                     'name' => 'Super Premium Smartphone',
                 ]);
    }

    public function test_product_detail_api_returns_rich_parameters(): void
    {
        $product = Product::create([
            'name' => 'Interactive Smart Watch',
            'slug' => 'interactive-smart-watch',
            'sku' => 'WATCH-INT',
            'price' => 299.99,
            'stock' => 5,
            'status' => 1,
            'thumbnail' => 'products/watch.jpg',
        ]);

        $response = $this->getJson('/api/products/interactive-smart-watch');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'product' => ['id', 'name', 'slug', 'sku', 'price', 'stock'],
                     'options',
                     'variations',
                     'related_products',
                     'reviews',
                     'flash_sale'
                 ]);
    }

    public function test_fallback_route_serves_spa_index_file(): void
    {
        // Touch or create storefront.blade.php in views directory for the test to succeed
        $viewPath = resource_path('views/storefront.blade.php');
        if (!file_exists(dirname($viewPath))) {
            mkdir(dirname($viewPath), 0755, true);
        }
        $originalContent = file_exists($viewPath) ? file_get_contents($viewPath) : null;

        file_put_contents($viewPath, '<!-- Mocked SPA Shell -->');

        try {
            $response = $this->get('/non-existent-storefront-subpage');
            $response->assertStatus(200);
            $this->assertEquals('<!-- Mocked SPA Shell -->', $response->getContent());
        } finally {
            if ($originalContent !== null) {
                file_put_contents($viewPath, $originalContent);
            } else {
                @unlink($viewPath);
            }
        }
    }

    public function test_validate_coupon_endpoint(): void
    {
        Discount::create([
            'code' => 'SAVE20',
            'type' => 'fixed',
            'value' => 20.00,
            'active' => true,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addDay(),
        ]);

        $response = $this->postJson('/api/coupon/validate', [
            'code' => 'SAVE20',
            'subtotal' => 100.00,
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'valid' => true,
                     'code' => 'SAVE20',
                     'type' => 'fixed',
                     'value' => 20.00,
                 ]);
    }

    public function test_place_order_endpoint(): void
    {
        Country::firstOrCreate(
            ['code' => 'US'],
            ['name' => 'United States', 'active' => true]
        );

        $zone = ShippingZone::create(['name' => 'Domestic']);
        $shipping = ShippingMethod::create([
            'shipping_zone_id' => $zone->id,
            'name' => 'Standard Ground Shipping',
            'cost' => 10.00,
            'active' => true,
        ]);

        Setting::create(['key' => 'cod_enabled', 'value' => 'true']);
        // Seed guest checkout enabled
        Setting::create(['key' => 'customer_auth_guest_checkout_enabled', 'value' => 'true']);

        $product = Product::create([
            'name' => 'E-Commerce Test Product',
            'slug' => 'e-commerce-test-product',
            'sku' => 'TEST-PROD-1',
            'price' => 50.00,
            'stock' => 100,
            'status' => 1,
            'thumbnail' => 'products/test.jpg',
        ]);

        $response = $this->postJson('/api/orders', [
            'email' => 'customer@example.com',
            'phone' => '1234567890',
            'billing' => [
                'name' => 'John Doe',
                'address_line1' => '123 E-Commerce St',
                'city' => 'New York',
                'country_code' => 'US',
            ],
            'ship_to_different_address' => false,
            'shipping_method_id' => $shipping->id,
            'payment_method' => 'cod',
            'cart' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ]
            ],
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'order_number',
                     'total',
                 ]);

        // Assert order exists in database and stock decreased
        $this->assertDatabaseHas('orders', [
            'payment_method' => 'cod',
            'total' => 110.00, // 50*2 + 10 shipping
        ]);

        $this->assertDatabaseHas('addresses', [
            'email' => 'customer@example.com',
            'type' => 'billing',
            'city' => 'New York',
        ]);

        $this->assertEquals(98, $product->fresh()->stock);
    }
}
