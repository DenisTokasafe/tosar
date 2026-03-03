@props([
    'label' => null,
    'placeholder' => 'Cari...',
    'modelsearch' => null,
    'modelid' => null,
    'options' => [],
    'showdropdown' => false,
    'required' => false,
    'disabled' => false,
    'clickaction' => 'selectLocation',
    'namedb' => 'name',
])

<fieldset class="fieldset">
    @if ($label)
        <x-form.label :label="$label" :required="$required" />
    @endif

    <div class="relative" x-data="{ open: @entangle($attributes->wire('model') . '.live') }">
        {{-- 1. Tambahkan x-ref sebagai jangkar posisi --}}
        <input
            x-ref="trigger"
            {{ $disabled ? 'disabled' : '' }}
            type="text"
            wire:model.live.debounce.300ms="{{ $modelsearch }}"
            placeholder="{{ $placeholder }}"
            x-on:focus="open = true"
            x-on:keydown.escape="open = false"
            {{ $attributes->merge([
                'class' =>
                    'input input-bordered w-full focus-within:outline-none focus-within:border-info focus-within:ring-0 input-xs ' .
                    ($disabled ? 'bg-base-200 opacity-70 ' : '') .
                    ($errors->has($modelid) ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : ''),
            ]) }}
        />

        {{-- 2. Teleport ke body untuk menghindari masalah overflow layout --}}
        @if ($showdropdown)
            <template x-teleport="body">
                {{-- 3. wire:ignore.self menjaga element tetap ada saat Livewire update data --}}
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
                        <span class="text-[10px] italic text-gray-400">{{ __('Mencari...') }}</span>
                    </div>

                    {{-- Render Opsi --}}
                    @if(count($options) > 0)
                        @foreach ($options as $opt)
                            <li
                                wire:click="{{ $clickaction }}({{ $opt->id }}, '{{ addslashes($opt->{$namedb}) }}')"
                                wire:key="opt-location-field-{{ $opt->id }}"
                                x-on:click="open = false"
                                class="px-3 py-2 text-sm cursor-pointer hover:bg-base-200 text-base-content"
                            >
                                {{ $opt->{$namedb} }}
                            </li>
                        @endforeach
                    @else
                        {{-- State jika data tidak ditemukan (Loading selesai tapi array kosong) --}}
                        <div wire:loading.remove wire:target="{{ $modelsearch }}" class="px-3 py-2 text-xs italic text-gray-500">
                            {{ __('Data tidak ditemukan') }}
                        </div>
                    @endif
                </ul>
            </template>
        @endif
    </div>

    @if ($modelid)
        <x-label-error :messages="$errors->get($modelid)" />
    @endif
</fieldset>
