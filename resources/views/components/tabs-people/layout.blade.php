@props(['idUser'])
<x-tabs-relation.layout>
    <div
        class="flex w-full flex-1 flex-col gap-4   h-full max-h-[calc(100vh-16rem)] sm:max-h-[calc(100vh-16rem)] md:max-h-[calc(100vh-14rem)] lg:max-h-[calc(100vh-14rem)] 2xl:max-h-[calc(100vh-14rem)]">
        <div
            class="flex-1 h-full p-4 overflow-x-hidden overflow-y-auto ">
            <div class="w-full max-w-full ">
                <div class="flex items-start max-md:flex-col">
                    <div class="me-10 w-full pb-4 md:w-[220px]">
                        <flux:navlist>
                            <flux:navlist.item :href="route('people.details', $idUser)" wire:navigate>{{ __('Details') }}</flux:navlist.item>

                        </flux:navlist>
                    </div>

                    <flux:separator class="md:hidden" />

                    <div class="self-stretch flex-1 max-md:pt-6">
                        <flux:heading>{{ $heading ?? '' }}</flux:heading>
                        <flux:subheading>{{ $subheading ?? '' }}</flux:subheading>

                        <div class="w-full mt-5 ">
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-tabs-relation.layout>
