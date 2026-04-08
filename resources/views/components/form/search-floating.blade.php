@props([
'label' => null,
'placeholder' => ' ',
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
    {{--
        Inisialisasi AlpineJS.
        Gunakan local state 'open' untuk UI,
        namun tetap mendukung sinkronisasi jika dibutuhkan.
    --}}
    <div class="relative w-full" x-data="{ open: false }">

        <label class="w-full floating-label">
            <input
                x-ref="trigger"
                {{ $disabled ? 'disabled' : '' }}
                type="text"
                {{-- Livewire Model untuk pencarian --}}
                wire:model.live.debounce.300ms="{{ $modelsearch }}"
                placeholder="{{ $placeholder }}"

                {{-- Event Listeners Alpine --}}
                x-on:focus="open = true"
                x-on:keydown.escape="open = false"
                x-on:keydown.down.prevent="$focus.wrap().next()"

                {{-- Merge Classes & Attributes --}}
                {{ $attributes->merge([
                    'class' => 'input input-xs w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden ' .
                    ($errors->has($modelid) ? 'border-error ring-1 ring-error focus:ring-error focus:border-error' : 'input-bordered')
                ]) }} />

            @if($label)
            <span>
                {{ $label }}
                @if($required)<span class="text-error">*</span>@endif
            </span>
            @endif
        </label>

        {{-- Dropdown Hasil Pencarian dengan Teleport ke Body (Menghindari masalah z-index & overflow) --}}
        @if ($showdropdown && !$disabled)
        <template x-teleport="body">
            <ul
                x-show="open"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-anchor.bottom-start.offset.4="$refs.trigger"
                x-on:click.outside="open = false"
                wire:ignore.self
                :style="{ width: $refs.trigger.offsetWidth + 'px' }"
                class="fixed z-[9999] overflow-auto border shadow-2xl rounded-box bg-base-100 max-h-60 border-base-content/10 p-0">
                {{-- Spinner Loading - Muncul saat Livewire sedang fetch data --}}
                <div wire:loading wire:target="{{ $modelsearch }}" class="flex items-center justify-center p-4">
                    <span class="loading loading-spinner loading-sm text-info"></span>
                </div>

                {{-- List Hasil --}}
                <div wire:loading.remove wire:target="{{ $modelsearch }}">
                    @if(count($options) > 0)
                    @foreach ($options as $opt)
                    <li
                        {{-- Gunakan parameter dari objek secara dinamis --}}
                        wire:click="{{ $clickaction }}({{ $opt->id }}, '{{ addslashes($opt->{$namedb}) }}')"
                        wire:key="opt-{{ $modelid }}-{{ $opt->id }}"
                        x-on:click="open = false"
                        class="px-4 py-3 text-sm list-none transition-colors border-b cursor-pointer hover:bg-base-200 active:bg-base-300 border-base-content/5 last:border-none text-base-content">
                        {{ $opt->{$namedb} }}
                    </li>
                    @endforeach
                    @else
                    {{-- State Kosong --}}
                    <li class="px-4 py-3 text-xs italic list-none text-base-content/50">
                        {{ __('Tidak ada hasil ditemukan') }}
                    </li>
                    @endif
                </div>
            </ul>
        </template>
        @endif
    </div>

    {{-- Error Message --}}
    @if($modelid && $errors->has($modelid))
    <x-label-error :messages="$errors->get($modelid)" />
    @endif
</fieldset>
