<section class="w-full">
    <x-toast />
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
    @include('partials.manhours')
    <div class="flex flex-col items-center px-2 rounded-lg shadow-sm lg:flex-row lg:justify-between bg-stone-400/20">

        {{-- BAGIAN KIRI: Tombol Aksi (Create & Import) --}}
        {{-- Ikon Tombol Sejajar Horizontal --}}
        <div class="flex flex-row gap-2">
            @can('create', \App\Models\Manhour::class)
                {{-- Tombol 'tambah data' --}}
                {{-- <flux:tooltip content="tambah data" position="top">
                    <flux:button size="xs" wire:click='open_modal' icon="add-icon" variant="primary"></flux:button>
                </flux:tooltip> --}}

                {{-- Komponen Import --}}
                @livewire('manhours.manhours-import')
            @endcan
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
                    initFlatpickr() {
                        if (this.fp) this.fp.destroy();
                        this.fp = flatpickr(this.$refs.tanggalInput2, {
                            disableMobile: true,
                            enableTime: false,
                            altInput: true,
                            altFormat: 'd-M-Y',
                            dateFormat: 'd-m-Y',
                            mode: 'range',
                            onChange: (dates, str) => $wire.set('range_date', str),
                            locale: { rangeSeparator: ' Ke ' },
                        });
                    },
                    clearDate() {
                        if (this.fp) this.fp.clear(); // 🔥 kosongkan input di flatpickr
                        $wire.set('range_date', null); // 🔥 kosongkan properti Livewire
                    }
                }" x-init="initFlatpickr();
                Livewire.hook('message.processed', () => initFlatpickr());" x-ref="wrapper">

                    <input name="range_date" type="text" x-ref="tanggalInput2" wire:model.live="range_date"
                        placeholder="Pilih Rentang Tanggal"
                        class="w-full input input-bordered md:max-w-sm focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs join-item"
                        readonly />

                    <label @click="clearDate(); $wire.call('clearFilter')" class="btn btn-xs btn-neutral join-item"
                        title="Bersihkan Filter">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-refresh-cw-icon lucide-refresh-cw">
                            <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8" />
                            <path d="M21 3v5h-5" />
                            <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16" />
                            <path d="M8 16H3v5" />
                        </svg>
                    </label>
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
                        @can('create', \App\Models\Manhour::class)<th>Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data_manhours as $no => $manhour)
                            <tr>
                                <th>{{ $data_manhours->firstItem() + $no }}</th>
                                <td>{{ \Carbon\Carbon::parse($manhour->date)->translatedFormat('M-Y') }}</td>
                                <td>{{ $manhour->company_category }}</td>
                                <td>{{ $manhour->company }}</td>
                                <td>{{ $manhour->department }}</td>
                                <td>{{ $manhour->dept_group }}</td>
                                <td>{{ $manhour->job_class }}</td>
                                <td>{{ $manhour->manhours }}</td>
                                <td>{{ $manhour->manpower }}</td>
                                @can('create', \App\Models\Manhour::class)
                                    {{-- <th class='flex flex-row justify-center gap-2'>
                                        <flux:tooltip content="edit" position="top">
                                            <flux:button wire:click="open_modal({{ $manhour->id }})" size="xs"
                                                icon="pencil-square" variant="subtle"></flux:button>
                                        </flux:tooltip>
                                        <flux:modal.trigger name="delete-bu">
                                            <flux:tooltip content="hapus" position="top">
                                                <flux:button wire:click="showDelete({{ $manhour->id }})" size="xs"
                                                    icon="trash" variant="danger"></flux:button>
                                            </flux:tooltip>
                                        </flux:modal.trigger>
                                    </th> --}}
                            @endif
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $data_manhours->links() }}</div>
                <div class="modal {{ $modalOpen }}">

                </div>
                {{-- Modal konfirmasi --}}
                {{-- <flux:modal name="delete-bu" wire:model="confirmingDelete">
                    <div class="p-4 space-y-4">
                        <h2 class="text-lg font-semibold">Konfirmasi Hapus</h2>
                        <p>Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak bisa dibatalkan.</p>

                        <div class="flex justify-end gap-2">
                            <flux:button wire:click="$set('confirmingDelete', false)" variant="subtle">Batal</flux:button>
                            <flux:button wire:click="delete" variant="danger">Hapus</flux:button>
                        </div>
                    </div>
                </flux:modal> --}}

                @livewire('manhours.grafik.index')
            </x-manhours.layout>
        </section>
