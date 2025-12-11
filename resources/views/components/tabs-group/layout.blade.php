<div class="flex md:flex-col min-h-screen">
    <div class=" w-full md:w-60 ">
        <flux:navlist-horizontal>
            </flux:navlist-horizontal>
    </div>

    <div class="p-2 flex-1 flex flex-col">
        <flux:heading>{{ $heading ?? '' }}</flux:heading>
        <flux:subheading size='xs'>{{ $subheading ?? '' }}</flux:subheading>

        <div class="flex w-full flex-1 flex-col gap-4 rounded-xl inset-shadow-sm">

            <div class="h-full flex-1 overflow-y-auto overflow-x-hidden rounded-xl border border-neutral-200 dark:border-base-200 p-4">

                <div class="w-full max-w-full ">
                    {{-- 💡 Konten Tabel Anda ($slot) Berada di sini --}}
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>
