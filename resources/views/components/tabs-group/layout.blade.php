@props(['activeTab'])
<div class="flex flex-row  md:flex-col ">
    <div class=" w-full  md:w-60 ">
        <flux:navlist-horizontal>
            <flux:navlist-horizontal.item :href="route('administration-department-group')"
                wire:navigate>{{ __('Departemen Group') }}</flux:navlist-horizontal.item>
            <flux:navlist-horizontal.item :href="route('administration-department-group-group')"
                wire:navigate>{{ __('Group') }}</flux:navlist-horizontal.item>
        </flux:navlist-horizontal>
    </div>

    {{-- <flux:separator class="md:hidden" /> --}}
    <div class=" py-2 ">
        {{-- 💡 LOGIKA KONDISIONAL BERDASARKAN ROUTE AKTIF --}}
            @if ($activeTab === 'department')
                <x-tabs-group.url_panel />
            @endif
            @if ($activeTab === 'group')
                <x-tabs-group.url_panel_group />
            @endif
        <flux:heading>{{ $heading ?? '' }}</flux:heading>
        <flux:subheading size='xs'>{{ $subheading ?? '' }}</flux:subheading>
        <div
            class="flex w-full flex-1 flex-col gap-4 rounded-xl inset-shadow-sm h-full max-h-[calc(100vh-16rem)] sm:max-h-[calc(100vh-16rem)] md:max-h-[calc(100vh-14rem)] lg:max-h-[calc(100vh-14rem)] 2xl:max-h-[calc(100vh-14rem)]">
            <div
                class="h-full flex-1 overflow-y-auto overflow-x-hidden rounded-xl border border-neutral-200 dark:border-base-200 p-4">
                <div class="w-full max-w-full ">

                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>
