<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Livewire\Component;
use LdapRecord\Container;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule; // <-- Import Rule untuk validasi (Opsional, tapi praktik baik)

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

        /*
    |--------------------------------------------------------------------------
    | 1️⃣ COBA LOGIN LDAP (ACTIVE DIRECTORY)
    |--------------------------------------------------------------------------
    */
        try {
            $connection = Container::getDefaultConnection();

            // Format UPN: username@domain
            $ldapUsername = $this->credential . '@archimining.com';

            if ($connection->auth()->attempt($ldapUsername, $this->password)) {

                // Sinkronisasi / buat user lokal
                $user = User::firstOrCreate(
                    ['username' => $this->credential],
                    [
                        'name'     => $this->credential,
                        'email'    => null,
                        'password' => null,
                        'is_ldap'  => true,
                    ]
                );

                Auth::login($user, $this->remember);

                RateLimiter::clear($this->throttleKey());
                Session::regenerate();

                $this->redirectIntended(
                    default: route('dashboard', absolute: false),
                    navigate: true
                );
                return;
            }
        } catch (\Throwable $e) {
            // LDAP error → lanjut fallback
            // (jangan tampilkan error LDAP ke user)
        }

        /*
    |--------------------------------------------------------------------------
    | 2️⃣ FALLBACK LOGIN DATABASE (EMAIL / USERNAME)
    |--------------------------------------------------------------------------
    */

        $field = filter_var($this->credential, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'username';

        $credentials = [
            $field => $this->credential,
            'password' => $this->password,
        ];

        if (! Auth::attempt($credentials, $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'credential' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $this->redirectIntended(
            default: route('dashboard', absolute: false),
            navigate: true
        );
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
        return Str::transliterate(Str::lower($this->credential)) . '|' . request()->ip();
    }
}
