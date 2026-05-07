@extends('install.layout')

@section('content')
    <div class="stepbar">
        <span class="step done">1. Requirements</span>
        <span class="step done">2. Database</span>
        <span class="step done">3. Settings</span>
        <span class="step active">4. Finalize</span>
        <span class="step">5. Complete</span>
    </div>

    <div class="stack" style="gap: 32px;">
        <div class="grid grid-2" style="gap: 24px;">
            <div class="card stack" style="gap: 20px;">
                @php($canRun = collect($permissions)->every(fn ($permission) => $permission['ok']))
                <div>
                    <h2 class="section-title">Finalize Installation</h2>
                    <p class="section-desc">We'll run migrations, seed initial data, and lock the setup.</p>
                </div>

                <div style="padding: 16px; background: #f8fafc; border: 1px solid var(--line); border-radius: var(--radius-md);">
                    <h3 class="section-title" style="font-size: 14px; margin-bottom: 12px;">What will happen</h3>
                    <div class="stack" style="gap: 10px;">
                        <div class="small muted" style="display: flex; gap: 10px;">
                            <span style="color: var(--accent); font-weight: 800;">•</span>
                            <span>Write session configuration to environment</span>
                        </div>
                        <div class="small muted" style="display: flex; gap: 10px;">
                            <span style="color: var(--accent); font-weight: 800;">•</span>
                            <span>Execute database migrations & seeders</span>
                        </div>
                        <div class="small muted" style="display: flex; gap: 10px;">
                            <span style="color: var(--accent); font-weight: 800;">•</span>
                            <span>Initialize store settings & currencies</span>
                        </div>
                        <div class="small muted" style="display: flex; gap: 10px;">
                            <span style="color: var(--accent); font-weight: 800;">•</span>
                            <span>Secure the installer automatically</span>
                        </div>
                    </div>
                </div>

                <div style="padding: 16px; background: #f8fafc; border: 1px solid var(--line); border-radius: var(--radius-md);">
                    <h3 class="section-title" style="font-size: 14px; margin-bottom: 8px;">Permissions Check</h3>
                    <div class="stack" style="gap: 8px;">
                        @foreach($permissions as $permission)
                            <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; background: #fff; border: 1px solid var(--line); border-radius: var(--radius-sm);">
                                <div>
                                    <div class="small" style="font-weight: 600;">{{ $permission['label'] }}</div>
                                    <div class="xsmall muted">{{ $permission['path'] }}</div>
                                </div>
                                @if($permission['ok'])
                                    <span class="xsmall" style="color: var(--good); font-weight: 700;">Writable</span>
                                @else
                                    <span class="xsmall" style="color: var(--bad); font-weight: 700;">Fix needed</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="btn-row" style="margin-top: 12px; padding-top: 16px; justify-content: flex-start; border-top: 1px dashed var(--line);">
                    <form method="POST" action="{{ route('install.finalize.run') }}">
                        @csrf
                        <button class="btn btn-primary" type="submit" @disabled(! $canRun)>Run installation</button>
                    </form>
                    <a class="btn btn-soft" href="{{ route('install.settings') }}">Back to settings</a>
                </div>
                @if(! $canRun)
                    <p class="xsmall" style="color: var(--bad); font-weight: 500;">Please fix permissions before continuing.</p>
                @endif
            </div>

            <div class="card stack" style="gap: 20px;">
                <div>
                    <h3 class="section-title">Installation Log</h3>
                    <p class="section-desc">Real-time feedback of the setup process.</p>
                </div>

                <div class="stack" style="gap: 12px; margin-top: 16px; border-top: 1px solid var(--line); padding-top: 24px;">
                    @if(!empty($logs))
                        <div class="stack" style="max-height: 320px; overflow: auto; gap: 8px;">
                            @foreach($logs as $log)
                                <div style="padding: 12px; background: #f8fafc; border: 1px solid var(--line); border-radius: var(--radius-sm);">
                                    <div class="xsmall muted" style="font-size: 9px; margin-bottom: 2px;">{{ $log['time'] ?? '' }}</div>
                                    <div class="small" style="font-weight: 600; line-height: 1.4;">{{ $log['message'] ?? '' }}</div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="padding: 24px; text-align: center; background: #f8fafc; border: 1px dashed var(--line); border-radius: var(--radius-sm);">
                            <p class="small muted">Run installation to see the live log here.</p>
                        </div>
                    @endif
                </div>

                <div style="margin-top: auto; padding-top: 20px; border-top: 1px solid var(--line);">
                    <div class="xsmall muted" style="margin-bottom: 4px;">Primary Administrator</div>
                    <div class="small" style="font-weight: 700;">{{ $adminEmail }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
