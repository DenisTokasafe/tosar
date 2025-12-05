<?php

namespace App\Livewire\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Illuminate\Validation\Rule; // <-- Import Rule untuk validasi (Opsional, tapi praktik baik)
use Livewire\Component;
#[Layout('components.layouts.auth')]
class Login extends Component
{
    // Ganti $email menjadi $credential.
    // Aturan validasi diubah menjadi required|string
    #[Validate('required|string')]
    public string $credential = ''; // <-- Properti baru untuk menampung email atau username

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        // Tentukan field otentikasi berdasarkan input
        $field = filter_var($this->credential, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Buat array credential untuk otentikasi
        $credentials = [
            $field => $this->credential,
            'password' => $this->password,
        ];

        // Coba otentikasi menggunakan field (email atau username) yang sesuai
        if (! Auth::attempt($credentials, $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'credential' => __('auth.failed'), // <-- Ubah 'email' menjadi 'credential'
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'credential' => __('auth.throttle', [ // <-- Ubah 'email' menjadi 'credential'
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        // Gunakan $this->credential untuk throttle key
        return Str::transliterate(Str::lower($this->credential)).'|'.request()->ip();
    }
}
