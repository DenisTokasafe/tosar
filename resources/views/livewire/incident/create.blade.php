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
            <div class="font-semibold collapse-title bg-linear-to-r/oklab from-success to-info text-base-content">

                <h3 class="text-base font-semibold">PART 1 <span class="badge badge-sm badge-soft badge-info">Detail Laporan</span></h3>


            </div>
            <div class="text-xs collapse-content"> @include('livewire.incident.incident-step-1')</div>
        </div>
        <div class="border collapse collapse-arrow bg-base-100 border-base-300">
            <input type="radio" name="my-accordion-2" />
            <div class="font-semibold collapse-title bg-linear-to-r/oklab from-success to-info text-base-content">

                <h3 class="text-base font-semibold">PART 2 <span class="badge badge-sm badge-soft badge-info">Pihak Terlibat Langsung (Saksi, korban cedera, kontraktor, operator, dll.)</span></h3>

            </div>
            <div class="text-xs collapse-content">@include('livewire.incident.incident-step-2')</div>
        </div>
        <div class="border collapse collapse-arrow bg-base-100 border-base-300">
            <input type="radio" name="my-accordion-2" />
            <div class="font-semibold collapse-title bg-linear-to-r/oklab from-success to-info text-base-content">

                <h3 class="text-base font-semibold">PART 3 <span class="badge badge-sm badge-soft badge-info">Partisipan Investigasi</span></h3>

            </div>
            <div class="text-xs collapse-content"> @include('livewire.incident.incident-step-3')</div>
        </div>
        <div class="border collapse collapse-arrow bg-base-100 border-base-300">
            <input type="radio" name="my-accordion-2" />
            <div class="font-semibold collapse-title bg-linear-to-r/oklab from-success to-info text-base-content">

                <h3 class="text-base font-semibold">PART 4 <span class="badge badge-sm badge-soft badge-info">PEEPO Investigation questions for identification of the incident factors</span></h3>

            </div>
            <div class="text-xs collapse-content"> @include('livewire.incident.incident-step-4')</div>
        </div>
        <div class="border collapse collapse-arrow bg-base-100 border-base-300">
            <input type="radio" name="my-accordion-2" />
            <div class="font-semibold collapse-title bg-linear-to-r/oklab from-success to-info text-base-content">

                <h3 class="text-base font-semibold">PART 5 <span class="badge badge-sm badge-soft badge-info">Time Line dan Analisis Informasi</span></h3>

            </div>
            <div class="text-xs collapse-content"> @include('livewire.incident.incident-step-5')</div>
        </div>
        <div class="border collapse collapse-arrow bg-base-100 border-base-300">
            <input type="radio" name="my-accordion-2" />
            <div class="font-semibold collapse-title bg-linear-to-r/oklab from-success to-info text-base-content">

                <h3 class="text-base font-semibold">PART 6 <span class="badge badge-sm badge-soft badge-info">Investigasi Kecelakaan (Daftar Checklist Mengacu pada TT-MGT-LMS-025A)</span></h3>

            </div>
            <div class="text-xs collapse-content"> @include('livewire.incident.incident-step-6')</div>
        </div>
        <div class="border collapse collapse-arrow bg-base-100 border-base-300">
            <input type="radio" name="my-accordion-2" />
            <div class="font-semibold collapse-title bg-linear-to-r/oklab from-success to-info text-base-content">

                <h3 class="text-base font-semibold">PART 7 <span class="badge badge-sm badge-soft badge-info">Dokumentasi & Tindakan Perbaikan</span></h3>

            </div>
            <div class="text-xs collapse-content"> @include('livewire.incident.incident-step-7')</div>
        </div>
        <div class="border collapse collapse-arrow bg-base-100 border-base-300">
            <input type="radio" name="my-accordion-2" />
            <div class="font-semibold collapse-title bg-linear-to-r/oklab from-success to-info text-base-content">

                <h3 class="text-base font-semibold">PART 8 <span class="badge badge-sm badge-soft badge-info">Kunci Pembelajaran</span></h3>

            </div>
            <div class="text-xs collapse-content"> @include('livewire.incident.incident-step-8')</div>
        </div>
        <div class="border collapse collapse-arrow bg-base-100 border-base-300">
            <input type="radio" name="my-accordion-2" />
            <div class="font-semibold collapse-title bg-linear-to-r/oklab from-success to-info text-base-content">

                <h3 class="text-base font-semibold">PART 9 <span class="badge badge-sm badge-soft badge-info">Penerimaan & Komentar Peninjauan Investigasi</span></h3>

            </div>
            <div class="text-xs collapse-content"> @include('livewire.incident.incident-step-9')</div>
        </div>
        {{-- Navigasi Step --}}
        <div class="flex justify-end gap-2 p-2 md:mt-4 bg-base-100">
            <button type="button" class="btn btn-xs btn-success" wire:click="submit">Submit</button>
        </div>

    </x-incident.layout>

</section>