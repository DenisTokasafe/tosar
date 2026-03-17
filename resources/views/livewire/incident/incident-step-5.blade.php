<div class="mt-4">
    {{-- Header Control --}}
    <div class="flex items-center justify-between pb-2 border-b">
        <h2 class="text-lg font-bold uppercase text-primary">BAGIAN 5 – Time Line dan Analisis Informasi</h2>
        <div class="flex gap-2">

            <flux:tooltip content=" Tambah Kolom " Mengapa" position="top">
                <flux:button wire:click="addWhyColumn"
                    size="xs" icon="add-icon" variant="outline" color="primary">
                </flux:button>
            </flux:tooltip>
        </div>
    </div>

    @foreach($timelines as $index => $line)
    <div class="mt-4 border shadow-sm card bg-base-100 border-base-300" wire:key="timeline-card-{{ $index }}">
        <div class="p-4 card-body">
            <div class="grid grid-cols-1 gap-4">

                {{-- Desain Pengumuman/Notice untuk Kronologi (Read Only) --}}
                <div class="relative p-4 border-l-4 rounded-r-lg bg-info/5 border-info group">
                    <div class="flex items-center gap-2 mb-2 text-info">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                        </svg>
                        <span class="text-[10px] font-bold uppercase tracking-widest">Referensi Kronologi & Tanggal (Step 1)</span>
                    </div>

                    <div class="pl-1">
                        <p class="text-sm leading-relaxed whitespace-pre-line text-base-content/80">
                            {{ $description ?: 'Deskripsi kejadian belum diisi pada Step 1.' }}
                        </p>
                    </div>

                    <div class="absolute top-2 right-2">
                        <div class="italic opacity-50 badge badge-ghost badge-xs">Hanya Baca</div>
                    </div>
                </div>

                {{-- Grid untuk Why (Input Analisis) --}}
                <div @class([ 'grid gap-4 mt-2' ,
                    $this->gridClass => $whyCount >= 1,
                    'divide-x-2 divide-dashed divide-base-300' => $whyCount > 1
                    ])>
                    @for($i = 1; $i <= $whyCount; $i++)
                        <div @class(['flex items-start gap-4 px-2', 'pl-4'=> $i > 1]) wire:key="why-col-{{ $index }}-{{ $i }}">
                        <div class="flex-1">
                            <x-form.text_area
                                wire:key="why-input-{{ $index }}-{{ $i }}"
                                label="Analisis Mengapa (Why {{ $i }})"
                                wire:model.blur="timelines.{{ $index }}.why{{ $i }}"
                                placeholder="Jelaskan alasan ke-{{ $i }}..."
                                rows="2" />
                        </div>

                        @if($whyCount > 1 && $i == $whyCount) {{-- Tombol hapus hanya di kolom terakhir --}}
                        <div class="pt-9">
                            <flux:tooltip content="Hapus Kolom Why" position="top" wire:key="remove-why-{{ $index }}-{{ $i }}">
                                <flux:button wire:click="removeWhyColumn" size="xs" icon="trash" variant="danger" />
                            </flux:tooltip>
                        </div>
                        @endif
                </div>
                @endfor
            </div> {{-- Penutup Grid Why --}}

        </div> {{-- Penutup Grid Utama --}}
    </div> {{-- Penutup Card Body --}}
</div> {{-- Penutup Card --}}
@endforeach

</div>