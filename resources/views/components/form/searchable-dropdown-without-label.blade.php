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

<div class="relative mb-1" x-data="{ open: @entangle($attributes->wire('model').'.live') }">
    {{-- 1. Tambahkan x-ref="trigger" untuk jangkar posisi --}}
    <input
        x-ref="trigger"
        {{ $disabled ? 'disabled' : '' }}
        type="text"
        wire:model.live.debounce.300ms="{{ $modelsearch }}"
        placeholder="{{ __($placeholder) }}"
        x-on:focus="open = true"
        x-on:keydown.escape="open = false"
        {{ $attributes->merge([
            'class' => 'input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs ' .
            ($disabled ? 'bg-base-200 opacity-70 ' : '') .
            ($errors->has($modelid) ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '')
        ]) }}
    />

    {{-- 2. Gunakan x-teleport agar bebas dari overflow layout --}}
    @if ($showdropdown)
        <template x-teleport="body">
            {{-- 3. Tambahkan wire:ignore.self agar Livewire tidak merusak element saat render ulang --}}
            <ul
                x-show="open"
                wire:ignore.self
                x-anchor.bottom-start.offset.4="$refs.trigger"
                x-on:click.outside="open = false"
                :style="{ width: $refs.trigger.offsetWidth + 'px' }"
                class="fixed z-[9999] overflow-auto border rounded-md shadow-xl bg-base-100 border-base-300 max-h-60"
            >
                {{-- Spinner Loading --}}
                <div wire:loading wire:target="{{ $modelsearch }}" class="flex flex-col items-center justify-center p-4 space-y-2 text-center">
                    <span class="loading loading-spinner loading-sm text-secondary"></span>
                    <span class="text-[10px] italic text-gray-400">{{ __('Memuat data...') }}</span>
                </div>

                {{-- Render List Opsi --}}
                @if(count($options) > 0)
                    @foreach ($options as $opt)
                        <li
                            wire:click="{{ $clickaction }}({{ $opt->id }}, '{{ addslashes($opt->{$namedb}) }}')"
                            wire:key="opt-location-{{ $opt->id }}"
                            x-on:click="open = false"
                            class="px-3 py-2 text-sm cursor-pointer hover:bg-base-200 text-base-content"
                        >
                            {{ $opt->{$namedb} }}
                        </li>
                    @endforeach
                @else
                    {{-- State ketika loading selesai tapi data kosong --}}
                    <div wire:loading.remove wire:target="{{ $modelsearch }}" class="px-3 py-2 text-xs italic text-gray-500">
                        {{ __('Lokasi tidak ditemukan') }}
                    </div>
                @endif
            </ul>
        </template>
    @endif
</div>

@if($modelid)
    <x-label-error :messages="$errors->get($modelid)" />
@endif
