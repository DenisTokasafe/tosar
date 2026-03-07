@props([
'label' => 'Lampirkan foto atau dokumentasi',
'id' => 'upload-' . md5($attributes->get('wire:model') ?? uniqid()),
'model' => null,
'file' => null,
'placeholder' => 'Pilih file atau gambar',
'optional' => true,
'disabled' => false
])

<div class="flex flex-col gap-1">
    {{-- Label menggunakan helper __() dan memisahkan string (optional) agar fleksibel --}}
    <x-form.label :label="__($label) . ($optional ? ' (' . __('optional') . ')' : '')" />

    <label for="{{ $disabled ? '' : $id }}"
        @class([ 'flex items-center gap-2 border rounded border-info' , 'cursor-pointer hover:ring-1 hover:border-info hover:ring-info hover:outline-hidden'=> !$disabled,
        'cursor-not-allowed bg-gray-100 opacity-60 border-gray-300' => $disabled,
        ])>

        <span @class([ 'btn btn-xs' , 'btn-info'=> !$disabled,
            'btn-disabled bg-gray-300 text-gray-500 border-none' => $disabled
            ])>
            {{ __($placeholder) }}
        </span>

        {{-- Loading State --}}
        <span class="hidden" wire:loading.remove.class='hidden' wire:target="{{ $model }}">
            <span class="flex items-center gap-1 px-2">
                <span class="loading loading-bars loading-xs text-info"></span>
                <span class="text-xs text-info">{{ __('Mengunggah...') }}</span>
            </span>
        </span>

        {{-- File Name State --}}
        <span wire:loading.remove wire:target="{{ $model }}" class="px-2 text-xs text-gray-500 truncate">
            @if ($file && is_object($file))
            {{ $file->getClientOriginalName() }}
            @elseif ($file && is_string($file))
            {{ basename($file) }}
            @else
            {{ __('Belum ada file') }}
            @endif
        </span>
    </label>

    <input
        type="file"
        id="{{ $id }}"
        {{ $attributes->whereDoesntStartWith('wire:model') }}
        wire:model="{{ $model }}"
        class="hidden"
        @disabled($disabled) />

    @error($model)
    <span class="mt-1 text-xs text-error">{{ $message }}</span>
    @enderror
</div>