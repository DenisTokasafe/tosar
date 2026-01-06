@props([
    'label' => null,
    'placeholder' => 'Cari...',
    'modelSearch' => null,     // Contoh: searchLocation
    'modelId' => null,         // Contoh: location_id
    'options' => [],           // Koleksi data (locations)
    'showDropdown' => false,
    'selectedName' => '',      // Nama yang sudah dipilih untuk ditampilkan di input
    'required' => false
])

<fieldset class="fieldset">
    @if($label)
        <x-form.label :label="$label" :required="$required" />
    @endif

    <div class="relative" x-data="{ open: @entangle($showDropdown) }" @click.away="open = false">
        <input
            type="text"
            wire:model.live.debounce.300ms="{{ $modelSearch }}"
            placeholder="{{ $placeholder }}"
            @focus="open = true"
            class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs {{ $errors->has($modelId) ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}"
        />

        @if ($showDropdown && count($options) > 0)
            <ul class="absolute z-20 w-full mt-1 overflow-auto border rounded-md shadow bg-base-100 max-h-60">
                <div wire:loading wire:target="selectLocation" class="p-2 text-center">
                    <span class="loading loading-spinner loading-sm text-secondary"></span>
                </div>

                @foreach ($options as $option)
                    <li wire:click="selectLocation({{ $option->id }}, '{{ $option->name }}')"
                        @click="open = false"
                        class="px-3 py-2 text-xs cursor-pointer hover:bg-base-200">
                        {{ $option->name }}
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <x-label-error :messages="$errors->get($modelId)" />
</fieldset>
