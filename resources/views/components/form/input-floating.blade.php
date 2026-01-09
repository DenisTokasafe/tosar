@props([
    'label' => null,
    'type' => 'text',
    'placeholder' => '',
    'model' => null,
    'size' => 'input-xs' // default size sesuai permintaan Anda
])

<label {{ $attributes->merge(['class' => 'floating-label w-full']) }}>
    <input
        type="{{ $type }}"
        placeholder="{{ $placeholder ?: $label }}"
        {{ $model ? "wire:model=$model" : '' }}
        {{ $attributes->whereDoesntStartWith('class') }}
        class="input {{ $size }} w-full"
    />
    @if($label)
        <span>{{ $label }}</span>
    @endif
</label>

{{-- Tampilkan Error jika menggunakan WireModel --}}
@if($model)
    @error($model)
        <span class="mt-1 text-xs text-error">{{ $message }}</span>
    @enderror
@endif
