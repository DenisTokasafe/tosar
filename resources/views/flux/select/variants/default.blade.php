@props([
'name' => $attributes->whereStartsWith('wire:model')->first(),
'placeholder' => null,
'invalid' => null,
'size' => null,
])

@php
$invalid ??= ($name && $errors->has($name));

$classes = Flux::classes()
->add('appearance-none ') // Strip the browser's default <select> styles...
    ->add('w-full ps-3 pe-10 block ')
    ->add(match ($size) {
    default => 'h-10 py-2 text-base sm:text-sm leading-[1.375rem] rounded-lg',
    'sm' => 'h-8 py-1.5 text-sm leading-[1.125rem] rounded-xs',
    'xs' => 'h-6 text-xs leading-[1.125rem] rounded-xs',
    })
    ->add('shadow-xs  ')
    ->add('bg-base-100 dark:border dark:bg-base-100 dark:disabled:bg-white/[9%] ')
    ->add('text-zinc-700 dark:text-zinc-700 font-semibold')
    // Make the placeholder match the text color of standard input placeholders...
    ->add('has-[option.placeholder:checked]:text-zinc-400 dark:has-[option.placeholder:checked]:text-zinc-400')
    // Options on Windows don't inherit dark mode styles, so we need to force them...
    ->add('dark:[&>option]:bg-base-100 dark:[&>option]:text-base-content ')
    ->add('disabled:shadow-none')
    ->add($invalid
    ? 'border border-rose-500'
    : 'border border-zinc-200  border-black/10 focus:outline-none '
    )
    ;
    @endphp

    <select {{ $attributes->class($classes) }} @if ($invalid) aria-invalid="true" data-invalid @endif @isset ($name) name="{{ $name }}" @endisset @if (is_numeric($size)) size="{{ $size }}" @endif data-flux-control data-flux-select-native data-flux-group-target>
        <?php if ($placeholder): ?>
        <option value="" selected class="placeholder">{{ $placeholder }}</option>
        <?php endif; ?>

        {{ $slot }}
    </select>
