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
    'manualMode' => false,
    'manualModelName' => null,
    'enableManualAction' => 'enableManualMode',
    'addManualAction' => 'addManualData',
])

<fieldset class="relative fieldset md:col-span-1"
    x-data="{
        open: false,
        {{-- Fungsi untuk memastikan dropdown terbuka jika Livewire menyuruh tampil --}}
        syncOpen() {
            if (@js($showdropdown)) {
                this.open = true;
            }
        }
    }"
    {{-- Pantau perubahan variabel showdropdown dari Livewire --}}
    x-init="$watch('@js($showdropdown)', value => { if(value) open = true })"
>
    @if ($label)
        <x-form.label :label="$label" :required="$required" />
    @endif

    <div class="relative">
        <input
            x-ref="searchInput"
            {{ $disabled ? 'disabled' : '' }}
            type="text"
            wire:model.live.debounce.300ms="{{ $modelsearch }}"
            placeholder="{{ __($placeholder) }}"
            x-on:focus="open = true"
            x-on:input="open = true"
            @click.away="open = false"
            {{ $attributes->merge([
                'class' => 'input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs ' .
                    ($disabled ? 'bg-base-200 opacity-70 ' : '') .
                    ($errors->has($modelid) || ($manualModelName && $errors->has($manualModelName))
                        ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500'
                        : ''),
            ]) }}
        />

        {{-- Bungkus dengan wire:ignore agar Livewire tidak merusak teleport saat render ulang --}}
        <div wire:ignore>
            @if (!$disabled)
                <template x-teleport="body">
                    <ul
                        {{-- Cek state Alpine DAN state Livewire --}}
                        x-show="open && @js($showdropdown)"
                        x-anchor.bottom-start="$refs.searchInput"
                        :style="{ width: $refs.searchInput.offsetWidth + 'px' }"
                        class="z-[9999] mt-1 overflow-auto border rounded-md shadow-2xl bg-base-100 max-h-60 border-base-content/10"
                        @click.stop {{-- Mencegah tutup saat klik di dalam dropdown --}}
                    >
                        {{-- Loading State --}}
                        <div wire:loading wire:target="{{ $modelsearch }}" class="flex flex-col items-center justify-center px-4 py-4 text-center">
                            <span class="loading loading-spinner loading-sm text-info"></span>
                        </div>

                        <div wire:loading.remove wire:target="{{ $modelsearch }}">
                            @if (count($options) > 0)
                                @foreach ($options as $opt)
                                    <li
                                        wire:click="{{ $clickaction }}({{ $opt->id }}, '{{ addslashes($opt->{$columnName}) }}')"
                                        wire:key="opt-{{ $modelsearch }}-{{ $opt->id }}"
                                        x-on:click="open = false"
                                        class="px-3 py-2 text-sm transition-colors border-b cursor-pointer hover:bg-base-200 border-base-content/5 last:border-none"
                                    >
                                        {{ $opt->{$columnName} }}
                                    </li>
                                @endforeach
                            @else
                                @if (!$manualMode)
                                    <li wire:click="{{ $enableManualAction }}"
                                        class="px-3 py-3 text-sm italic cursor-pointer text-warning hover:bg-base-200">
                                        {{ __('Tidak ditemukan, klik untuk tambah manual') }}
                                    </li>
                                @endif
                            @endif

                            @if ($manualMode)
                                <li class="p-2 border-t bg-base-200/50">
                                    <div class="flex items-center gap-1">
                                        <input type="text" wire:model.live="{{ $manualModelName }}"
                                            placeholder="{{ __('Nama manual...') }}"
                                            class="w-full input input-bordered input-xs focus:ring-1 focus:ring-info bg-base-100"
                                            @keydown.enter.prevent="$wire.{{ $addManualAction }}(); open = false"
                                        />
                                        <button type="button" wire:click="{{ $addManualAction }}" x-on:click="open = false" class="btn btn-primary btn-xs">
                                            Tambah
                                        </button>
                                    </div>
                                </li>
                            @endif
                        </div>
                    </ul>
                </template>
            @endif
        </div>
    </div>

    @if ($manualMode && $manualModelName)
        <x-label-error :messages="$errors->get($manualModelName)" />
    @else
        <x-label-error :messages="$errors->get($modelid)" />
    @endif
</fieldset>
