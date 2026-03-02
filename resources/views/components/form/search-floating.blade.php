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

<fieldset  wire:ignore.self class="fieldset">
    {{-- Container utama --}}
    <div class="relative w-full" x-data="{ open: false }">

        {{-- DaisyUI v5 Floating Label Structure --}}
        <label class="w-full floating-label" x-ref="floatingContainer">
            <input
                x-ref="floatingInput" {{-- Ref untuk jangkar dropdown --}}
                {{ $disabled ? 'disabled' : '' }}
                type="text"
                wire:model.live.debounce.300ms="{{ $modelsearch }}"
                placeholder="{{ $placeholder }}"
                x-on:focus="open = true"
                @click.away="open = false"
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

        {{-- Dropdown Hasil Pencarian dengan Teleport --}}
        @if ($showdropdown && count($options) > 0)
            <template  x-teleport="body">
                <ul
                    x-show="open"
                    {{-- Menempelkan dropdown di bawah container label agar tidak menutupi teks input --}}
                    x-anchor.bottom-start="$refs.floatingContainer"
                    {{-- Menyesuaikan lebar dengan container floating label --}}
                    :style="{ width: $refs.floatingContainer.offsetWidth + 'px' }"
                    class="z-[9999] mt-2 overflow-auto border shadow-2xl rounded-box bg-base-100 max-h-60 border-base-content/10"
                >

                    {{-- Spinner Loading (Targeting search model) --}}
                    <div wire:loading wire:target="{{ $modelsearch }}" class="flex flex-col items-center justify-center p-4 space-y-2 text-center">
                        <span class="loading loading-spinner loading-sm text-info"></span>
                    </div>

                    {{-- List Opsi --}}
                    <div wire:loading.remove wire:target="{{ $modelsearch }}">
                        @foreach ($options as $opt)
                            <li
                                wire:click="{{ $clickaction }}({{ $opt->id }}, '{{ addslashes($opt->{$namedb}) }}')"
                                wire:key="loc-opt-{{ $opt->id }}"
                                x-on:click="open = false"
                                class="px-4 py-3 text-sm transition-colors border-b cursor-pointer hover:bg-base-200 active:bg-base-300 border-base-content/5 last:border-none"
                            >
                                {{ $opt->{$namedb} }}
                            </li>
                        @endforeach
                    </div>
                </ul>
            </template>
        @endif
    </div>

    {{-- Error Message --}}
    @if($modelid)
        <x-label-error :messages="$errors->get($modelid)" />
    @endif
</fieldset>
