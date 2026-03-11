@props([
    'label' => null,
    'placeholder' => 'Cari...',
    'modelsearch' => null,
    'modelid' => null,
    'options' => [],
    'showdropdown' => false,
    'required' => false,
    'disabled' => false,
    'columnName' => 'name',
    'clickaction' => 'selectPelapor',

    // Properti khusus Mode Manual
    'manualMode' => false,
    'manualModelName' => null,
    'enableManualAction' => 'enableManualMode',
    'addManualAction' => 'addManualData',
    ])

    <fieldset class="relative fieldset md:col-span-1">
        @if($label)
            <x-form.label :label="$label" :required="$required" />
        @endif

        <div class="relative" x-data="{ open: @entangle($attributes->wire('model') . '.live') }">
            {{-- 1. Tambahkan x-ref="trigger" sebagai jangkar --}}
            <input x-ref="trigger" {{ $disabled ? 'disabled' : '' }}
                type="text" wire:model.live.debounce.300ms="{{ $modelsearch }}" placeholder="{{ __($placeholder) }}"
                x-on:focus="open = true" x-on:keydown.escape="open = false" {{ $attributes->merge([
                'class' =>
                    'input input-bordered w-full focus-within:outline-none focus-within:border-info focus-within:ring-0 input-xs ' .
                    ($disabled ? 'bg-base-200 opacity-70 ' : '') .
                    ($errors->has($modelid) || ($manualModelName && $errors->has($manualModelName))
                        ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500'
                        : ''),
            ]) }} />

            {{-- 2. Teleport Dropdown ke Body --}}
            @if(!$disabled && $showdropdown)
                <template x-teleport="body">
                    <ul x-show="open" wire:ignore.self
                        {{-- Menggunakan x-anchor agar posisi presisi di bawah input --}}
                        x-anchor.bottom-start.offset.4="$refs.trigger" x-on:click.outside="open = false"
                        {{-- Sinkronisasi lebar dengan input trigger --}}
                        :style="{ width: $refs.trigger.offsetWidth + 'px' }"
                        {{-- Z-index tinggi (9999) sesuai standar CSS kamu --}}
                        class="fixed z-[9999] overflow-auto border rounded-md shadow-xl bg-base-100 border-base-300 max-h-60">
                        {{-- Loading State --}}
                        <div wire:loading wire:target="{{ $clickaction }}, {{ $enableManualAction }}"
                            class="flex flex-col items-center justify-center px-4 py-2 text-center">
                            <span class="loading loading-spinner loading-sm text-secondary"></span>
                        </div>

                        {{-- Opsi List --}}
                        @if(count($options) > 0)
                            @foreach($options as $opt)
                                <li wire:click="{{ str_replace(['$id', '$name'], [$opt->id, \"'\".addslashes($opt->{$columnName}).\"'\"], $clickaction) }}"
                                    wire:key="opt-{{ $opt->id }}" x-on:click="open = false"
                                    class="px-3 py-1.5 text-sm cursor-pointer hover:bg-base-200 transition-colors text-base-content">
                                    {{ $opt->{$columnName} }}
                                </li>
                            @endforeach
                        @else
                            @if(!$manualMode)
                                <li wire:click="{{ $enableManualAction }}"
                                    class="px-3 py-2 text-sm italic cursor-pointer text-warning hover:bg-base-200">
                                    {{ __('Tidak ditemukan, klik untuk tambah manual') }}
                                </li>
                            @endif
                        @endif

                        {{-- Input Manual Field --}}
                        @if($manualMode)
                            <li class="p-2 border-t border-base-300 bg-base-200/50" x-on:click.stop>
                                <div class="flex items-center gap-1">
                                    <input type="text" wire:model.live="{{ $manualModelName }}"
                                        placeholder="{{ __('Nama manual...') }}"
                                        class="w-full input input-bordered input-xs focus:ring-1 focus:ring-info bg-base-100" />
                                    <button type="button" wire:click="{{ $addManualAction }}"
                                        class="btn btn-primary btn-xs">
                                        {{ __('Tambah') }}
                                    </button>
                                </div>
                            </li>
                        @endif
                    </ul>
                </template>
            @endif
        </div>

        @if($manualMode && $manualModelName)
            <x-label-error :messages="$errors->get($manualModelName)" />
        @else
            <x-label-error :messages="$errors->get($modelid)" />
        @endif
    </fieldset>
