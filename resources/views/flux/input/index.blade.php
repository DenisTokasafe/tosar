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
    // ... (Logika awal icon, wire model, loading, dan trailing icon count tetap sama)
    $iconLeading ??= $icon;
    $hasLeadingIcon = (bool) $iconLeading;

    // Icon Classes: Menggunakan warna dasar yang diredam, berubah menjadi emas saat input aktif
    $iconClasses = Flux::classes('text-[var(--color-base-content)]/40 group-focus-within/input:text-[var(--color-primary)] transition-colors duration-200')
        ->add($iconVariant === 'outline' ? 'size-5' : '');

    $classes = Flux::classes()
        // Menggunakan class DaisyUI 'input' sebagai pondasi
        ->add('input input-bordered w-full max-w-sm block disabled:shadow-none dark:shadow-none transition-all duration-200')

        // FOCUS STATE: Efek Cahaya Emas (Golden Glow) dan border yang tegas
        ->add('focus:outline-none focus:border-[var(--color-primary)] focus:ring-[3px] focus:ring-[var(--color-primary)]/20 focus-within:outline-none focus-within:border-[var(--color-primary)] focus-within:ring-[3px] focus-within:ring-[var(--color-primary)]/20 ring-0')

        ->add('appearance-none')
        ->add(match ($size) {
            'xs' => 'input-xs text-xs py-1 h-6 leading-tight',
            'sm' => 'input-sm text-sm py-1.5 h-8',
            default => 'text-[var(--color-base-content)] sm:text-sm py-2 h-10 leading-[1.375rem]',
        })
        ->add($hasLeadingIcon ? 'ps-10' : 'ps-3')
        ->add(match ($countOfTrailingIcons) {
            0 => 'pe-3', 1 => 'pe-10', 2 => 'pe-16', 3 => 'pe-23', 4 => 'pe-30', 5 => 'pe-37', 6 => 'pe-44',
        })
        ->add(match ($variant) {
            // Outline: Latar belakang bersih mengikuti base tema
            'outline' => 'bg-[var(--color-base-100)] dark:bg-transparent dark:disabled:bg-[var(--color-base-content)]/5',
            // Filled: Latar belakang sedikit lebih kontras (krem/batuan halus)
            'filled' => 'bg-[var(--color-base-200)] border-transparent hover:bg-[var(--color-base-300)]',
        })
        ->add(match ($variant) {
            'outline' => 'text-[var(--color-base-content)] disabled:text-[var(--color-base-content)]/50 placeholder-[var(--color-base-content)]/40',
            'filled' => 'text-[var(--color-base-content)] placeholder-[var(--color-base-content)]/50',
        })
        ->add(match ($variant) {
            // Validasi Error: Menggunakan warna Error (Merah Tanah) tema
            'outline' => $invalid ? 'border-[var(--color-error)] focus:border-[var(--color-error)] focus:ring-[var(--color-error)]/20' : 'border-[var(--color-base-300)] shadow-xs',
            'filled' => $invalid ? 'border-[var(--color-error)] focus:border-[var(--color-error)] focus:ring-[var(--color-error)]/20' : 'border-0',
        })
        ->add($attributes->pluck('class:input'));
@endphp

<?php if ($type === 'file'): ?>
<flux:with-field :$attributes :$name>
    <flux:input.file :$attributes :$name :$size />
</flux:with-field>
<?php elseif ($as !== 'button'): ?>
<flux:with-field :$attributes :$name>
    <div {{ $attributes->only('class')->class('w-full relative block group/input') }} data-flux-input>
        @if ($iconLeading)
        <div class="absolute top-0 bottom-0 flex items-center justify-center text-xs pointer-events-none ps-3 start-0">
            @if (is_string($iconLeading))
                <flux:icon :icon="$iconLeading" :variant="$iconVariant" :class="$iconClasses" />
            @else
                <div class="{{ $iconClasses }}"> {{ $iconLeading }} </div>
            @endif
        </div>
        @endif

        <input type="{{ $type }}" {{ $attributes->except('class')->class($type === 'file' ? '' : $classes) }}
            @isset($name) name="{{ $name }}" @endisset
            @if ($mask) x-mask="{{ $mask }}" @endif
            @if ($invalid) aria-invalid="true" data-invalid @endif
            @if (is_numeric($size)) size="{{ $size }}" @endif data-flux-control data-flux-group-target
            @if ($loading) wire:loading.class="{{ $inputLoadingClasses }}" @endif
            @if ($loading && $wireTarget) wire:target="{{ $wireTarget }}" @endif>

        <div class="absolute top-0 bottom-0 flex items-center gap-x-1.5 pe-3 end-0 text-xs text-[var(--color-base-content)]/50">
            @if ($loading)
                <flux:icon name="loading" :variant="$iconVariant" :class="$iconClasses" wire:loading :wire:target="$wireTarget" />
            @endif

            @if ($clearable) <flux:input.clearable inset="left right" :$size /> @endif

            @if ($kbd)
                {{-- KBD: Bergaya tombol fisik panel kontrol --}}
                <kbd class="kbd kbd-sm bg-[var(--color-base-200)] border-[var(--color-base-content)]/10">{{ $kbd }}</kbd>
            @endif

            @if ($expandable) <flux:input.expandable inset="left right" :$size /> @endif
            @if ($copyable) <flux:input.copyable inset="left right" :$size /> @endif
            @if ($viewable) <flux:input.viewable inset="left right" :$size /> @endif

            @if (is_string($iconTrailing))
                <flux:icon :icon="$iconTrailing" :variant="$iconVariant" :class="$iconClasses" />
            @elseif ($iconTrailing)
                {{ $iconTrailing }}
            @endif
        </div>
    </div>
</flux:with-field>
<?php else: ?>
{{-- Variant: AS BUTTON (Dropdown Trigger Style) --}}
<button {{ $attributes->merge(['type' => 'button'])->class([$classes, 'w-full relative flex items-center']) }}>
    @if ($iconLeading)
        <div class="absolute top-0 bottom-0 flex items-center justify-center text-xs ps-3 start-0">
            <flux:icon :icon="is_string($iconLeading) ? $iconLeading : ''" :variant="$iconVariant" :class="$iconClasses" />
            @if (!$is_string($iconLeading)) {{ $iconLeading }} @endif
        </div>
    @endif

    <div class="self-center flex-1 font-medium text-start truncate {{ $attributes->has('placeholder') ? 'text-[var(--color-base-content)]/40' : 'text-[var(--color-base-content)]' }}">
        {{ $attributes->get('placeholder') ?? $slot }}
    </div>

    @if ($kbd)
        <kbd class="kbd kbd-xs bg-[var(--color-base-200)] mx-2">{{ $kbd }}</kbd>
    @endif

    @if ($iconTrailing)
        <div class="ms-2">
            <flux:icon :icon="is_string($iconTrailing) ? $iconTrailing : ''" :variant="$iconVariant" :class="$iconClasses" />
            @if (!is_string($iconTrailing)) {{ $iconTrailing }} @endif
        </div>
    @endif
</button>
<?php endif; ?>
