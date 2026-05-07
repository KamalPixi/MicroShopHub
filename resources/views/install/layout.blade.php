<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MicroShopHub Installer</title>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        :root {
            --bg: #f8fafc;
            --card: #ffffff;
            --line: #e2e8f0;
            --text: #0f172a;
            --muted: #64748b;
            --primary: #0f172a;
            --primary-hover: #1e293b;
            --good: #15803d;
            --good-bg: #f0fdf4;
            --bad: #b91c1c;
            --bad-bg: #fef2f2;
            --accent: #3b82f6;
            --radius-lg: 16px;
            --radius-md: 12px;
            --radius-sm: 8px;
        }
        * { box-sizing: border-box; }
        body { 
            margin: 0; 
            font-family: 'Inter', system-ui, -apple-system, sans-serif; 
            background: var(--bg); 
            color: var(--text); 
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }
        a { color: var(--accent); text-decoration: none; transition: 0.2s; }
        a:hover { opacity: 0.8; }
        
        .wrap { min-height: 100vh; display: flex; align-items: flex-start; justify-content: center; padding: 40px 20px; }
        .shell { width: min(840px, 100%); background: var(--card); border: 1px solid var(--line); border-radius: var(--radius-lg); box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1); overflow: hidden; }
        
        .notice-bar { background: #fffbeb; border-bottom: 1px solid #fef3c7; padding: 12px 24px; font-size: 13px; color: #92400e; font-weight: 500; text-align: center; }
        
        .top { padding: 32px 40px; border-bottom: 1px solid var(--line); background: #fff; }
        .title { margin: 0; font-size: 24px; font-weight: 700; letter-spacing: -0.025em; }
        .subtitle { margin: 4px 0 0; color: var(--muted); font-size: 14px; }
        
        .content { padding: 40px; background: #fff; }
        
        .section-title { font-size: 16px; font-weight: 600; margin-bottom: 4px; color: var(--text); }
        .section-desc { font-size: 13px; color: var(--muted); margin-bottom: 20px; }
        
        .grid { display: grid; gap: 20px; }
        .grid-2 { grid-template-columns: repeat(2, 1fr); }
        .grid-3 { grid-template-columns: repeat(3, 1fr); }
        .grid-4 { grid-template-columns: repeat(4, 1fr); }
        
        .card { border: 1px solid var(--line); border-radius: var(--radius-md); background: #fff; padding: 24px; transition: 0.2s; }
        .stack { display: grid; gap: 24px; }
        
        .muted { color: var(--muted); }
        .small { font-size: 13px; }
        .xsmall { font-size: 12px; }
        
        .stepbar { display: flex; gap: 10px; margin-bottom: 32px; border-bottom: 1px solid var(--line); padding-bottom: 20px; }
        .step { font-size: 13px; font-weight: 500; color: var(--muted); display: flex; align-items: center; gap: 6px; }
        .step.active { color: var(--text); font-weight: 600; }
        .step.done { color: var(--good); }
        .step.done::before { content: "✓"; font-weight: 800; }
        
        label { display: block; font-size: 13px; font-weight: 500; color: #475569; margin-bottom: 6px; }
        input, select, textarea { 
            width: 100%; 
            padding: 10px 14px; 
            border: 1px solid #cbd5e1; 
            border-radius: var(--radius-sm); 
            font-size: 14px; 
            background: #fff; 
            color: var(--text); 
            transition: all 0.2s;
            outline: none;
        }
        input:focus, select:focus, textarea:focus { border-color: var(--accent); ring: 2px solid #3b82f622; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
        
        .btn { 
            display: inline-flex; 
            align-items: center; 
            justify-content: center; 
            gap: 8px; 
            border-radius: var(--radius-sm); 
            padding: 10px 20px; 
            font-size: 14px; 
            font-weight: 600; 
            cursor: pointer; 
            transition: 0.2s;
            border: 1px solid transparent;
        }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-hover); }
        .btn-soft { background: #f1f5f9; color: #475569; }
        .btn-soft:hover { background: #e2e8f0; }
        
        .btn-row { display: flex; gap: 12px; justify-content: flex-end; margin-top: 32px; padding-top: 24px; border-top: 1px solid var(--line); }
        
        .bad-alert { color: var(--bad); background: var(--bad-bg); border: 1px solid #fecaca; border-radius: var(--radius-md); padding: 16px; margin-bottom: 24px; }
        .ok-alert { color: var(--good); background: var(--good-bg); border: 1px solid #bbf7d0; border-radius: var(--radius-md); padding: 16px; margin-bottom: 24px; }
        
        .help { font-size: 12px; color: var(--muted); margin-top: 5px; }
        .footer { padding: 24px 40px; border-top: 1px solid var(--line); font-size: 13px; color: var(--muted); text-align: center; background: #fcfcfc; }

        .checkbox-group { display: flex; align-items: center; gap: 10px; cursor: pointer; }
        .checkbox-group input { width: auto; margin: 0; }

        @media (max-width: 640px) {
            .grid-2, .grid-3, .grid-4 { grid-template-columns: 1fr; }
            .content { padding: 24px; }
            .top { padding: 24px; }
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="shell">
            <div class="notice-bar">
                ⚠️ Installer must be completed once. After finalization the installer is locked automatically.
            </div>
            <div class="top">
                <h1 class="title">MicroShopHub Installer</h1>
                <p class="subtitle">Step-by-step setup for a fresh installation.</p>
            </div>
            <div class="content">
                @if ($errors->any())
                    <div class="bad-alert">
                        <div class="small" style="font-weight:700;margin-bottom:6px">Please fix the following:</div>
                        <ul style="margin:0;padding-left:18px" class="small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('message'))
                    <div class="ok-alert">{{ session('message') }}</div>
                @endif

                @yield('content')
            </div>
            <div class="footer">
                &copy; {{ date('Y') }} {{ config('app.name', 'MicroShopHub') }}. All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>
