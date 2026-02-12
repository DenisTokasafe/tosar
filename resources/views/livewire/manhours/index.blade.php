<section class="w-full">
    <x-toast />
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
    @endpush
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
    @endpush
    @include('partials.manhours')
    <div class="flex flex-col items-center px-2 rounded-lg shadow-sm lg:flex-row lg:justify-between bg-stone-400/20">

        {{-- BAGIAN KIRI: Tombol Aksi (Create & Import) --}}
        {{-- Ikon Tombol Sejajar Horizontal --}}
        <div class="flex flex-row gap-2">
            @can('create', \App\Models\Manhour::class)
                {{-- Tombol 'tambah data' --}}

                <x-button.btn-tooltip modalId="manhours_modal" color="primary" icon="add" wireClick='close_modal' tooltip="Tambah Data" />
                {{-- Komponen Import --}}
            @endcan
            @can('viewAdmin', \App\Models\Manhour::class)
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
                        // Pastikan elemen ada sebelum inisialisasi
                        if (!this.$refs.tanggalInput2) return;

                        if (this.fp) this.fp.destroy();

                        this.fp = flatpickr(this.$refs.tanggalInput2, {
                            disableMobile: true,
                            enableTime: false,
                            altInput: true,
                            altFormat: 'd-M-Y',
                            dateFormat: 'd-m-Y',
                            mode: 'range',
                            onChange: (dates, str) => {
                                $wire.set('range_date', str);
                            },
                            locale: { rangeSeparator: ' Ke ' },
                        });
                    },
                    clearDate() {
                        if (this.fp) this.fp.clear();
                        $wire.set('range_date', null);
                    }
                }" x-init="$nextTick(() => { initFlatpickr(); });
                /* Gunakan hook Livewire yang lebih modern jika menggunakan v3 */
                Livewire.hook('morph.updated', (el) => {
                    if (el.component) initFlatpickr();
                });">

                    <input name="range_date" type="text" x-ref="tanggalInput2" wire:model.live="range_date"
                        placeholder="Pilih Rentang Tanggal"
                        class="w-full input input-bordered md:max-w-sm focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs join-item"
                        readonly />

                    <button type="button" @click="clearDate(); $wire.call('clearFilter')"
                        class="btn btn-xs btn-neutral join-item" title="Bersihkan Filter">
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
                                <th class='flex flex-row justify-center gap-2'>

                                    <div class="tooltip">
                                        <div class="z-50 tooltip-content tooltip-warning">
                                            <div class="text-sm font-black animate-bounce">Edit</div>
                                        </div>
                                        <label onclick="manhours_modal.showModal()"
                                            wire:click="open_modal({{ $manhour->id }})"
                                            class="btn btn-square btn-xs btn-soft btn-warning">
                                            <x-icon.edit />
                                        </label>
                                    </div>
                                    <div class="tooltip ">
                                        <div class="z-50 tooltip-content tooltip-error">
                                            <div class="text-sm font-black animate-bounce">hapus data </div>
                                        </div>
                                        <label wire:click="showDelete({{ $manhour->id }})"
                                            onclick="delete_modal.showModal()"
                                            class="btn btn-square btn-xs btn-soft btn-error">
                                            <x-icon.delete />
                                        </label>
                                    </div>

                                </th>
                            @endcan
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
        <div class="absolute inset-x-0 bottom-0 z-50 mt-4 shadow-md bg-base-100 inset-shadow-sm">
            {{ $data_manhours->links() }}
        </div>
        <dialog id='manhours_modal' class="modal" wire:ignore.self wire:loading.add.class='skeleton'
            wire:target='update,store,close_modal,'>
            <div class="overflow-y-auto modal-box">
                <form wire:submit.prevent="{{ $selectedId ? "update($selectedId)" : 'store' }}">
                    <fieldset wire.ignore.self
                        class="p-4 overflow-y-auto border fieldset bg-base-200 border-base-300 rounded-box">
                        <legend class="fieldset-legend">Formulir {{ $form }} Manhours & Manpower</legend>
                        <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                            {{-- Bulan --}}
                            <fieldset class="fieldset">
                                <x-form.label label="Bulan" required />

                                <div wire:ignore wire:key="manhours-month-picker-{{ time() }}"
                                    x-data="{
                                        fp: null,
                                        dateValue: @entangle('date').live,
                                        initFlatpickr() {
                                            // Gunakan nextTick untuk memastikan DOM input sudah render sempurna
                                            this.$nextTick(() => {
                                                if (this.fp) {
                                                    this.fp.destroy();
                                                }

                                                // Pastikan x-ref input tersedia
                                                if (!this.$refs.input) return;

                                                this.fp = flatpickr(this.$refs.input, {
                                                    plugins: [
                                                        new monthSelectPlugin({
                                                            disableMobile: false,
                                                            shorthand: true,
                                                            dateFormat: 'M-Y',
                                                            altFormat: 'F Y',
                                                            theme: 'light'
                                                        })
                                                    ],
                                                    defaultDate: this.dateValue,
                                                    onChange: (selectedDates, dateStr) => {
                                                        this.dateValue = dateStr;
                                                    }
                                                });
                                            });
                                        }
                                    }" x-init="initFlatpickr()"
                                    x-effect="if(fp && dateValue) fp.setDate(dateValue, false)">

                                    <input x-ref="input" type="text" readonly
                                        class="w-full input input-bordered md:max-w-md focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs"
                                        placeholder="Pilih bulan" />
                                </div>
                                <x-label-error :messages="$errors->get('date')" />
                            </fieldset>

                            {{-- Kategori Perusahaan --}}
                            <fieldset class="fieldset">
                                <x-form.label label="Pilih Entitas" required />
                                <select wire:model.live="entityType"
                                    class="w-full select select-xs md:select-xs select-bordered md:max-w-md focus:ring-1 focus:border-info focus:ring-info focus:outline-none">
                                    <option value="">-- Pilih --</option>
                                    <option value="owner">Perusahaan (Owner)</option>
                                    <option value="contractor">Kontraktor</option>

                                </select>

                                <x-label-error :messages="$errors->get('entityType')" />
                            </fieldset>

                            {{-- Perusahaan --}}
                            <fieldset class="fieldset">
                                <x-form.label label="Perusahaan" required />
                                <select wire:model.live="company"
                                    class="w-full select select-xs md:select-xs select-bordered md:max-w-md focus:ring-1 focus:border-info focus:ring-info focus:outline-none">
                                    <option value="">-- Pilih --</option>

                                    {{-- kalau ada owners --}}
                                    @isset($companies['owners'])
                                        @foreach ($companies['owners'] as $comp)
                                            <option value="{{ $comp->company_name }}" @selected($company === $comp->company_name)>
                                                {{ $comp->company_name }}
                                            </option>
                                        @endforeach
                                    @endisset

                                    @isset($companies['contractors'])
                                        @foreach ($companies['contractors'] as $cont)
                                            <option value="{{ $cont->contractor_name }}" @selected($company === $cont->contractor_name)>
                                                {{ $cont->contractor_name }}
                                            </option>
                                        @endforeach
                                    @endisset
                                </select>
                                <x-label-error :messages="$errors->get('company')" />
                            </fieldset>


                            {{-- Departemen --}}
                            <fieldset class="fieldset">
                                {{-- MODIFIKASI DIMULAI DI SINI --}}
                                @if ($entityType === 'contractor')
                                    <x-form.label label="Custodian" required />
                                @elseif ($entityType === 'owner')
                                    <x-form.label label="Department" required />
                                @else
                                    {{-- Default jika belum memilih atau nilainya kosong --}}
                                    <x-form.label label="Department / Custodian" required />
                                @endif
                                {{-- MODIFIKASI BERAKHIR DI SINI --}}
                                <select wire:model.live="department"
                                    class="w-full select select-xs md:select-xs select-bordered md:max-w-md focus:ring-1 focus:border-info focus:ring-info focus:outline-none">
                                    <option value="">-- Pilih --</option>
                                    @if ($entityType === 'contractor')
                                        @foreach ($custodian as $cust)
                                            <option value="{{ $cust->Departemen->department_name }}"
                                                @selected($department === $cust->Departemen->department_name)>
                                                {{ $cust->Departemen->department_name }}
                                            </option>
                                        @endforeach
                                    @else
                                        @foreach ($deptGroup as $dg)
                                            <option value="{{ $dg->Departemen->department_name }}"
                                                @selected($department === $dg->Departemen->department_name)>
                                                {{ $dg->Departemen->department_name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <x-label-error :messages="$errors->get('department')" />
                            </fieldset>
                        </div>

                        {{-- Job Class Section --}}
                        @foreach ($jobclasses as $key => $label)
                            <fieldset class="px-3 border rounded-lg fieldset border-base-300">
                                <legend class="flex items-center gap-2 text-xs font-semibold">
                                    <span>{{ $label }}</span>

                                    {{-- Checkbox untuk 'Tidak Ada [Job Class]' --}}
                                    <label class="flex items-center space-x-1 cursor-pointer">
                                        <input type="checkbox" wire:model.live="hide.{{ $key }}"
                                            class="checkbox checkbox-xs">
                                        <span class="text-[8px] text-rose-500 capitalize select-none">
                                            centang jika ada data {{ $label }}
                                        </span>
                                    </label>
                                </legend>

                                {{-- Container Manhours dan Manpower --}}
                                <div class="grid grid-cols-1 gap-2 md:grid-cols-2">

                                    {{-- Manhours (Jumlah Jam Kerja) --}}
                                    <fieldset class="fieldset">
                                        <x-form.label label="Jumlah Jam Kerja" :required="!$hide[$key]" />

                                        <input type="number" wire:model.live="manhours.{{ $key }}"
                                            placeholder="Masukkan Jumlah Jam Kerja..."
                                            class="w-full input input-bordered input-xs focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden"
                                            @disabled(!$hide[$key]) /> {{-- 🛑 PERBAIKAN: DISABLED jika $hide[$key] FALSE --}}

                                        <x-label-error :messages="$errors->get('manhours.' . $key)" />
                                    </fieldset>

                                    {{-- Manpower (Jumlah Tenaga Kerja) --}}
                                    <fieldset class="fieldset">
                                        <x-form.label label="Jumlah Tenaga Kerja" :required="!$hide[$key]" />

                                        <input type="number" wire:model.live="manpower.{{ $key }}"
                                            placeholder="Masukkan Jumlah Tenaga Kerja..."
                                            class="w-full input input-bordered input-xs focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden"
                                            @disabled(!$hide[$key]) /> {{-- 🛑 PERBAIKAN: DISABLED jika $hide[$key] FALSE --}}

                                        <x-label-error :messages="$errors->get('manpower.' . $key)" />
                                    </fieldset>
                                </div>
                            </fieldset>
                        @endforeach

                        {{-- Tombol Aksi --}}
                        <div class="flex justify-end gap-2 mt-2">

                            @if ($selectedId)
                                <button class="btn btn-xs btn-soft btn-error" wire:click='close_modal'
                                    onclick="manhours_modal.close()">Batal</button>
                                <button class="btn btn-xs btn-soft btn-success" type="submit">Update</button>
                            @else
                                <button class="btn btn-xs btn-soft btn-error"
                                    onclick="manhours_modal.close()">Batal</button>
                                <button class="btn btn-xs btn-soft btn-success" type="submit">Simpan</button>
                            @endif
                        </div>
                </form>
            </div>
        </dialog>
        {{-- Modal konfirmasi --}}
        <dialog id='delete_modal' class="modal" wire:ignore.self>
            <div class="modal-box">
                <h3 class="text-lg font-bold">Konfirmasi Hapus</h3>
                <p class="py-4">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak bisa dibatalkan.</p>

                <div class="modal-action">
                    <label onclick="delete_modal.close()" class="btn btn-xs btn-soft btn-warning">
                        Batal
                    </label>

                    <label class="btn btn-error btn-xs btn-soft" wire:click="delete" wire:loading.attr="disabled"
                        wire:target="delete">
                        Hapus
                    </label>
                </div>
            </div>
        </dialog>

        @livewire('manhours.grafik.index')

    </x-manhours.layout>
</section>
<script>
    window.addEventListener('close-delete-modal', event => {
        delete_modal.close();
    });
</script>
