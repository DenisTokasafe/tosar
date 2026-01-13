@props([
    'label' => null,
    'placeholder' => '',
    'model' => null,
    'type' => 'text',
    'size' => 'input-xs',
    'required' => false,
])

<fieldset class="w-full fieldset">
    {{-- Label dengan indikator required --}}
   @if ($label)
        <x-form.label :label="$label" :required="$required" />
    @endif

    {{-- Input Element --}}
    <input
        type="{{ $type }}"
        {{ $model ? "wire:model.live=$model" : '' }}
        {{ $required ? 'required' : '' }}
        placeholder="{{ $placeholder ?: $label }}"
        {{ $attributes->merge([
            'class' => "input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden $size border-gray-300 rounded " .
            ($errors->has($model) ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '')
        ]) }}
    />

    {{-- Penanganan Error Otomatis --}}
    @if($model)
        <x-label-error :messages="$errors->get($model)" />
    @endif
</fieldset>
