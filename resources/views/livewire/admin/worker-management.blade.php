<div>
    <div class="mx-4 p-6">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Worker Management</h2>
                <p class="text-slate-500 mt-2 text-lg">Monitor and control your background process worker.</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold {{ $status === 'running' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }} border {{ $status === 'running' ? 'border-emerald-200' : 'border-rose-200' }} shadow-sm">
                    <span class="flex h-2 w-2 mr-2">
                        <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full {{ $status === 'running' ? 'bg-emerald-400' : 'bg-rose-400' }} opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 {{ $status === 'running' ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                    </span>
                    {{ ucfirst($status) }}
                </span>
            </div>
        </div>

        @if (session()->has('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center shadow-sm animate-fade-in">
                <svg class="w-5 h-5 mr-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl flex items-center shadow-sm animate-fade-in">
                <svg class="w-5 h-5 mr-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Control Panel -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
                    <div class="p-8 border-b border-slate-50 bg-slate-50/50">
                        <h3 class="text-xl font-bold text-slate-800">Worker Controls</h3>
                        <p class="text-sm text-slate-500 mt-1">Manage the lifecycle of your queue process.</p>
                    </div>
                    <div class="p-8 space-y-4">
                        <button wire:click="startWorker" wire:loading.attr="disabled" 
                            class="w-full flex items-center justify-center gap-3 px-6 py-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-bold transition-all shadow-lg shadow-emerald-200 disabled:opacity-50 group">
                            <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Start Worker
                        </button>

                        <button wire:click="stopWorker" wire:loading.attr="disabled"
                            class="w-full flex items-center justify-center gap-3 px-6 py-4 bg-rose-600 hover:bg-rose-700 text-white rounded-2xl font-bold transition-all shadow-lg shadow-rose-200 disabled:opacity-50 group">
                            <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10h6"></path></svg>
                            Stop Worker
                        </button>

                        <button wire:click="checkStatus" wire:loading.attr="disabled"
                            class="w-full flex items-center justify-center gap-3 px-6 py-4 bg-white border-2 border-slate-100 hover:bg-slate-50 text-slate-700 rounded-2xl font-bold transition-all shadow-sm group">
                            <svg wire:loading.class="animate-spin" class="w-5 h-5 group-hover:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Refresh Status
                        </button>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-indigo-600 to-blue-700 rounded-3xl p-8 text-white shadow-xl shadow-blue-200">
                    <h4 class="text-lg font-bold mb-2">Info</h4>
                    <p class="text-sm text-blue-100 leading-relaxed">The worker processes jobs like email notifications, backups, and data synchronization in the background to keep the main site fast.</p>
                    <div class="mt-4 pt-4 border-t border-white/10 flex items-center justify-between text-xs font-mono">
                        <span class="text-blue-200">Current PID:</span>
                        <span class="font-bold">{{ $workerProcessId ?: 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Logs View -->
            <div class="lg:col-span-2">
                <div class="bg-slate-950 rounded-3xl shadow-2xl overflow-hidden border border-slate-800 flex flex-col h-[600px]">
                    <div class="px-8 py-5 border-b border-slate-800 bg-slate-900 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex gap-1.5">
                                <div class="w-3 h-3 rounded-full bg-rose-500"></div>
                                <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                                <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                            </div>
                            <span class="text-sm font-bold text-slate-400 font-mono ml-4">worker.log</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button wire:click="loadLogs" class="p-2 text-slate-400 hover:text-white transition-colors" title="Refresh Logs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            </button>
                            <button wire:click="clearLogs" class="p-2 text-rose-400 hover:text-rose-300 transition-colors" title="Clear Logs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                    <div class="flex-1 overflow-auto p-8 font-mono text-xs leading-relaxed text-slate-300 custom-scrollbar">
                        <pre class="whitespace-pre-wrap">{{ $logs }}</pre>
                    </div>
                    <div class="px-8 py-4 bg-slate-900 border-t border-slate-800 text-[10px] text-slate-500 flex justify-between">
                        <span>Last updated: {{ now()->format('H:i:s') }}</span>
                        <span>Auto-refresh active</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #334155;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #475569;
    }
    </style>
</div>
