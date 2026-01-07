@props([
    'label' => null,
    'placeholder' => 'Cari...',
    'modelsearch' => null,
    'modelid' => null,
    'options' => [],
    'showdropdown' => false,
    'required' => false,
    'clickaction' => 'selectLocation',
    'columnname' => 'name' // Tambahkan default kolom 'name'
])

<fieldset class="fieldset">
    @if($label)
        <x-form.label :label="$label" :required="$required" />
    @endif

    <div class="relative" x-data="{ open: @entangle($showdropdown) }" x-on:click.outside="open = false">
        <input
            type="text"
            wire:model.live.debounce.300ms="{{ $modelsearch }}"
            placeholder="{{ $placeholder }}"
            x-on:focus="open = true"
            {{ $attributes->merge([
                'class' => 'input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs ' .
                ($errors->has($modelid) ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '')
            ]) }}
        />

        @if ($showdropdown && count($options) > 0)
            <ul x-show="open" class="absolute z-50 w-full mt-1 overflow-auto border rounded-md shadow bg-base-100 max-h-60">

                {{-- Spinner Loading --}}
                <div wire:loading wire:target="{{ $clickaction }}" class="p-2 text-center">
                    <span class="loading loading-spinner loading-sm text-secondary"></span>
                </div>

                @foreach ($options as $opt)
                    @php
                        // Mengambil nilai secara dinamis berdasarkan properti columnname
                        $displayValue = $opt->{$columnname};
                    @endphp
                    <li
                        wire:click="{{ $clickaction }}({{ $opt->id }}, '{{ addslashes($displayValue) }}')"
                        wire:key="opt-{{ $opt->id }}"
                        x-on:click="open = false"
                        class="px-3 py-2 text-sm cursor-pointer hover:bg-base-200"
                    >
                        {{ $displayValue }}
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    @if($modelid)
        <x-label-error :messages="$errors->get($modelid)" />
    @endif
</fieldset>
