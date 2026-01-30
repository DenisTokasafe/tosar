@props([
    'placeholder' => 'Cari...',
    'modelsearch' => null,    // Menampung 'searchLocation'
    'modelid' => null,        // Menampung 'location_id' untuk error highlight
    'options' => [],          // Data array/collection hasil search
    'showdropdown' => false,  // Boolean untuk kontrol visibility dropdown
    'disabled' => false, // Tambahkan prop disabled
    'clickaction' => 'selectLocation',
    'namedb' => 'name' // Nama fungsi di Parent
])

    <div class="relative mb-1" x-data="{ open: @entangle($attributes->wire('model').'.live') }">
        <input
        {{ $disabled ? 'disabled' : '' }}
            type="text"
            wire:model.live.debounce.300ms="{{ $modelsearch }}"
            placeholder="{{ $placeholder }}"
            {{ $attributes->merge([
                'class' => 'input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs ' .
                ($errors->has($modelid) ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '')
            ]) }}
        />

        @if ($showdropdown && count($options) > 0)
            <ul class="absolute z-50 w-full mt-1 overflow-auto border rounded-md shadow bg-base-100 max-h-60">

                {{-- Spinner Loading --}}
                <div wire:loading wire:target="{{ $clickaction }}" class="flex flex-col items-center justify-center p-4 space-y-2 text-center">
                    <span class="loading loading-spinner loading-sm text-secondary"></span>
                </div>

                @foreach ($options as $opt)
                    <li
                        wire:click="{{ $clickaction }}({{ $opt->id }}, '{{ addslashes($opt->{$namedb}) }}')"
                        wire:key="opt-{{ $opt->id }}"
                        class="px-3 py-2 text-sm cursor-pointer hover:bg-base-200"
                    >
                        {{ $opt->{$namedb} }}
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
    @if($modelid)
        <x-label-error :messages="$errors->get($modelid)" />
    @endif

