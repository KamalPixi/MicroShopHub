<div class="p-5 bg-gray-50/30 min-h-screen">
    <!-- Refined Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <div class="p-1.5 bg-indigo-50 rounded-lg">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                Worker Management
            </h2>
            <p class="text-[11px] text-slate-500 mt-0.5 ml-9 font-medium uppercase tracking-tight">System Background Monitoring</p>
        </div>
        
        <div class="flex items-center gap-2.5">
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg {{ $status === 'running' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200' }} border text-[10px] font-bold tracking-wide">
                <span class="relative flex h-2 w-2">
                    @if($status === 'running')
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    @endif
                    <span class="relative inline-flex rounded-full h-2 w-2 {{ $status === 'running' ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                </span>
                {{ strtoupper($status) }}
            </div>
            <button wire:click="checkStatus" wire:loading.attr="disabled" class="p-2 bg-white border border-gray-200 rounded-lg text-slate-400 hover:text-indigo-600 hover:border-indigo-200 transition-all shadow-sm">
                <svg wire:loading.class="animate-spin" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </button>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="mb-5 p-3 bg-white border-l-4 border-emerald-500 text-slate-700 rounded-r-xl text-xs font-medium flex items-center animate-fade-in shadow-sm">
            <svg class="w-4 h-4 mr-2 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Queue Stats Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="p-2 bg-indigo-50 rounded-lg">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pending Jobs</p>
                <p class="text-xl font-black text-slate-800">{{ $pendingJobsCount }}</p>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="p-2 bg-rose-50 rounded-lg">
                <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Failed Jobs</p>
                <p class="text-xl font-black text-slate-800">{{ $failedJobsCount }}</p>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="p-2 bg-emerald-50 rounded-lg">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Worker Status</p>
                <p class="text-sm font-bold text-emerald-600 uppercase">{{ $status }}</p>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="p-2 bg-amber-50 rounded-lg">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </div>
            <button wire:click="refreshStats" class="text-left w-full">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Server Time</p>
                <p class="text-[11px] font-bold text-slate-600 uppercase">{{ now()->format('H:i:s') }}</p>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
        <!-- Professional Control Sidebar -->
        <div class="xl:col-span-1 space-y-4">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Worker Controls</h3>
                </div>
                <div class="p-4 space-y-3">
                    <button wire:click="startWorker" wire:loading.attr="disabled" 
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-900 hover:bg-black text-white rounded-lg text-xs font-bold transition-all disabled:opacity-50">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path></svg>
                        Start Worker
                    </button>

                    <button wire:click="stopWorker" wire:loading.attr="disabled"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-rose-100 text-rose-600 hover:bg-rose-50 rounded-lg text-xs font-bold transition-all disabled:opacity-50">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10h6"></path></svg>
                        Stop Worker
                    </button>
                </div>
                <div class="px-4 py-4 bg-gray-50/50 border-t border-gray-100 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col">
                            <span class="text-[9px] text-slate-400 uppercase font-bold tracking-tighter">Process ID</span>
                            <span class="text-[11px] text-slate-700 font-mono font-bold">{{ $workerProcessId ?: 'Inactive' }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-[9px] text-slate-400 uppercase font-bold tracking-tighter">Process Owner</span>
                            <span class="block text-[11px] text-indigo-600 font-mono font-bold">{{ $workerUser ?: 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="pt-2 border-t border-gray-100">
                        <span class="text-[9px] text-slate-400 uppercase font-bold tracking-tighter block mb-1">Execution Binary</span>
                        <span class="block text-[10px] text-slate-600 font-mono break-all leading-tight">{{ $workerBinary ?: 'None' }}</span>
                    </div>
                </div>
            </div>

            <!-- Recent Jobs List -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <h3 class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Recent Jobs</h3>
                    <span class="px-1.5 py-0.5 bg-slate-100 text-slate-600 text-[9px] rounded font-bold">{{ count($recentJobs) }}</span>
                </div>
                <div class="divide-y divide-gray-100 max-h-[300px] overflow-auto custom-scrollbar">
                    @forelse($recentJobs as $job)
                        <div class="p-3 hover:bg-gray-50 transition-colors">
                            <div class="flex justify-between items-start mb-1">
                                <span class="text-[10px] font-bold text-slate-700 truncate pr-2">{{ $job['name'] }}</span>
                                <span class="text-[8px] bg-indigo-50 text-indigo-600 px-1 rounded font-bold">#{{ $job['id'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-[9px] text-slate-400">{{ $job['created_at'] }}</span>
                                <span class="text-[9px] text-slate-500">Tries: {{ $job['attempts'] }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center">
                            <p class="text-[10px] text-slate-400 font-medium">No pending jobs</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Terminal View -->
        <div class="xl:col-span-3">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col h-[550px] overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-100 bg-gray-50/30 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex gap-1.5">
                            <div class="w-2 h-2 rounded-full bg-gray-300"></div>
                            <div class="w-2 h-2 rounded-full bg-gray-300"></div>
                            <div class="w-2 h-2 rounded-full bg-gray-300"></div>
                        </div>
                        <span class="text-[10px] font-bold text-slate-500 font-mono tracking-tight uppercase">Activity Logs</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button wire:click="loadLogs" class="p-1.5 bg-white border border-gray-200 rounded-lg text-slate-400 hover:text-indigo-600 hover:border-indigo-100 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        </button>
                        <button wire:click="clearLogs" class="p-1.5 bg-white border border-gray-200 rounded-lg text-slate-400 hover:text-rose-500 hover:border-rose-100 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </div>
                <div class="flex-1 overflow-auto p-6 font-mono text-[11px] leading-relaxed text-slate-700 bg-slate-50/20 custom-scrollbar">
                    <pre class="whitespace-pre-wrap">{{ $logs ?: 'Waiting for worker activity...' }}</pre>
                </div>
                <div class="px-5 py-2.5 bg-gray-50/50 border-t border-gray-100 flex justify-between items-center">
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Last Sync: {{ now()->format('H:i:s') }}</span>
                    <div class="flex items-center gap-1.5 opacity-60">
                        <span class="w-1 h-1 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-[7px] font-bold text-slate-400 uppercase tracking-widest">Live Monitor</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 3px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 3px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #cbd5e1;
    }
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(-3px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fade-in 0.3s ease-out forwards;
    }
    </style>
</div>
