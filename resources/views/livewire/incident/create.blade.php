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
            {{--
            Penjelasan Class Efek (Gambar 2):
            1. transition-all duration-300 ease-in-out: Agar gerakan halus saat mouse masuk/keluar.
            2. hover:-translate-x-4: (Geser Kiri) Saat di-hover, elemen bergeser 1rem (16px) ke kiri.
               Ganti ke 'hover:scale-105' jika ingin efek membesar, bukan bergeser.
            3. hover:shadow-2xl: Memberikan bayangan sangat tegas saat menonjol keluar.
            4. hover:z-10 relative: Memastikan kartu yang menonjol berada di atas kartu lain (z-index).
        --}}
            class=" border collapse collapse-arrow bg-base-100 border-base-300 rounded-xl
               relative z-0
               transition-all duration-300 ease-in-out
               hover:-translate-x-4 hover:shadow-2xl hover:z-10 hover:border-info
               {{ $currentStep < $i ? 'opacity-60 pointer-events-none' : '' }}">
            {{-- Input Radio DaisyUI --}}
            <input type="radio" name="my-accordion-2"
                wire:click="goToStep({{ $i }})"
                value="{{ $i }}"
                {{ $currentStep == $i ? 'checked' : '' }} />

            {{-- Judul (Title) dengan Background Gradient --}}
            <div class="flex items-center justify-between font-semibold collapse-title bg-linear-to-r/oklab from-success to-info text-base-content">
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