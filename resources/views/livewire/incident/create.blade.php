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
            class="mb-2 border collapse collapse-arrow bg-base-100 border-base-300 transition-all duration-300 ease-in-out hover:border-info hover:shadow-md rounded-xl {{ $currentStep < $i ? 'opacity-60 pointer-events-none' : '' }}">
            <input type="radio" name="my-accordion-2"
                wire:click="goToStep({{ $i }})"
                value="{{ $i }}"
                {{ $currentStep == $i ? 'checked' : '' }} />

            <div @class([ 'font-semibold collapse-title transition-colors duration-300' , 'bg-linear-to-r from-success/20 to-info/20 text-info-content'=> $currentStep == $i,
                'bg-base-200 text-base-content' => $currentStep != $i
                ])>
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-6 h-6 text-xs text-white rounded-full bg-info">
                        {{ $i }}
                    </span>
                    <h3 class="text-sm font-bold tracking-wide uppercase">PART {{ $i }}</h3>
                </div>
            </div>

            <div class="transition-all duration-500 collapse-content">
                <div class="pt-4 text-xs">
                    @include('livewire.incident.incident-step-' . $i)

                    {{-- Navigasi di dalam --}}
                    <div class="flex justify-end pt-4 mt-6 border-t border-base-200">
                        @if ($i < 9)
                            <button wire:click="nextStep" wire:loading.attr="disabled" class="transition-transform shadow-sm btn btn-primary btn-xs sm:btn-sm hover:scale-105">
                            Lanjut ke Part {{ $i + 1 }}
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                            </button>
                            @else
                            <button type="button" class="transition-all shadow-lg btn btn-sm btn-success hover:scale-105" wire:click="save" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="save">🚀 Kirim Laporan</span>
                                <span wire:loading wire:target="save" class="loading loading-spinner loading-xs"></span>
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