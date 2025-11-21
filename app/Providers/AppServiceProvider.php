<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;
use Diglactic\Breadcrumbs\Breadcrumbs;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */

    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (file_exists(base_path('routes/breadcrumbs.php'))) {
            require_once base_path('routes/breadcrumbs.php');
        }
        App::setLocale(Session::get('locale', config('app.locale')));

        Blade::if('role', function ($roles) {
            $user = Auth::user();
            if (!$user) return false;

            $roles = is_array($roles) ? $roles : [$roles];

            // ✅ Kasus: single role (role_id)
            if (method_exists($user, 'role') && $user->role) {
                if (in_array($user->role->name, $roles)) {
                    return true;
                }
            }

            // ✅ Kasus: multiple role (pivot)
            if (method_exists($user, 'roles') && $user->roles()->whereIn('name', $roles)->exists()) {
                return true;
            }

            return false;
        });
    }
}
