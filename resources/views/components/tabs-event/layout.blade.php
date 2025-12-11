<div class="flex  flex-col ">
     <div class=" overflow-x-auto w-auto md:overflow-x-hidden ">
        <flux:navlist-horizontal>
            <flux:navlist-horizontal.item :href="route('administration-event_general-eventCategory')" wire:navigate>{{ __('Event Category') }}</flux:navlist-horizontal.item>
            <flux:navlist-horizontal.item :href="route('administration-event_general-eventType')" wire:navigate>{{ __('Event Type') }}</flux:navlist-horizontal.item>
            <flux:navlist-horizontal.item :href="route('administration-event_general-eventSubType')" wire:navigate>{{ __('Sub Event Type') }}</flux:navlist-horizontal.item>
            <flux:navlist-horizontal.item :href="route('administration-event_general-ModeratorAssignmentManager')" wire:navigate>{{ __('Moderator Assigment') }}</flux:navlist-horizontal.item>
            <flux:navlist-horizontal.item :href="route('administration-event_general-ErmAssignmentManager')" wire:navigate>{{ __('ERM Assigment') }}</flux:navlist-horizontal.item>
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
