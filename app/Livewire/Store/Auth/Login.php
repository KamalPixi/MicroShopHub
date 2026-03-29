<?php

namespace App\Livewire\Store\Auth;

use App\Services\CustomerAuthService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    protected CustomerAuthService $customerAuthService;

    public $authSettings = [];
    public $activeMethod = 'password';

    public $email = '';
    public $password = '';
    public $remember = false;

    public $otp = '';
    public $otpSent = false;

    public $showForgotPassword = false;
    public $resetEmail = '';
    public $resetOtp = '';
    public $resetOtpSent = false;
    public $newPassword = '';
    public $newPasswordConfirmation = '';

    public function boot(CustomerAuthService $customerAuthService): void
    {
        $this->customerAuthService = $customerAuthService;
    }

    public function mount(): void
    {
        if (Auth::check()) {
            $this->redirect(route('customer.dashboard'), navigate: true);
            return;
        }

        $this->authSettings = $this->customerAuthService->getAuthSettings();

        if ($this->authSettings['email_password_enabled']) {
            $this->activeMethod = 'password';
        } elseif ($this->authSettings['email_otp_enabled']) {
            $this->activeMethod = 'otp';
        }
    }

    public function setMethod(string $method): void
    {
        if ($method === 'password' && $this->authSettings['email_password_enabled']) {
            $this->activeMethod = 'password';
        }

        if ($method === 'otp' && $this->authSettings['email_otp_enabled']) {
            $this->activeMethod = 'otp';
        }

        $this->resetLoginState();
    }

    public function sendOtp(): void
    {
        if (! $this->authSettings['email_otp_enabled']) {
            $this->addError('email', 'Email OTP login is currently disabled.');
            return;
        }

        $validated = $this->validate([
            'email' => 'required|email',
        ]);

        $this->customerAuthService->sendLoginOtp($validated['email']);
        $this->otpSent = true;

        session()->flash('message', __('store.login_otp_sent'));
    }

    public function verifyOtp()
    {
        if (! $this->authSettings['email_otp_enabled']) {
            $this->addError('otp', 'Email OTP login is currently disabled.');
            return;
        }

        $validated = $this->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric|digits:6',
        ]);

        $user = $this->customerAuthService->loginWithOtp($validated['email'], $validated['otp']);

        if (! $user) {
            $this->addError('otp', 'Invalid or expired code.');
            return;
        }

        return redirect()->intended(route('customer.dashboard'));
    }

    public function loginWithPassword()
    {
        if (! $this->authSettings['email_password_enabled']) {
            $this->addError('email', 'Email and password login is currently disabled.');
            return;
        }

        $validated = $this->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if (! $this->customerAuthService->loginWithPassword($validated['email'], $validated['password'], (bool) $this->remember)) {
            $this->addError('password', 'Invalid email or password.');
            return;
        }

        return redirect()->intended(route('customer.dashboard'));
    }

    public function showForgotPasswordForm(): void
    {
        $this->showForgotPassword = true;
        $this->resetOtpSent = false;
        $this->resetOtp = '';
        $this->newPassword = '';
        $this->newPasswordConfirmation = '';
        $this->resetErrorBag();
    }

    public function hideForgotPasswordForm(): void
    {
        $this->showForgotPassword = false;
        $this->resetOtpSent = false;
        $this->resetOtp = '';
        $this->newPassword = '';
        $this->newPasswordConfirmation = '';
        $this->resetErrorBag();
    }

    public function sendPasswordResetOtp(): void
    {
        $validated = $this->validate([
            'resetEmail' => 'required|email',
        ]);

        $this->customerAuthService->sendPasswordResetOtp($validated['resetEmail']);
        $this->resetOtpSent = true;

        session()->flash('message', __('store.password_reset_code_sent'));
    }

    public function resetPassword(): void
    {
        $validated = $this->validate([
            'resetEmail' => 'required|email',
            'resetOtp' => 'required|numeric|digits:6',
            'newPassword' => 'required|string|min:6',
            'newPasswordConfirmation' => 'required|same:newPassword',
        ]);

        $updated = $this->customerAuthService->resetPassword(
            email: $validated['resetEmail'],
            token: $validated['resetOtp'],
            newPassword: $validated['newPassword'],
        );

        if (! $updated) {
            $this->addError('resetOtp', 'Invalid code or account not found.');
            return;
        }

        $this->hideForgotPasswordForm();
        $this->email = $validated['resetEmail'];
        session()->flash('message', __('store.password_reset_success'));
    }

    public function resetLoginState(): void
    {
        $this->otpSent = false;
        $this->otp = '';
        $this->password = '';
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.store.auth.login');
    }
}
