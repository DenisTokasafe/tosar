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
        {{-- STEP 1: Info Dasar --}}
        @if($currentStep == 1)
        <div class="grid grid-cols-1 gap-4 space-y-4 md:grid-cols-2 lg:grid-cols-3">
            <x-form.tgl-waktu label="Tanggal & Waktu Kejadian" model="date_time" required />
            <x-form.searchable-dropdown label="Lokasi" required modelsearch="searchLocation" modelid="location_id"
                :options="$locations" :showdropdown="$show_location" clickaction="selectLocation" namedb="name" />
            {{-- Lokasi spesifik muncul hanya jika lokasi utama sudah dipilih --}}
            @if ($location_id)
            <x-form.input-text label="Lokasi Spesifik" model="location_specific" placeholder="Masukkan detail lokasi spesifik..." required />
            @endif
            <x-form.select label="Tipe Insiden" model="event_type_id" :options="$eventTypes" option-label="event_type_name" required />
            <x-form.select label="Jenis Insiden" model="event_sub_type_id" :options="$eventSubTypes" option-label="event_sub_type_name" required />
            <x-form.select-categroy-bahaya :key-word="$keyWord" :ktas="$ktas" :ttas="$ttas" model_kta="kondisi_tidak_aman" model_tta="tindakan_tidak_aman" />
        </div>
        @endif
        {{-- STEP 2: Detail Kejadian --}}
        @if($currentStep == 2)
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3" wire:key="field-description">
                <fieldset class="mb-4 fieldset lg:col-span-2">
                    <x-form.label label="Deskripsi" required />
                    <div x-data="ckeditorHelper('description')" wire:ignore>
                        <div x-ref="editorElement"></div>
                    </div>
                    <x-label-error :messages="$errors->get('description')" />
                </fieldset>
            </div>
        </div>
        @endif
        {{-- STEP 3: Tindakan --}}
        @if($currentStep == 3)
        <div class="grid grid-cols-1 gap-4 space-y-4 md:grid-cols-2 lg:grid-cols-3">

        </div>
        @endif
        {{-- Navigasi Step --}}
        <div class="flex justify-end gap-2 mt-4">
            @if($currentStep > 1)
            <button type="button" class="btn btn-xs btn-outline" wire:click="$set('currentStep', {{ $currentStep - 1 }})">Sebelumnya</button>
            @endif
            @if($currentStep < $totalSteps)
            <button type="button" class="btn btn-xs btn-primary" wire:click="$set('currentStep', {{ $currentStep + 1 }})">Selanjutnya</button>
            @else
            <button type="button" class="btn btn-xs btn-success" wire:click="submit">Submit</button>
            @endif
        </div>
    </x-incident.layout>
</section>
