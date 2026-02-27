<section class="w-full">
    <x-toast />
    {{-- Breadcrumb di sebelah kanan --}}
    <div class="flex justify-start mb-2 " wire:ignore>
        {{ Breadcrumbs::render('incident-create') }}
    </div>
    <x-incident.layout></x-incident.layout>
        <x-slot name="heading">Buat Laporan Insiden</x-slot>
        <x-slot name="subheading">Laporkan insiden dengan detail untuk penanganan yang tepat.</x-slot>

    </x-incident.layout>
</section>
