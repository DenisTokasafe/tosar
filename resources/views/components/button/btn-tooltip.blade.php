@props([
'modalId' => null,
'wireClick' => null,
'tooltip' => 'Tambah Data',
'color' => 'primary',
'icon' => 'add',
'href' => null
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

// Bersihkan prefix tooltip jika color adalah default
$tooltipColorClass = $color === 'default' ? '' : 'tooltip-' . $color;

// Tentukan tag
$tag = $href ? 'a' : 'button';

// Pisahkan atribut khusus (seperti class/id dari luar) untuk diletakkan di pembungkus atau tombol
@endphp

{{-- Pembungkus Tooltip --}}
<div class="tooltip {{ $tooltipColorClass }} sm:tooltip-right" data-tip="{{ $tooltip }}">

    <{{ $tag }}
        {{-- Jika ada href, jadi link. Jika tidak, jadi button modal --}}
        @if($href)
        href="{{ $href }}"
        @else
        type="button"
        @if($modalId) onclick="{{ $modalId }}.showModal()" @endif
        @endif

        {{-- Pasang wire:click jika ada --}}
        @if($wireClick) wire:click="{{ $wireClick }}" @endif

        {{-- Merge atribut dari luar (termasuk class tambahan) ke tombol --}}
        {{ $attributes->class(['btn btn-square btn-xs shadow-sm', $colorClass]) }}>

        <x-dynamic-component :component="'icon.' . $icon" class="w-4 h-4" />
    </{{ $tag }}>
</div>