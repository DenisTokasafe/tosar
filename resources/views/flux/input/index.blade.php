@php $iconTrailing = $iconTrailing ??= $attributes->pluck('icon:trailing'); @endphp
@php $iconLeading = $iconLeading ??= $attributes->pluck('icon:leading'); @endphp
@php $iconVariant = $iconVariant ??= $attributes->pluck('icon:variant'); @endphp

@props([
    'name' => $attributes->whereStartsWith('wire:model')->first(),
    'iconVariant' => 'mini',
    'variant' => 'outline',
    'iconTrailing' => null,
    'iconLeading' => null,
    'expandable' => null,
    'clearable' => null,
    'copyable' => null,
    'viewable' => null,
    'invalid' => null,
    'loading' => null,
    'type' => 'text',
    'mask' => null,
    'size' => null,
    'icon' => null,
    'kbd' => null,
    'as' => null,
])

@php
    $wireModel = $attributes->wire('model');
    $wireTarget = null;

    if ($loading !== false) {
        if ($loading === true) {
            $loading = true;
        } elseif ($wireModel?->directive) {
            $loading = $wireModel->hasModifier('live');
            $wireTarget = $loading ? $wireModel->value() : null;
        } else {
            $wireTarget = $loading;
            $loading = (bool) $loading;
        }
    }

    $invalid ??= $name && $errors->has($name);
    $iconLeading ??= $icon;
    $hasLeadingIcon = (bool) $iconLeading;

    $countOfTrailingIcons = collect([
        (bool) $iconTrailing,
        (bool) $kbd,
        (bool) $clearable,
        (bool) $copyable,
        (bool) $viewable,
        (bool) $expandable,
    ])
        ->filter()
        ->count();

    $iconClasses = Flux::classes()->add($iconVariant === 'outline' ? 'size-5' : '');

    $inputLoadingClasses = Flux::classes()->add(
        match ($countOfTrailingIcons) {
            0 => 'pe-10',
            1 => 'pe-16',
            2 => 'pe-23',
            3 => 'pe-30',
            4 => 'pe-37',
            5 => 'pe-44',
            6 => 'pe-51',
        },
    );

    $classes = Flux::classes()
        // Integrasi class utama Anda di sini
        ->add('input input-bordered w-full max-w-sm block disabled:shadow-none dark:shadow-none')
        // Integrasi focus state Anda
        ->add('focus-within:outline-none focus-within:border-info focus-within:ring-0 ring-0')
        ->add('appearance-none')
        ->add(
            match ($size) {
                // Integrasi input-xs Anda
                'xs' => 'input-xs text-xs py-1 h-6 leading-tight',
                'sm' => 'text-sm py-1.5 h-8 leading-[1.125rem]',
                default => 'text-black sm:text-sm py-2 h-10 leading-[1.375rem]',
            },
        )
        ->add(
            match ($hasLeadingIcon) {
                true => 'ps-10',
                false => 'ps-3',
            },
        )
        ->add(
            match ($countOfTrailingIcons) {
                0 => 'pe-3',
                1 => 'pe-10',
                2 => 'pe-16',
                3 => 'pe-23',
                4 => 'pe-30',
                5 => 'pe-37',
                6 => 'pe-44',
            },
        )
        ->add(
            match ($variant) {
                'outline' => 'bg-white dark:bg-transparent dark:disabled:bg-white/[7%]',
                'filled' => 'bg-zinc-800/5 dark:bg-white/10 dark:disabled:bg-white/[7%]',
            },
        )
        ->add(
            match ($variant) {
                'outline' => 'text-black disabled:text-zinc-500 placeholder-zinc-400 dark:text-white',
                'filled' => 'text-zinc-700 placeholder-zinc-500 dark:text-zinc-200',
            },
        )
        ->add(
            match ($variant) {
                'outline' => $invalid ? 'border-red-500' : 'shadow-xs',
                'filled' => $invalid ? 'border-red-500' : 'border-0',
            },
        )
        ->add($attributes->pluck('class:input'));
@endphp

<?php if ($type === 'file'): ?>
<flux:with-field :$attributes :$name>
    <flux:input.file :$attributes :$name :$size />
