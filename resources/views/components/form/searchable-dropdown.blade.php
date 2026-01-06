@props([
    'label' => null,
    'placeholder' => 'Cari...',
    'modelsearch' => null, {{-- Nama property untuk input pencarian (ex: searchLocation) --}}
    'modelid' => null,     {{-- Nama property untuk ID yang dipilih (ex: location_id) --}}
    'options' => [],       {{-- Data hasil search --}}
    'showdropdown' => false,
    'labelfield' => 'name', {{-- Nama kolom yang ingin ditampilkan --}}
    'required' => false
])

<fieldset class="fieldset">
    @if($label)
        <x-form.label :label="$label" :required="$required" />
    @endif

    <div class="relative" x-data="{ open: @entangle($showdropdown).live }" @click.away="open = false">
        <div class="relative flex items-center">
            <input
                type="text"
                wire:model.live.debounce.300ms="{{ $modelsearch }}"
                placeholder="{{ $placeholder }}"
                @focus="open = true"
                class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs {{ $errors->has($modelid) ? 'border-error ring-1 ring-error' : '' }}"
            />

            <div wire:loading wire:target="{{ $modelsearch }}" class="absolute right-2">
                <span class="loading loading-spinner loading-xs text-info"></span>
            </div>
        </div>

        <ul x-show="open && @js(count($options) > 0)"
            x-transition
            class="absolute z-10 w-full mt-1 overflow-auto border rounded-md shadow bg-base-100 max-h-60">

            <div wire:loading wire:target="selectLocation" class="p-2 text-center bg-base-200">
                <span class="loading loading-bars loading-xs text-secondary"></span>
            </div>

            @foreach ($options as $option)
                <li wire:key="item-{{ $option->id }}"
                    @click="$wire.selectLocation({{ $option->id }}, '{{ addslashes($option->$labelfield) }}'); open = false"
                    class="flex items-center justify-between px-3 py-2 text-xs cursor-pointer hover:bg-base-200">
                    <span>{{ $option->$labelfield }}</span>

                    @if($this->{$modelid} == $option->id)
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                        </svg>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>

    @if($modelid)
        <x-label-error :messages="$errors->get($modelid)" />
    @endif
</fieldset>
