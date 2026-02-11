<label {{ $attributes->merge(['class' => 'text-xs font-medium capitalize dark:text-slate-500 text-black']) }}>
    {{ $label }}
    @if ($required)
    <span class="font-bold text-red-500">*</span>
    @endif
</label>
