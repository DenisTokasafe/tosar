@props([
    'label' => null,
    'placeholder' => '',
    'model' => null,
    'type' => 'text',
    'size' => 'input-xs',
    'required' => false,
     'disabled' => false,
])

<fieldset class="w-full fieldset">
    {{-- Label dengan indikator required --}}
   @if ($label)
        <x-form.label :label="$label" :required="$required" />
    @endif

    {{-- Input Element --}}
    <input  {{ $disabled ? 'disabled' : '' }}
        type="{{ $type }}"
        {{ $model ? "wire:model.live=$model" : '' }}
        placeholder="{{ $placeholder ?: $label }}"
        {{ $attributes->merge([
            'class' => "input input-bordered w-full focus-within:outline-none focus-within:border-info focus-within:ring-0 $size border-gray-300 rounded " .
            ($errors->has($model) ? 'focus:ring-rose-500  focus-within:outline-none focus-within:border-rose-500  focus-within:ring-0' : '')
        ]) }}
    />

    {{-- Penanganan Error Otomatis --}}
    @if($model)
        <x-label-error :messages="$errors->get($model)" />
    @endif
</fieldset>
