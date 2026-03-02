@props([
    'label' => null,
    'placeholder' => ' ', // Floating label daisyUI butuh placeholder (minimal spasi) agar bekerja
    'modelsearch' => null,
    'modelid' => null,
    'options' => [],
    'showdropdown' => false,
    'required' => false,
    'disabled' => false,
    'clickaction' => 'selectLocation',
    'namedb' => 'name'
])

<fieldset class="fieldset">
    {{-- Container utama harus relative agar dropdown tidak berantakan --}}
    <div class="relative w-full" x-data="{ open: false }">

        {{-- DaisyUI v5 Floating Label Structure --}}
        <label class="w-full floating-label">
            <input
                {{ $disabled ? 'disabled' : '' }}
                type="text"
                wire:model.live.debounce.300ms="{{ $modelsearch }}"
                placeholder="{{ $placeholder }}"
                {{ $attributes->merge([
                    'class' => 'input input-xs w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden ' .
                    ($errors->has($modelid) ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : 'input-bordered')
                ]) }}
            />

            @if($label)
                <span>
                    {{ $label }}
                    @if($required)<span class="text-error">*</span>@endif
                </span>
            @endif
        </label>

        {{-- Dropdown Hasil Pencarian --}}
        @if ($showdropdown && count($options) > 0)
            <ul class="absolute z-50 w-full mt-2 overflow-auto border shadow-lg rounded-box bg-base-100 max-h-60 border-base-content/10">

                {{-- Spinner Loading (Targeting search model) --}}
                <div wire:loading wire:target="{{ $modelsearch }}" class="flex flex-col items-center justify-center p-4 space-y-2 text-center">
                    <span class="loading loading-spinner loading-sm text-info"></span>
                </div>

                @foreach ($options as $opt)
                    <li
                        wire:click="{{ $clickaction }}({{ $opt->id }}, '{{ addslashes($opt->{$namedb}) }}')"
                        wire:key="opt-{{ $opt->id }}"
                        class="px-4 py-3 text-sm transition-colors border-b cursor-pointer hover:bg-base-200 active:bg-base-300 border-base-content/5 last:border-none"
                    >
                        {{ $opt->{$namedb} }}
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    {{-- Error Message --}}
    @if($modelid)
        <x-label-error :messages="$errors->get($modelid)" />
    @endif
</fieldset>
