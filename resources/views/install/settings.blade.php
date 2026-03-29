@extends('install.layout')

@section('content')
    <div class="stepbar">
        <span class="step done">1. Requirements</span>
        <span class="step done">2. Database</span>
        <span class="step active">3. Settings</span>
        <span class="step">4. Finish</span>
    </div>

    <form method="POST" action="{{ route('install.settings.store') }}" enctype="multipart/form-data" class="card stack">
        @csrf
        @php($customCurrencies = old('custom_currencies'))
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
                <div class="help">Auto-filled from your site URL when possible.</div>
            </div>
            <div>
                <label>Slogan</label>
                <input type="text" name="slogan" value="{{ old('slogan', $settings['slogan'] ?? '') }}" placeholder="Short brand slogan">
                <div class="help">A short line derived from your shop name.</div>
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

        <div class="grid grid-2">
            <div>
                <label>Default Language</label>
                <select name="store_default_locale" class="select-field">
                    <option value="en" @selected(old('store_default_locale', $settings['store_default_locale'] ?? 'en') === 'en')>English</option>
                    <option value="bn" @selected(old('store_default_locale', $settings['store_default_locale'] ?? 'en') === 'bn')>Bengali</option>
                </select>
                <div class="help">This is the first language customers will see on the storefront.</div>
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
                <div class="help">This is the base currency for the store. Add any extra currencies below if needed.</div>
            </div>
        </div>

        <div class="card" style="padding:16px" x-data="{ rows: @js(array_values($customCurrencies ?: [])) }">
            <div class="inline" style="justify-content:space-between;align-items:center;margin-bottom:8px">
                <div>
                    <h3 style="margin:0 0 4px;font-size:16px">Additional Currencies</h3>
                    <p class="muted xsmall" style="margin:0">Add only what you need. Exchange rate is relative to the default currency above.</p>
                </div>
                <button type="button" class="btn btn-soft" @click="rows.push({code:'', name:'', symbol:'', exchange_rate:1, active:true})">Add Currency</button>
            </div>

            <div class="stack">
                <template x-for="(row, index) in rows" :key="index">
                    <div class="card" style="padding:14px">
                        <div class="grid grid-4" style="gap:10px">
                            <div>
                                <label>Code</label>
                                <input type="text" :name="`custom_currencies[${index}][code]`" x-model="row.code" placeholder="USD">
                            </div>
                            <div>
                                <label>Name</label>
                                <input type="text" :name="`custom_currencies[${index}][name]`" x-model="row.name" placeholder="US Dollar">
                            </div>
                            <div>
                                <label>Symbol</label>
                                <input type="text" :name="`custom_currencies[${index}][symbol]`" x-model="row.symbol" placeholder="$">
                            </div>
                            <div>
                                <label>Exchange Rate</label>
                                <input type="number" step="0.0001" min="0.0001" :name="`custom_currencies[${index}][exchange_rate]`" x-model="row.exchange_rate" placeholder="1.0000">
                            </div>
                        </div>
                        <div class="btn-row" style="justify-content:space-between;margin-top:10px">
                            <label class="checkbox" style="margin-top:0">
                                <input type="checkbox" :name="`custom_currencies[${index}][active]`" value="1" x-model="row.active">
                                <span class="small">Active</span>
                            </label>
                            <button type="button" class="btn btn-soft" @click="rows.splice(index, 1)">Remove</button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="grid grid-2">
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

        <div class="card" style="padding:16px" x-data="{ rows: [] }">
            <div class="inline" style="justify-content:space-between;align-items:center;margin-bottom:8px">
                <div>
                    <h3 style="margin:0 0 4px;font-size:16px">Supported Countries</h3>
                    <p class="muted xsmall" style="margin:0">Select countries that should be active on the storefront or add a new country below.</p>
                </div>
                <button type="button" class="btn btn-soft" @click="rows.push({code:'', name:'', active:true})">Add Country</button>
            </div>

            <div class="checkboxes" style="margin-bottom:14px">
                @foreach($countryOptions as $code => $name)
                    <label class="checkbox">
                        <input type="checkbox" name="country_codes[]" value="{{ $code }}" @checked(in_array($code, old('country_codes', $settings['country_codes'] ?? ['BD'])))>
                        <span class="small">{{ $name }} ({{ $code }})</span>
                    </label>
                @endforeach
            </div>

            <div class="stack">
                <template x-for="(row, index) in rows" :key="index">
                    <div class="card" style="padding:14px">
                        <div class="grid grid-3" style="gap:10px">
                            <div>
                                <label>Code</label>
                                <input type="text" :name="`custom_countries[${index}][code]`" x-model="row.code" placeholder="US">
                            </div>
                            <div>
                                <label>Name</label>
                                <input type="text" :name="`custom_countries[${index}][name]`" x-model="row.name" placeholder="United States">
                            </div>
                            <div>
                                <label>Status</label>
                                <label class="checkbox" style="margin-top:0">
                                    <input type="checkbox" :name="`custom_countries[${index}][active]`" value="1" x-model="row.active">
                                    <span class="small">Active</span>
                                </label>
                            </div>
                        </div>
                        <div class="btn-row" style="justify-content:flex-end;margin-top:10px">
                            <button type="button" class="btn btn-soft" @click="rows.splice(index, 1)">Remove</button>
                        </div>
                    </div>
                </template>
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
                <div class="card" style="padding:14px">
                    <h4 style="margin:0 0 10px;font-size:14px">Stripe</h4>
                    <div class="stack">
                        <div>
                            <label>API Key</label>
                            <input type="text" name="stripe_api_key" value="{{ old('stripe_api_key', $settings['stripe_api_key'] ?? '') }}" placeholder="Optional">
                        </div>
                        <div>
                            <label>Label</label>
                            <input type="text" name="stripe_label" value="{{ old('stripe_label', $settings['stripe_label'] ?? '') }}" placeholder="Credit / Debit Card">
                        </div>
                    </div>
                </div>
                <div class="card" style="padding:14px">
                    <h4 style="margin:0 0 10px;font-size:14px">PayPal</h4>
                    <div class="stack">
                        <div>
                            <label>API Key</label>
                            <input type="text" name="paypal_api_key" value="{{ old('paypal_api_key', $settings['paypal_api_key'] ?? '') }}" placeholder="Optional">
                        </div>
                        <div>
                            <label>Label</label>
                            <input type="text" name="paypal_label" value="{{ old('paypal_label', $settings['paypal_label'] ?? '') }}" placeholder="PayPal">
                        </div>
                    </div>
                </div>
                <div class="card" style="padding:14px">
                    <h4 style="margin:0 0 10px;font-size:14px">SSLCommerz</h4>
                    <div class="stack">
                        <div>
                            <label>Store ID</label>
                            <input type="text" name="sslcommerz_store_id" value="{{ old('sslcommerz_store_id', $settings['sslcommerz_store_id'] ?? '') }}" placeholder="Optional">
                        </div>
                        <div>
                            <label>API Key</label>
                            <input type="text" name="sslcommerz_api_key" value="{{ old('sslcommerz_api_key', $settings['sslcommerz_api_key'] ?? '') }}" placeholder="Optional">
                        </div>
                        <div>
                            <label>Label</label>
                            <input type="text" name="sslcommerz_label" value="{{ old('sslcommerz_label', $settings['sslcommerz_label'] ?? '') }}" placeholder="SSLCommerz">
                        </div>
                        <label class="checkbox" style="margin-top:0">
                            <input type="checkbox" name="sslcommerz_sandbox" value="1" @checked(old('sslcommerz_sandbox', $settings['sslcommerz_sandbox'] ?? false))>
                            <span class="small">Use sandbox</span>
                        </label>
                    </div>
                </div>
                <div class="card" style="padding:14px">
                    <h4 style="margin:0 0 10px;font-size:14px">bKash</h4>
                    <div class="stack">
                        <div>
                            <label>Base URL</label>
                            <input type="text" name="bkash_base_url" value="{{ old('bkash_base_url', $settings['bkash_base_url'] ?? '') }}" placeholder="Optional">
                        </div>
                        <div>
                            <label>App Key</label>
                            <input type="text" name="bkash_app_key" value="{{ old('bkash_app_key', $settings['bkash_app_key'] ?? '') }}" placeholder="Optional">
                        </div>
                        <div>
                            <label>App Secret</label>
                            <input type="text" name="bkash_app_secret" value="{{ old('bkash_app_secret', $settings['bkash_app_secret'] ?? '') }}" placeholder="Optional">
                        </div>
                        <div>
                            <label>Username</label>
                            <input type="text" name="bkash_username" value="{{ old('bkash_username', $settings['bkash_username'] ?? '') }}" placeholder="Optional">
                        </div>
                        <div>
                            <label>Password</label>
                            <input type="password" name="bkash_password" value="{{ old('bkash_password', $settings['bkash_password'] ?? '') }}" placeholder="Optional">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="btn-row">
            <a class="btn btn-soft" href="{{ route('install.database') }}">Back</a>
            <button class="btn btn-primary" type="submit">Finalize Installation</button>
        </div>
    </form>
@endsection
