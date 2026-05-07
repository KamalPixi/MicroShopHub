<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

class WorkerManagement extends Component
{
    public $status = 'stopped';
    public $logs = '';
    public $workerProcessId = null;
    public $workerUser = null;
    public $workerBinary = null;
    public $pendingJobsCount = 0;
    public $failedJobsCount = 0;
    public $recentJobs = [];

    public function mount()
    {
        $this->checkStatus();
        $this->loadLogs();
        $this->refreshStats();
    }

    public function checkStatus()
    {
        $finalPid = null;
        $storedPid = \App\Models\Setting::where('key', 'worker_last_pid')->value('value');

        // 1. Try pgrep (fastest/cleanest)
        $pid = shell_exec("pgrep -f 'artisan queue:work' | head -n 1");
        if ($pid && is_numeric(trim($pid))) {
            $finalPid = trim($pid);
        } 
        
        // 2. Try stored PID
        if (!$finalPid && $storedPid && is_numeric($storedPid)) {
            $cmd = shell_exec("ps -p {$storedPid} -o command=");
            if ($cmd && str_contains($cmd, 'artisan queue:work')) {
                $finalPid = $storedPid;
            }
        }

        // 3. Fallback to ps aux grep
        if (!$finalPid) {
            $output = shell_exec('ps aux | grep "artisan queue:work" | grep -v "grep" | grep -v "php-fpm" | grep -v "nginx" | head -n 1');
            if ($output) {
                $parts = preg_split('/\s+/', trim($output));
                $finalPid = $parts[1] ?? null;
            }
        }

        // Finalize state
        if ($finalPid && is_numeric($finalPid)) {
            $this->status = 'running';
            $this->workerProcessId = $finalPid;
            
            // Unified details fetch
            $details = shell_exec("ps -p {$finalPid} -o user=,command=");
            if ($details) {
                $parts = preg_split('/\s+/', trim($details));
                $this->workerUser = $parts[0] ?? 'Unknown';
                // Find binary in command string
                $this->workerBinary = $parts[1] ?? 'PHP';
            }

            // Keep DB in sync
            if ($this->workerProcessId !== $storedPid) {
                \App\Models\Setting::updateOrCreate(['key' => 'worker_last_pid'], ['value' => $this->workerProcessId]);
            }
        } else {
            $this->status = 'stopped';
            $this->workerProcessId = null;
            $this->workerUser = null;
            $this->workerBinary = null;
        }
    }

    public function startWorker()
    {
        if ($this->status === 'running') {
            session()->flash('error', 'Worker is already running.');
            return;
        }

        // Fetch the connection from settings to ensure worker listens to the right place
        $connection = \App\Models\Setting::where('key', 'queue_connection')->value('value') ?? 'database';
        
        // Start worker in background with explicit connection
        $command = 'nohup php ' . base_path('artisan') . ' queue:work ' . $connection . ' --tries=3 > ' . storage_path('logs/worker.log') . ' 2>&1 &';
        
        shell_exec($command);
        
        sleep(1); // Give it a second to start
        
        // Find the new PID
        $newPid = shell_exec("pgrep -f 'artisan queue:work' | head -n 1");
        if ($newPid && is_numeric(trim($newPid))) {
            \App\Models\Setting::updateOrCreate(['key' => 'worker_last_pid'], ['value' => trim($newPid)]);
        }

        $this->checkStatus();
        session()->flash('success', 'Worker started successfully.');
    }

    public function stopWorker()
    {
        if ($this->status === 'stopped' || !$this->workerProcessId) {
            session()->flash('error', 'Worker is not running.');
            return;
        }

        shell_exec("kill {$this->workerProcessId}");
        
        // Clear stored PID
        \App\Models\Setting::where('key', 'worker_last_pid')->delete();
        
        sleep(1);
        $this->checkStatus();
        session()->flash('success', 'Worker stopped successfully.');
    }

    public function refreshStats()
    {
        try {
            $this->pendingJobsCount = \Illuminate\Support\Facades\DB::table('jobs')->count();
            $this->failedJobsCount = \Illuminate\Support\Facades\DB::table('failed_jobs')->count();
            
            // Fetch recent jobs with some payload info
            $this->recentJobs = \Illuminate\Support\Facades\DB::table('jobs')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function($job) {
                    $payload = json_decode($job->payload, true);
                    $jobName = $payload['displayName'] ?? ($payload['job'] ?? 'Unknown Job');
                    // Strip namespace
                    $jobName = class_basename($jobName);
                    
                    return [
                        'id' => $job->id,
                        'name' => $jobName,
                        'queue' => $job->queue,
                        'attempts' => $job->attempts,
                        'created_at' => \Carbon\Carbon::createFromTimestamp($job->created_at)->diffForHumans(),
                    ];
                })->toArray();
        } catch (\Exception $e) {
            // Tables might not exist
            $this->pendingJobsCount = 0;
            $this->failedJobsCount = 0;
            $this->recentJobs = [];
        }
    }

    public function loadLogs()
    {
        $logFile = storage_path('logs/worker.log');
        if (File::exists($logFile)) {
            // Get last 50 lines
            $lines = explode("\n", File::get($logFile));
            $this->logs = implode("\n", array_slice($lines, -50));
        } else {
            $this->logs = 'No logs found.';
        }
    }

    public function clearLogs()
    {
        $logFile = storage_path('logs/worker.log');
        if (File::exists($logFile)) {
            File::put($logFile, '');
            $this->loadLogs();
            session()->flash('success', 'Logs cleared.');
        }
    }

    public function render()
    {
        return view('livewire.admin.worker-management')
            ->extends('admin.layouts.default')
            ->section('content');
    }
}
