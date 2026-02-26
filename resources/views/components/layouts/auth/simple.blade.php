<!DOCTYPE html>
<html data-theme="{{ session('theme', 'goldore-light') }}" lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen antialiased bg-linear-to-b from-neutral-950 to-neutral-900">
    <div class="flex flex-col items-center justify-center gap-6 p-6 bg-background min-h-svh md:p-10">
        <div class="flex flex-col w-full max-w-sm gap-2 md:max-w-lg">
            <div class="flex flex-col gap-6">
                {{ $slot }}
            </div>
        </div>
    </div>
    @fluxScripts
</body>

</html>
