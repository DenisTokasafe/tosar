<section class="w-full">
    <x-toast />
    @push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
    @endpush
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
    @endpush

    <div class="flex flex-col items-center px-2 rounded-lg shadow-sm lg:flex-row lg:justify-between bg-stone-400/20">

        {{-- BAGIAN KIRI: Tombol Aksi (Create & Import) --}}
        {{-- Ikon Tombol Sejajar Horizontal --}}
        <div class="flex flex-row gap-2">

            {{-- Tombol 'tambah data' --}}

            <x-button.btn-tooltip modalId="manhours_modal" color="primary" icon="add" tooltip="Tambah Data" />
            {{-- Komponen Import --}}


        </div>

        {{-- BAGIAN KANAN: Filter (Search & Date Range) --}}
        {{-- Menggunakan flex-row untuk membuat input search dan date range bersebelahan --}}
        <div class="flex flex-col gap-2 md:flex-row md:items-center">

            {{-- 1. Input Search (w-60) --}}
            <div class="w-full">
                {{-- flux:input sudah ada di sini --}}
                <input type="text" wire:model.live="search"
                    class="w-full input input-bordered md:max-w-sm focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs"
                    placeholder="pencarian..." />
            </div>
            {{-- 2. Input Rentang Tanggal (w-60) --}}
            <div class="w-full">
                <div class="join" wire:ignore x-data="{
                    fp: null,
                    range_date: @entangle('range_date').live, // Tambahkan .live agar hook updated langsung terpanggil

                    initFlatpickr() {
                        if (this.fp) this.fp.destroy();

                        this.fp = flatpickr(this.$refs.tanggalInput2, {
                            disableMobile: true,
                            enableTime: false,
                            altInput: true,
                            altFormat: 'd-M-Y',
                            dateFormat: 'd-m-Y',
                            mode: 'range',
                            defaultDate: this.range_date,
                            onChange: (dates, str) => {
                                // Hanya update saat 2 tanggal sudah terpilih (start & end)
                                // Ini akan otomatis memicu public function updatedRangeDate($value)
                                if (dates.length === 2) {
                                    this.range_date = str;
                                }
                            },
                            locale: { rangeSeparator: ' Ke ' },
                        });
                    },
                    clearDate() {
                        if (this.fp) this.fp.clear();
                        this.range_date = null; // Memicu updatedRangeDate dengan nilai null (else condition)
                    }
                }" x-init="initFlatpickr()">

                    <input type="text" x-ref="tanggalInput2" placeholder="Pilih Rentang Tanggal"
                        class="w-full input input-bordered md:max-w-sm focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs join-item"
                        readonly />

                    <button type="button" @click="clearDate()" class="btn btn-xs btn-neutral join-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-refresh-cw">
                            <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8" />
                            <path d="M21 3v5h-5" />
                            <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16" />
                            <path d="M8 16H3v5" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <x-manhours.layout>
        <div class="overflow-x-auto ">
            <table class="table table-xs table-pin-rows">
                <thead>
                    <tr>
                        <th></th>
                        <th>Tanggal</th>
                        <th>Jenis Entitas</th>
                        <th>Perusahaan</th>
                        <th>Departemen</th>
                        <th>Departemen Group</th>
                        <th>Job Class</th>
                        <th>Manhour</th>
                        <th>Manpower</th>
                        @can('create', \App\Models\Manhour::class)
                        <th>Aksi</th>
                        @endcan
                    </tr>
                </thead>
                <tbody>


                </tbody>
            </table>
        </div>
        <div class="absolute inset-x-0 bottom-0 z-50 mt-4 shadow-md bg-base-100 inset-shadow-sm">

        </div>
        <dialog id='manhours_modal' class="modal" wire:ignore.self wire:target='store'>
            <div class="overflow-y-auto modal-box">

                <div class="modal-action">
                    <form method="dialog">
                        <button class="btn btn-xs btn-soft btn-error">Close</button>
                    </form>
                </div>
            </div>
        </dialog>
        {{-- Modal konfirmasi --}}
        <dialog id='delete_modal' class="modal" wire:ignore.self>
            <div class="modal-box">
                <h3 class="text-lg font-bold">Konfirmasi Hapus</h3>
                <p class="py-4">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak bisa dibatalkan.</p>

                <div class="modal-action">
                    <form method="dialog">
                        <button class="btn btn-xs btn-soft btn-error">Batal</button>
                    </form>

                    <label class="btn btn-error btn-xs btn-soft" wire:click="delete" wire:loading.attr="disabled"
                        wire:target="delete">
                        Hapus
                    </label>
                </div>
            </div>
        </dialog>
    </x-manhours.layout>
</section>
