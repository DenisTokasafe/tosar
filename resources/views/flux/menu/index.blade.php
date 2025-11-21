@php
$classes = Flux::classes()
->add('[:where(&)]:min-w-48 p-[.3125rem]')
->add('rounded-sm shadow-xs')
->add('border border-zinc-200 dark:border-base-100')
->add('bg-white dark:bg-base-200')
->add('focus:outline-hidden')
;
@endphp

<ui-menu {{ $attributes->class($classes) }} popover="manual" data-flux-menu>
    {{ $slot }}
</ui-menu>
