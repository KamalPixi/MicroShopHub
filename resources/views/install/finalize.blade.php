@extends('install.layout')

@section('content')
    <div class="stepbar">
        <span class="step done">1. Requirements</span>
        <span class="step done">2. Database</span>
        <span class="step done">3. Settings</span>
        <span class="step active">4. Finalize</span>
        <span class="step">5. Complete</span>
    </div>

    <div class="two-col">
        <div class="card stack">
            @php($canRun = collect($permissions)->every(fn ($permission) => $permission['ok']))
            <div>
                <h2 style="margin:0 0 6px;font-size:18px">Ready to install</h2>
                <p class="muted small" style="margin:0">This step will run migrations, seed the default data, save your settings, and lock the installer.</p>
            </div>

            <div class="card" style="padding:14px;background:#f8fafc">
                <h3 style="margin:0 0 8px;font-size:14px">What will happen</h3>
                <div class="stack small muted">
                    <div>• Write database and environment settings</div>
                    <div>• Run migrations and seed default data</div>
                    <div>• Save store settings and currency data</div>
                    <div>• Create the first admin account</div>
                    <div>• Lock the installer so it cannot run again</div>
                </div>
            </div>

            <div class="card" style="padding:14px;background:#f8fafc">
                <h3 style="margin:0 0 6px;font-size:13px">File permissions check</h3>
                <div class="stack" style="gap:8px">
                    @foreach($permissions as $permission)
                        <div class="inline" style="justify-content:space-between;padding:8px 10px;border:1px solid #e5e7eb;border-radius:10px;background:#fff">
                            <div>
                                <div style="font-weight:700;font-size:12px">{{ $permission['label'] }}</div>
                                <div class="muted xsmall" style="font-size:10px">{{ $permission['path'] }}</div>
                            </div>
                            <span class="pill {{ $permission['ok'] ? 'ok' : 'bad' }}" style="padding:4px 8px;font-size:11px">{{ $permission['ok'] ? 'Writable' : 'Fix needed' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="btn-row" style="justify-content:flex-start">
                <form method="POST" action="{{ route('install.finalize.run') }}">
                    @csrf
                    <button class="btn btn-primary" type="submit" @disabled(! $canRun)>Run installation</button>
                </form>
                <a class="btn btn-soft" style="padding:9px 12px;font-size:12px" href="{{ route('install.settings') }}">Back to settings</a>
            </div>
            @if(! $canRun)
                <p class="muted small" style="margin:0;color:#b45309">Fix the file permissions above before running the installation.</p>
            @endif
        </div>

        <div class="card stack">
            <h3 style="margin:0;font-size:18px">Installation log</h3>
            <p class="muted small" style="margin:0">The log below will show the installation steps once you run the installer.</p>

            @if(!empty($logs))
                <div class="stack" style="max-height:260px;overflow:auto;padding-right:4px">
                    @foreach($logs as $log)
                        <div class="card" style="padding:10px;background:#f8fafc">
                            <div class="small muted" style="font-size:10px;line-height:1.2">{{ $log['time'] ?? '' }}</div>
                            <div style="font-size:12px;font-weight:600;line-height:1.35">{{ $log['message'] ?? '' }}</div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="card" style="padding:12px;background:#f8fafc">
                    <div class="muted small" style="font-size:12px">Run installation to see the log here.</div>
                </div>
            @endif

            <div class="card" style="padding:12px;background:#f8fafc">
                <div class="small muted" style="font-size:11px">Admin login</div>
                <div style="font-weight:700;font-size:13px">{{ $adminEmail }}</div>
            </div>
        </div>
    </div>
@endsection
