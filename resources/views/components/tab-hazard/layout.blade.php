<div class="z-30 flex flex-col items-start w-full min-h-screen p-4 md:flex-row">
    <div class="self-stretch flex-1 w-full">
        <div class="flex flex-col w-full h-full gap-4 rounded-xl
            /* Mobile (Android) */
            max-h-[calc(100vh-12rem)]
            /* Tablet/Small Laptop */
            md:max-h-[calc(100vh-10rem)]
            /* PC / Desktop */
            lg:max-h-[calc(100vh-8rem)]
            /* Large PC (Ultra Wide) */
            2xl:max-h-[calc(100vh-6rem)]">

            <div class="flex-1 h-full px-4 py-2 overflow-x-hidden overflow-y-auto bg-white border shadow-sm rounded-xl border-neutral-200 dark:border-neutral-800 dark:bg-neutral-900">
                <div class="w-full">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>
