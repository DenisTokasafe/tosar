@props([
    'label' => 'Lampirkan foto atau dokumentasi',
    'id' => 'upload-' . uniqid(), // ID unik otomatis jika tidak diisi
    'model' => null,              // Target wire:model
    'file' => null,               // Variabel file dari Livewire untuk cek status
    'optional' => true
])

<div class="flex flex-col gap-1">
    <x-form.label :label="$label . ($optional ? ' (optional)' : '')" />

    <label wire:ignore for="{{ $id }}"
        class="flex items-center gap-2 border rounded cursor-pointer border-info hover:ring-1 hover:border-info hover:ring-info hover:outline-hidden">

        <span class="btn btn-info btn-xs">
            Pilih file atau gambar
        </span>

        {{-- Loading State --}}
        <span wire:loading.class.remove='hidden' class="hidden" wire:target="{{ $model }}">
            <span class="mr-2 loading loading-bars loading-xs text-info"></span>
            <span class="text-xs text-info">Mengunggah...</span>
        </span>

        {{-- File Name State --}}
        <span id="name-{{ $id }}" class="text-xs text-gray-500" wire:loading.remove wire:target="{{ $model }}">
            @if ($file && is_object($file))
                {{ $file->getClientOriginalName() }}
            @else
                Belum ada file
            @endif
        </span>
    </label>

    <input
        {{ $attributes->merge(['type' => 'file', 'id' => $id, 'class' => 'hidden']) }}
        wire:model.live="{{ $model }}"
        onchange="document.getElementById('name-{{ $id }}').textContent = this.files[0]?.name ?? 'Belum ada file'"
    />

    @error($model)
        <span class="mt-1 text-xs text-error">{{ $message }}</span>
    @enderror
</div>
