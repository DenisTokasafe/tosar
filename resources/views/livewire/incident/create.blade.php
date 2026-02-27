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
            <x-form.select label="Tipe Insiden" model="event_type_id" :options="$eventTypes" option-label="event_type_name" required />
            <x-form.select label="Jenis Insiden" model="event_sub_type_id" :options="$eventSubTypes" option-label="event_sub_type_name" required />
            <x-form.select-categroy-bahaya :key-word="$keyWord" :ktas="$ktas" :ttas="$ttas" model_kta="kondisi_tidak_aman" model_tta="tindakan_tidak_aman" />
        </div>
    </x-incident.layout>
</section>
