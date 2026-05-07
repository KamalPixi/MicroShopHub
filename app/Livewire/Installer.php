<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class Installer extends Component
{
    use WithFileUploads;

    public $step = 1;
    public $logs = [];

    // Requirements Step
    public $requirements = [];

    // Database Step
    public $db = [
        'connection' => 'mysql',
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => '',
        'username' => '',
        'password' => '',
        'prefix' => '',
    ];
    public $drivers = [];

    // Settings Step
    public $settings = [
        'app_url' => '',
        'shop_name' => '',
        'slogan' => '',
        'branding_color' => '#111111',
        'secondary_color' => '#6B7280',
        'accent_color' => '#F59E0B',
        'store_default_locale' => 'en',
        'currency' => 'BDT',
        'admin_name' => 'Admin',
        'admin_email' => 'admin@example.com',
        'admin_password' => '',
        'admin_password_confirmation' => '',
        'cod_enabled' => true,
        'home_hero_title' => '',
        'home_hero_subtitle' => '',
        'home_shop_by_category_title' => 'Shop by Category',
        'home_featured_products_title' => 'Featured Products',
        'home_new_arrivals_title' => 'New Arrivals',
        'home_newsletter_title' => 'Stay Updated',
        'footer_about_title' => 'ShopHub',
        'footer_about_description' => '',
        'footer_support_hours_1' => 'Mon-Fri: 9AM-6PM',
        'footer_support_hours_2' => 'Sat-Sun: 10AM-4PM',
        'email' => '',
        'phone' => '',
        'mail_host' => '',
        'mail_port' => '587',
        'mail_encryption' => 'tls',
        'mail_username' => '',
        'mail_password' => '',
        'mail_from_address' => '',
        'mail_from_name' => '',
        'mail_queue_enabled' => false,
        'aws_access_key_id' => '',
        'aws_secret_access_key' => '',
        'aws_default_region' => 'us-east-1',
        'aws_bucket' => '',
        'aws_endpoint' => '',
        'aws_url' => '',
        'aws_use_path_style_endpoint' => false,
        'backup_enabled' => true,
        'background_mode' => 'cron',
        'stripe_api_key' => '',
        'stripe_label' => 'Credit Card',
        'paypal_api_key' => '',
        'paypal_label' => 'PayPal',
        'sslcommerz_store_id' => '',
        'sslcommerz_api_key' => '',
        'sslcommerz_label' => 'SSLCommerz',
        'sslcommerz_sandbox' => false,
        'portpos_app_key' => '',
        'portpos_secret_key' => '',
        'portpos_label' => 'PortPos',
        'portpos_sandbox' => false,
        'bkash_base_url' => '',
        'bkash_app_key' => '',
        'bkash_app_secret' => '',
        'bkash_username' => '',
        'bkash_password' => '',
    ];
    public $logo;
    public $custom_currencies = [];
    public $country_codes = ['BD'];
    public $custom_countries = [];

    // Constants
    public $currencyPresets = [
        'BDT' => ['symbol' => '৳', 'name' => 'Bangladeshi Taka'],
        'USD' => ['symbol' => '$', 'name' => 'US Dollar'],
        'EUR' => ['symbol' => '€', 'name' => 'Euro'],
        'GBP' => ['symbol' => '£', 'name' => 'British Pound'],
        'INR' => ['symbol' => '₹', 'name' => 'Indian Rupee'],
    ];
    public $countryOptions = [
        'BD' => 'Bangladesh',
        'US' => 'United States',
        'GB' => 'United Kingdom',
        'CA' => 'Canada',
        'AU' => 'Australia',
        'IN' => 'India',
    ];

    // Finalize Step
    public $isInstalling = false;
    public $installationProgress = 0;
    public $currentTask = '';

    public function mount()
    {
        if ($this->isInstalled()) {
            return redirect()->route('store.index');
        }

        $this->checkRequirements();
        $this->checkDrivers();
        
        $this->settings['app_url'] = url('/');
        $this->settings['mail_from_address'] = 'noreply@' . parse_url(url('/'), PHP_URL_HOST);
    }

    protected function isInstalled()
    {
        return file_exists(storage_path('installed'));
    }

    public function checkRequirements()
    {
        $this->requirements = [
            ['label' => 'PHP >= 8.2', 'ok' => version_compare(PHP_VERSION, '8.2.0', '>=')],
            ['label' => 'BCMath', 'ok' => extension_loaded('bcmath')],
            ['label' => 'Ctype', 'ok' => extension_loaded('ctype')],
            ['label' => 'OpenSSL', 'ok' => extension_loaded('openssl')],
            ['label' => 'PDO', 'ok' => extension_loaded('pdo')],
            ['label' => 'Mbstring', 'ok' => extension_loaded('mbstring')],
            ['label' => 'Tokenizer', 'ok' => extension_loaded('tokenizer')],
            ['label' => 'XML', 'ok' => extension_loaded('xml')],
            ['label' => 'cURL', 'ok' => extension_loaded('curl')],
            ['label' => 'Fileinfo', 'ok' => extension_loaded('fileinfo')],
            ['label' => 'Zip', 'ok' => extension_loaded('zip')],
            ['label' => 'Storage writable', 'ok' => is_writable(storage_path())],
            ['label' => 'Cache writable', 'ok' => is_writable(base_path('bootstrap/cache'))],
        ];
    }

    public function checkDrivers()
    {
        $this->drivers = [
            'mysql' => extension_loaded('pdo_mysql'),
            'pgsql' => extension_loaded('pdo_pgsql'),
            'sqlite' => extension_loaded('pdo_sqlite'),
        ];
    }

    public function goToStep($step)
    {
        if ($step > $this->step) {
            if ($this->step == 1 && collect($this->requirements)->contains('ok', false)) {
                $this->addError('requirements', 'Please fix requirements first.');
                return;
            }
            if ($this->step == 2) {
                if (!$this->testDatabase()) return;
            }
        }
        $this->step = $step;
    }

    public function testDatabase()
    {
        $this->resetErrorBag();

        if (!$this->drivers[$this->db['connection']]) {
            $this->addError('db.connection', "The {$this->db['connection']} driver is not enabled on your server.");
            return false;
        }

        try {
            if ($this->db['connection'] === 'sqlite') {
                $path = database_path($this->db['database'] ?: 'database.sqlite');
                if (!file_exists($path)) {
                    File::ensureDirectoryExists(dirname($path));
                    touch($path);
                }
            }

            Config::set('database.connections.setup', [
                'driver' => $this->db['connection'],
                'host' => $this->db['host'],
                'port' => $this->db['port'],
                'database' => $this->db['connection'] === 'sqlite' ? database_path($this->db['database'] ?: 'database.sqlite') : $this->db['database'],
                'username' => $this->db['username'],
                'password' => $this->db['password'],
                'prefix' => $this->db['prefix'],
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ]);

            DB::purge('setup');
            DB::connection('setup')->getPdo();
            
            return true;
        } catch (\Exception $e) {
            $this->addError('db.database', 'Connection failed: ' . $e->getMessage());
            return false;
        }
    }

    public function addCurrency()
    {
        $this->custom_currencies[] = ['code' => '', 'name' => '', 'symbol' => '', 'exchange_rate' => 1, 'active' => true];
    }

    public function removeCurrency($index)
    {
        unset($this->custom_currencies[$index]);
        $this->custom_currencies = array_values($this->custom_currencies);
    }

    public function addCountry()
    {
        $this->custom_countries[] = ['code' => '', 'name' => '', 'active' => true];
    }

    public function removeCountry($index)
    {
        unset($this->custom_countries[$index]);
        $this->custom_countries = array_values($this->custom_countries);
    }

    public function startInstallation()
    {
        $this->validate([
            'settings.shop_name' => 'required',
            'settings.admin_email' => 'required|email',
            'settings.admin_password' => 'required|min:8|confirmed',
        ]);

        $this->step = 4;
        $this->isInstalling = true;
        $this->logs = [];
        $this->installationProgress = 0;
        
        $this->dispatch('run-task', task: 'initialize');
    }

    public function runTask($task)
    {
        switch ($task) {
            case 'initialize':
                $this->addLog('Initializing installation...');
                $this->currentTask = 'Writing configuration...';
                $this->installationProgress = 10;
                $this->writeEnv();
                $this->dispatch('run-task', task: 'migrate');
                break;

            case 'migrate':
                $this->addLog('Running database migrations...');
                $this->currentTask = 'Migrating database...';
                $this->installationProgress = 30;
                try {
                    Artisan::call('migrate:fresh', ['--force' => true]);
                    $output = Artisan::output();
                    if (!empty(trim($output))) {
                        $this->addLog($output);
                    }
                    $this->addLog('Migrations completed successfully.');
                    $this->dispatch('run-task', task: 'seed');
                } catch (\Exception $e) {
                    $this->handleError('Migration failed: ' . $e->getMessage());
                }
                break;

            case 'seed':
                $this->addLog('Seeding default data...');
                $this->currentTask = 'Seeding data...';
                $this->installationProgress = 60;
                try {
                    Artisan::call('db:seed', ['--force' => true]);
                    $output = Artisan::output();
                    if (!empty(trim($output))) {
                        $this->addLog($output);
                    }
                    $this->saveStoreSettings();
                    $this->addLog('Seeding and settings saved.');
                    $this->dispatch('run-task', task: 'finalize');
                } catch (\Exception $e) {
                    $this->handleError('Seeding failed: ' . $e->getMessage());
                }
                break;

            case 'finalize':
                $this->addLog('Finalizing setup...');
                $this->currentTask = 'Cleaning up...';
                $this->installationProgress = 90;
                $this->lockInstaller();
                $this->installationProgress = 100;
                $this->addLog('Installation completed!');
                $this->isInstalling = false;
                $this->step = 5;
                break;
        }
    }

    protected function addLog($message)
    {
        $this->logs[] = [
            'time' => now()->format('H:i:s'),
            'message' => $message
        ];
    }

    protected function handleError($message)
    {
        $this->addLog('ERROR: ' . $message);
        $this->isInstalling = false;
        $this->addError('installation', $message);
    }

    protected function writeEnv()
    {
        $envPath = base_path('.env');
        $env = File::get($envPath);

        $replacements = [
            'APP_URL' => $this->settings['app_url'],
            'DB_CONNECTION' => $this->db['connection'],
            'DB_HOST' => $this->db['host'],
            'DB_PORT' => $this->db['port'],
            'DB_DATABASE' => $this->db['connection'] === 'sqlite' ? ($this->db['database'] ?: 'database.sqlite') : $this->db['database'],
            'DB_USERNAME' => $this->db['username'],
            'DB_PASSWORD' => $this->db['password'],
            'AWS_ACCESS_KEY_ID' => $this->settings['aws_access_key_id'],
            'AWS_SECRET_ACCESS_KEY' => $this->settings['aws_secret_access_key'],
            'AWS_DEFAULT_REGION' => $this->settings['aws_default_region'],
            'AWS_BUCKET' => $this->settings['aws_bucket'],
            'AWS_ENDPOINT' => $this->settings['aws_endpoint'],
            'AWS_URL' => $this->settings['aws_url'],
            'AWS_USE_PATH_STYLE_ENDPOINT' => $this->settings['aws_use_path_style_endpoint'] ? 'true' : 'false',
            'MAIL_HOST' => $this->settings['mail_host'],
            'MAIL_PORT' => $this->settings['mail_port'],
            'MAIL_USERNAME' => $this->settings['mail_username'],
            'MAIL_PASSWORD' => $this->settings['mail_password'],
            'MAIL_ENCRYPTION' => $this->settings['mail_encryption'],
            'MAIL_FROM_ADDRESS' => $this->settings['mail_from_address'],
            'MAIL_FROM_NAME' => $this->settings['mail_from_name'] ?: $this->settings['shop_name'],
            'QUEUE_CONNECTION' => $this->settings['mail_queue_enabled'] ? 'database' : 'sync',
        ];

        foreach ($replacements as $key => $value) {
            if (preg_match("/^{$key}=/m", $env)) {
                $env = preg_replace("/^{$key}=.*/m", "{$key}=\"{$value}\"", $env);
            } else {
                $env .= "\n{$key}=\"{$value}\"";
            }
        }

        File::put($envPath, $env);
    }

    protected function saveStoreSettings()
    {
        $db = DB::connection();

        // Create Admin
        $db->table('users')->updateOrInsert(
            ['email' => $this->settings['admin_email']],
            [
                'name' => $this->settings['admin_name'],
                'password' => Hash::make($this->settings['admin_password']),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Save Basic Settings
        $settingsToSave = [
            'shop_name' => $this->settings['shop_name'],
            'slogan' => $this->settings['slogan'],
            'branding_color' => $this->settings['branding_color'],
            'secondary_color' => $this->settings['secondary_color'],
            'accent_color' => $this->settings['accent_color'],
            'store_default_locale' => $this->settings['store_default_locale'],
            'store_default_currency' => $this->settings['currency'],
            'cod_enabled' => $this->settings['cod_enabled'] ? '1' : '0',
            'home_hero_title' => $this->settings['home_hero_title'],
            'home_hero_subtitle' => $this->settings['home_hero_subtitle'],
            'home_shop_by_category_title' => $this->settings['home_shop_by_category_title'],
            'home_featured_products_title' => $this->settings['home_featured_products_title'],
            'home_new_arrivals_title' => $this->settings['home_new_arrivals_title'],
            'home_newsletter_title' => $this->settings['home_newsletter_title'],
            'footer_about_title' => $this->settings['footer_about_title'],
            'footer_about_description' => $this->settings['footer_about_description'],
            'footer_support_hours_1' => $this->settings['footer_support_hours_1'],
            'footer_support_hours_2' => $this->settings['footer_support_hours_2'],
        ];

        foreach ($settingsToSave as $key => $value) {
            $db->table('settings')->updateOrInsert(['key' => $key], ['value' => $value, 'updated_at' => now()]);
        }

        // Save Currencies
        foreach ($this->custom_currencies as $curr) {
            if (!empty($curr['code'])) {
                $db->table('currencies')->updateOrInsert(
                    ['code' => $curr['code']],
                    [
                        'name' => $curr['name'],
                        'symbol' => $curr['symbol'],
                        'exchange_rate' => $curr['exchange_rate'],
                        'active' => $curr['active'] ? 1 : 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }

        // Save Countries
        foreach ($this->country_codes as $code) {
            $db->table('countries')->updateOrInsert(['code' => $code], ['active' => 1, 'updated_at' => now()]);
        }
    }

    protected function lockInstaller()
    {
        touch(storage_path('installed'));
    }

    public function render()
    {
        return view('livewire.installer')->layout('install.layout-livewire');
    }
}
