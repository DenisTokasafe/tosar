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
        class="input input-bordered {{ $size }} w-full focus:border-info focus:ring-info focus:outline-hidden
            border-gray-300 rounded
            {{ $errors->has($model) ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}"
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
