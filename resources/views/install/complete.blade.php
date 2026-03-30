@extends('install.layout')

@section('content')
    <div class="stepbar">
        <span class="step done">1. Requirements</span>
        <span class="step done">2. Database</span>
        <span class="step done">3. Settings</span>
        <span class="step active">4. Finish</span>
    </div>

    <div class="two-col">
        <div class="card" style="text-align:center">
            <div style="width:72px;height:72px;border-radius:999px;background:#dcfce7;color:#166534;display:flex;align-items:center;justify-content:center;font-size:30px;font-weight:800;margin:0 auto 16px;">✓</div>
            <h2 style="margin:0 0 8px;font-size:22px">Installation completed</h2>
            <p class="muted small" style="margin:0 0 14px">Your store is ready to use.</p>
            <div class="stack small muted" style="text-align:left">
                <div>• Store: {{ $storeName }}</div>
                <div>• Admin login: {{ $adminEmail }}</div>
                <div>• Admin URL: {{ $adminUrl }}</div>
                <div>• Default data, languages, currencies, countries, and settings are loaded.</div>
                <div>• The installer is locked and will not appear again.</div>
            </div>
        </div>

        <div class="card stack">
            <h3 style="margin:0;font-size:18px">Next steps</h3>
            <div class="stack small muted">
                <div>1. Log in to the admin panel and review store settings.</div>
                <div>2. Update shop name, logo, colors, payment gateways, and support details if needed.</div>
                <div>3. Add products, shipping methods, and coupons.</div>
                <div>4. Test a storefront order before going live.</div>
            </div>
            <div class="btn-row" style="justify-content:flex-start">
                <a class="btn btn-primary" href="{{ route('admin.login') }}">Open Admin Login</a>
                <a class="btn btn-soft" href="{{ route('store.index') }}">Visit Storefront</a>
            </div>
        </div>
    </div>
@endsection
