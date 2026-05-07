@extends('install.layout')

@section('content')
    <div class="stepbar">
        <span class="step done">1. Requirements</span>
        <span class="step done">2. Database</span>
        <span class="step active">3. Settings</span>
        <span class="step">4. Finalize</span>
        <span class="step">5. Complete</span>
    </div>

    <form method="POST" action="{{ route('install.settings.store') }}" enctype="multipart/form-data" class="stack">
        @csrf
        @php($customCurrencies = old('custom_currencies'))
        
        <div>
            <h2 class="section-title">Store Configuration</h2>
            <p class="section-desc">Set up your shop branding, locale, and administrative details.</p>
        </div>

        <div class="card grid grid-2">
            <div style="grid-column: 1 / -1">
                <label>Domain / App URL</label>
                <input type="url" name="app_url" value="{{ old('app_url', $settings['app_url'] ?? '') }}" placeholder="https://example.com">
                <div class="help">Optional. Used as the base URL for links and assets.</div>
            </div>
            <div>
                <label>Shop Name</label>
                <input type="text" name="shop_name" value="{{ old('shop_name', $settings['shop_name'] ?? '') }}" placeholder="Shop name">
            </div>
            <div>
                <label>Slogan</label>
                <input type="text" name="slogan" value="{{ old('slogan', $settings['slogan'] ?? '') }}" placeholder="Short brand slogan">
            </div>
            <div>
                <label>Brand Logo</label>
                <input type="file" name="logo" accept="image/*">
            </div>
            <div class="grid grid-3" style="grid-column: 1 / -1; gap: 12px;">
                <div>
                    <label>Brand Color</label>
                    <input type="text" name="branding_color" value="{{ old('branding_color', $settings['branding_color'] ?? '#111111') }}" placeholder="#111111">
                </div>
                <div>
                    <label>Secondary Color</label>
                    <input type="text" name="secondary_color" value="{{ old('secondary_color', $settings['secondary_color'] ?? '#6B7280') }}" placeholder="#6B7280">
                </div>
                <div>
                    <label>Accent Color</label>
                    <input type="text" name="accent_color" value="{{ old('accent_color', $settings['accent_color'] ?? '#F59E0B') }}" placeholder="#F59E0B">
                </div>
            </div>
        </div>

        <div class="card grid grid-2">
            <div>
                <label>Default Language</label>
                <select name="store_default_locale" class="select-field">
                    <option value="en" @selected(old('store_default_locale', $settings['store_default_locale'] ?? 'en') === 'en')>English</option>
                    <option value="bn" @selected(old('store_default_locale', $settings['store_default_locale'] ?? 'en') === 'bn')>Bengali</option>
                </select>
            </div>
            <div>
                <label>Default Currency</label>
                <select name="currency" class="select-field">
                    @foreach($currencyPresets as $code => $currency)
                        <option value="{{ $code }}" @selected(old('currency', $settings['currency'] ?? 'BDT') === $code)>
                            {{ $code }} - {{ $currency['symbol'] }} {{ $currency['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="card stack" style="gap: 16px;">
            <div>
                <h3 class="section-title" style="font-size:15px">Admin Account</h3>
                <p class="section-desc" style="margin-bottom:12px">Create the primary administrator account.</p>
            </div>
            <div class="grid grid-2" style="gap:12px">
                <div>
                    <label>Admin Name</label>
                    <input type="text" name="admin_name" value="{{ old('admin_name', $settings['admin_name'] ?? 'Admin') }}" placeholder="Admin">
                </div>
                <div>
                    <label>Admin Email</label>
                    <input type="email" name="admin_email" value="{{ old('admin_email', $settings['admin_email'] ?? ($adminEmailSuggestion ?? 'admin@e.com')) }}" placeholder="{{ $adminEmailSuggestion ?? 'admin@e.com' }}">
                </div>
                <div>
                    <label>Admin Password</label>
                    <input type="password" name="admin_password" value="{{ old('admin_password', $settings['admin_password'] ?? '') }}" placeholder="Strong password">
                </div>
                <div>
                    <label>Confirm Password</label>
                    <input type="password" name="admin_password_confirmation" value="{{ old('admin_password_confirmation', '') }}" placeholder="Repeat password">
                </div>
            </div>
        </div>

        <div class="card stack" x-data="{ rows: @js(array_values($customCurrencies ?: [])) }" style="gap:16px">
            <div class="inline" style="justify-content:space-between;align-items:center">
                <div>
                    <h3 class="section-title" style="font-size:15px">Additional Currencies</h3>
                    <p class="section-desc" style="margin-bottom:0">Exchange rates relative to base currency.</p>
                </div>
                <button type="button" class="btn btn-soft" style="padding: 6px 12px; font-size: 12px;" @click="rows.push({code:'', name:'', symbol:'', exchange_rate:1, active:true})">+ Add Currency</button>
            </div>

            <div class="stack" style="gap:10px">
                <template x-for="(row, index) in rows" :key="index">
                    <div style="padding:14px; background:#f8fafc; border-radius:var(--radius-md); border: 1px solid var(--line)">
                        <div class="grid grid-4" style="gap:10px">
                            <div>
                                <label class="xsmall">Code</label>
                                <input type="text" :name="`custom_currencies[${index}][code]`" x-model="row.code" placeholder="USD">
                            </div>
                            <div>
                                <label class="xsmall">Name</label>
                                <input type="text" :name="`custom_currencies[${index}][name]`" x-model="row.name" placeholder="US Dollar">
                            </div>
                            <div>
                                <label class="xsmall">Symbol</label>
                                <input type="text" :name="`custom_currencies[${index}][symbol]`" x-model="row.symbol" placeholder="$">
                            </div>
                            <div>
                                <label class="xsmall">Rate</label>
                                <input type="number" step="0.0001" min="0.0001" :name="`custom_currencies[${index}][exchange_rate]`" x-model="row.exchange_rate" placeholder="1.0000">
                            </div>
                        </div>
                        <div class="btn-row" style="justify-content:space-between;margin-top:10px; padding-top:10px; border-top: 1px dashed var(--line)">
                            <label class="checkbox-group" style="margin-bottom:0">
                                <input type="checkbox" :name="`custom_currencies[${index}][active]`" value="1" x-model="row.active">
                                <span class="small">Active</span>
                            </label>
                            <button type="button" class="btn btn-soft" style="padding:4px 8px; font-size:11px" @click="rows.splice(index, 1)">Remove</button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="card grid grid-2">
            <div class="checkbox-group">
                <input type="checkbox" name="cod_enabled" id="cod_enabled" value="1" @checked(old('cod_enabled', $settings['cod_enabled'] ?? true))>
                <label for="cod_enabled" style="margin-bottom:0">Enable Cash on Delivery (COD)</label>
            </div>
        </div>

        <div class="card stack" style="gap:16px">
            <div>
                <h3 class="section-title" style="font-size:15px">Homepage Defaults</h3>
                <p class="section-desc" style="margin-bottom:0">Starter content for your landing page.</p>
            </div>
            <div class="grid grid-2" style="gap:12px">
                <div style="grid-column: 1 / -1">
                    <label>Hero Title</label>
                    <input type="text" name="home_hero_title" value="{{ old('home_hero_title', $settings['home_hero_title'] ?? '') }}" placeholder="Find what fits your life">
                </div>
                <div style="grid-column: 1 / -1">
                    <label>Hero Subtitle</label>
                    <textarea name="home_hero_subtitle" rows="2" placeholder="Curated products, fast delivery, and easy browsing.">{{ old('home_hero_subtitle', $settings['home_hero_subtitle'] ?? '') }}</textarea>
                </div>
                <div>
                    <label>Category Section Title</label>
                    <input type="text" name="home_shop_by_category_title" value="{{ old('home_shop_by_category_title', $settings['home_shop_by_category_title'] ?? '') }}" placeholder="Shop by Category">
                </div>
                <div>
                    <label>Featured Products Title</label>
                    <input type="text" name="home_featured_products_title" value="{{ old('home_featured_products_title', $settings['home_featured_products_title'] ?? '') }}" placeholder="Featured Products">
                </div>
                <div>
                    <label>New Arrivals Title</label>
                    <input type="text" name="home_new_arrivals_title" value="{{ old('home_new_arrivals_title', $settings['home_new_arrivals_title'] ?? '') }}" placeholder="New Arrivals">
                </div>
                <div>
                    <label>Newsletter Title</label>
                    <input type="text" name="home_newsletter_title" value="{{ old('home_newsletter_title', $settings['home_newsletter_title'] ?? '') }}" placeholder="Stay Updated">
                </div>
            </div>
        </div>

        <div class="card stack" style="gap:16px">
            <div>
                <h3 class="section-title" style="font-size:15px">Footer Defaults</h3>
                <p class="section-desc" style="margin-bottom:0">Contact details and about section.</p>
            </div>
            <div class="grid grid-2" style="gap:12px">
                <div>
                    <label>Footer Brand Name</label>
                    <input type="text" name="footer_about_title" value="{{ old('footer_about_title', $settings['footer_about_title'] ?? '') }}" placeholder="ShopHub">
                </div>
                <div>
                    <label>Footer About Text</label>
                    <input type="text" name="footer_about_description" value="{{ old('footer_about_description', $settings['footer_about_description'] ?? '') }}" placeholder="Your trusted marketplace...">
                </div>
                <div>
                    <label>Support Hours (Weekdays)</label>
                    <input type="text" name="footer_support_hours_1" value="{{ old('footer_support_hours_1', $settings['footer_support_hours_1'] ?? '') }}" placeholder="Mon-Fri: 9AM-6PM">
                </div>
                <div>
                    <label>Support Hours (Weekends)</label>
                    <input type="text" name="footer_support_hours_2" value="{{ old('footer_support_hours_2', $settings['footer_support_hours_2'] ?? '') }}" placeholder="Sat-Sun: 10AM-4PM">
                </div>
            </div>
        </div>

        <div class="card stack" x-data="{ rows: [] }" style="gap:16px">
            <div class="inline" style="justify-content:space-between;align-items:center">
                <div>
                    <h3 class="section-title" style="font-size:15px">Supported Countries</h3>
                    <p class="section-desc" style="margin-bottom:0">Active countries for shipping and billing.</p>
                </div>
                <button type="button" class="btn btn-soft" style="padding: 6px 12px; font-size: 12px;" @click="rows.push({code:'', name:'', active:true})">+ Add Country</button>
            </div>

            <div class="grid grid-4" style="gap:8px">
                @foreach($countryOptions as $code => $name)
                    <label class="checkbox-group card" style="padding: 8px 12px; border-radius: 8px; margin-bottom:0">
                        <input type="checkbox" name="country_codes[]" value="{{ $code }}" @checked(in_array($code, old('country_codes', $settings['country_codes'] ?? ['BD'])))>
                        <span class="xsmall">{{ $name }}</span>
                    </label>
                @endforeach
            </div>

            <div class="stack" style="gap:8px">
                <template x-for="(row, index) in rows" :key="index">
                    <div style="padding:12px; background:#f8fafc; border-radius:var(--radius-md); border: 1px solid var(--line)">
                        <div class="grid grid-3" style="gap:10px; align-items: end;">
                            <div>
                                <label class="xsmall">ISO Code</label>
                                <input type="text" :name="`custom_countries[${index}][code]`" x-model="row.code" placeholder="US">
                            </div>
                            <div>
                                <label class="xsmall">Country Name</label>
                                <input type="text" :name="`custom_countries[${index}][name]`" x-model="row.name" placeholder="United States">
                            </div>
                            <div class="btn-row" style="border:0; padding:0; margin:0; justify-content:space-between">
                                <label class="checkbox-group" style="margin-bottom:0">
                                    <input type="checkbox" :name="`custom_countries[${index}][active]`" value="1" x-model="row.active">
                                    <span class="xsmall">Active</span>
                                </label>
                                <button type="button" class="btn btn-soft" style="padding:4px 8px; font-size:11px" @click="rows.splice(index, 1)">Remove</button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="card stack" style="gap:16px">
            <div>
                <h3 class="section-title" style="font-size:15px">Support & Email</h3>
                <p class="section-desc" style="margin-bottom:0">Contact details and SMTP configuration.</p>
            </div>
            <div class="grid grid-2" style="gap:12px">
                <div>
                    <label>Support Email</label>
                    <input type="email" name="email" value="{{ old('email', $settings['email'] ?? '') }}" placeholder="support@example.com">
                </div>
                <div>
                    <label>Support Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $settings['phone'] ?? '') }}" placeholder="+1 555 123 4567">
                </div>
                <div>
                    <label>Mail Host</label>
                    <input type="text" name="mail_host" value="{{ old('mail_host', $settings['mail_host'] ?? '') }}" placeholder="smtp.mailserver.com">
                </div>
                <div class="grid grid-2" style="gap:10px">
                    <div>
                        <label>Mail Port</label>
                        <input type="number" name="mail_port" value="{{ old('mail_port', $settings['mail_port'] ?? '') }}" placeholder="587">
                    </div>
                    <div>
                        <label>Encryption</label>
                        <select name="mail_encryption" class="select-field">
                            <option value="tls" @selected(old('mail_encryption', $settings['mail_encryption'] ?? 'tls') === 'tls')>TLS</option>
                            <option value="ssl" @selected(old('mail_encryption', $settings['mail_encryption'] ?? 'tls') === 'ssl')>SSL</option>
                            <option value="none" @selected(old('mail_encryption', $settings['mail_encryption'] ?? 'tls') === 'none')>None</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label>Mail Username</label>
                    <input type="text" name="mail_username" value="{{ old('mail_username', $settings['mail_username'] ?? '') }}" placeholder="username">
                </div>
                <div>
                    <label>Mail Password</label>
                    <input type="password" name="mail_password" value="{{ old('mail_password', $settings['mail_password'] ?? '') }}" placeholder="••••••••">
                </div>
                <div>
                    <label>Mail From Address</label>
                    <input type="email" name="mail_from_address" value="{{ old('mail_from_address', $settings['mail_from_address'] ?? '') }}" placeholder="noreply@example.com">
                </div>
                <div>
                    <label>Mail From Name</label>
                    <input type="text" name="mail_from_name" value="{{ old('mail_from_name', $settings['mail_from_name'] ?? '') }}" placeholder="Store Name">
                </div>
                <div style="grid-column: 1 / -1">
                    <label>Email Sending Mode</label>
                    <select name="mail_queue_enabled" class="select-field">
                        <option value="0" @selected(old('mail_queue_enabled', $settings['mail_queue_enabled'] ?? '0') === '0')>Synchronous (Immediate)</option>
                        <option value="1" @selected(old('mail_queue_enabled', $settings['mail_queue_enabled'] ?? '0') === '1')>Asynchronous (Queued)</option>
                    </select>
                    <p class="help">Queued mode requires a background worker but improves performance.</p>
                </div>
            </div>
        </div>

        <div class="card stack" style="gap:16px">
            <div>
                <h3 class="section-title" style="font-size:15px">Cloud Storage (S3 / R2)</h3>
                <p class="section-desc" style="margin-bottom:0">External storage for your media files.</p>
            </div>
            <div class="grid grid-2" style="gap:12px">
                <div>
                    <label>Access Key ID</label>
                    <input type="text" name="aws_access_key_id" value="{{ old('aws_access_key_id', $settings['aws_access_key_id'] ?? '') }}" placeholder="Optional">
                </div>
                <div>
                    <label>Secret Access Key</label>
                    <input type="password" name="aws_secret_access_key" value="{{ old('aws_secret_access_key', $settings['aws_secret_access_key'] ?? '') }}" placeholder="Optional">
                </div>
                <div>
                    <label>Default Region</label>
                    <input type="text" name="aws_default_region" value="{{ old('aws_default_region', $settings['aws_default_region'] ?? 'us-east-1') }}">
                    <div class="help">Use <code style="background:#f1f5f9;padding:0 3px;border-radius:4px">auto</code> for Cloudflare R2.</div>
                </div>
                <div>
                    <label>Bucket Name</label>
                    <input type="text" name="aws_bucket" value="{{ old('aws_bucket', $settings['aws_bucket'] ?? '') }}" placeholder="my-bucket">
                </div>
                <div style="grid-column: 1 / -1">
                    <label>S3 Endpoint</label>
                    <input type="text" name="aws_endpoint" value="{{ old('aws_endpoint', $settings['aws_endpoint'] ?? '') }}" placeholder="https://<accountid>.r2.cloudflarestorage.com">
                    <div class="help">Required for R2/DigitalOcean. Leave empty for standard AWS S3.</div>
                </div>
                <div style="grid-column: 1 / -1">
                    <label>Custom Public URL</label>
                    <input type="text" name="aws_url" value="{{ old('aws_url', $settings['aws_url'] ?? '') }}" placeholder="https://pub-xyz.r2.dev">
                    <div class="help">Optional. The public URL for accessing uploaded files.</div>
                </div>
                <div style="grid-column: 1 / -1">
                    <label class="checkbox-group">
                        <input type="checkbox" name="aws_use_path_style_endpoint" value="1" @checked(old('aws_use_path_style_endpoint', $settings['aws_use_path_style_endpoint'] ?? false))>
                        <span class="small">Use path-style endpoint (Recommended for MinIO)</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="card stack" style="gap:16px">
            <div>
                <h3 class="section-title" style="font-size:15px">System Maintenance</h3>
                <p class="section-desc" style="margin-bottom:0">Backups and task scheduling.</p>
            </div>
            <div class="grid grid-2" style="gap:12px">
                <div>
                    <label>Automatic Backups</label>
                    <select name="backup_enabled" class="select-field">
                        <option value="1" @selected(old('backup_enabled', $settings['backup_enabled'] ?? '1') === '1')>Enabled (Daily)</option>
                        <option value="0" @selected(old('backup_enabled', $settings['backup_enabled'] ?? '1') === '0')>Disabled</option>
                    </select>
                </div>
                <div>
                    <label>Execution Mode</label>
                    <select name="background_mode" class="select-field">
                        <option value="cron" @selected(old('background_mode', $settings['background_mode'] ?? 'cron') === 'cron')>Shared Hosting (Cron)</option>
                        <option value="worker" @selected(old('background_mode', $settings['background_mode'] ?? 'cron') === 'worker')>VPS / Dedicated (Worker)</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="grid grid-2" style="gap:20px">
            <div class="card stack" style="gap:12px">
                <h4 class="section-title" style="font-size:14px">Stripe</h4>
                <div>
                    <label class="xsmall">API Key</label>
                    <input type="text" name="stripe_api_key" value="{{ old('stripe_api_key', $settings['stripe_api_key'] ?? '') }}" placeholder="sk_test_...">
                </div>
                <div>
                    <label class="xsmall">Label</label>
                    <input type="text" name="stripe_label" value="{{ old('stripe_label', $settings['stripe_label'] ?? '') }}" placeholder="Credit Card">
                </div>
            </div>

            <div class="card stack" style="gap:12px">
                <h4 class="section-title" style="font-size:14px">PayPal</h4>
                <div>
                    <label class="xsmall">Client ID</label>
                    <input type="text" name="paypal_api_key" value="{{ old('paypal_api_key', $settings['paypal_api_key'] ?? '') }}" placeholder="PayPal Client ID">
                </div>
                <div>
                    <label class="xsmall">Label</label>
                    <input type="text" name="paypal_label" value="{{ old('paypal_label', $settings['paypal_label'] ?? '') }}" placeholder="PayPal">
                </div>
            </div>

            <div class="card stack" style="gap:12px">
                <h4 class="section-title" style="font-size:14px">SSLCommerz</h4>
                <div class="grid grid-2" style="gap:10px">
                    <div>
                        <label class="xsmall">Store ID</label>
                        <input type="text" name="sslcommerz_store_id" value="{{ old('sslcommerz_store_id', $settings['sslcommerz_store_id'] ?? '') }}" placeholder="Store ID">
                    </div>
                    <div>
                        <label class="xsmall">API Key</label>
                        <input type="text" name="sslcommerz_api_key" value="{{ old('sslcommerz_api_key', $settings['sslcommerz_api_key'] ?? '') }}" placeholder="API Key">
                    </div>
                </div>
                <div>
                    <label class="xsmall">Label</label>
                    <input type="text" name="sslcommerz_label" value="{{ old('sslcommerz_label', $settings['sslcommerz_label'] ?? '') }}" placeholder="SSLCommerz">
                </div>
                <label class="checkbox-group">
                    <input type="checkbox" name="sslcommerz_sandbox" value="1" @checked(old('sslcommerz_sandbox', $settings['sslcommerz_sandbox'] ?? false))>
                    <span class="xsmall">Use Sandbox Mode</span>
                </label>
            </div>

            <div class="card stack" style="gap:12px">
                <h4 class="section-title" style="font-size:14px">PortPos</h4>
                <div class="grid grid-2" style="gap:10px">
                    <div>
                        <label class="xsmall">App Key</label>
                        <input type="text" name="portpos_app_key" value="{{ old('portpos_app_key', $settings['portpos_app_key'] ?? '') }}" placeholder="App Key">
                    </div>
                    <div>
                        <label class="xsmall">Secret Key</label>
                        <input type="password" name="portpos_secret_key" value="{{ old('portpos_secret_key', $settings['portpos_secret_key'] ?? '') }}" placeholder="••••••••">
                    </div>
                </div>
                <div>
                    <label class="xsmall">Label</label>
                    <input type="text" name="portpos_label" value="{{ old('portpos_label', $settings['portpos_label'] ?? '') }}" placeholder="PortPos">
                </div>
                <label class="checkbox-group">
                    <input type="checkbox" name="portpos_sandbox" value="1" @checked(old('portpos_sandbox', $settings['portpos_sandbox'] ?? false))>
                    <span class="xsmall">Use Sandbox Mode</span>
                </label>
            </div>

            <div class="card stack" style="grid-column: 1 / -1; gap:12px">
                <h4 class="section-title" style="font-size:14px">bKash</h4>
                <div class="grid grid-2" style="gap:10px">
                    <div>
                        <label class="xsmall">Base URL</label>
                        <input type="text" name="bkash_base_url" value="{{ old('bkash_base_url', $settings['bkash_base_url'] ?? '') }}" placeholder="https://...">
                    </div>
                    <div>
                        <label class="xsmall">App Key</label>
                        <input type="text" name="bkash_app_key" value="{{ old('bkash_app_key', $settings['bkash_app_key'] ?? '') }}" placeholder="App Key">
                    </div>
                    <div>
                        <label class="xsmall">App Secret</label>
                        <input type="text" name="bkash_app_secret" value="{{ old('bkash_app_secret', $settings['bkash_app_secret'] ?? '') }}" placeholder="App Secret">
                    </div>
                    <div>
                        <label class="xsmall">Username</label>
                        <input type="text" name="bkash_username" value="{{ old('bkash_username', $settings['bkash_username'] ?? '') }}" placeholder="Username">
                    </div>
                    <div style="grid-column: 1 / -1">
                        <label class="xsmall">Password</label>
                        <input type="password" name="bkash_password" value="{{ old('bkash_password', $settings['bkash_password'] ?? '') }}" placeholder="••••••••">
                    </div>
                </div>
            </div>
        </div>

        <div class="btn-row">
            <a class="btn btn-soft" href="{{ route('install.database') }}">Back</a>
            <button class="btn btn-primary" type="submit">Review Finalize Step</button>
        </div>
    </form>
@endsection
