<section class="w-full">
    <x-toast />
    {{-- Breadcrumb di sebelah kanan --}}
    <div class="flex justify-start mb-2 " wire:ignore>
        {{ Breadcrumbs::render('incident-create') }}
    </div>
    <flux:heading level="1" class="mb-1 capitalize">{{ __('Buat Laporan Insiden') }}</flux:heading>
    <flux:subheading size="sm" class="mb-1 text-accent">{{ __('Laporkan insiden dengan detail untuk penanganan yang tepat.') }}</flux:subheading>
    <x-incident.layout>
        {{-- PROGRESS & STEPS VISUAL --}}
        <ul class="absolute inset-x-0 top-0 z-10 border-t border-l-0 border-r-0 rounded-t-sm shadow-md border-base-300 steps lg:steps-horizontal bg-base-100">
            <li class="step {{ $currentStep >= 1 ? 'step-primary' : '' }} text-[10px] uppercase font-bold">{{ __('Informasi Dasar') }}</li>
            <li class="step {{ $currentStep >= 2 ? 'step-primary' : '' }} text-[10px] uppercase font-bold">{{ __('Detail & Risiko') }}</li>
            <li class="step {{ $currentStep >= 3 ? 'step-primary' : '' }} text-[10px] uppercase font-bold">{{ __('Dokumentasi') }}& {{ __('Tindakan Perbaikan') }}</li>
        </ul>
        {{-- STEP 1: Info Dasar --}}
        @if($currentStep == 1)
        @include('livewire.incident.incident-step-1')
        @endif
        {{-- STEP 2: Detail Kejadian --}}
        @if($currentStep == 2)
        @include('livewire.incident.incident-step-2')
        @endif
        {{-- STEP 3: Tindakan --}}
        @if($currentStep == 3)
        @include('livewire.incident.incident-step-3')
        @endif
        {{-- Navigasi Step --}}
        <div class="flex justify-end gap-2 p-2 md:mt-4 bg-base-100">
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