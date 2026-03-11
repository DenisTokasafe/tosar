@props([
    'label' => null,
    'required' => false,
    'model' => null,
    'options' => [], // Menampung koleksi data
    'optionValue' => 'id',
    'optionLabel' => 'name',
    'placeholder' => '-- Pilih --'
    ])

    <fieldset class="fieldset">
        @if($label)
            <x-form.label :label="$label" :required="$required" />
        @endif

        <select {{ $model ? "wire:model.live=$model" : '' }} {{ $attributes->merge([
            'class' => 'select select-xs select-bordered w-full focus-within:outline-none focus-within:border-info focus-within:ring-0 ' .
            ($errors->has($model) ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '')
        ]) }}>
            <option value="">{{ __($placeholder) }}</option>

            @foreach($options as $key => $option)
                @php
                    // Cek apakah $option itu objek atau array
                    $value = is_object($option) ? $option->$optionValue : (is_array($options) ? $key : $option);
                    $label = is_object($option) ? $option->$optionLabel : $option;
                @endphp

                <option value="{{ $value }}">
                    {{ __($label) }}
                </option>
            @endforeach
        </select>

        @if($model)
            <x-label-error :messages="$errors->get($model)" />
        @endif
    </fieldset>
