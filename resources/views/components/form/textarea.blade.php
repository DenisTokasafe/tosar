@props([
    'label' => null,
    'placeholder' => '',
    'model' => null,
    'rows' => 3,
    'size' => 'text-xs'
])

<div class="w-full">
    <label class="w-full floating-label">
        <textarea
            {{ $model ? "wire:model.live=$model" : '' }}
            placeholder="{{ $placeholder ?: $label }}"
            rows="{{ $rows }}"
            {{ $attributes->merge(['class' => "textarea textarea-bordered w-full $size border-gray-300 rounded focus:border-info focus:ring-info".($errors->has($model) ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '')]) }}
        ></textarea>
        @if($label)
            <span>{{ $label }}</span>
        @endif
    </label>

    {{-- Penanganan Error Otomatis --}}
    @if($model)
        <x-label-error :messages="$errors->get($model)" />
    @endif
</div>
