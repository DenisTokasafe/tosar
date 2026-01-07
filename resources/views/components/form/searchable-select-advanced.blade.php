@props([
    'label' => null,
    'placeholder' => 'Cari...',
    'modelsearch' => null,
    'modelid' => null,
    'options' => [],
    'showdropdown' => false,
    'required' => false,
    'disabled' => false,
    'columnname' => 'name',
    'clickaction' => 'selectPelapor',

    // Properti khusus Mode Manual
    'manualmode' => false,
    'manualmodelname' => null,
    'enablemanualaction' => 'enableManualMode',
    'addmanualaction' => 'addManualData'
])

<fieldset class="relative fieldset md:col-span-1">
    @if($label)
        <x-form.label :label="$label" :required="$required" />
    @endif

   <div class="relative" x-data="{ open: @entangle($showdropdown).live }" x-on:click.outside="open = false">
        {{-- Input Search --}}
        <input
            type="text"
            {{ $disabled ? 'disabled' : '' }}
            wire:model.live.debounce.300ms="{{ $modelsearch }}"
            placeholder="{{ $placeholder }}"
            x-on:focus="open = true"
            @class([
                'input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs',
                'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' => $errors->has($modelid) || $errors->has($manualmodelname),
                'bg-base-200 opacity-70' => $disabled
            ])
        />

        {{-- Dropdown --}}
        @if ($showdropdown)
            <ul x-show="open" class="absolute z-50 w-full mt-1 overflow-auto border rounded-md shadow bg-base-100 max-h-60">

                <div wire:loading wire:target="{{ $clickaction }}, {{ $enablemanualaction }}" class="p-2 text-center">
                    <span class="loading loading-spinner loading-sm text-secondary"></span>
                </div>

                @if (count($options) > 0)
                    @foreach ($options as $opt)
                        <li wire:click="{{ $clickaction }}({{ $opt->id }}, '{{ addslashes($opt->{$columnname}) }}')"
                            wire:key="opt-{{ $opt->id }}"
                            x-on:click="open = false"
                            class="px-3 py-2 text-sm cursor-pointer hover:bg-base-200">
                            {{ $opt->{$columnname} }}
                        </li>
                    @endforeach
                @else
                    {{-- Mode Manual Trigger --}}
                    @if (!$manualmode)
                        <li wire:click="{{ $enablemanualaction }}"
                            class="px-3 py-2 text-sm italic cursor-pointer text-warning hover:bg-base-200">
                            Tidak ditemukan, klik untuk tambah manual
                        </li>
                    @endif
                @endif

                {{-- Input Manual Field --}}
                @if ($manualmode)
                    <li class="p-2 border-t bg-base-50">
                        <div class="flex items-center gap-1">
                            <input
                                type="text"
                                wire:model.live="{{ $manualmodelname }}"
                                placeholder="Masukkan nama manual..."
                                class="w-full input input-bordered input-xs focus:ring-1 focus:ring-info"
                            />
                            <button
                                type="button"
                                wire:click="{{ $addmanualaction }}"
                                class="btn btn-primary btn-xs"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                Tambah
                            </button>
                        </div>
                    </li>
                @endif
            </ul>
        @endif
    </div>

    {{-- Error handling dinamis --}}
    @if ($manualmode && $manualmodelname)
        <x-label-error :messages="$errors->get($manualmodelname)" />
    @else
        <x-label-error :messages="$errors->get($modelid)" />
    @endif
</fieldset>
