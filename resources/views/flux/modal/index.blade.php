@props([
'dismissible' => null,
'position' => null,
'closable' => null,
'trigger' => null,
'variant' => null,
'name' => null,
])

@php
$closable ??= $variant === 'bare' ? false : true;

$classes = Flux::classes()
->add('relative w-full outline-none')
->add(match ($variant) {
default => 'max-w-xl p-6 bg-white dark:bg-zinc-900 text-zinc-800 dark:text-zinc-200 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-700',
'flyout' => match($position) {
'bottom' => 'max-w-none w-full mt-auto p-6 bg-white dark:bg-zinc-900 text-zinc-800 dark:text-zinc-200 border-t border-zinc-200 dark:border-zinc-700 rounded-t-2xl rounded-b-none translate-y-0 shadow-lg',
'left' => 'max-w-md h-full max-h-screen mr-auto ml-0 p-6 bg-white dark:bg-zinc-900 text-zinc-800 dark:text-zinc-200 border-r border-zinc-200 dark:border-zinc-700 rounded-r-2xl rounded-l-none shadow-lg',
default => 'max-w-md h-full max-h-screen ml-auto mr-0 p-6 bg-white dark:bg-zinc-900 text-zinc-800 dark:text-zinc-200 border-l border-zinc-200 dark:border-zinc-700 rounded-l-2xl rounded-r-none shadow-lg',
},
'bare' => 'bg-transparent shadow-none p-0 max-w-none',
});

// Support adding the .self modifier to the wire:model directive...
if (($wireModel = $attributes->wire('model')) && $wireModel->directive && ! $wireModel->hasModifier('self')) {
unset($attributes[$wireModel->directive]);

$wireModel->directive .= '.self';

$attributes = $attributes->merge([$wireModel->directive => $wireModel->value]);
}

// Support <flux:modal ... @close="?"> syntax...
    if ($attributes['@close'] ?? null) {
    $attributes['wire:close'] = $attributes['@close'];
    unset($attributes['@close']);
    }

    // Support <flux:modal ... @cancel="?"> syntax...
        if ($attributes['@cancel'] ?? null) {
        $attributes['wire:cancel'] = $attributes['@cancel'];
        unset($attributes['@cancel']);
        }

        if ($dismissible === false) {
        $attributes = $attributes->merge(['disable-click-outside' => '']);
        }

        [ $styleAttributes, $attributes ] = Flux::splitAttributes($attributes, ['autofocus', 'class', 'style', 'wire:close', 'x-on:close', 'wire:cancel', 'x-on:cancel']);
        @endphp

        <ui-modal {{ $attributes }} data-flux-modal>
            @if ($trigger)
            {{ $trigger }}
            @endif

            <dialog
                wire:ignore.self
                {{ $styleAttributes->class([
            'fixed inset-0 m-0 p-0 w-full h-full max-w-none max-h-none bg-transparent flex justify-center items-center backdrop:bg-black/50 dark:backdrop:bg-black/80',
            'justify-start items-stretch' => $variant === 'flyout' && $position === 'left',
            'justify-end items-stretch' => $variant === 'flyout' && $position !== 'left' && $position !== 'bottom',
            'justify-end items-end' => $variant === 'flyout' && $position === 'bottom',
        ]) }}
                @if ($name) data-modal="{{ $name }}" @endif
                @if ($variant==='flyout' ) data-flux-flyout @endif
                x-data
                @isset($__livewire)
                x-on:modal-show.document="
            if ($event.detail.name === @js($name) && ($event.detail.scope === @js($__livewire->getId()))) $el.showModal();
            if ($event.detail.name === @js($name) && (! $event.detail.scope)) $el.showModal();
        "
                x-on:modal-close.document="
            if ($event.detail.name === @js($name) && ($event.detail.scope === @js($__livewire->getId()))) $el.close();
            if (! $event.detail.name || ($event.detail.name === @js($name) && (! $event.detail.scope))) $el.close();
        "
                @else
                x-on:modal-show.document="if ($event.detail.name === @js($name) && (! $event.detail.scope)) $el.showModal()"
                x-on:modal-close.document="if (! $event.detail.name || ($event.detail.name === @js($name) && (! $event.detail.scope))) $el.close()"
                @endif>
                <div class="{{ $classes }}">
                    {{ $slot }}

                    @if ($closable)
                    <div class="absolute top-4 right-4">
                        <flux:modal.close>
                            <flux:button variant="ghost" icon="x-mark" size="sm" alt="Close" />
                        </flux:modal.close>
                    </div>
                    @endif
                </div>
            </dialog>
        </ui-modal>