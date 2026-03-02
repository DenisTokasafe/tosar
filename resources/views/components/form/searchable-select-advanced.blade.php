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
        {{-- Logika untuk memaksa buka jika data tersedia --}}
        checkDropdown() {
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
        {{-- Input Search --}}
        <input
            x-ref="searchInput"
            {{ $disabled ? 'disabled' : '' }}
            type="text"
            wire:model.live.debounce.300ms="{{ $modelsearch }}"
            placeholder="{{ __($placeholder) }}"
            x-on:focus="open = true"
            x-on:input="open = true"
            @click.away="open = false"
            @keydown.escape="open = false"
            {{ $attributes->merge([
                'class' => 'input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs ' .
                    ($disabled ? 'bg-base-200 opacity-70 ' : '') .
                    ($errors->has($modelid) || ($manualModelName && $errors->has($manualModelName))
                        ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500'
                        : ''),
            ]) }}
        />

        {{-- Dropdown Teleport --}}
        @if (!$disabled && $showdropdown)
            <template x-teleport="body">
                <ul
                    x-show="open"
                    x-anchor.bottom-start="$refs.searchInput"
                    :style="{ width: $refs.searchInput.offsetWidth + 'px' }"
                    class="z-[9999] mt-1 overflow-auto border rounded-md shadow-2xl bg-base-100 max-h-60 border-base-content/10"
                    {{-- Mencegah penutupan saat mengklik di dalam area dropdown (terutama untuk input manual) --}}
                    @click.stop
                >
                    {{-- Spinner saat mencari --}}
                    <div wire:loading wire:target="{{ $modelsearch }}" class="flex flex-col items-center justify-center px-4 py-4 text-center">
                        <span class="loading loading-spinner loading-sm text-info"></span>
                    </div>

                    {{-- Konten List --}}
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
                            {{-- State Kosong & Manual --}}
                            @if (!$manualMode)
                                <li wire:click="{{ $enableManualAction }}"
                                    class="px-3 py-3 text-sm italic cursor-pointer text-warning hover:bg-base-200 group">
                                    <div class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        <span>{{ __('Tidak ditemukan, klik untuk tambah manual') }}</span>
                                    </div>
                                </li>
                            @endif
                        @endif

                        {{-- Input Manual Field --}}
                        @if ($manualMode)
                            <li class="p-2 border-t bg-base-200/50">
                                <div class="flex flex-col gap-2">
                                    <p class="text-[10px] font-bold uppercase text-base-content/50 px-1 italic">Input Nama Baru</p>
                                    <div class="flex items-center gap-1">
                                        <input type="text"
                                            wire:model.live="{{ $manualModelName }}"
                                            placeholder="{{ __('Masukkan nama...') }}"
                                            class="w-full input input-bordered input-xs focus:ring-1 focus:ring-info bg-base-100"
                                            @keydown.enter.prevent="$wire.{{ $addManualAction }}(); open = false"
                                        />
                                        <button type="button" wire:click="{{ $addManualAction }}" x-on:click="open = false" class="btn btn-primary btn-xs">
                                            {{ __('Tambah') }}
                                        </button>
                                    </div>
                                </div>
                            </li>
                        @endif
                    </div>
                </ul>
            </template>
        @endif
    </div>

    {{-- Error handling --}}
    <div class="mt-1">
        @if ($manualMode && $manualModelName)
            <x-label-error :messages="$errors->get($manualModelName)" />
        @else
            <x-label-error :messages="$errors->get($modelid)" />
        @endif
    </div>
</fieldset>
