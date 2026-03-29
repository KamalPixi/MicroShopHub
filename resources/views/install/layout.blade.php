<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'MicroShopHub') }} Installer</title>
    <style>
        :root{--bg:#f4f6f8;--card:#ffffff;--line:#d9dde3;--text:#111827;--muted:#6b7280;--primary:#111111;--good:#166534;--good-bg:#dcfce7;--bad:#991b1b;--bad-bg:#fee2e2}
        *{box-sizing:border-box}
        body{margin:0;font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;background:var(--bg);color:var(--text)}
        a{color:inherit;text-decoration:none}
        .wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:18px}
        .shell{width:min(1120px,100%);background:var(--card);border:1px solid var(--line);border-radius:22px;box-shadow:0 16px 40px rgba(15,23,42,.08);overflow:hidden}
        .top{padding:20px 24px;border-bottom:1px solid var(--line);background:linear-gradient(180deg,#fff,#fbfbfc)}
        .title{margin:0;font-size:22px;line-height:1.2}
        .subtitle{margin:6px 0 0;color:var(--muted);font-size:14px}
        .content{padding:22px}
        .grid{display:grid;gap:14px}
        .grid-2{grid-template-columns:repeat(2,minmax(0,1fr))}
        .grid-3{grid-template-columns:repeat(3,minmax(0,1fr))}
        .grid-4{grid-template-columns:repeat(4,minmax(0,1fr))}
        .card{border:1px solid var(--line);border-radius:18px;background:#fff;padding:16px}
        .muted{color:var(--muted)}
        .small{font-size:13px}
        .xsmall{font-size:12px}
        .stepbar{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px}
        .step{padding:7px 11px;border:1px solid var(--line);border-radius:999px;font-size:12px;color:var(--muted);background:#fff}
        .step.active{background:var(--primary);border-color:var(--primary);color:#fff}
        .step.done{background:#f3f4f6;border-color:#d1d5db;color:#111827}
        .step.done::before{content:"✓";display:inline-block;margin-right:6px;font-weight:800}
        label{display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px}
        input, select, textarea{width:100%;padding:10px 12px;border:1px solid #cfd5dd;border-radius:12px;font:inherit;background:#fff;color:var(--text)}
        .select-field{appearance:none;-webkit-appearance:none;-moz-appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 20 20' fill='none'%3E%3Cpath d='M5 7.5L10 12.5L15 7.5' stroke='%236b7280' stroke-width='1.8' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 12px center;background-size:16px;padding-right:38px;height:42px}
        textarea{min-height:112px;resize:vertical}
        .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:1px solid transparent;border-radius:14px;padding:10px 14px;font-weight:700;cursor:pointer}
        .btn-primary{background:var(--primary);color:#fff}
        .btn-soft{background:#fff;border-color:var(--line);color:var(--text)}
        .btn-row{display:flex;gap:12px;flex-wrap:wrap;justify-content:flex-end}
        .ok{color:var(--good);background:var(--good-bg);border:1px solid #bbf7d0}
        .bad{color:var(--bad);background:var(--bad-bg);border:1px solid #fecaca}
        .pill{display:inline-flex;align-items:center;padding:6px 10px;border-radius:999px;font-size:12px;font-weight:700}
        .stack{display:grid;gap:12px}
        .checklist{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px}
        .checkitem{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:10px 12px;border:1px solid var(--line);border-radius:12px;background:#fff}
        .footer{padding:16px 24px;border-top:1px solid var(--line);font-size:12px;color:var(--muted)}
        .two-col{display:grid;grid-template-columns:1.15fr .85fr;gap:20px}
        .inline{display:flex;gap:10px;align-items:center;flex-wrap:wrap}
        .checkboxes{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px}
        .checkbox{display:flex;align-items:center;gap:10px;padding:10px 12px;border:1px solid var(--line);border-radius:12px;background:#fff}
        .checkbox input{width:auto}
        .help{font-size:12px;color:var(--muted);margin-top:6px}
        .error{margin-top:6px;font-size:12px;color:#b91c1c}
        @media (max-width: 900px){.grid-2,.grid-3,.grid-4,.two-col,.checkboxes,.checklist{grid-template-columns:1fr}}
        @media (max-width: 640px){.content,.top,.footer{padding-left:16px;padding-right:16px}.title{font-size:20px}}
    </style>
</head>
<body>
    <div class="wrap">
        <div class="shell">
            <div class="top">
                <h1 class="title">{{ config('app.name', 'MicroShopHub') }} Installer</h1>
                <p class="subtitle">Step-by-step setup for a fresh installation.</p>
            </div>
            <div class="content">
                @if ($errors->any())
                    <div class="card bad" style="margin-bottom:16px">
                        <div class="small" style="font-weight:700;margin-bottom:6px">Please fix the following:</div>
                        <ul style="margin:0;padding-left:18px" class="small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('message'))
                    <div class="card ok" style="margin-bottom:16px">{{ session('message') }}</div>
                @endif

                @yield('content')
            </div>
            <div class="footer">
                Installer must be completed once. After finalization the installer is locked automatically.
            </div>
        </div>
    </div>
</body>
</html>
