@props([
    'label' => null,
    'placeholder' => '',
    'model' => null,
    'size' => 'textarea-xs',
    'required' => false,
    'disabled' => false,
])

<fieldset class="w-full fieldset">
    {{-- Label --}}
    @if ($label)
        <x-form.label :label="$label" :required="$required" />
    @endif

    {{-- Textarea Element --}}
    <textarea
        @if($disabled) disabled @endif
        @if($model) wire:model.live="{{ $model }}" @endif
        placeholder="{{ $placeholder ?: $label }}"
        {{ $attributes->merge([
            'class' => "textarea w-full textarea-bordered focus-within:outline-none focus-within:border-info focus-within:ring-0 $size border-gray-300 rounded " .
            ($errors->has($model) ? 'border-rose-500 focus-within:border-rose-500' : '')
        ]) }}
    ></textarea> {{-- WAJIB ditutup seperti ini --}}

    {{-- Error Handling --}}
    @if($model)
        <x-label-error :messages="$errors->get($model)" />
    @endif
</fieldset>
