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
        /* Gunakan entangle langsung ke properti showdropdown agar lebih stabil */
        open: @entangle($showdropdown),
        dropdownStyles: { top: '0px', left: '0px', width: '0px' },
        calculatePosition() {
            this.$nextTick(() => {
                if (!this.$refs.inputField) return;
                const rect = this.$refs.inputField.getBoundingClientRect();
                this.dropdownStyles = {
                    top: (rect.bottom + window.scrollY) + 'px',
                    left: (rect.left + window.scrollX) + 'px',
                    width: rect.width + 'px'
                };
            });
        }
    }"
    @search-updated.window="calculatePosition()"
    x-init="$watch('open', value => { if(value) calculatePosition() })"
    @scroll.window="if(open) calculatePosition()"
    @resize.window="if(open) calculatePosition()"
>
    @if ($label)
        <x-form.label :label="$label" :required="$required" />
    @endif

    <div class="relative">
        <input
            x-ref="inputField"
            {{ $disabled ? 'disabled' : '' }}
            type="text"
            wire:model.live.debounce.300ms="{{ $modelsearch }}"
            placeholder="{{ __($placeholder) }}"
            x-on:input="open = true; calculatePosition()"
            x-on:focus="open = true; calculatePosition()"
            {{ $attributes->merge([
                'class' => 'input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs ' .
                    ($disabled ? 'bg-base-200 opacity-70 ' : '') .
                    ($errors->has($modelid) || ($manualModelName && $errors->has($manualModelName))
                        ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500'
                        : ''),
            ]) }}
        />

        @if (!$disabled && $showdropdown)
            <template x-teleport="body">
                <div
                    x-show="open"
                    x-on:click.outside="open = false"
                    :style="dropdownStyles"
                    class="fixed z-[9999] mt-1 overflow-hidden border rounded-md shadow-lg bg-base-100"
                    style="display: none;"
                >
                    <ul class="overflow-auto max-h-60">
                        <div wire:loading wire:target="{{ $modelsearch }}, {{ $clickaction }}, {{ $enableManualAction }}" class="flex flex-col items-center justify-center px-4 py-2 text-center">
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

                        @if ($manualMode)
                            <li class="p-2 border-t bg-base-50">
                                <div class="flex items-center gap-1">
                                    <input type="text" wire:model.live="{{ $manualModelName }}"
                                        placeholder="{{ __('Masukkan nama manual...') }}"
                                        class="w-full input input-bordered input-xs focus:ring-1 focus:ring-info" />
                                    <button type="button" wire:click="{{ $addManualAction }}" class="btn btn-primary btn-xs">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        {{ __('Tambah') }}
                                    </button>
                                </div>
                            </li>
                        @endif
                    </ul>
                </div>
            </template>
        @endif
    </div>

    @if ($manualMode && $manualModelName)
        <x-label-error :messages="$errors->get($manualModelName)" />
    @else
        <x-label-error :messages="$errors->get($modelid)" />
    @endif
</fieldset>
