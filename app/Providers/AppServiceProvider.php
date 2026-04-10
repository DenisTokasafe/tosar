<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL; // <-- Tambahkan ini

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */

    public function register(): void
    {
        if (config('app.env') === 'production') {
            $this->app['request']->server->set('HTTPS', true);
        }
        $this->app->bind('path.public', function () {
            return realpath(base_path() . '/../public_html');
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // === START: PERBAIKAN MIXED CONTENT UNTUK LIVEWIRE ===
        if (config('app.env') === 'local' || config('app.env') === 'production') {
            URL::forceScheme('https');
        }
        // === END: PERBAIKAN MIXED CONTENT ===

        // === START: CLOCKWORK MONITORING FILTER (Hanya Admin) ===
        // Matikan Clockwork secara default
        config(['clockwork.enable' => false]);

        // Gunakan view composer agar pengecekan Auth::user() tersedia
        view()->composer('*', function () {
            $user = Auth::user();
            if ($user) {
                $isAdmin = false;

                // Gunakan logika role yang sama dengan aplikasi SENTRY Anda
                if (method_exists($user, 'role') && $user->role && $user->role->name === 'admin') {
                    $isAdmin = true;
                } elseif (method_exists($user, 'roles') && $user->roles()->where('name', 'admin')->exists()) {
                    $isAdmin = true;
                }

                if ($isAdmin) {
                    config(['clockwork.enable' => true]);
                }
            }
        });
        // === END: CLOCKWORK MONITORING FILTER ===

        if (file_exists(base_path('routes/breadcrumbs.php'))) {
            require_once base_path('routes/breadcrumbs.php');
        }

        App::setLocale(Session::get('locale', config('app.locale')));

        // Implementasi Blade If untuk Role yang sudah ada
        Blade::if('role', function ($roles) {
            $user = Auth::user();
            if (!$user) return false;

            $roles = is_array($roles) ? $roles : [$roles];

            if (method_exists($user, 'role') && $user->role) {
                if (in_array($user->role->name, $roles)) {
                    return true;
                }
            }

            if (method_exists($user, 'roles') && $user->roles()->whereIn('name', $roles)->exists()) {
                return true;
            }

            return false;
        });
    }
}
