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
            <div
            wire:key="step-container-{{ $i }}"
            {{--
            Logika Class:
            Jika $currentStep == $i, kita tambahkan class hover:-translate-x-4 dan shadow-2xl.
            Jika tidak, kita biarkan class standar.
        --}}
            @class([ 'mb-3 border collapse collapse-arrow bg-base-100 border-base-300 rounded-xl transition-all duration-300 ease-in-out relative' ,
            // Class di bawah ini hanya muncul jika step ini sedang OPEN/ACTIVE 'z-10 shadow-lg border-info hover:-translate-x-4 hover:shadow-2xl'=> $currentStep == $i,
            // Class jika sedang tertutup (Opsional: opacity rendah agar fokus ke yang buka)
            'z-0 opacity-60 pointer-events-none' => $currentStep < $i, 'z-0 opacity-90'=> $currentStep > $i,
                ])
                >
                <input type="radio" name="my-accordion-2"
                    wire:click="goToStep({{ $i }})"
                    value="{{ $i }}"
                    {{ $currentStep == $i ? 'checked' : '' }} />

                {{-- Judul --}}
                <div @class([ 'font-semibold collapse-title flex items-center justify-between transition-colors duration-300' , 'bg-linear-to-r from-success to-info text-white'=> $currentStep == $i,
                    'bg-base-200 text-base-content' => $currentStep != $i,
                    ])>
                    <h3 class="text-sm font-bold tracking-wide uppercase">PART {{ $i }}</h3>
                </div>

                <div class="collapse-content bg-base-100">
                    <div class="pt-4 text-xs">
                        @include('livewire.incident.incident-step-' . $i)

                        {{-- Tombol Navigasi --}}
                        <div class="flex justify-end pt-4 mt-4 border-t border-base-200">
                            @if ($i < 9)
                                <button wire:click="nextStep" class="btn btn-primary btn-xs">
                                Lanjut ke Part {{ $i + 1 }}
                                </button>
                                @else
                                <button type="button" class="btn btn-xs btn-success" wire:click="save">
                                    Submit Laporan
                                </button>
                                @endif
                        </div>
                    </div>
                </div>
                </div>
                @endfor

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