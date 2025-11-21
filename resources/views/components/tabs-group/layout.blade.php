<div class="flex  md:flex-col ">
    <div class=" w-full  md:w-60 ">
        <flux:navlist-horizontal>
            <flux:navlist-horizontal.item :href="route('administration-department-group')"    wire:navigate>{{ __('Departemen Group') }}</flux:navlist-horizontal.item>
            <flux:navlist-horizontal.item :href="route('administration-department-group-group')"   wire:navigate>{{ __('Group') }}</flux:navlist-horizontal.item>
        </flux:navlist-horizontal>
    </div>

    {{-- <flux:separator class="md:hidden" /> --}}

    <div class=" p-2 ">
        <flux:heading>{{ $heading ?? '' }}</flux:heading>
        <flux:subheading size='xs'>{{ $subheading ?? '' }}</flux:subheading>
        <div class="mt-5  w-full ">
            {{ $slot }}
        </div>
    </div>
</div>
