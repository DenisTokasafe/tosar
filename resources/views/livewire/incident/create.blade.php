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
            {{-- Ubah wire:model.live menjadi wire:click --}}
            <input type="radio" name="my-accordion-2"
                wire:click="goToStep({{ $i }})"
                value="{{ $i }}"
                {{ $currentStep == $i ? 'checked' : '' }} />

            <div class="font-semibold collapse-title bg-linear-to-r/oklab from-success to-info text-base-content">
                <h3 class="text-base font-semibold">PART {{ $i }}</h3>
            </div>

            <div class="text-xs collapse-content">
                @include('livewire.incident.incident-step-' . $i)

                {{-- Tombol Navigasi --}}
                <div class="flex justify-end mt-4">
                    @if ($i < 9)
                        <button wire:click="nextStep" class="btn btn-primary btn-xs">
                        Lanjut ke Part {{ $i + 1 }}
                        </button>
                        @else

                        {{-- Tombol Submit muncul KHUSUS di dalam Part 9 --}}
                        <button type="button" class="btn btn-xs btn-success" wire:click="save">
                            Submit Laporan
                        </button>
                        @endif
                </div>
            </div>
            </div>
            @endfor
            {{-- Navigasi Step --}}


    </x-incident.layout>

    @push('scripts')
    <script>
        window.addEventListener('scroll-to-top', event => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>
    @endpush

</section>