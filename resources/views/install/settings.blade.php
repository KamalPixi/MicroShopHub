@extends('install.layout')

@section('content')
    @php($progress = 75)
    <div class="progress-shell">
        <div class="progress-top">
            <div>
                <div class="progress-meta">Step 3 of 4</div>
                <div class="small" style="font-weight:700;color:#111827">Store Settings</div>
            </div>
            <div class="progress-meta">75%</div>
        </div>
        <div class="progress-track"><div class="progress-fill" style="width:75%"></div></div>
    </div>

    <div class="stepbar">
        <span class="step">1. Requirements</span>
        <span class="step">2. Database</span>
        <span class="step active">3. Settings</span>
        <span class="step">4. Finish</span>
    </div>

    <form method="POST" action="{{ route('install.settings.store') }}" enctype="multipart/form-data" class="card stack">
        @csrf
        <div>
            <h2 style="margin:0 0 6px;font-size:18px">Default store settings</h2>
            <p class="muted small" style="margin:0">Fill what you need now. You can skip optional fields and edit them later from admin.</p>
        </div>

        <div class="grid grid-2">
            <div>
                <label>Domain / App URL</label>
                <input type="url" name="app_url" value="{{ old('app_url', $settings['app_url'] ?? '') }}" placeholder="https://example.com">
                <div class="help">Optional. Used as the base URL.</div>
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
                <label>Logo</label>
                <input type="file" name="logo" accept="image/*">
            </div>
        </div>

        <div class="grid grid-3">
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

        <div class="grid grid-3">
            <div>
                <label>Default Language</label>
                <select name="store_default_locale">
                    <option value="en" @selected(old('store_default_locale', $settings['store_default_locale'] ?? 'en') === 'en')>English</option>
                    <option value="bn" @selected(old('store_default_locale', $settings['store_default_locale'] ?? 'en') === 'bn')>Bengali</option>
                </select>
            </div>
            <div>
                <label>Currency</label>
                <input type="text" name="currency" value="{{ old('currency', $settings['currency'] ?? 'BDT') }}" placeholder="BDT">
            </div>
            <div>
                <label>Enable COD</label>
                <div class="checkbox" style="margin-top:0">
                    <input type="checkbox" name="cod_enabled" value="1" @checked(old('cod_enabled', $settings['cod_enabled'] ?? true))>
                    <span class="small">Cash on delivery</span>
                </div>
            </div>
        </div>

        <div class="card" style="padding:16px">
            <h3 style="margin:0 0 10px;font-size:16px">Homepage Defaults</h3>
            <div class="grid grid-2">
                <div>
                    <label>Hero Title</label>
                    <input type="text" name="home_hero_title" value="{{ old('home_hero_title', $settings['home_hero_title'] ?? '') }}" placeholder="Find what fits your life">
                </div>
                <div>
                    <label>Hero Subtitle</label>
                    <input type="text" name="home_hero_subtitle" value="{{ old('home_hero_subtitle', $settings['home_hero_subtitle'] ?? '') }}" placeholder="Curated products, fast delivery, and easy browsing.">
                </div>
                <div>
                    <label>Shop by Category Title</label>
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
                <div class="md:col-span-2">
                    <label>Newsletter Subtitle</label>
                    <input type="text" name="home_newsletter_subtitle" value="{{ old('home_newsletter_subtitle', $settings['home_newsletter_subtitle'] ?? '') }}" placeholder="Subscribe for new arrivals, exclusive offers, and restock alerts.">
                </div>
            </div>
        </div>

        <div class="card" style="padding:16px">
            <h3 style="margin:0 0 10px;font-size:16px">Footer Defaults</h3>
            <div class="grid grid-2">
                <div>
                    <label>Footer About Title</label>
                    <input type="text" name="footer_about_title" value="{{ old('footer_about_title', $settings['footer_about_title'] ?? '') }}" placeholder="ShopHub">
                </div>
                <div>
                    <label>Footer About Description</label>
                    <input type="text" name="footer_about_description" value="{{ old('footer_about_description', $settings['footer_about_description'] ?? '') }}" placeholder="Your trusted marketplace...">
                </div>
                <div>
                    <label>Support Hours 1</label>
                    <input type="text" name="footer_support_hours_1" value="{{ old('footer_support_hours_1', $settings['footer_support_hours_1'] ?? '') }}" placeholder="Mon-Fri: 9AM-6PM">
                </div>
                <div>
                    <label>Support Hours 2</label>
                    <input type="text" name="footer_support_hours_2" value="{{ old('footer_support_hours_2', $settings['footer_support_hours_2'] ?? '') }}" placeholder="Sat-Sun: 10AM-4PM">
                </div>
            </div>
        </div>

        <div class="card" style="padding:16px">
            <h3 style="margin:0 0 10px;font-size:16px">Supported Countries</h3>
            <p class="muted xsmall" style="margin:0 0 12px">Select the countries you want active on the storefront.</p>
            <div class="checkboxes">
                @foreach($countryOptions as $code => $name)
                    <label class="checkbox">
                        <input type="checkbox" name="country_codes[]" value="{{ $code }}" @checked(in_array($code, old('country_codes', $settings['country_codes'] ?? ['BD'])))>
                        <span class="small">{{ $name }} ({{ $code }})</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="card" style="padding:16px">
            <h3 style="margin:0 0 10px;font-size:16px">Support & Email</h3>
            <div class="grid grid-2">
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
                <div>
                    <label>Mail Port</label>
                    <input type="number" name="mail_port" value="{{ old('mail_port', $settings['mail_port'] ?? '') }}" placeholder="587">
                </div>
                <div>
                    <label>Mail Username</label>
                    <input type="text" name="mail_username" value="{{ old('mail_username', $settings['mail_username'] ?? '') }}" placeholder="username">
                </div>
                <div>
                    <label>Mail Password</label>
                    <input type="password" name="mail_password" value="{{ old('mail_password', $settings['mail_password'] ?? '') }}" placeholder="Optional">
                </div>
                <div>
                    <label>Mail Encryption</label>
                    <select name="mail_encryption">
                        <option value="tls" @selected(old('mail_encryption', $settings['mail_encryption'] ?? 'tls') === 'tls')>TLS</option>
                        <option value="ssl" @selected(old('mail_encryption', $settings['mail_encryption'] ?? 'tls') === 'ssl')>SSL</option>
                        <option value="none" @selected(old('mail_encryption', $settings['mail_encryption'] ?? 'tls') === 'none')>None</option>
                    </select>
                </div>
                <div>
                    <label>Mail From Address</label>
                    <input type="email" name="mail_from_address" value="{{ old('mail_from_address', $settings['mail_from_address'] ?? '') }}" placeholder="noreply@example.com">
                </div>
                <div>
                    <label>Mail From Name</label>
                    <input type="text" name="mail_from_name" value="{{ old('mail_from_name', $settings['mail_from_name'] ?? '') }}" placeholder="Store Name">
                </div>
            </div>
        </div>

        <div class="card" style="padding:16px">
            <h3 style="margin:0 0 10px;font-size:16px">Gateway Details (Optional)</h3>
            <div class="grid grid-2">
                <div>
                    <label>Stripe API Key</label>
                    <input type="text" name="stripe_api_key" value="{{ old('stripe_api_key', $settings['stripe_api_key'] ?? '') }}" placeholder="Optional">
                </div>
                <div>
                    <label>Stripe Label</label>
                    <input type="text" name="stripe_label" value="{{ old('stripe_label', $settings['stripe_label'] ?? '') }}" placeholder="Credit / Debit Card">
                </div>
                <div>
                    <label>PayPal API Key</label>
                    <input type="text" name="paypal_api_key" value="{{ old('paypal_api_key', $settings['paypal_api_key'] ?? '') }}" placeholder="Optional">
                </div>
                <div>
                    <label>PayPal Label</label>
                    <input type="text" name="paypal_label" value="{{ old('paypal_label', $settings['paypal_label'] ?? '') }}" placeholder="PayPal">
                </div>
                <div>
                    <label>SSLCommerz Store ID</label>
                    <input type="text" name="sslcommerz_store_id" value="{{ old('sslcommerz_store_id', $settings['sslcommerz_store_id'] ?? '') }}" placeholder="Optional">
                </div>
                <div>
                    <label>SSLCommerz API Key</label>
                    <input type="text" name="sslcommerz_api_key" value="{{ old('sslcommerz_api_key', $settings['sslcommerz_api_key'] ?? '') }}" placeholder="Optional">
                </div>
                <div>
                    <label>SSLCommerz Label</label>
                    <input type="text" name="sslcommerz_label" value="{{ old('sslcommerz_label', $settings['sslcommerz_label'] ?? '') }}" placeholder="SSLCommerz">
                </div>
                <div class="checkbox" style="align-self:end">
                    <input type="checkbox" name="sslcommerz_sandbox" value="1" @checked(old('sslcommerz_sandbox', $settings['sslcommerz_sandbox'] ?? false))>
                    <span class="small">Use SSLCommerz sandbox</span>
                </div>
                <div>
                    <label>bKash Base URL</label>
                    <input type="text" name="bkash_base_url" value="{{ old('bkash_base_url', $settings['bkash_base_url'] ?? '') }}" placeholder="Optional">
                </div>
                <div>
                    <label>bKash App Key</label>
                    <input type="text" name="bkash_app_key" value="{{ old('bkash_app_key', $settings['bkash_app_key'] ?? '') }}" placeholder="Optional">
                </div>
                <div>
                    <label>bKash App Secret</label>
                    <input type="text" name="bkash_app_secret" value="{{ old('bkash_app_secret', $settings['bkash_app_secret'] ?? '') }}" placeholder="Optional">
                </div>
                <div>
                    <label>bKash Username</label>
                    <input type="text" name="bkash_username" value="{{ old('bkash_username', $settings['bkash_username'] ?? '') }}" placeholder="Optional">
                </div>
                <div>
                    <label>bKash Password</label>
                    <input type="password" name="bkash_password" value="{{ old('bkash_password', $settings['bkash_password'] ?? '') }}" placeholder="Optional">
                </div>
            </div>
        </div>

        <div class="btn-row">
            <a class="btn btn-soft" href="{{ route('install.database') }}">Back</a>
            <button class="btn btn-primary" type="submit">Finalize Installation</button>
        </div>
    </form>
@endsection
