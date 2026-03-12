<section class="w-full">
    <x-toast />
    {{-- Breadcrumb di sebelah kanan --}}
    <div class="flex justify-start mb-2 " wire:ignore>
        {{ Breadcrumbs::render('incident-create') }}
    </div>
    <flux:heading level="1" class="mb-1 capitalize">{{ __('Buat Laporan Insiden') }}
    </flux:heading>
    <flux:subheading size="sm" class="mb-1 text-accent">
        {{ __('Laporkan insiden dengan detail untuk penanganan yang tepat.') }}
    </flux:subheading>
    <x-incident.layout>
        {{-- PROGRESS & STEPS VISUAL --}}

        {{-- STEP 1: Info Dasar --}}
        <div class="border collapse collapse-arrow bg-base-100 border-base-300">
            <input type="radio" name="my-accordion-2" checked="checked" />
            <div class="font-semibold collapse-title bg-linear-to-r/oklab from-base-200 to-base-300 text-base-content">
                <span class="flex">
                    <h3>BAGIAN 1 </h3>- Detail Laporan
                </span>
            </div>
            <div class="text-xs collapse-content"> @include('livewire.incident.incident-step-1')</div>
        </div>
        <div class="border collapse collapse-arrow bg-base-100 border-base-300">
            <input type="radio" name="my-accordion-2" />
            <div class="font-semibold collapse-title bg-linear-to-r/oklab from-base-200 to-base-300 text-base-content">
                <span class="flex">
                    <h3>BAGIAN 2 </h3>- Pihak Terlibat Langsung (Saksi, korban cedera, kontraktor, operator, dll.)
                </span>
            </div>
            <div class="text-xs collapse-content">@include('livewire.incident.incident-step-2')</div>
        </div>
        <div class="border collapse collapse-arrow bg-base-100 border-base-300">
            <input type="radio" name="my-accordion-2" />
            <div class="font-semibold collapse-title bg-linear-to-r/oklab from-base-200 to-base-300 text-base-content">
                <span class="flex">
                    <h3>BAGIAN 3 </h3>- Partisipan Investigasi
                </span>
            </div>
            <div class="text-xs collapse-content"> @include('livewire.incident.incident-step-3')</div>
        </div>
        {{-- Navigasi Step --}}
        <div class="flex justify-end gap-2 p-2 md:mt-4 bg-base-100">
            <button type="button" class="btn btn-xs btn-success" wire:click="submit">Submit</button>
        </div>

    </x-incident.layout>

</section>