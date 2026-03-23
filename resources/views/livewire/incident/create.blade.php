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
        {{-- STEP 1 - 9: Iterasi Collapse --}}
        @for ($i = 1; $i <= 9; $i++)
            @php
            // Cek apakah ada error di dalam Part ini
            $hasErrorInStep=$errors->any() && $this->isFieldInStep($i, $errors->toArray());
            @endphp

            <div
                wire:key="step-container-{{ $i }}"
                class="border collapse collapse-arrow bg-base-100 border-base-300 rounded-xl relative z-0 transition-all duration-300 ease-in-out
               hover:-translate-x-4 hover:shadow-2xl hover:z-10
               {{ $hasErrorInStep ? 'border-error shadow-md' : 'hover:border-info' }}
               {{ $currentStep < $i ? 'opacity-60 pointer-events-none' : '' }}">

                <input type="radio" name="my-accordion-2" wire:click="goToStep({{ $i }})" value="{{ $i }}" {{ $currentStep == $i ? 'checked' : '' }} />

                {{-- HEADER COLLAPSE --}}
                <div class="flex items-center justify-between font-semibold collapse-title
            {{ $hasErrorInStep
                ? 'bg-error text-error-content animate-pulse'
                : ($currentStep == $i ? 'bg-linear-to-r from-success to-info text-white' : 'bg-base-200 text-base-content')
            }}">

                    <h3 class="flex items-center gap-2 text-sm font-bold tracking-wide uppercase">
                        <span>PART {{ $i }}</span>
                        @if($hasErrorInStep)
                        <span class="text-white border-none badge badge-sm badge-ghost bg-white/20">⚠️ ERROR</span>
                        @endif
                    </h3>
                </div>

                {{-- KONTEN --}}
                <div class="text-xs collapse-content bg-base-100">
                    <div class="pt-4">
                        {{-- Tampilkan Pesan Error Global Per Step jika ada --}}
                        @if($hasErrorInStep)
                        <div class="p-2 mb-4 text-xs border rounded-lg bg-error/10 text-error border-error/20">
                            <strong>Perhatian:</strong> Beberapa kolom wajib di Part ini belum terisi dengan benar.
                        </div>
                        @endif

                        @include('livewire.incident.step_create.incident-step-' . $i)

                        {{-- NAVIGASI TOMBOL --}}
                        <div class="flex justify-end pt-4 mt-4 border-t border-base-200">
                            @if ($i < 9)
                                <button wire:click="nextStep" class="transition-transform btn btn-primary btn-xs hover:scale-110">
                                Lanjut ke Part {{ $i + 1 }}
                                </button>
                                @else
                                <button type="button" class="transition-transform shadow-lg btn btn-xs btn-success hover:scale-110" wire:click="save">
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