@props([
    'placeholder' => 'Cari...',
    'modelsearch' => null,    // Menampung 'searchLocation'
    'modelid' => null,        // Menampung 'location_id' untuk error highlight
    'options' => [],          // Data array/collection hasil search
    'showdropdown' => false,  // Boolean untuk kontrol visibility dropdown
    'disabled' => false,
    'clickaction' => 'selectLocation',
    'namedb' => 'name'
])

{{-- Gunakan @entangle langsung ke properti showdropdown yang ada di Class Livewire --}}
<div class="relative mb-1"
     x-data="{ open: @entangle($showdropdown) }"
     @click.away="open = false">

    <input
        {{ $disabled ? 'disabled' : '' }}
        type="text"
        {{-- Gunakan atribut wire:model.live secara dinamis --}}
        wire:model.live.debounce.300ms="{{ $modelsearch }}"
        placeholder="{{ $placeholder }}"
        {{-- Memastikan dropdown terbuka saat input fokus --}}
        @focus="open = true"
        {{ $attributes->merge([
            'class' => 'input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs ' .
            ($modelid && $errors->has($modelid) ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '')
        ]) }}
    />
    {{-- Gunakan x-show agar AlpineJS bisa menyembunyikan elemen dengan smooth --}}
    <ul x-show="open && @js(count($options) > 0)"
        x-cloak
        class="absolute z-50 w-full mt-1 overflow-auto border rounded-md shadow bg-base-100 max-h-60">

        {{-- Spinner Loading (Targetkan ke model search agar muncul saat mengetik) --}}
        <div wire:loading wire:target="{{ $modelsearch }}" class="p-2 text-center">
            <span class="loading loading-spinner loading-sm text-secondary"></span>
        </div>

        @foreach ($options as $opt)
            <li
                {{-- Gunakan .stop agar klik tidak mentrigger event lain --}}
                wire:click.prevent="{{ $clickaction }}({{ $opt->id }}, '{{ addslashes($opt->{$namedb}) }}')"
                wire:key="opt-{{ $opt->id }}"
                class="px-3 py-2 text-sm cursor-pointer hover:bg-base-200"
            >
                {{ $opt->{$namedb} }}
            </li>
        @endforeach
    </ul>
</div>

@if($modelid)
    <x-label-error :messages="$errors->get($modelid)" />
@endif
