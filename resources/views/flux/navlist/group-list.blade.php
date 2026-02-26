@props([
'expandable' => false,
'expanded' => true,
'heading' => null,
'route' => null,
])

@if ($expandable && $heading)
@php
$isActive = Request::is($route);
@endphp
<ui-disclosure {{ $attributes->class('group/disclosure') }} {{ $isActive ? 'open' : '' }} data-flux-navlist-group>
    <button type="button"
        class="w-full h-10 lg:h-8 flex items-center group/disclosure-button mb-[2px] rounded-sm transition-colors
            {{ $isActive
                ? 'bg-neutral text-neutral-content font-semibold'
                : 'text-base-content/80 hover:bg-accent hover:text-accent-content'
            }}">

        <div class="ps-3 pe-4">
            {{-- Toggle icon berdasarkan state open/closed --}}
            <flux:icon.chevron-down class="size-3! hidden group-data-open/disclosure:block" />
            <flux:icon.chevron-right class="size-3! block group-data-open/disclosure:hidden" />
        </div>

        <span class="text-xs font-medium leading-none">{{ $heading }}</span>
    </button>

    <div class="relative hidden data-open:block space-y-[2px] ps-7" @if ($expanded===true) data-open @endif>
        {{-- Garis indikator vertikal (Sub-menu line) --}}
        <div class="absolute inset-y-[3px] w-px bg-base-content/10 start-0 ms-4"></div>

        {{ $slot }}
    </div>
</ui-disclosure>

@elseif ($heading)
<div {{ $attributes->class('block space-y-[2px]') }}>
    <div class="px-3 py-2">
        {{-- Header statis jika tidak expandable --}}
        <div class="text-xs font-bold leading-none tracking-wider uppercase text-base-content/50">
            {{ $heading }}
        </div>
    </div>

    <div>
        {{ $slot }}
    </div>
</div>
@else
<div {{ $attributes->class('block space-y-[2px]') }}>
    {{ $slot }}
</div>
@endif
