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
    @if ($label)
    <x-form.label :label="$label" :required="$required" />
    @endif

    {{-- Update bagian input dan dropdown di component view Anda --}}
    <div class="relative" x-data="{
    open: @entangle($attributes->wire('model') . '.live'),
    triggerEl: null,
    dropdownStyles: { top: '0px', left: '0px', width: '0px' },
    calculatePosition() {
        const rect = this.$refs.inputField.getBoundingClientRect();
        this.dropdownStyles = {
            top: (rect.bottom + window.scrollY) + 'px',
            left: (rect.left + window.scrollX) + 'px',
            width: rect.width + 'px'
        };
    }
}" x-init="triggerEl = $refs.inputField">

        {{-- Input Search --}}
        <input
            x-ref="inputField"
            {{ $disabled ? 'disabled' : '' }}
            type="text"
            wire:model.live.debounce.300ms="{{ $modelsearch }}"
            placeholder="{{ __($placeholder) }}"
            x-on:focus="open = true; calculatePosition()"
            x-on:input="calculatePosition()"
            {{ $attributes->merge([
            'class' => 'input input-bordered w-full input-xs ' . ($disabled ? 'bg-base-200 opacity-70' : '')
        ]) }} />

        {{-- Teleport Dropdown ke Body --}}
        <template x-teleport="body">
            <div
                x-show="open"
                x-on:click.outside="open = false"
                :style="dropdownStyles"
                class="fixed z-[9999] overflow-hidden border rounded-md shadow-lg bg-base-100"
                style="display: none;">
                <ul class="overflow-auto max-h-60">
                    <div wire:loading wire:target="{{ $clickaction }}" class="flex flex-col items-center justify-center px-4 py-2">
                        <span class="loading loading-spinner loading-sm text-secondary"></span>
                    </div>

                    @if (count($options) > 0)
                    @foreach ($options as $opt)
                    <li wire:click="{{ $clickaction }}({{ $opt->id }}, '{{ addslashes($opt->{$columnName}) }}')"
                        wire:key="opt-{{ $opt->id }}"
                        x-on:click="open = false"
                        class="px-3 py-1 text-sm cursor-pointer hover:bg-base-200">
                        {{ $opt->{$columnName} }}
                    </li>
                    @endforeach
                    @else
                    @if (!$manualMode)
                    <li wire:click="{{ $enableManualAction }}"
                        class="px-3 py-2 text-sm italic cursor-pointer text-warning hover:bg-base-200">
                        {{ __('Tidak ditemukan, klik untuk tambah manual') }}
                    </li>
                    @endif
                    @endif

                    {{-- Input Manual Field --}}
                    @if ($manualMode)
                    <li class="p-2 border-t bg-base-200">
                        <div class="flex items-center gap-1">
                            <input type="text" wire:model.live="{{ $manualModelName }}"
                                class="w-full input input-bordered input-xs" />
                            <button type="button" wire:click="{{ $addManualAction }}" class="btn btn-primary btn-xs">
                                Tambah
                            </button>
                        </div>
                    </li>
                    @endif
                </ul>
            </div>
        </template>
    </div>

    @if ($manualMode && $manualModelName)
    <x-label-error :messages="$errors->get($manualModelName)" />
    @else
    <x-label-error :messages="$errors->get($modelid)" />
    @endif
</fieldset>
