<div class="flex items-start max-md:flex-col z-30 ">
    <div class="flex-1 self-stretch max-md:pt-3">
        <div class="flex w-full flex-1 flex-col gap-4 rounded-xl h-full
            max-h-[calc(100vh-20rem)] 
            sm:max-h-[calc(100vh-11rem)] 
            md:max-h-[calc(100vh-16rem)] 
            lg:max-h-[calc(100vh-18rem)] 
            2xl:max-h-[calc(100vh-18rem)]">
            <div class="h-full flex-1 overflow-y-auto overflow-x-hidden inset-shadow-sm rounded-xl border border-neutral-200 dark:border-base-200 px-4 py-2">
                <div class="w-full max-w-full ">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>
