@props([
    'label' => null,
    'placeholder' => 'Cari...',
    'modelsearch' => null,
    'modelid' => null,
    'options' => [],
    'showdropdown' => false,
    'labelfield' => 'name',
    'required' => false
])

<fieldset class="w-full fieldset">
    @if($label)
        <x-form.label :label="$label" :required="$required" />
    @endif

    <div class="relative" x-data="{ open: @entangle($showdropdown) }" @click.away="open = false">
        <div class="relative flex items-center">
            <input
                type="text"
                wire:model.live.debounce.300ms="{{ $modelsearch }}"
                placeholder="{{ $placeholder }}"
                @focus="open = true"
                class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs
                {{ $errors->has($modelid) ? 'border-error ring-1 ring-error' : '' }}"
            />

            <div wire:loading wire:target="{{ $modelsearch }}" class="absolute right-2">
                <span class="loading loading-spinner loading-xs text-info"></span>
            </div>
        </div>

        {{-- PERBAIKAN: Menggunakan $this->{$modelsearch} untuk cek panjang karakter --}}
        @if ($showdropdown && strlen($this->{$modelsearch}) > 0)
            <ul class="absolute z-[100] w-full mt-1 overflow-auto border rounded-md shadow-xl bg-base-100 max-h-60 custom-scrollbar">

                <div wire:loading wire:target="selectLocation" class="p-3 text-center bg-base-200">
                    <span class="loading loading-bars loading-xs text-secondary"></span>
                </div>

                @forelse ($options as $option)
                    <li wire:key="item-{{ $option->id }}"
                        wire:click="selectLocation({{ $option->id }}, '{{ addslashes($option->$labelfield) }}')"
                        class="flex items-center justify-between px-4 py-2 text-xs transition-colors duration-150 border-b cursor-pointer hover:bg-info hover:text-white border-base-200 last:border-none">

                        <span>{{ $option->$labelfield }}</span>

                        {{-- PERBAIKAN: Cek id terpilih menggunakan $this->{$modelid} --}}
                        @if($this->{$modelid} == $option->id)
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                        @endif
                    </li>
                @empty
                    <li class="px-4 py-3 text-xs italic text-gray-500 bg-base-100">
                        {{-- PERBAIKAN: Menampilkan teks pencarian yang sedang diketik --}}
                        Data "{{ $this->{$modelsearch} }}" tidak ditemukan...
                    </li>
                @endforelse
            </ul>
        @endif
    </div>

    @if($modelid)
        <x-label-error :messages="$errors->get($modelid)" />
    @endif
</fieldset>
