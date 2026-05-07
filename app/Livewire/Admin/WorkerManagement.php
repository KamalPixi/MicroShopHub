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

    public function mount()
    {
        $this->checkStatus();
        $this->loadLogs();
    }

    public function checkStatus()
    {
        // Check for running queue worker process
        $output = shell_exec('ps aux | grep "artisan queue:work" | grep -v grep');
        if ($output) {
            $this->status = 'running';
            // Extract PID
            $parts = preg_split('/\s+/', trim($output));
            $this->workerProcessId = $parts[1] ?? null;
        } else {
            $this->status = 'stopped';
            $this->workerProcessId = null;
        }
    }

    public function startWorker()
    {
        if ($this->status === 'running') {
            session()->flash('error', 'Worker is already running.');
            return;
        }

        // Start worker in background
        // Note: Using nohup to keep it running after request
        $command = 'nohup php ' . base_path('artisan') . ' queue:work --stop-when-empty > /dev/null 2>&1 &';
        
        // Actually, for a persistent worker:
        $command = 'nohup php ' . base_path('artisan') . ' queue:work --tries=3 > ' . storage_path('logs/worker.log') . ' 2>&1 &';
        
        shell_exec($command);
        
        sleep(1); // Give it a second to start
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
        
        sleep(1);
        $this->checkStatus();
        session()->flash('success', 'Worker stopped successfully.');
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
            ->layout('admin.layouts.default');
    }
}
