@props([
'modalId' => null,
'wireClick' => null,
'tooltip' => 'Tambah Data',
'color' => 'primary',
'icon' => 'add',
'href' => null, // Tambahkan prop href
'position' => 'top' // Default posisi di atas
])

@php
$colorClass = match($color) {
'secondary' => 'btn-secondary',
'accent' => 'btn-accent',
'info' => 'btn-info',
'success' => 'btn-success',
'warning' => 'btn-warning',
'error' => 'btn-error',
'default' => '',
default => 'btn-primary',
};

$tooltipColor = $color === 'default' ? '' : 'tooltip-' . $color;
$positionClass = match($position) {
'bottom' => 'tooltip-bottom',
'left' => 'tooltip-left',
'right' => 'tooltip-right',
default => 'tooltip-top', // Default tetap top
};
// Logika menentukan tag yang digunakan
$tag = $href ? 'a' : 'button';
@endphp

{{-- Gabungkan class tooltip, warna, dan posisi di sini --}}
<div {{ $attributes->merge(['class' => 'tooltip ' . $tooltipColor . ' ' . $positionClass]) }} data-tip="{{ $tooltip }}">
    {{-- Custom Tooltip Content --}}
    <div class="z-[9999] tooltip-content ">
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

        {{ $attributes->class(['btn btn-square btn-xs ', $colorClass]) }}>
        <x-dynamic-component :component="'icon.' . $icon" class="w-4 h-4" />
    </{{ $tag }}>
</div>