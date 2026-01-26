<?php

namespace App\Livewire\Store\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\User;

class Login extends Component
{
    public $email = '';
    public $otp = '';
    public $otpSent = false;
    public $isLoading = false;

    protected $rules = [
        'email' => 'required|email',
        'otp' => 'required|numeric|digits:6',
    ];

    public function sendOtp()
    {
        $this->validate(['email' => 'required|email']);
        $this->isLoading = true;

        // Generate 6-digit OTP
        $token = rand(100000, 999999);
        
        // Save to DB (Update existing or insert new)
        DB::table('otps')->updateOrInsert(
            ['identifier' => $this->email],
            [
                'token' => $token,
                'expires_at' => now()->addMinutes(10),
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        // TODO: Send Email using Mail::to($this->email)...
        // For local dev, we log it.
        Log::info("Login OTP for {$this->email}: {$token}");

        $this->otpSent = true;
        $this->isLoading = false;
        session()->flash('message', 'We sent a 6-digit code to your email.');
    }

    public function verifyOtp()
    {
        $this->validate(['otp' => 'required|numeric|digits:6']);
        $this->isLoading = true;

        $record = DB::table('otps')
            ->where('identifier', $this->email)
            ->where('token', $this->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (!$record) {
            $this->addError('otp', 'Invalid or expired code.');
            $this->isLoading = false;
            return;
        }

        // Find or Create User
        // If new, we set a default name. They can change it in Dashboard -> Profile.
        $user = User::firstOrCreate(
            ['email' => $this->email],
            [
                'name' => 'User ' . Str::random(4),
                'password' => bcrypt(Str::random(32)), // Random secure password
                'email_verified_at' => now(),
            ]
        );

        // Login the user
        Auth::login($user);

        // Cleanup OTP
        DB::table('otps')->where('identifier', $this->email)->delete();

        // Redirect to Dashboard or Home
        return redirect()->intended(route('customer.dashboard'));
    }

    public function resetInput()
    {
        $this->otpSent = false;
        $this->otp = '';
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.store.auth.login');
    }
}
