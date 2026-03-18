@for ($i = 1; $i <= 9; $i++)
    <div
    wire:key="step-container-{{ $i }}"
    @class([
    // Class Dasar 'mb-3 border collapse collapse-arrow bg-base-100 border-base-300 rounded-xl transition-all duration-500 ease-in-out relative' ,

    // EFEK KHUSUS: Hanya jika Step ini sedang OPEN ($currentStep==$i)
    // Kita tambahkan 'hover:-translate-x-6' dan 'hover:shadow-2xl' 'z-10 border-info shadow-md hover:-translate-x-6 hover:shadow-2xl'=> $currentStep == $i,

    // Class jika Step masih terkunci atau sudah dilewati
    'z-0 opacity-60 pointer-events-none' => $currentStep < $i, 'z-0 opacity-90 grayscale-[0.5]'=> $currentStep > $i,
        ])
        >
        <input type="radio" name="my-accordion-2"
            wire:click="goToStep({{ $i }})"
            value="{{ $i }}"
            {{ $currentStep == $i ? 'checked' : '' }} />

        {{-- Judul Part --}}
        <div @class([ 'font-semibold collapse-title flex items-center justify-between transition-all duration-300' , 'bg-linear-to-r from-success to-info text-white'=> $currentStep == $i,
            'bg-base-200 text-base-content' => $currentStep != $i,
            ])>
            <div class="flex items-center gap-2">
                <span class="badge badge-sm {{ $currentStep == $i ? 'badge-white text-info' : 'badge-ghost' }} font-bold">{{ $i }}</span>
                <h3 class="text-sm font-bold tracking-widest uppercase">PART {{ $i }}</h3>
            </div>
        </div>

        <div class="collapse-content bg-base-100">
            <div class="pt-4 text-xs">
                @include('livewire.incident.incident-step-' . $i)

                {{-- Tombol Navigasi --}}
                <div class="flex justify-end pt-4 mt-4 border-t border-base-200">
                    @if ($i < 9)
                        <button wire:click="nextStep" class="transition-transform btn btn-primary btn-xs hover:scale-105">
                        Lanjut ke Part {{ $i + 1 }}
                        </button>
                        @else
                        {{-- Tombol Submit muncul hanya di Part 9 --}}
                        <button type="button" class="transition-all shadow-md btn btn-xs btn-success hover:scale-105" wire:click="save">
                            🚀 Kirim Laporan
                        </button>
                        @endif
                </div>
            </div>
        </div>
        </div>
        @endfor