@props([
'expandable' => false,
'expanded' => true,
'heading' => null,
'route' => null,
'icon' => null,
'iconVariant' => null,
'iconClasses' => 'size-4',
'iconDot' => false,
])

@if ($expandable && $heading)
@php
// Cek apakah route saat ini sedang aktif
$isActive = $route ? Request::is($route) : false;
// Buka dropdown jika menu sedang aktif ATAU diset expanded dari prop
$isOpen = $isActive || $expanded;
@endphp

<li>
    <details {{ $isOpen ? 'open' : '' }}>
        <summary class="{{ $isActive ? 'active bg-neutral text-neutral-content font-semibold' : 'text-base-content/80 hover:bg-accent hover:text-accent-content' }}">

            {{-- Logika Icon --}}
            @if ($icon)
            <div class="relative">
                @if (is_string($icon) && $icon !== '')
                <flux:icon :$icon :variant="$iconVariant" class="{!! $iconClasses !!}" />
                @else
                {{ $icon }}
                @endif

                @if ($iconDot)
                <div class="absolute top-[-2px] end-[-2px]">
                    <div class="size-[6px] rounded-sm bg-neutral-content/50"></div>
                </div>
                @endif
            </div>
            @endif

            {{ $heading }}
        </summary>

        <ul>
            {{ $slot }}
        </ul>
    </details>
</li>

@elseif ($heading)
{{-- Jika ada heading tapi tidak expandable (Group Header Statis) --}}
<li>
    <h2 class="menu-title text-xs font-bold leading-none tracking-wider uppercase text-base-content/50 flex items-center gap-2">

        {{-- Logika Icon untuk Header Statis (Opsional) --}}
        @if ($icon)
        <div class="relative">
            @if (is_string($icon) && $icon !== '')
            <flux:icon :$icon :variant="$iconVariant" class="{!! $iconClasses !!}" />
            @else
            {{ $icon }}
            @endif

            @if ($iconDot)
            <div class="absolute top-[-2px] end-[-2px]">
                <div class="size-[6px] rounded-sm bg-neutral-content/50"></div>
            </div>
            @endif
        </div>
        @endif

        {{ $heading }}
    </h2>
    <ul>
        {{ $slot }}
    </ul>
</li>

@else
{{-- Fallback jika hanya memanggil slot biasa --}}
{{ $slot }}
@endif