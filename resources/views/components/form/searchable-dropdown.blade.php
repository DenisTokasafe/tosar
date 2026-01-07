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
    // Props baru untuk fitur Pelapor Manual
    'manualmode' => false,
    'manualmodelname' => null,
    'enablemanualaction' => null, // Contoh: enableManualActPelapor
    'addmanualaction' => null     // Contoh: addActPelaporManual
])

<fieldset class="fieldset">
    @if($label)
        <x-form.label :label="$label" :required="$required" />
    @endif

    {{-- Gunakan @entangle untuk sinkronisasi otomatis antara Alpine.js dan Livewire --}}
    <div class="relative" x-data="{ open: @entangle($showdropdown) }" x-on:click.outside="open = false">

        <input
            {{ $disabled ? 'disabled' : '' }}
            type="text"
            wire:model.live.debounce.300ms="{{ $modelsearch }}"
            placeholder="{{ $placeholder }}"
            x-on:focus="open = true"
            {{ $attributes->merge([
                'class' => 'input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs ' .
                ($errors->has($modelid) || ($manualmodelname && $errors->has($manualmodelname)) ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '') .
                ($disabled ? ' bg-base-200 cursor-not-allowed opacity-70' : '')
            ]) }}
        />
        {{-- Dropdown hanya muncul jika variabel $showdropdown bernilai true --}}
        @if ($showdropdown)
            <ul x-show="open" class="absolute z-50 w-full mt-1 overflow-auto border rounded-md shadow bg-base-100 max-h-60">

                {{-- Spinner Loading saat proses klik atau aktifkan mode manual --}}
                <div wire:loading wire:target="{{ $clickaction }}, {{ $enablemanualaction }}" class="p-2 text-center">
                    <span class="loading loading-spinner loading-sm text-secondary"></span>
                </div>
                {{-- LOOP DATA HASIL PENCARIAN --}}
                @if (count($options) > 0)
                    @foreach ($options as $opt)
                        <li
                            wire:click="{{ $clickaction }}({{ $opt->id }}, '{{ addslashes($opt->{$namedb}) }}')"
                            wire:key="opt-{{ $opt->id }}"
                            x-on:click="open = false"
                            class="px-3 py-2 text-sm cursor-pointer hover:bg-base-200"
                        >
                            {{ $opt->{$namedb} }}
                        </li>
                    @endforeach

                {{-- JIKA DATA KOSONG: Munculkan opsi tambah manual jika ada aksi enablemanualaction --}}
                @elseif($enablemanualaction && !$manualmode)
                    <li wire:click="{{ $enablemanualaction }}"
                        class="px-3 py-2 text-sm italic cursor-pointer text-warning hover:bg-base-200">
                        Tidak ditemukan, tambah manual?
                    </li>
                @endif

                {{-- INPUT MANUAL: Muncul jika mode manual aktif --}}
                @if ($manualmode)
                    <li class="p-2 border-t bg-base-50">
                        <div class="flex items-center gap-1">
                            <input
                                type="text"
                                wire:model.live="{{ $manualmodelname }}"
                                placeholder="Ketik nama manual..."
                                class="w-full input input-bordered input-xs"
                            />
                            <button type="button" wire:click="{{ $addmanualaction }}" class="btn btn-primary btn-xs">
                                Tambah
                            </button>
                        </div>
                    </li>
                @endif
            </ul>
        @endif
    </div>

    {{-- Error handling: Pilih menampilkan error ID database atau error input manual --}}
    @if($manualmode && $manualmodelname && $errors->has($manualmodelname))
        <x-label-error :messages="$errors->get($manualmodelname)" />
    @else
        <x-label-error :messages="$errors->get($modelid)" />
    @endif
</fieldset>
