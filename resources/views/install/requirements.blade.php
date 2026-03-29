@extends('install.layout')

@section('content')
    @php($progress = 25)
    <div class="progress-shell">
        <div class="progress-top">
            <div>
                <div class="progress-meta">Step 1 of 4</div>
                <div class="small" style="font-weight:700;color:#111827">System Requirements</div>
            </div>
            <div class="progress-meta">25%</div>
        </div>
        <div class="progress-track"><div class="progress-fill" style="width:25%"></div></div>
    </div>

    <div class="stepbar">
        <span class="step active">1. Requirements</span>
        <span class="step">2. Database</span>
        <span class="step">3. Settings</span>
        <span class="step">4. Finish</span>
    </div>

    <div class="two-col">
        <div class="card">
            <h2 style="margin:0 0 8px;font-size:18px">System checks</h2>
            <p class="muted small" style="margin:0 0 16px">Make sure the server can run the app before moving on.</p>

            <div class="checklist">
                @foreach($checks as $check)
                    <div class="checkitem">
                        <div class="small">{{ $check['label'] }}</div>
                        <span class="pill {{ $check['ok'] ? 'ok' : 'bad' }}">{{ $check['ok'] ? 'OK' : 'Missing' }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card">
            <h2 style="margin:0 0 8px;font-size:18px">What this installer does</h2>
            <div class="stack small muted">
                <div>• Verifies PHP extensions and writable folders.</div>
                <div>• Saves database credentials.</div>
                <div>• Runs migrations and seeders.</div>
                <div>• Saves shop name, slogan, brand colors, language, countries, payments, and email defaults.</div>
                <div>• Locks the installer after completion.</div>
            </div>

            <form method="POST" action="{{ route('install.requirements.store') }}" style="margin-top:16px">
                @csrf
                <div class="btn-row">
                    <button class="btn btn-primary" type="submit">Continue</button>
                </div>
            </form>
        </div>
    </div>
@endsection
