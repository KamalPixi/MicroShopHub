<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;
use Illuminate\Support\Str;

class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:6',
    ];

    public function login()
    {
        $this->validate();

        $key = $this->throttleKey();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $this->addError('email', 'Too many login attempts. Try again in ' . $this->formatWaitTime($seconds) . '.');
            return;
        }

        if (Auth::guard('admin')->attempt([
            'email' => $this->email,
            'password' => $this->password,
        ], $this->remember)) {
            RateLimiter::clear($key);
            session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        RateLimiter::hit($key, 3600);
        $this->addError('email', 'Invalid email or password.');
    }

    protected function throttleKey(): string
    {
        return 'admin-login|' . Str::lower($this->email) . '|' . request()->ip();
    }

    protected function formatWaitTime(int $seconds): string
    {
        if ($seconds < 60) {
            return $seconds . ' seconds';
        }

        $minutes = (int) ceil($seconds / 60);

        return $minutes . ' minute' . ($minutes === 1 ? '' : 's');
    }

    public function render()
    {
        return view('livewire.admin.login');
    }
}
