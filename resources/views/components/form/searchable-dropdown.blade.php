@props([
'label' => null,
'placeholder' => 'Cari...',
'modelsearch' => null,
'modelid' => null,
'options' => [],
'showdropdown' => false,
'required' => false,
'disabled' => false,
'clickaction' => 'selectLocation',
'namedb' => 'name',
])

<fieldset class="relative fieldset md:col-span-1">
    @if ($label)
    <x-form.label :label="$label" :required="$required" />
    @endif

    <div class="relative" x-data="{ open: @entangle($attributes->wire('model') . '.live') }">
        {{-- Input Search --}}
        <input
            x-ref="trigger"
            {{ $disabled ? 'disabled' : '' }}
            type="text"
            wire:model.live.debounce.300ms="{{ $modelsearch }}"
            placeholder="{{ __($placeholder) }}"
            x-on:focus="open = true"
            x-on:keydown.escape="open = false"
            {{ $attributes->merge([
                'class' => 'input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs ' .
                    ($disabled ? 'bg-base-200 opacity-70 ' : '') .
                    ($errors->has($modelid) ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : ''),
            ]) }} />

        {{-- Dropdown Teleport (Z-index dinaikkan agar di depan modal) --}}
        @if (!$disabled && $showdropdown)
        <template x-teleport="body">
            <ul
                x-show="open"
                wire:ignore.self {{-- KRUSIAL: Agar list tidak hilang saat Livewire update data --}}
                x-anchor.bottom-start.offset.4="$refs.trigger"
                x-on:click.outside="open = false"
                :style="{ width: $refs.trigger.offsetWidth + 'px' }"
                {{-- z-[99999] untuk memastikan berada di atas modal --}}
                class="fixed z-[99999] overflow-auto border rounded-md shadow-2xl bg-base-100 border-base-300 max-h-60">
                {{-- Spinner Loading --}}
                <div wire:loading wire:target="{{ $modelsearch }}"
                    class="flex flex-col items-center justify-center px-4 py-2 text-center">
                    <span class="loading loading-spinner loading-sm text-secondary"></span>
                </div>

                {{-- List Hasil Pencarian --}}
                @if (count($options) > 0)
                @foreach ($options as $opt)
                <li wire:click="{{ $clickaction }}({{ $opt->id }}, '{{ addslashes($opt->{$namedb}) }}')"
                    wire:key="opt-modal-{{ $opt->id }}"
                    x-on:click="open = false"
                    class="px-3 py-2 text-sm transition-colors border-b cursor-pointer hover:bg-base-200 text-base-content border-base-200 last:border-0">
                    {{ $opt->{$namedb} }}
                </li>
                @endforeach
                @else
                {{-- Pesan data kosong (Hanya muncul jika tidak sedang loading) --}}
                <li wire:loading.remove wire:target="{{ $modelsearch }}"
                    class="px-3 py-2 text-sm italic text-warning bg-base-100">
                    {{ __('Data tidak ditemukan') }}
                </li>
                @endif
            </ul>
        </template>
        @endif
    </div>

    @if ($modelid)
    <x-label-error :messages="$errors->get($modelid)" />
    @endif
</fieldset>
