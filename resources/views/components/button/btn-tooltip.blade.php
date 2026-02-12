@props([
    'modalId',
    'wireClick' => null,
    'tooltip' => 'Tambah Data',
    'color' => 'primary',
    'icon' => 'add' // Nama file di folder components/icon/
])

@php
    // Mapping class DaisyUI untuk btn-soft
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

    // Mapping class untuk tooltip agar warnanya senada
    $tooltipColor = match($color) {
        'default' => '',
        default   => 'tooltip-' . $color,
    };
@endphp

<div {{ $attributes->merge(['class' => 'tooltip']) }}>
    {{-- Custom Tooltip Content --}}
    <div class="z-40 tooltip-content {{ $tooltipColor }}">
        <div class="text-sm font-black animate-bounce">{{ $tooltip }}</div>
    </div>

    <button
        type="button"
        onclick="{{ $modalId }}.showModal()"
        @if($wireClick) wire:click="{{ $wireClick }}" @endif
        {{ $attributes->class(['btn btn-square btn-xs btn-soft', $colorClass]) }}
    >
        {{-- Memanggil icon secara dinamis dari folder components/icon/ --}}
        <x-dynamic-component :component="'icon.' . $icon" class="w-4 h-4" />
    </button>
</div>
