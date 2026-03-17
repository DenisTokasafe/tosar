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
        @for ($i = 1; $i <= 9; $i++)
            <div class="border collapse collapse-arrow bg-base-100 border-base-300 {{ $currentStep < $i ? 'opacity-60 pointer-events-none' : '' }}">
            {{-- Kita binding wire:model ke currentStep --}}
            <input type="radio" name="my-accordion-2" wire:model.live="currentStep" value="{{ $i }}" />

            <div class="font-semibold collapse-title bg-linear-to-r/oklab from-success to-info text-base-content">
                <h3 class="text-base font-semibold">PART {{ $i }}</h3>
            </div>

            <div class="text-xs collapse-content">
                @include('livewire.incident.incident-step-' . $i)

                {{-- Tombol Navigasi --}}
                @if ($i < 9)
                    <div class="flex justify-end mt-4">
                    <button wire:click="nextStep" class="btn btn-primary btn-xs">
                        Lanjut ke Part {{ $i + 1 }}
                    </button>
            </div>
            @endif
            </div>
            </div>
            @endfor
            {{-- Navigasi Step --}}
            <div class="flex justify-end gap-2 p-2 md:mt-4 bg-base-100">
                <button type="button" class="btn btn-xs btn-success" wire:click="save">Submit</button>
            </div>

    </x-incident.layout>

</section>