</flux:with-field>
<?php elseif ($as !== 'button'): ?>
<flux:with-field :$attributes :$name>
    <div {{ $attributes->only('class')->class('w-full relative block group/input') }} data-flux-input>
        <?php if (is_string($iconLeading)): ?>
        <div
            class="absolute top-0 bottom-0 flex items-center justify-center text-xs pointer-events-none text-zinc-400/75 ps-3 start-0">
            <flux:icon :icon="$iconLeading" :variant="$iconVariant" :class="$iconClasses" />
        </div>
        <?php elseif ($iconLeading): ?>
        <div
            {{ $iconLeading->attributes->class('absolute top-0 bottom-0 flex items-center justify-center text-xs text-zinc-400/75 ps-3 start-0') }}>
            {{ $iconLeading }}
        </div>
        <?php endif; ?>

        <input type="{{ $type }}" {{ $attributes->except('class')->class($type === 'file' ? '' : $classes) }}
            @isset($name) name="{{ $name }}" @endisset
            @if ($mask) x-mask="{{ $mask }}" @endif
            @if ($invalid) aria-invalid="true" data-invalid @endif
            @if (is_numeric($size)) size="{{ $size }}" @endif data-flux-control data-flux-group-target
            @if ($loading) wire:loading.class="{{ $inputLoadingClasses }}" @endif
            @if ($loading && $wireTarget) wire:target="{{ $wireTarget }}" @endif>

        <div class="absolute top-0 bottom-0 flex items-center gap-x-1.5 pe-3 end-0 text-xs text-zinc-400">
            <?php if ($loading): ?>
            <flux:icon name="loading" :variant="$iconVariant" :class="$iconClasses" wire:loading
                :wire:target="$wireTarget" />
            <?php endif; ?>

            <?php if ($clearable): ?>
            <flux:input.clearable inset="left right" :$size />
            <?php endif; ?>

            <?php if ($kbd): ?>
            <span class="pointer-events-none">{{ $kbd }}</span>
            <?php endif; ?>

            <?php if ($expandable): ?>
            <flux:input.expandable inset="left right" :$size />
            <?php endif; ?>

            <?php if ($copyable): ?>
            <flux:input.copyable inset="left right" :$size />
            <?php endif; ?>

            <?php if ($viewable): ?>
            <flux:input.viewable inset="left right" :$size />
            <?php endif; ?>

            <?php if (is_string($iconTrailing)): ?>
            <?php
            $trailingIconClasses = clone $iconClasses;
            $trailingIconClasses->add('pointer-events-none text-zinc-400/75');
            ?>
            <flux:icon :icon="$iconTrailing" :variant="$iconVariant" :class="$trailingIconClasses" />
            <?php elseif ($iconTrailing): ?>
            {{ $iconTrailing }}
            <?php endif; ?>
        </div>
    </div>
</flux:with-field>
<?php else: ?>
<button {{ $attributes->merge(['type' => 'button'])->class([$classes, 'w-full relative flex']) }}>
    @if (is_string($iconLeading))
        <div class="absolute top-0 bottom-0 flex items-center justify-center text-xs text-zinc-400/75 ps-3 start-0">
            <flux:icon :icon="$iconLeading" :variant="$iconVariant" :class="$iconClasses" />
        </div>
    @elseif ($iconLeading)
        <div
            {{ $iconLeading->attributes->class('absolute top-0 bottom-0 flex items-center justify-center text-xs text-zinc-400/75 ps-3 start-0') }}>
            {{ $iconLeading }}
        </div>
    @endif

    @if ($attributes->has('placeholder'))
        <div class="self-center flex-1 block font-medium text-start text-zinc-400 dark:text-white/40">
            {{ $attributes->get('placeholder') }}
        </div>
    @else
        <div class="self-center flex-1 font-medium text-start text-zinc-800 dark:text-white">
            {{ $slot }}
        </div>
    @endif

    @if ($kbd)
        <div class="absolute top-0 bottom-0 flex items-center justify-center text-xs text-zinc-400/75 pe-4 end-0">
            {{ $kbd }}
        </div>
    @endif

    @if (is_string($iconTrailing))
        <div class="absolute top-0 bottom-0 flex items-center justify-center text-xs text-zinc-400/75 pe-3 end-0">
            <flux:icon :icon="$iconTrailing" :variant="$iconVariant" :class="$iconClasses" />
        </div>
    @elseif ($iconTrailing)
        <div
            {{ $iconTrailing->attributes->class('absolute top-0 bottom-0 flex items-center justify-center text-xs text-zinc-400/75 pe-2 end-0') }}>
            {{ $iconTrailing }}
        </div>
    @endif
</button>
<?php endif; ?>
