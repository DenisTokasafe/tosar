@props([
    'modalId' => null,
    'wireClick' => null,
    'tooltip' => 'Tambah Data',
    'color' => 'primary',
    'icon' => 'add',
    'href' => null // Tambahkan prop href
])

@php
    $colorClass = match($color) {
        'secondary' => 'btn-secondary',
        'accent'    => 'btn-accent',
        'info'      => 'btn-info',
        'success'   => 'btn-success',
        'warning'   => 'btn-warning',
        'error'     => 'btn-error',
        'default'   => '',
        default     => 'btn-primary',
    };

    $tooltipColor = $color === 'default' ? '' : 'tooltip-' . $color;

    // Logika menentukan tag yang digunakan
    $tag = $href ? 'a' : 'button';
@endphp

<div {{ $attributes->merge(['class' => 'tooltip']) }} data-tip="{{ $tooltip }}">
    {{-- Custom Tooltip Content --}}
    <div class="z-40 tooltip-content {{ $tooltipColor }}">
        <div class="text-sm font-black animate-bounce">{{ $tooltip }}</div>
    </div>

    <{{ $tag }}
        @if($href)
            href="{{ $href }}"
        @else
            type="button"
            onclick="{{ $modalId }}.showModal()"
        @endif

        @if($wireClick) wire:click="{{ $wireClick }}" @endif

        {{ $attributes->class(['btn btn-square btn-xs btn-soft', $colorClass]) }}
    >
        <x-dynamic-component :component="'icon.' . $icon" class="w-4 h-4" />
    </{{ $tag }}>
</div>
