<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    public function login() {
        return view('admin.auth.login');
    }

    public function passwordRequest()
    {
        return view('admin.auth.password-request');
    }

    public function sendPasswordResetLink(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $key = $this->passwordResetRequestThrottleKey($validated['email'], $request->ip());
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Too many reset requests. Please try again in ' . $this->formatWaitTime($seconds) . '.']);
        }

        $status = Password::broker('admins')->sendResetLink($validated);

        return $status === Password::RESET_LINK_SENT
            ? tap(back()->with('status', __($status)), fn () => RateLimiter::hit($key, 900))
            : back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
    }

    public function resetPasswordForm(string $token, Request $request)
    {
        return view('admin.auth.password-reset', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $key = $this->passwordResetAttemptThrottleKey($validated['email'], $request->ip());
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Too many wrong reset attempts. Try again in ' . $this->formatWaitTime($seconds) . '.']);
        }

        $status = Password::broker('admins')->reset(
            $validated,
            function ($admin, string $password) {
                $admin->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? tap(redirect()->route('admin.login')->with('status', __($status)), fn () => RateLimiter::clear($key))
            : tap(
                back()->withInput($request->only('email'))->withErrors(['email' => __($status)]),
                fn () => RateLimiter::hit($key, 900)
            );
    }

    protected function passwordResetRequestThrottleKey(string $email, string $ip): string
    {
        return 'admin-password-reset-request|' . Str::lower($email) . '|' . $ip;
    }

    protected function passwordResetAttemptThrottleKey(string $email, string $ip): string
    {
        return 'admin-password-reset-attempt|' . Str::lower($email) . '|' . $ip;
    }

    protected function formatWaitTime(int $seconds): string
    {
        if ($seconds < 60) {
            return $seconds . ' seconds';
        }

        $minutes = (int) ceil($seconds / 60);

        return $minutes . ' minute' . ($minutes === 1 ? '' : 's');
    }

    public function logout(Request $request) {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
