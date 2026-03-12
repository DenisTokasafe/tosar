<div class="mt-4 ">
    <div class="flex items-center justify-between pb-2 border-b">

        <div class="flex gap-2">
            {{-- Tombol Hapus Kolom (Hanya tampil jika > 1) --}}
            @if($whyCount > 1)
            <button type="button"
                wire:click="removeWhyColumn"
                class="btn btn-sm btn-outline btn-error">
                - Hapus Kolom "Mengapa"
            </button>
            @endif

            <button type="button"
                wire:click="addWhyColumn"
                class="btn btn-sm btn-outline btn-primary">
                + Tambah Kolom "Mengapa"
            </button>
        </div>
    </div>

    @foreach($timelines as $index => $line)
    <div class="border shadow-sm card bg-base-100 border-base-300" wire:key="timeline-card-{{ $index }}">
        <div class="p-4 card-body">
            <!-- <div class="flex items-start justify-between mb-4">
                <span class="font-mono badge badge-ghost">Kronologi #{{ $index + 1 }}</span>
                <button type="button" wire:click="removeRow('timelines', {{ $index }})" class="btn btn-xs btn-error btn-outline">
                    Hapus Baris
                </button>
            </div> -->

            <div class="grid grid-cols-1 gap-4">
                <x-form.text_area disabled label="Kronologi Kejadian & Tanggal"
                    wire:model="timelines.{{ $index }}.kejadian"
                    placeholder="Contoh: 10:00 WITA - Unit LV menabrak tanggul karena jalan licin"
                    rows="3" />
                {{-- Implementasi pada elemen grid --}}
                <div class="grid {{ $this->gridClass }} gap-4 mt-2">
                    @for($i = 1; $i
                    <= $whyCount; $i++)
                        <x-form.text_area
                        label="Text area why {{ $i }}"
                        wire:model="timelines.{{ $index }}.why{{ $i }}"
                        placeholder="Jelaskan alasan ke-{{ $i }}..."
                        rows="2" />
                    @endfor
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- <div class="flex justify-center mt-4">
        <button type="button" wire:click="addRow('timelines')" class="btn btn-block btn-success btn-outline">
            + Tambah Baris Kronologi Baru
        </button>
    </div> -->
</div>