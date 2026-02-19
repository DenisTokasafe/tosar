@props(['idUser'])
<x-tabs-relation.layout>
    <div
        class="flex w-full flex-1 flex-col    h-full max-h-[calc(100vh-16rem)] sm:max-h-[calc(100vh-16rem)] md:max-h-[calc(100vh-14rem)] lg:max-h-[calc(100vh-14rem)] 2xl:max-h-[calc(100vh-14rem)]">
        <div class="flex-1 h-full p-4 overflow-x-hidden ">
            <div class="w-full max-w-full ">
                <div class="flex items-start max-md:flex-col">
                    <div class=" gap-4 w-full p-2 md:w-[220px] overflow-y-auto ">
                        <flux:navlist>
                            <flux:navlist.item :href="route('people.details', $idUser)" wire:navigate>
                                {{ __('Details') }}</flux:navlist.item>
                        </flux:navlist>
                    </div>

                    <div class="m-2">
                        <flux:separator class="md:hidden" />
                        <flux:separator vertical class="hidden md:block" />
                    </div>

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
