@props([
    'placeholder' => 'Cari lokasi...',
    'modelsearch' => null,
    'modelid' => null,
    'options' => [],
    'showdropdown' => false,
    'disabled' => false,
    'clickaction' => 'selectLocation',
    'namedb' => 'name'
])

<div wire:ignore.self class="relative mb-1" x-data="{ open: false }">
    <input
        x-ref="locationInput"
        {{ $disabled ? 'disabled' : '' }}
        type="text"
        wire:model.live.debounce.300ms="{{ $modelsearch }}"
        placeholder="{{ __($placeholder) }}"
        x-on:focus="open = true"
        {{-- Menutup dropdown saat klik di luar area input/dropdown --}}
        @click.away="open = false"
        {{ $attributes->merge([
            'class' => 'input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs ' .
            ($disabled ? 'bg-base-200 opacity-70 ' : '') .
            ($errors->has($modelid) ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '')
        ]) }}
    />

    @if ($showdropdown && count($options) > 0)
        <template  x-teleport="body">
            <ul
                x-show="open"
                {{-- Memposisikan dropdown tepat di bawah input lokasi --}}
                x-anchor.bottom-start="$refs.locationInput"
                {{-- Menyamakan lebar dropdown dengan input agar proporsional --}}
                :style="{ width: $refs.locationInput.offsetWidth + 'px' }"
                class="z-[9999] mt-1 overflow-auto border rounded-md shadow-2xl bg-base-100 max-h-60"
            >
                {{-- Spinner Loading --}}
                <div wire:loading wire:target="{{ $modelsearch }}" class="flex flex-col items-center justify-center p-4 space-y-2 text-center">
                    <span class="loading loading-spinner loading-sm text-secondary"></span>
                    <span class="text-[10px] italic text-gray-400">{{ __('Memuat data...') }}</span>
                </div>

                {{-- Daftar Opsi Lokasi --}}
                <div wire:loading.remove wire:target="{{ $modelsearch }}">
                    @foreach ($options as $opt)
                        <li
                            wire:click="{{ $clickaction }}({{ $opt->id }}, '{{ addslashes($opt->{$namedb}) }}')"
                            wire:key="location-opt-{{ $opt->id }}"
                            x-on:click="open = false"
                            class="px-3 py-2 text-sm border-b cursor-pointer hover:bg-base-200 border-base-200 last:border-none"
                        >
                            {{ $opt->{$namedb} }}
                        </li>
                    @endforeach
                </div>
            </ul>
        </template>
    @endif
</div>

@if($modelid)
    <x-label-error :messages="$errors->get($modelid)" />
@endif
