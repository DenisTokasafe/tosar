<div class="mt-4">
    {{-- Header Control --}}
    <div class="flex items-center justify-between pb-2 border-b">
        <h2 class="text-lg font-bold uppercase text-primary">BAGIAN 5 – Analisis Informasi (5 Why)</h2>
        <div class="flex gap-2">
            <flux:tooltip content="Tambah Kolom Mengapa" position="top">
                <flux:button wire:click="addWhyColumn" size="xs" icon="plus" variant="outline" color="primary">
                    Tambah Why
                </flux:button>
            </flux:tooltip>
        </div>
    </div>

    {{-- Kartu Analisis Tunggal --}}
    <div class="mt-4 border shadow-sm card bg-base-100 border-base-300">
        <div class="p-4 card-body">

            {{-- Referensi Kronologi (Tetap dipertahankan agar investigator ingat konteks) --}}
            <div class="relative p-4 mb-4 border-l-4 rounded-r-lg bg-info/5 border-info">
                <div class="flex items-center gap-2 mb-2 text-info">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                    </svg>
                    <span class="text-[10px] font-bold uppercase tracking-widest">Deskripsi Kejadian</span>
                </div>
                <p class="text-sm italic text-base-content/70">{{ $description ?: 'Deskripsi belum diisi.' }}</p>
            </div>

            {{-- Grid Why yang Dinamis secara Horizontal --}}
            <div class="pb-2 overflow-x-auto">
                <div @class([ 'grid gap-4 min-w-[300px]' ,
                    $this->gridClass => $whyCount >= 1,
                    'divide-x-2 divide-dashed divide-base-300' => $whyCount > 1
                    ])>
                    @for($i = 1; $i <= $whyCount; $i++)
                        <div class="flex items-start gap-2 px-2" wire:key="why-col-{{ $i }}">
                        <div class="flex-1">
                            <x-form.text_area
                                label="Analisis Mengapa (Why {{ $i }})"
                                model="why_analysis.why{{ $i }}"
                                placeholder="Jelaskan alasan ke-{{ $i }}..."
                                rows="3" />
                        </div>

                        {{-- Tombol Hapus hanya muncul di kolom terakhir --}}
                        @if($whyCount > 1 && $i == $whyCount)
                        <div class="pt-9">
                            <flux:button wire:click="removeWhyColumn" size="xs" icon="trash" variant="danger" ghost />
                        </div>
                        @endif
                </div>
                @endfor
            </div>
        </div>

    </div>
</div>
</div>