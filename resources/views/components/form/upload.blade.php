@props([
'label' => 'Lampirkan foto atau dokumentasi',
'id' => 'upload-' . md5($attributes->get('wire:model') ?? uniqid()),
'model' => null,
'file' => null,
'title' => 'Pilih file atau gambar',
'keterangan' => 'Tidak ada file yang dipilih',
'optional' => true,
'disabled' => false,
'multiple' => false, // Tambahkan prop multiple
])

<div class="flex flex-col gap-1">
    <x-form.label :label="__($label) . ($optional ? ' (' . __('optional') . ')' : '')" />

    <label for="{{ $disabled ? '' : $id }}"
        @class([ 'flex items-center gap-2 border rounded border-info' , 'cursor-pointer hover:ring-1 hover:border-info hover:ring-info hover:outline-hidden'=> !$disabled,
        'cursor-not-allowed bg-gray-100 opacity-60 border-gray-300' => $disabled,
        ])>

        <span @class([ 'btn btn-xs' , 'btn-info'=> !$disabled,
            'btn-disabled bg-gray-300 text-gray-500 border-none' => $disabled
            ])>
            {{ __($title) }}
        </span>

        {{-- Loading State --}}
        <span class="hidden" wire:loading.remove.class='hidden' wire:target="{{ $model }}">
            <span class="flex items-center gap-1 px-2">
                <span class="loading loading-bars loading-xs text-info"></span>
                <span class="text-xs text-info">{{ __('Mengunggah...') }}</span>
            </span>
        </span>

        {{-- File Name State (Logic Updated for Multiple) --}}
        <span wire:loading.remove wire:target="{{ $model }}" class="px-2 text-xs text-gray-500 truncate">
            @if ($multiple && is_array($file) && count($file) > 0)
            {{ count($file) }} {{ __('file terpilih') }}
            @elseif (!$multiple && $file && is_object($file))
            {{ $file->getClientOriginalName() }}
            @elseif ($file && is_string($file))
            {{ basename($file) }}
            @else
            {{ __($keterangan) }}
            @endif
        </span>
    </label>

    <input
        type="file"
        id="{{ $id }}"
        {{-- Gabungkan semua atribut sekaligus --}}
        {{ $attributes->merge([
        'accept' => '*',
        'wire:model' => $model,
        'multiple' => $multiple,
        'disabled' => $disabled
    ])->whereDoesntStartWith('wire:model') }}
        {{-- ^ Note: Kita tetap memisahkan wire:model jika Anda ingin kontrol manual via prop $model --}}

        {{-- Karena kita pakai wire:model="{{ $model }}" secara eksplisit di bawah,
        kita harus membuang wire:model dari $attributes agar tidak double --}}
        wire:model="{{ $model }}"
        class="hidden" />

    @error($model . '.*')
    <span class="mt-1 text-[10px] text-error font-medium italic">
        * {{ $message }}
    </span>
    @enderror

    {{-- Tambahkan juga error untuk field utamanya (misal jika array kosong) --}}
    @error($model)
    <span class="mt-1 text-[10px] text-error font-medium italic">
        * {{ $message }}
    </span>
    @enderror
</div>