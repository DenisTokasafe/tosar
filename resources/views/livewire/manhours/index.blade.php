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
                <flux:tooltip content="tambah data" position="top">
                    <flux:button size="xs" wire:click='open_modal' icon="add-icon" variant="primary"></flux:button>
                </flux:tooltip>

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
                                    <th class='flex flex-row justify-center gap-2'>
                                        <flux:tooltip content="edit" position="top">
                                            <flux:button wire:click="open_modal({{ $manhour->id }})" size="xs"
                                                icon="pencil-square" variant="subtle"></flux:button>
                                        </flux:tooltip>
                                        <flux:tooltip content="hapus" position="top">
                                            <flux:button wire:click="showDelete({{ $manhour->id }})" size="xs"
                                                icon="trash" variant="danger"></flux:button>
                                        </flux:tooltip>
                                    </th>
                            @endif
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $data_manhours->links() }}</div>
                <div class="modal {{ $modalOpen }}">
                    <div class="modal-box max-w-4xl w-11/12 max-h-[90vh] md:max-h-[85vh] lg:max-h-[85vh] overflow-y-auto">
                        <form wire:submit.prevent="{{ $selectedId ? "update($selectedId)" : 'store' }}">
                            <fieldset wire.ignore.self
                                class="p-4 overflow-y-auto border fieldset bg-base-200 border-base-300 rounded-box">
                                <legend class="fieldset-legend">Formulir {{ $form }} Manhours & Manpower</legend>
                                <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                                    {{-- Bulan --}}
                                    <fieldset class="fieldset">
                                        <x-form.label label="Bulan" required />
                                        <div x-data="{
                                            fp: null,
                                            initFlatpickr() {
                                                this.fp = flatpickr(this.$refs.input, {
                                                    plugins: [
                                                        new monthSelectPlugin({
                                                            disableMobile: true,
                                                            shorthand: true, // Jan, Feb, ...
                                                            dateFormat: 'M-Y', // format yang dikirim ke Livewire
                                                            altFormat: 'F Y', // format yang ditampilkan ke user (September 2025)
                                                            theme: 'light'
                                                        })
                                                    ],
                                                    onChange: (selectedDates, dateStr) => {
                                                        $wire.set('date', dateStr)
                                                    }
                                                })
                                            }
                                        }" x-init="initFlatpickr()"
                                            x-effect="if($wire.date) fp.setDate($wire.date, true)" wire:ignore>
                                            <input x-ref="input" type="text" wire:model.live="date"
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

                                {{-- Job Class --}}
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
                                                    centang jika tidak ada {{ $label }}
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
                                                    @disabled($hide[$key]) />

                                                <x-label-error :messages="$errors->get('manhours.' . $key)" />
                                            </fieldset>

                                            {{-- Manpower (Jumlah Tenaga Kerja) --}}
                                            <fieldset class="fieldset">
                                                <x-form.label label="Jumlah Tenaga Kerja" :required="!$hide[$key]" />

                                                <input type="number" wire:model.live="manpower.{{ $key }}"
                                                    placeholder="Masukkan Jumlah Tenaga Kerja..."
                                                    class="w-full input input-bordered input-xs focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden"
                                                    @disabled($hide[$key]) />

                                                <x-label-error :messages="$errors->get('manpower.' . $key)" />
                                            </fieldset>
                                        </div>
                                    </fieldset>
                                @endforeach

                            </fieldset>

                            {{-- Tombol Aksi --}}
                            <div class="flex justify-end gap-2 mt-2">
                                <flux:button size="xs" variant="danger" wire:click="close_modal">Batal</flux:button>
                                @if ($selectedId)
                                    <flux:button size="xs" variant="primary" type="submit">Update</flux:button>
                                @else
                                    <flux:button size="xs" variant="primary" type="submit">Simpan</flux:button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
                {{-- Modal konfirmasi --}}
                <div class="modal {{ $confirmingDelete ? 'modal-open' : '' }}">
                    <div class="modal-box">
                        <h3 class="text-lg font-bold">Konfirmasi Hapus</h3>
                        <p class="py-4">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak bisa dibatalkan.</p>

                        <div class="modal-action">
                            <button type="button" @click="showModal = false" class="btn btn-warning"
                                wire:click="$set('confirmingDelete', false)">
                                Batal
                            </button>

                            <button type="button" class="btn btn-error" wire:click="delete" wire:loading.attr="disabled"
                                wire:target="delete">
                                Hapus
                            </button>
                        </div>
                    </div>

                    <label class="modal-backdrop" @click="showModal = false"
                        wire:click="$set('confirmingDelete', false)"></label>
                </div>

                @livewire('manhours.grafik.index')
            </x-manhours.layout>
        </section>
