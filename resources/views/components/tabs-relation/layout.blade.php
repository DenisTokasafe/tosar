<div class="flex  flex-col ">
    <div class=" hidden md:block w-60">
        <flux:navlist-horizontal>
            <flux:navlist-horizontal.item :href="route('departmentUserManager')" wire:navigate>{{ __('Depertement User Manager') }}</flux:navlist-horizontal.item>
            <flux:navlist-horizontal.item :href="route('contractorUserManager')" wire:navigate>{{ __('Contractor User Manager') }}</flux:navlist-horizontal.item>
            <flux:navlist-horizontal.item :href="route('roles')" wire:navigate>{{ __('Role') }}</flux:navlist-horizontal.item>
            <flux:navlist-horizontal.item :href="route('user_roles')" wire:navigate>{{ __('Role User') }}</flux:navlist-horizontal.item>
            <flux:navlist-horizontal.item :href="route('people')" wire:navigate>{{ __('People') }}</flux:navlist-horizontal.item>
        </flux:navlist-horizontal>
    </div>
    <div class=" md:hidden w-full">
        <flux:navlist>
            <flux:navlist-horizontal.item :href="route('departmentUserManager')" wire:navigate>{{ __('Depertement User Manager') }}</flux:navlist-horizontal.item>
            <flux:navlist-horizontal.item :href="route('contractorUserManager')" wire:navigate>{{ __('Contractor User Manager') }}</flux:navlist-horizontal.item>
            <flux:navlist-horizontal.item :href="route('roles')" wire:navigate>{{ __('Role') }}</flux:navlist-horizontal.item>
             <flux:navlist-horizontal.item :href="route('user_roles')" wire:navigate>{{ __('Role User') }}</flux:navlist-horizontal.item>
            <flux:navlist-horizontal.item :href="route('people')" wire:navigate>{{ __('People') }}</flux:navlist-horizontal.item>
        </flux:navlist>
    </div>
    {{-- <flux:separator class="md:hidden" /> --}}
    <div class=" p-2 ">
        <flux:heading>{{ $heading ?? '' }}</flux:heading>
        <flux:subheading size='xs'>{{ $subheading ?? '' }}</flux:subheading>
        <div class="flex w-full flex-1 flex-col gap-4 rounded-xl 
            h-[calc(100vh-7rem)] 
            sm:max-h-[calc(100vh-9rem)] 
            md:max-h-[calc(100vh-11rem)] 
            lg:max-h-[calc(100vh-14rem)] 
            2xl:max-h-[44rem]">
            <div class="h-full flex-1 overflow-y-auto overflow-x-hidden rounded-xl border border-neutral-200 dark:border-base-200 p-4">
                <div class="w-full max-w-full break-words">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>
