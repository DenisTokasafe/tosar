@props([
    'label' => 'Lampirkan foto atau dokumentasi',
    'id' => 'upload-' . md5($attributes->get('wire:model') ?? uniqid()),
    'model' => null,
    'file' => null,
    'optional' => true,
    'disabled' => false {{-- Tambahkan prop baru --}}
])

<div class="flex flex-col gap-1">
    <x-form.label :label="$label . ($optional ? ' (optional)' : '')" />

    {{-- Gunakan kondisional class: jika disabled, matikan pointer-events dan ubah visualnya --}}
    <label for="{{ $disabled ? '' : $id }}"
        @class([
            'flex items-center gap-2 border rounded border-info',
            'cursor-pointer hover:ring-1 hover:border-info hover:ring-info hover:outline-hidden' => !$disabled,
            'cursor-not-allowed bg-gray-100 opacity-60 border-gray-300' => $disabled,
        ])>

        <span @class([
            'btn btn-xs',
            'btn-info' => !$disabled,
            'btn-disabled bg-gray-300 text-gray-500 border-none' => $disabled
        ])>
            Pilih file atau gambar
        </span>

        {{-- Loading State --}}
        <span class="hidden" wire:loading.remove.class='hidden' wire:target="{{ $model }}">
            <span class="flex items-center gap-1 px-2">
                <span class="loading loading-bars loading-xs text-info"></span>
                <span class="text-xs text-info">Mengunggah...</span>
            </span>
        </span>

        {{-- File Name State --}}
        <span wire:loading.remove wire:target="{{ $model }}" class="px-2 text-xs text-gray-500 truncate">
            @if ($file && is_object($file))
                {{ $file->getClientOriginalName() }}
            @elseif ($file && is_string($file))
                {{ basename($file) }}
            @else
                Belum ada file
            @endif
        </span>
    </label>

    <input
        type="file"
        id="{{ $id }}"
        {{ $attributes->whereDoesntStartWith('wire:model') }}
        wire:model="{{ $model }}"
        class="hidden"
        @disabled($disabled) {{-- Menonaktifkan input file --}}
    />

    @error($model)
        <span class="mt-1 text-xs text-error">{{ $message }}</span>
    @enderror
</div>
