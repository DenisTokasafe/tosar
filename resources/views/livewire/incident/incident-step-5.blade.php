<div class="mt-4">
    {{-- Header Control --}}
    <div class="flex items-center justify-between pb-2 border-b">
        <h2 class="text-lg font-bold">BAGIAN 5 – Time Line dan Analisis Informasi</h2>
        <div class="flex gap-2">
            <button type="button" wire:click="addWhyColumn" class="btn btn-sm btn-outline btn-primary">
                + Tambah Kolom "Mengapa"
            </button>
        </div>
    </div>

    @foreach($timelines as $index => $line)
    <div class="mt-4 border shadow-sm card bg-base-100 border-base-300" wire:key="timeline-card-{{ $index }}">
        <div class="p-4 card-body">
            <div class="grid grid-cols-1 gap-4">
                {{-- Input Kronologi --}}
                <x-form.text_area
                    disabled
                    label="Kronologi Kejadian & Tanggal"
                    model="description"
                    placeholder="Contoh: 10:00 WITA - Unit LV menabrak tanggul karena jalan licin"
                    rows="3" />

                {{-- Grid untuk Why --}}
                <div @class([ 'grid gap-4 mt-2' ,
                    $this->gridClass => $whyCount >= 1,
                    'divide-x-2 divide-dashed divide-base-300' => $whyCount > 1
                    ])>
                    @for($i = 1; $i <= $whyCount; $i++)
                        <div @class(['flex items-start gap-4 px-2', 'pl-4'=> $i > 1]) wire:key="why-col-{{ $index }}-{{ $i }}">
                        <div class="flex-1">
                            <x-form.text_area
                                wire:key="why-input-{{ $index }}-{{ $i }}"
                                label="Text area why {{ $i }}"
                                model="timelines.{{ $index }}.why{{ $i }}"
                                placeholder="Jelaskan alasan ke-{{ $i }}..."
                                rows="2" />
                        </div>

                        @if($whyCount > 1)
                        <div class="pt-9"> {{-- Padding top agar sejajar dengan input (melewati label) --}}
                            <flux:tooltip content="hapus" position="top" wire:key="remove-why-{{ $index }}-{{ $i }}">
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
<!--
<div class="flex justify-center mt-4">
    <button type="button" wire:click="addRow('timelines')" class="btn btn-block btn-success btn-outline">
        + Tambah Baris Kronologi Baru
    </button>
</div> -->
</div>