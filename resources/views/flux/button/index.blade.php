@php $iconTrailing = $iconTrailing ??= $attributes->pluck('icon:trailing'); @endphp
@php $iconLeading = $iconLeading ??= $attributes->pluck('icon:leading'); @endphp
@php $iconVariant = $iconVariant ??= $attributes->pluck('icon:variant'); @endphp

@props([
'iconTrailing' => null,
'variant' => 'outline',
'iconVariant' => null,
'iconLeading' => null,
'type' => 'button',
'loading' => null,
'size' => 'base',
'square' => null,
'inset' => null,
'icon' => null,
'kbd' => null,
])

@php
$iconLeading = $icon ??= $iconLeading;

$square ??= $slot->isEmpty();

$iconVariant ??= ($size === 'xs')
? ($square ? 'micro' : 'micro')
: ($square ? 'mini' : 'micro');

$iconClasses = Flux::classes()
->add($iconVariant === 'outline' ? ($square && $size !== 'xs' ? 'size-5' : 'size-4') : '')
;

$isTypeSubmitAndNotDisabledOnRender = $type === 'submit' && ! $attributes->has('disabled');
$isJsMethod = str_starts_with($attributes->whereStartsWith('wire:click')->first() ?? '', '$js.');
$loading ??= $loading ?? ($isTypeSubmitAndNotDisabledOnRender || $attributes->whereStartsWith('wire:click')->isNotEmpty() && ! $isJsMethod);

if ($loading && $type !== 'submit' && ! $isJsMethod) {
$attributes = $attributes->merge(['wire:loading.attr' => 'data-flux-loading']);
if (! $attributes->has('wire:target') && $target = $attributes->whereStartsWith('wire:click')->first()) {
$attributes = $attributes->merge(['wire:target' => $target], escape: false);
}
}

$classes = Flux::classes()
->add('relative items-center font-medium justify-center gap-2 whitespace-nowrap transition-all duration-200 active:scale-[0.98]')
->add('disabled:opacity-50 disabled:cursor-not-allowed disabled:pointer-events-none')
->add(match ($size) {
'base' => 'h-10 text-sm rounded-lg' . ' ' . ($square ? 'w-10' : 'px-4'),
'sm' => 'h-8 text-sm rounded-md' . ' ' . ($square ? 'w-8' : 'px-3'),
'xs' => 'h-6 text-xs rounded-md' . ' ' . ($square ? 'w-6' : 'px-2'),
})
->add('inline-flex')
->add($inset ? match ($size) {
'base' => $square
? Flux::applyInset($inset, top: '-mt-2.5', right: '-me-2.5', bottom: '-mb-2.5', left: '-ms-2.5')
: Flux::applyInset($inset, top: '-mt-2.5', right: '-me-4', bottom: '-mb-3', left: '-ms-4'),
'sm' => $square
? Flux::applyInset($inset, top: '-mt-1.5', right: '-me-1.5', bottom: '-mb-1.5', left: '-ms-1.5')
: Flux::applyInset($inset, top: '-mt-1.5', right: '-me-3', bottom: '-mb-1.5', left: '-ms-3'),
'xs' => $square
? Flux::applyInset($inset, top: '-mt-1', right: '-me-1', bottom: '-mb-1', left: '-ms-1')
: Flux::applyInset($inset, top: '-mt-1', right: '-me-2', bottom: '-mb-1', left: '-ms-2'),
} : '')

