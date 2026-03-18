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
            {{-- Container Utama Collapse --}}
            <div
            wire:key="step-container-{{ $i }}"
            @class([
            // Class Dasar 'mb-3 border collapse collapse-arrow bg-base-100 border-base-300 rounded-xl relative transition-all duration-500 ease-in-out group' ,

            // EFEK SHIFT LEFT SAAT FOCUS (Aktif Hanya Jika Step ini Terbuka)
            // Menggunakan focus-within agar kartu tetap bergeser selama user berinteraksi di dalamnya 'z-10 border-info shadow-md focus-within:-translate-x-6 focus-within:shadow-2xl'=> $currentStep == $i,

            // Status untuk Part yang Terkunci atau Belum Sampai
            'z-0 opacity-60 pointer-events-none' => $currentStep < $i, 'z-0 opacity-90'=> $currentStep > $i,
                ])
                >
                {{-- Input Radio DaisyUI --}}
                <input type="radio" name="my-accordion-2"
                    class="focus:outline-none" {{-- Menghilangkan ring biru default browser --}}
                    wire:click="goToStep({{ $i }})"
                    value="{{ $i }}"
                    {{ $currentStep == $i ? 'checked' : '' }} />

                {{-- Judul (Title) dengan Background Gradient --}}
                <div @class([ 'flex items-center justify-between font-semibold collapse-title transition-all duration-300' , 'bg-linear-to-r/oklab from-success to-info text-white'=> $currentStep == $i,
                    'bg-base-200 text-base-content' => $currentStep != $i,
                    ])>
                    <h3 class="text-sm font-bold tracking-wide uppercase">PART {{ $i }}</h3>
                </div>

                {{-- Konten --}}
                <div class="text-xs collapse-content bg-base-100">
                    <div class="pt-4">
                        @include('livewire.incident.incident-step-' . $i)

                        {{-- Navigasi Tombol --}}
                        <div class="flex justify-end pt-4 mt-4 border-t border-base-200">
                            @if ($i < 9)
                                <button wire:click="nextStep" class="transition-transform btn btn-primary btn-xs hover:scale-110">
                                Lanjut ke Part {{ $i + 1 }}
                                </button>
                                @else
                                {{-- Tombol Submit di Part 9 --}}
                                <button type="button" class="transition-transform btn btn-xs btn-success hover:scale-110" wire:click="save">
                                    Submit Laporan SENTRY
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