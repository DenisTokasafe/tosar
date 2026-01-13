<div class="z-30 flex items-start max-md:flex-col">
    <div class="self-stretch flex-1 max-md:pt-6">

        {{-- Header Section: Breadcrumbs, Heading, and Action Button --}}
        <div class="flex flex-col items-start justify-between gap-4 mb-6 md:flex-row md:items-center">
            <div>
                {{-- Breadcrumbs Otomatis --}}
                @php $currentRoute = Route::currentRouteName(); @endphp
                @if (Breadcrumbs::exists($currentRoute))
                    <div class="mb-2">
                        {!! Breadcrumbs::render($currentRoute, $reportId ?? null) !!}
                    </div>
                @endif

                <flux:heading size="xl" class="font-bold">{{ $heading ?? '' }}</flux:heading>
                <flux:subheading size="sm" class="text-gray-500">{{ $subheading ?? '' }}</flux:subheading>
            </div>

            <div class="flex items-center gap-3">
                @if (request()->routeIs('wpi.list'))
                    <a href="{{ route('wpi.create') }}" class="gap-2 text-xs uppercase btn btn-primary btn-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-plus">
                            <path d="M5 12h14" />
                            <path d="M12 5v14" />
                        </svg>
                        Laporan Baru
                    </a>
                @endif
            </div>
        </div>

        {{-- Main Content Card Area --}}
        <div
            class="flex flex-col flex-1 w-full h-full rounded-xl inset-shadow-sm
                    max-h-[calc(100vh-14rem)]
                    sm:max-h-[calc(100vh-14rem)]
                    md:max-h-[calc(100vh-12rem)]">

            <div
                class="flex-1 h-full p-4 overflow-x-hidden overflow-y-auto bg-white border shadow-sm dark:bg-neutral-900 border-neutral-200 dark:border-neutral-800 rounded-xl">

                <div class="w-full max-w-full mx-auto">
                    {{ $slot }}
                </div>
            </div>
        </div>

    </div>
</div>
