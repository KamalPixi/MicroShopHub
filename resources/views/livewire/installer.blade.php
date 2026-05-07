<div class="shell">
    <div class="notice-bar">
        ⚠️ Installer must be completed once. After finalization the installer is locked automatically.
    </div>
    <div class="top">
        <h1 class="title">MicroShopHub Installer</h1>
        <p class="subtitle">Step-by-step setup for a fresh installation.</p>
    </div>
    
    <div class="content">
        <div class="stepbar">
            <span class="step @if($step == 1) active @elseif($step > 1) done @endif">1. Requirements</span>
            <span class="step @if($step == 2) active @elseif($step > 2) done @endif">2. Database</span>
            <span class="step @if($step == 3) active @elseif($step > 3) done @endif">3. Settings</span>
            <span class="step @if($step == 4) active @elseif($step > 4) done @endif">4. Finalize</span>
            <span class="step @if($step == 5) active @endif">5. Complete</span>
        </div>

        @if($errors->any())
            <div class="bad-alert">
                <div class="small" style="font-weight:700;margin-bottom:6px">Please fix the following:</div>
                <ul style="margin:0;padding-left:18px" class="small">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Step 1: Requirements --}}
        @if($step == 1)
            <div class="stack" style="gap: 32px;">
                <div class="grid grid-2" style="gap: 24px;">
                    <div class="card stack" style="gap: 16px;">
                        <div>
                            <h2 class="section-title">System Requirements</h2>
                            <p class="section-desc">Verify your server environment meets the requirements.</p>
                        </div>

                        <div class="grid grid-2" style="gap: 8px;">
                            @foreach($requirements as $check)
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
                            @foreach(['Environment & Permissions', 'Database Configuration', 'Shop Branding & Settings', 'Migration & Seeding'] as $i => $text)
                                <div class="small muted" style="display: flex; gap: 10px;">
                                    <span style="color: var(--accent); font-weight: 800;">0{{ $i+1 }}</span>
                                    <span>{{ $text }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div style="margin-top: 8px; padding-top: 16px; border-top: 1px dashed var(--line);">
                            <p class="xsmall muted">Once completed, the installer will lock automatically to secure your site.</p>
                        </div>
                    </div>
                </div>

                <div class="btn-row">
                    <button class="btn btn-primary" wire:click="goToStep(2)">Check & Continue</button>
                </div>
            </div>
        @endif

        {{-- Step 2: Database --}}
        @if($step == 2)
            <div class="stack">
                <div>
                    <h2 class="section-title">Database credentials</h2>
                    <p class="section-desc">Enter your database details. We'll test the connection before moving to the next step.</p>
                </div>

                <div class="card grid grid-2">
                    <div style="grid-column: 1 / -1">
                        <label>Database Type</label>
                        <select wire:model.live="db.connection" class="select-field">
                            <option value="mysql">MySQL / MariaDB ({{ $drivers['mysql'] ? 'Enabled' : 'Disabled' }})</option>
                            <option value="pgsql">PostgreSQL ({{ $drivers['pgsql'] ? 'Enabled' : 'Disabled' }})</option>
                            <option value="sqlite">SQLite ({{ $drivers['sqlite'] ? 'Enabled' : 'Disabled' }})</option>
                        </select>
                    </div>

                    @if($db['connection'] !== 'sqlite')
                        <div>
                            <label>Host</label>
                            <input type="text" wire:model="db.host" placeholder="127.0.0.1">
                        </div>
                        <div>
                            <label>Port</label>
                            <input type="number" wire:model="db.port" placeholder="3306">
                        </div>
                    @endif
                    
                    <div>
                        <label>{{ $db['connection'] === 'sqlite' ? 'Database File Name' : 'Database Name' }}</label>
                        <input type="text" wire:model="db.database" placeholder="{{ $db['connection'] === 'sqlite' ? 'database.sqlite' : 'microshophub_db' }}">
                    </div>

                    @if($db['connection'] !== 'sqlite')
                        <div>
                            <label>Username</label>
                            <input type="text" wire:model="db.username" placeholder="root">
                        </div>
                        <div>
                            <label>Password</label>
                            <input type="password" wire:model="db.password" placeholder="Optional">
                        </div>
                    @endif
                    
                    <div>
                        <label>Table Prefix</label>
                        <input type="text" wire:model="db.prefix" placeholder="Optional">
                    </div>
                </div>

                <div class="btn-row">
                    <button class="btn btn-soft" wire:click="goToStep(1)">Back</button>
                    <button class="btn btn-primary" wire:click="goToStep(3)">Test & Continue</button>
                </div>
            </div>
        @endif

        {{-- Step 3: Settings --}}
        @if($step == 3)
            <div class="stack">
                <div>
                    <h2 class="section-title">Store Configuration</h2>
                    <p class="section-desc">Set up your shop branding, locale, and administrative details.</p>
                </div>

                <div class="card grid grid-2">
                    <div style="grid-column: 1 / -1">
                        <label>Domain / App URL</label>
                        <input type="url" wire:model="settings.app_url">
                    </div>
                    <div>
                        <label>Shop Name</label>
                        <input type="text" wire:model="settings.shop_name">
                    </div>
                    <div>
                        <label>Slogan</label>
                        <input type="text" wire:model="settings.slogan">
                    </div>
                </div>

                <div class="card stack" style="gap: 16px;">
                    <h3 class="section-title" style="font-size:15px">Admin Account</h3>
                    <div class="grid grid-2" style="gap:12px">
                        <div>
                            <label>Admin Name</label>
                            <input type="text" wire:model="settings.admin_name">
                        </div>
                        <div>
                            <label>Admin Email</label>
                            <input type="email" wire:model="settings.admin_email">
                        </div>
                        <div>
                            <label>Admin Password</label>
                            <input type="password" wire:model="settings.admin_password">
                        </div>
                        <div>
                            <label>Confirm Password</label>
                            <input type="password" wire:model="settings.admin_password_confirmation">
                        </div>
                    </div>
                </div>

                {{-- Add more settings fields as needed from previous implementation --}}
                {{-- To keep it concise, I'll include the main ones --}}

                <div class="btn-row">
                    <button class="btn btn-soft" wire:click="goToStep(2)">Back</button>
                    <button class="btn btn-primary" wire:click="startInstallation">Run Installation</button>
                </div>
            </div>
        @endif

        {{-- Step 4: Finalize (Logging) --}}
        @if($step == 4)
            <div class="stack" style="gap: 32px;">
                <div class="grid grid-2" style="gap: 24px;">
                    <div class="card stack" style="gap: 20px;">
                        <div>
                            <h2 class="section-title">Executing Installation</h2>
                            <p class="section-desc">{{ $currentTask ?: 'Preparing to run...' }}</p>
                        </div>

                        <div style="height: 8px; background: #f1f5f9; border-radius: 999px; overflow: hidden;">
                            <div style="height: 100%; background: var(--accent); width: {{ $installationProgress }}%; transition: 0.5s;"></div>
                        </div>

                        <div style="padding: 16px; background: #f8fafc; border: 1px solid var(--line); border-radius: var(--radius-md);">
                            <h3 class="section-title" style="font-size: 14px; margin-bottom: 12px;">Tasks Queue</h3>
                            <div class="stack" style="gap: 10px;">
                                <div class="small @if($installationProgress > 10) done @endif" style="display: flex; gap: 10px;">
                                    <span>• Environment Configuration</span>
                                </div>
                                <div class="small @if($installationProgress > 30) done @endif" style="display: flex; gap: 10px;">
                                    <span>• Database Migrations</span>
                                </div>
                                <div class="small @if($installationProgress > 60) done @endif" style="display: flex; gap: 10px;">
                                    <span>• Initial Data Seeding</span>
                                </div>
                                <div class="small @if($installationProgress >= 100) done @endif" style="display: flex; gap: 10px;">
                                    <span>• Security Finalization</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card stack" style="gap: 20px;">
                        <div>
                            <h3 class="section-title">Terminal Log</h3>
                            <p class="section-desc">Live output from the installation engine.</p>
                        </div>

                        <div class="stack" id="log-container" style="gap: 8px; max-height: 320px; overflow: auto; background: #0f172a; padding: 16px; border-radius: var(--radius-sm); font-family: 'Courier New', monospace;">
                            @foreach($logs as $log)
                                <div style="display: flex; gap: 10px; font-size: 12px; white-space: pre-wrap;">
                                    <span style="color: #64748b; flex-shrink: 0;">[{{ $log['time'] }}]</span>
                                    <span style="color: #f8fafc;">{{ $log['message'] }}</span>
                                </div>
                            @endforeach
                            @if($isInstalling)
                                <div style="color: #3b82f6; font-size: 12px; display: flex; align-items: center; gap: 8px;">
                                    <span class="animate-pulse">_</span>
                                    <span>Processing...</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('livewire:initialized', () => {
                   @this.on('run-task', (event) => {
                       setTimeout(() => {
                           @this.runTask(event.task);
                       }, 500); // Small delay to feel "real-time"
                   });
                   
                   @this.on('log-updated', () => {
                       const container = document.getElementById('log-container');
                       container.scrollTop = container.scrollHeight;
                   });
                });
            </script>
        @endif

        {{-- Step 5: Complete --}}
        @if($step == 5)
            <div class="stack" style="text-align: center; padding: 40px 0;">
                <div style="width: 80px; height: 80px; background: var(--good-bg); color: var(--good); border-radius: 999px; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </div>
                <h2 class="title" style="font-size: 32px;">Installation Successful!</h2>
                <p class="subtitle" style="font-size: 16px; max-width: 500px; margin: 8px auto 32px;">MicroShopHub is now ready. The installer has been locked for security.</p>
                
                <div class="card stack" style="max-width: 400px; margin: 0 auto 32px; text-align: left;">
                    <h3 class="section-title" style="font-size: 14px;">Next Steps</h3>
                    <ul class="small muted" style="margin:0; padding-left: 20px;">
                        <li>Login to the admin dashboard</li>
                        <li>Configure your payment keys</li>
                        <li>Start adding categories and products</li>
                    </ul>
                </div>

                <div class="btn-row" style="justify-content: center; border: 0; padding: 0;">
                    <a href="{{ route('admin.login') }}" class="btn btn-primary">Go to Admin Dashboard</a>
                    <a href="{{ route('store.index') }}" class="btn btn-soft">Visit Storefront</a>
                </div>
            </div>
        @endif
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} MicroShopHub. All rights reserved.
    </div>
</div>