/* --- IMPLEMENTASI WARNA DAISYUI THEMES --- */
->add(match ($variant) {
'primary' => 'bg-[var(--color-primary)] hover:bg-[color-mix(in_oklab,var(--color-primary),black_10%)]',
'accent' => 'bg-[var(--color-accent)] hover:bg-[color-mix(in_oklab,var(--color-accent),black_10%)]',
'secondary' => 'bg-[var(--color-secondary)] hover:bg-[color-mix(in_oklab,var(--color-secondary),black_10%)]',
'filled' => 'bg-[var(--color-base-300)] hover:bg-[var(--color-base-content)]/10',
'outline' => 'bg-transparent border border-[var(--color-base-300)] hover:bg-[var(--color-base-200)]',
'danger' => 'bg-[var(--color-error)] hover:bg-[color-mix(in_oklab,var(--color-error),black_10%)]',
'success' => 'bg-[var(--color-success)] hover:bg-[color-mix(in_oklab,var(--color-success),black_10%)]',
'warning' => 'bg-[var(--color-warning)] hover:bg-[color-mix(in_oklab,var(--color-warning),black_10%)]',
'info' => 'bg-[var(--color-info)] hover:bg-[color-mix(in_oklab,var(--color-info),black_10%)]',
'ghost' => 'bg-transparent hover:bg-[var(--color-base-content)]/10',
'subtle' => 'bg-[var(--color-primary)]/10 hover:bg-[var(--color-primary)]/20',
})
->add(match ($variant) {
'primary' => 'text-[var(--color-primary-content)]',
'accent' => 'text-[var(--color-accent-content)]',
'secondary' => 'text-[var(--color-secondary-content)]',
'danger' => 'text-[var(--color-error-content)]',
'success' => 'text-[var(--color-success-content)]',
'warning' => 'text-[var(--color-warning-content)]',
'info' => 'text-[var(--color-info-content)]',
'subtle' => 'text-[var(--color-primary)]',
default => 'text-[var(--color-base-content)]',
})
->add(match ($variant) {
'outline' => 'border-[var(--color-base-300)] hover:border-[var(--color-base-content)]/30',
'primary', 'accent', 'secondary', 'danger', 'success', 'warning', 'info' => 'border border-black/5 shadow-sm',
default => '',
})

->add($loading ? [
'*:transition-opacity',
$type === 'submit' ? '[&[disabled]>:not([data-flux-loading-indicator])]:opacity-0' : '[&[data-flux-loading]>:not([data-flux-loading-indicator])]:opacity-0',
$type === 'submit' ? '[&[disabled]>[data-flux-loading-indicator]]:opacity-100' : '[&[data-flux-loading]>[data-flux-loading-indicator]]:opacity-100',
$type === 'submit' ? '[&[disabled]]:pointer-events-none' : 'data-flux-loading:pointer-events-none',
] : [])
;

$attributes = $attributes->merge([
'data-flux-group-target' => ! in_array($variant, ['subtle', 'ghost']),
]);
@endphp

<flux:with-tooltip :$attributes>
    <flux:button-or-link :$type :attributes="$attributes->class($classes)" data-flux-button>
        <?php if ($loading): ?>
            <div class="absolute inset-0 flex items-center justify-center opacity-0" data-flux-loading-indicator>
                <flux:icon icon="loading" :variant="$iconVariant" :class="$iconClasses" />
            </div>
        <?php endif; ?>

        <?php if (is_string($iconLeading) && $iconLeading !== ''): ?>
            <flux:icon :icon="$iconLeading" :variant="$iconVariant" :class="$iconClasses" />
        <?php elseif ($iconLeading): ?>
            {{ $iconLeading }}
        <?php endif; ?>

        <?php if ($loading && ! $slot->isEmpty()): ?>
            <span>{{ $slot }}</span>
        <?php else: ?>
            {{ $slot }}
        <?php endif; ?>

        <?php if ($kbd): ?>
            <div class="text-[10px] opacity-60 border border-current/20 px-1 rounded-sm bg-[var(--color-base-200)]">{{ $kbd }}</div>
        <?php endif; ?>

        <?php if (is_string($iconTrailing) && $iconTrailing !== ''): ?>
            <?php $iconClasses->add($square ? '' : '-ms-1'); ?>
            <flux:icon :icon="$iconTrailing" :variant="$iconVariant" :class="$iconClasses" />
        <?php elseif ($iconTrailing): ?>
            {{ $iconTrailing }}
        <?php endif; ?>
    </flux:button-or-link>
</flux:with-tooltip>