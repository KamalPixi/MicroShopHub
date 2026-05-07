@extends('install.layout')

@section('content')
    <div class="stepbar">
        <span class="step active">1. Requirements</span>
        <span class="step">2. Database</span>
        <span class="step">3. Settings</span>
        <span class="step">4. Finalize</span>
        <span class="step">5. Complete</span>
    </div>

    <div class="stack" style="gap: 32px;">
        <div class="grid grid-2" style="gap: 24px;">
            <div class="card stack" style="gap: 16px;">
                <div>
                    <h2 class="section-title">System Requirements</h2>
                    <p class="section-desc">Verify your server environment meets the requirements.</p>
                </div>

                <div class="grid grid-2" style="gap: 8px;">
                    @foreach($checks as $check)
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; background: #f8fafc; border: 1px solid var(--line); border-radius: var(--radius-sm);">
                            <span class="small" style="font-weight: 500;">{{ $check['label'] }}</span>
                            @if($check['ok'])
                                <span class="xsmall" style="color: var(--good); font-weight: 700; display: flex; align-items: center; gap: 4px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    Passed
                                </span>
                            @else
                                <span class="xsmall" style="color: var(--bad); font-weight: 700; display: flex; align-items: center; gap: 4px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                    Missing
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card stack" style="gap: 16px;">
                <div>
                    <h3 class="section-title">Installation Overview</h3>
                    <p class="section-desc">A quick guide to the setup process.</p>
                </div>
                <div class="stack" style="gap: 10px;">
                    <div class="small muted" style="display: flex; gap: 10px;">
                        <span style="color: var(--accent); font-weight: 800;">01</span>
                        <span>Environment & Permissions Check</span>
                    </div>
                    <div class="small muted" style="display: flex; gap: 10px;">
                        <span style="color: var(--accent); font-weight: 800;">02</span>
                        <span>Database Connectivity & Configuration</span>
                    </div>
                    <div class="small muted" style="display: flex; gap: 10px;">
                        <span style="color: var(--accent); font-weight: 800;">03</span>
                        <span>Shop Branding & Global Settings</span>
                    </div>
                    <div class="small muted" style="display: flex; gap: 10px;">
                        <span style="color: var(--accent); font-weight: 800;">04</span>
                        <span>Migration & Initial Data Seeding</span>
                    </div>
                </div>

                <div style="margin-top: 8px; padding-top: 16px; border-top: 1px dashed var(--line);">
                    <p class="xsmall muted">Once you complete these steps, the installer will lock automatically to secure your site.</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('install.requirements.store') }}">
            @csrf
            <div class="btn-row">
                <button class="btn btn-primary" type="submit">
                    Check & Continue
                    <svg style="margin-left: 4px" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </button>
            </div>
        </form>
    </div>
@endsection
