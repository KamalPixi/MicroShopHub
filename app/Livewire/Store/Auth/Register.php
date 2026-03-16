<?php

namespace App\Livewire\Store\Auth;

use App\Services\CustomerAuthService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Register extends Component
{
    protected CustomerAuthService $customerAuthService;

    public $authSettings = [];
    public $name = '';
    public $email = '';
    public $password = '';
    public $passwordConfirmation = '';

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
    }

    public function register()
    {
        if (! $this->authSettings['email_password_enabled']) {
            $this->addError('email', 'Registration is currently disabled.');
            return;
        }

        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'passwordConfirmation' => 'required|same:password',
        ], [
            'passwordConfirmation.same' => 'Passwords do not match.',
        ]);

        $user = $this->customerAuthService->registerWithPassword(
            name: $validated['name'],
            email: $validated['email'],
            password: $validated['password'],
        );

        if (! $user) {
            $this->addError('email', 'This email is already in use.');
            return;
        }

        return redirect()->intended(route('customer.dashboard'));
    }

    public function render()
    {
        return view('livewire.store.auth.register');
    }
}
