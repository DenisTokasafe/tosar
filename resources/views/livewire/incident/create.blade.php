<section class="w-full">
    <x-toast />
    {{-- Breadcrumb di sebelah kanan --}}
    <div class="flex justify-start mb-2 " wire:ignore>
        {{ Breadcrumbs::render('incident-create') }}
    </div>
    <flux:heading level="1" class="mb-1 capitalize">Buat Laporan Insiden</flux:heading>
    <flux:subheading size="sm" class="mb-1 text-accent">Laporkan insiden dengan detail untuk penanganan yang tepat.</flux:subheading>
    <flux:separator variant="subtle" class="mb-4" />
    <x-incident.layout>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
            <x-form.select
                label="Tipe Bahaya"
                model="tipe_bahaya"
                :options="$eventTypes"
                option-label="event_type_name"
                required />
        </div>
    </x-incident.layout>
</section>
