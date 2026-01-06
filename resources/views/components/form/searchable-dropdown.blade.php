@props([
    'label' => null,
    'placeholder' => 'Cari...',
    'modelSearch' => null,
    'modelId' => null,
    'options' => [],
    'showDropdown' => false,
    'labelField' => 'name', // <-- Tambahkan properti ini (default: name)
    'required' => false,
])

<fieldset class="fieldset">
    @if ($label)
        <x-form.label :label="$label" :required="$required" />
    @endif
    <div class="relative" x-data="{ open: @entangle($showDropdown) }" @click.away="open = false">
        <input type="text" wire:model.live.debounce.300ms="{{ $modelSearch }}" placeholder="{{ $placeholder }}"
            @focus="open = true"
            class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs {{ $errors->has($modelId) ? 'ring-1 ring-rose-500 focus:ring-rose-500' : '' }}" />
        @if ($showDropdown && count($options) > 0)
            <ul class="absolute z-20 w-full mt-1 overflow-auto border rounded-md shadow bg-base-100 max-h-60">
                @foreach ($options as $option)
                    {{-- Ganti $option->name menjadi $option->$labelField --}}
                    <li wire:click="selectLocation({{ $option->id }}, '{{ $option->$labelField }}')"
                        @click="open = false" class="px-3 py-2 text-xs cursor-pointer hover:bg-base-200">
                        {{ $option->$labelField }}
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</fieldset>
