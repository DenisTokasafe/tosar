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

        <div class="w-full" wire:key="field-description">
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
            <fieldset class=" fieldset">
                <x-form.upload label="Lampirkan Foto Dokumentasi Deskripsi" model="documentation_description"
                    :file="$documentation_description" />
                <div wire:loading.remove wire:target="documentation_description">
                    @if ($documentation_description)
                    @if (in_array($documentation_description->getClientOriginalExtension(), ['jpg', 'jpeg', 'png']))
                    <img src="{{ $documentation_description->temporaryUrl() }}"
                        class="mt-2 {{ $documentation_description ? 'w-40' : '' }} h-auto rounded border" />
                    @elseif (in_array($documentation_description->getClientOriginalExtension(), ['pdf', 'doc', 'docx']))
                    <div class="flex items-center gap-2 mt-2">
                        @if ($documentation_description->getClientOriginalExtension() == 'pdf')
                        <x-icon.pdf class="w-8 h-8" />
                        <span
                            class="text-sm text-red-600">{{ $documentation_description->getClientOriginalName() }}</span>
                        @elseif (in_array($documentation_description->getClientOriginalExtension(), ['doc', 'docx']))
                        <x-icon.word class="w-8 h-8" />
                        <span
                            class="text-sm text-blue-600">{{ $documentation_description->getClientOriginalName() }}</span>
                        @else
                        {{-- Ikon generik untuk file lain --}}
                        <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zM6 20V4h7v4h4v12H6z" />
                        </svg>
                        <span class="text-sm text-gray-600">File:
                            {{ $documentation_description->getClientOriginalName() }}</span>
                        @endif
                        @else
                        <p class="mt-2 text-sm text-gray-600">File:
                            {{ $documentation_description->getClientOriginalName() }}
                        </p>
                        @endif
                        @endif
                    </div>
                    <x-label-error :messages="$errors->get('documentation_description')" />
            </fieldset>
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
