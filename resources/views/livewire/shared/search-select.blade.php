<fieldset class="relative fieldset md:col-span-1">
    @if ($label)
        <label class="label">
            <span class="label-text">{{ $label }} @if($required) <span class="text-error">*</span> @endif</span>
        </label>
    @endif

    <div class="relative" x-data="{ open: @entangle('showdropdown') }">
        <input
            {{ $disabled ? 'disabled' : '' }}
            type="text"
            wire:model.live.debounce.300ms="modelsearch"
            placeholder="{{ $placeholder }}"
            x-on:focus="open = true"
            class="input input-bordered w-full input-xs {{ $errors->has('modelid') ? 'input-error' : '' }}"
        />

        @if (!$disabled && $showdropdown)
            <ul x-show="open" x-on:click.outside="open = false"
                class="absolute z-[9999] w-full mt-1 overflow-auto border rounded-md shadow bg-base-100 max-h-60">

                <div wire:loading class="p-2 text-center">
                    <span class="loading loading-spinner loading-sm text-secondary"></span>
                </div>

                @forelse ($options as $opt)
                    <li wire:click="selectOption({{ $opt['id'] }}, '{{ addslashes($opt[$columnName]) }}')"
                        class="px-3 py-2 text-sm cursor-pointer hover:bg-base-200">
                        {{ $opt[$columnName] }}
                    </li>
                @empty
                    @if (!$manualMode && !empty($modelsearch))
                        <li wire:click="enableManualMode"
                            class="px-3 py-2 text-sm italic cursor-pointer text-warning hover:bg-base-200">
                            Tidak ditemukan, klik untuk tambah manual
                        </li>
                    @endif
                @endforelse

                @if ($manualMode)
                    <li class="p-2 border-t bg-base-50">
                        <div class="flex items-center gap-1">
                            <input type="text" wire:model.live="manualModelName"
                                placeholder="Masukkan nama manual..."
                                class="w-full input input-bordered input-xs" />
                            <button type="button" wire:click="$dispatch('save-manual', {name: manualModelName})" class="btn btn-primary btn-xs">
                                Tambah
                            </button>
                        </div>
                    </li>
                @endif
            </ul>
        @endif
    </div>
</fieldset>
