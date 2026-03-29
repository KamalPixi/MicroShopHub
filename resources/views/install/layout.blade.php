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
        .wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px}
        .shell{width:min(1120px,100%);background:var(--card);border:1px solid var(--line);border-radius:24px;box-shadow:0 20px 50px rgba(15,23,42,.08);overflow:hidden}
        .top{padding:24px 28px;border-bottom:1px solid var(--line);background:linear-gradient(180deg,#fff,#fafafa)}
        .title{margin:0;font-size:24px;line-height:1.2}
        .subtitle{margin:6px 0 0;color:var(--muted);font-size:14px}
        .content{padding:28px}
        .grid{display:grid;gap:16px}
        .grid-2{grid-template-columns:repeat(2,minmax(0,1fr))}
        .grid-3{grid-template-columns:repeat(3,minmax(0,1fr))}
        .grid-4{grid-template-columns:repeat(4,minmax(0,1fr))}
        .card{border:1px solid var(--line);border-radius:18px;background:#fff;padding:18px}
        .muted{color:var(--muted)}
        .small{font-size:13px}
        .xsmall{font-size:12px}
        .stepbar{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:18px}
        .step{padding:8px 12px;border:1px solid var(--line);border-radius:999px;font-size:12px;color:var(--muted);background:#fff}
        .step.active{background:var(--primary);border-color:var(--primary);color:#fff}
        .progress-shell{background:#fff;border:1px solid var(--line);border-radius:16px;padding:14px 16px;margin-bottom:18px}
        .progress-top{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:10px}
        .progress-track{height:10px;background:#e5e7eb;border-radius:999px;overflow:hidden}
        .progress-fill{height:100%;background:var(--primary);border-radius:999px}
        label{display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px}
        input, select, textarea{width:100%;padding:11px 12px;border:1px solid #cfd5dd;border-radius:12px;font:inherit;background:#fff;color:var(--text)}
        textarea{min-height:120px;resize:vertical}
        .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:1px solid transparent;border-radius:14px;padding:11px 16px;font-weight:700;cursor:pointer}
        .btn-primary{background:var(--primary);color:#fff}
        .btn-soft{background:#fff;border-color:var(--line);color:var(--text)}
        .btn-row{display:flex;gap:12px;flex-wrap:wrap;justify-content:flex-end}
        .ok{color:var(--good);background:var(--good-bg);border:1px solid #bbf7d0}
        .bad{color:var(--bad);background:var(--bad-bg);border:1px solid #fecaca}
        .pill{display:inline-flex;align-items:center;padding:6px 10px;border-radius:999px;font-size:12px;font-weight:700}
        .stack{display:grid;gap:14px}
        .checklist{display:grid;gap:10px}
        .checkitem{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:12px 14px;border:1px solid var(--line);border-radius:14px;background:#fff}
        .footer{padding:20px 28px;border-top:1px solid var(--line);font-size:12px;color:var(--muted)}
        .two-col{display:grid;grid-template-columns:1.15fr .85fr;gap:20px}
        .inline{display:flex;gap:10px;align-items:center;flex-wrap:wrap}
        .checkboxes{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}
        .checkbox{display:flex;align-items:center;gap:10px;padding:10px 12px;border:1px solid var(--line);border-radius:12px;background:#fff}
        .checkbox input{width:auto}
        .help{font-size:12px;color:var(--muted);margin-top:6px}
        .error{margin-top:6px;font-size:12px;color:#b91c1c}
        .progress-meta{font-size:12px;color:var(--muted);font-weight:600}
        @media (max-width: 900px){.grid-2,.grid-3,.grid-4,.two-col,.checkboxes{grid-template-columns:1fr}}
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
