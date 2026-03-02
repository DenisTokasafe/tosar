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
    <input
        {{ $disabled ? 'disabled' : '' }}
        type="text"
        wire:model.live.debounce.300ms="{{ $modelsearch }}"
        {{-- Menggunakan __() untuk placeholder --}}
        placeholder="{{ __($placeholder) }}"
        {{ $attributes->merge([
            'class' => 'input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs ' .
            ($disabled ? 'bg-base-200 opacity-70 ' : '') .
            ($errors->has($modelid) ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '')
        ]) }}
    />

    @if ($showdropdown && count($options) > 0)
        <ul class="absolute z-50 w-full mt-1 overflow-auto border rounded-md shadow bg-base-100 max-h-60">

            {{-- Spinner Loading dengan Teks Lokalisasi --}}
            <div wire:loading wire:target="{{ $modelsearch }}" class="flex flex-col items-center justify-center p-4 space-y-2 text-center">
                <span class="loading loading-spinner loading-sm text-secondary"></span>
                <span class="text-[10px] italic text-gray-400">{{ __('Memuat data...') }}</span>
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
