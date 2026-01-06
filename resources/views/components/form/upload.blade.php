@props([
    'label' => 'Lampirkan foto atau dokumentasi',
    'id' => 'upload-' . md5($attributes->get('wire:model') ?? uniqid()), // ID lebih konsisten berdasarkan nama model
    'model' => null,
    'file' => null,
    'optional' => true
])

<div class="flex flex-col gap-1">
    <x-form.label :label="$label . ($optional ? ' (optional)' : '')" />

    {{-- Hapus wire:ignore agar Livewire bisa mengupdate konten span di dalamnya --}}
    <label for="{{ $id }}"
        class="flex items-center gap-2 border rounded cursor-pointer border-info hover:ring-1 hover:border-info hover:ring-info hover:outline-hidden">

        <span class="btn btn-info btn-xs">
            Pilih file atau gambar
        </span>

        {{-- Loading State --}}
        <span wire:loading wire:target="{{ $model }}">
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
                {{-- Menampilkan nama file jika input berupa string/path dari database --}}
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
    />

    @error($model)
        <span class="mt-1 text-xs text-error">{{ $message }}</span>
    @enderror
</div>
