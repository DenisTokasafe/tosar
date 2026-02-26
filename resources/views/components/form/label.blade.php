<label {{ $attributes->merge(['class' => 'text-xs font-medium capitalize text-base-content ']) }}>
    {{ __($label) }}
    @if ($required)
    <span class="font-bold text-red-500">*</span>
    @endif
</label>
