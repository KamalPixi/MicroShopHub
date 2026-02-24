<?php

namespace App\Services;

use App\Models\Otp;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CustomerAuthService
{
    private const OTP_PURPOSE_LOGIN = 'login';
    private const OTP_PURPOSE_PASSWORD_RESET = 'password_reset';

    public function getAuthSettings(): array
    {
        $settings = Setting::whereIn('key', [
            'customer_auth_email_otp_enabled',
            'customer_auth_email_password_enabled',
            'customer_auth_guest_checkout_enabled',
        ])->pluck('value', 'key')->toArray();

        return [
            'email_otp_enabled' => $this->toBool($settings['customer_auth_email_otp_enabled'] ?? false),
            'email_password_enabled' => $this->toBool($settings['customer_auth_email_password_enabled'] ?? true),
            'guest_checkout_enabled' => $this->toBool($settings['customer_auth_guest_checkout_enabled'] ?? false),
        ];
    }

    public function isEmailOtpEnabled(): bool
    {
        return $this->getAuthSettings()['email_otp_enabled'];
    }

    public function isEmailPasswordEnabled(): bool
    {
        return $this->getAuthSettings()['email_password_enabled'];
    }

    public function isGuestCheckoutEnabled(): bool
    {
        return $this->getAuthSettings()['guest_checkout_enabled'];
    }

    public function sendLoginOtp(string $email): void
    {
        $this->saveOtp($email, self::OTP_PURPOSE_LOGIN);
    }

    public function loginWithOtp(string $email, string $token): ?User
    {
        if (! $this->validateOtp($email, $token, self::OTP_PURPOSE_LOGIN)) {
            return null;
        }

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'User '.Str::random(4),
                'password' => Str::random(40),
                'email_verified_at' => now(),
            ]
        );

        Auth::login($user);
        $this->consumeOtp($email, self::OTP_PURPOSE_LOGIN);

        return $user;
    }

    public function loginWithPassword(string $email, string $password, bool $remember = false): bool
    {
        return Auth::attempt([
            'email' => $email,
            'password' => $password,
        ], $remember);
    }

    public function sendPasswordResetOtp(string $email): void
    {
        $user = User::where('email', $email)->first();

        // Keep response generic; only send token if account exists.
        if ($user) {
            $this->saveOtp($email, self::OTP_PURPOSE_PASSWORD_RESET);
        }
    }

    public function resetPassword(string $email, string $token, string $newPassword): bool
    {
        $user = User::where('email', $email)->first();

        if (! $user) {
            return false;
        }

        if (! $this->validateOtp($email, $token, self::OTP_PURPOSE_PASSWORD_RESET)) {
            return false;
        }

        $user->forceFill([
            'password' => Hash::make($newPassword),
            'email_verified_at' => $user->email_verified_at ?? now(),
        ])->save();

        $this->consumeOtp($email, self::OTP_PURPOSE_PASSWORD_RESET);

        return true;
    }

    private function saveOtp(string $email, string $purpose): void
    {
        $token = random_int(100000, 999999);

        Otp::query()->updateOrCreate(
            ['identifier' => $this->identifier($email, $purpose)],
            [
                'token' => (string) $token,
                'expires_at' => now()->addMinutes(10),
            ]
        );

        Log::info('Customer auth OTP generated.', [
            'email' => $email,
            'purpose' => $purpose,
            'token' => $token,
        ]);
    }

    private function validateOtp(string $email, string $token, string $purpose): bool
    {
        return Otp::query()
            ->where('identifier', $this->identifier($email, $purpose))
            ->where('token', (string) $token)
            ->where('expires_at', '>', now())
            ->exists();
    }

    private function consumeOtp(string $email, string $purpose): void
    {
        Otp::query()->where('identifier', $this->identifier($email, $purpose))->delete();
    }

    private function identifier(string $email, string $purpose): string
    {
        return $purpose.':'.strtolower(trim($email));
    }

    private function toBool(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
