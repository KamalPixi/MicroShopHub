<div class="p-4 sm:p-6 bg-gray-50/50 min-h-screen">
    <!-- Compact Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                Worker Management
            </h2>
            <p class="text-xs text-slate-500 mt-1">Background process monitoring & control</p>
        </div>
        
        <div class="flex items-center gap-2">
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-full {{ $status === 'running' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-rose-50 text-rose-700 border-rose-100' }} border text-[11px] font-bold shadow-sm">
                <span class="relative flex h-2 w-2">
                    @if($status === 'running')
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    @endif
                    <span class="relative inline-flex rounded-full h-2 w-2 {{ $status === 'running' ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                </span>
                {{ strtoupper($status) }}
            </div>
            <button wire:click="checkStatus" wire:loading.attr="disabled" class="p-1.5 text-slate-400 hover:text-indigo-600 transition-colors">
                <svg wire:loading.class="animate-spin" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </button>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 p-3 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-xl text-xs flex items-center animate-fade-in shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
        <!-- Compact Control Sidebar -->
        <div class="xl:col-span-1 space-y-4">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-4 border-b border-slate-100 bg-slate-50/30">
                    <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Quick Actions</h3>
                </div>
                <div class="p-4 space-y-2">
                    <button wire:click="startWorker" wire:loading.attr="disabled" 
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm disabled:opacity-50">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path></svg>
                        Start Worker
                    </button>

                    <button wire:click="stopWorker" wire:loading.attr="disabled"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-rose-200 text-rose-600 hover:bg-rose-50 rounded-xl text-xs font-bold transition-all disabled:opacity-50">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10h6"></path></svg>
                        Stop Worker
                    </button>
                </div>
                <div class="px-4 py-3 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-[10px] text-slate-500 font-mono">PID: {{ $workerProcessId ?: 'None' }}</span>
                    <span class="text-[10px] text-slate-400">Queue: database</span>
                </div>
            </div>

            <div class="bg-indigo-600 rounded-2xl p-5 text-white shadow-md">
                <h4 class="text-xs font-bold mb-1 opacity-90 uppercase tracking-tight">Deployment Tip</h4>
                <p class="text-[11px] text-indigo-100 leading-normal">For production, it is recommended to use **Supervisor** to keep the worker running permanently.</p>
            </div>
        </div>

        <!-- Terminal View -->
        <div class="xl:col-span-3">
            <div class="bg-slate-900 rounded-2xl shadow-lg overflow-hidden border border-slate-800 flex flex-col h-[450px]">
                <div class="px-5 py-3 border-b border-slate-800 bg-slate-900/50 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="flex gap-1">
                            <div class="w-2.5 h-2.5 rounded-full bg-slate-700"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-slate-700"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-slate-700"></div>
                        </div>
                        <span class="text-[10px] font-bold text-slate-500 font-mono ml-3">worker.log</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <button wire:click="loadLogs" class="p-1.5 text-slate-500 hover:text-white transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        </button>
                        <button wire:click="clearLogs" class="p-1.5 text-slate-500 hover:text-rose-400 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </div>
                <div class="flex-1 overflow-auto p-5 font-mono text-[10px] leading-relaxed text-emerald-500/80 bg-black/20 custom-scrollbar">
                    <pre class="whitespace-pre-wrap">{{ $logs }}</pre>
                </div>
                <div class="px-5 py-2.5 bg-slate-900 border-t border-slate-800 flex justify-between items-center">
                    <span class="text-[9px] text-slate-600">Updated: {{ now()->format('H:i:s') }}</span>
                    <div class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-[9px] text-slate-500 uppercase tracking-tighter">Live Monitor</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #334155;
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #475569;
    }
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(-5px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fade-in 0.3s ease-out forwards;
    }
    </style>
</div>
