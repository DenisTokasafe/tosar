<?php

namespace App\Http\Middleware;

use App\Models\Translation;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Set locale dari session
        $locale = session('locale', config('app.locale'));
        app()->setLocale($locale);

        // 2. Ambil data dari database (dengan Cache)
        $translations = cache()->remember("translations_json_{$locale}", 86400, function () use ($locale) {
            $column = ($locale === 'id') ? 'id_text' : 'en';
            return Translation::pluck($column, 'key')->toArray();
        });

        // 3. Suntikkan ke sistem translator Laravel secara real-time
        app('translator')->addLines($translations, $locale, '*');

        return $next($request);
    }
}
