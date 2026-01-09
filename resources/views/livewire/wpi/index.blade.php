<section class="w-full">
    <x-toast />
    <x-tabs-wpi.layout heading="Formulir Laporan WPI KPLH" subheading="TT-MGT-FRS-024A">
        <form wire:submit.prevent="save" class="overflow-hidden bg-white border border-gray-200 rounded-lg shadow-xl">

            <div class="p-6 border-b border-gray-200 bg-gray-50">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="space-y-4">
                        <fieldset class="relative fieldset">
                            <x-form.label label="Tanggal / Date" required />
                            <div
                                class="{{ $errors->has('report_date') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500 rounded' : 'ring-base-300 focus:ring-base-300 focus:border-base-300 rounded' }}">
                                <div class="relative " wire:ignore x-data="{
                                    fp: null,
                                    initFlatpickr() {
                                        if (this.fp) this.fp.destroy();
                                        this.fp = flatpickr(this.$refs.tanggalInput, {
                                            disableMobile: true,
                                            enableTime: false,
                                            time_24hr: false,
                                            defaultDate: this.$wire.entangle('report_date').defer,
                                            dateFormat: 'd-m-Y',
                                            clickOpens: true,
                                            // HAPUS ATAU KOMENTARI BARIS INI (appendTo)
                                            // appendTo: this.$refs.wrapper,

                                            // TAMBAHKAN ATAU UBAH OPSI POSITION
                                            position: 'auto-below', // Opsi ini akan memaksa kalender muncul di bawah input.

                                            onChange: (selectedDates, dateStr) => {
                                                this.$wire.set('report_date', dateStr);
                                            }
                                        });
                                    }
                                }" x-ref="wrapper"
                                    x-init="initFlatpickr();
                                    Livewire.hook('message.processed', () => {
                                        initFlatpickr();
                                    });">
                                    <input type="text" x-ref="tanggalInput" wire:model.live='report_date'
                                        placeholder="Pilih Tanggal dan Waktu..." readonly
                                        class="input input-bordered cursor-pointer w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs {{ $errors->has('report_date') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}" />
                                </div>
                            </div>
                            <x-label-error :messages="$errors->get('report_date')" />
                        </fieldset>

                        <fieldset class="relative fieldset">
                            <x-form.label label="Jam / Time" required />
                            <div
                                class="{{ $errors->has('report_time') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500 rounded' : 'ring-base-300 focus:ring-base-300 focus:border-base-300 rounded' }}">
                                <div class="relative " wire:ignore x-data="{
                                    fp: null,
                                    initFlatpickr() {
                                        if (this.fp) this.fp.destroy();
                                        this.fp = flatpickr(this.$refs.tanggalInput, {
                                            disableMobile: true,
                                            enableTime: true,
                                            noCalendar: true,
                                            time_24hr: false,
                                            defaultDate: this.$wire.entangle('report_time').defer,
                                            dateFormat: 'd-m-Y',
                                            clickOpens: true,
                                            // HAPUS ATAU KOMENTARI BARIS INI (appendTo)
                                            // appendTo: this.$refs.wrapper,

                                            // TAMBAHKAN ATAU UBAH OPSI POSITION
                                            position: 'auto-below', // Opsi ini akan memaksa kalender muncul di bawah input.

                                            onChange: (selectedDates, dateStr) => {
                                                this.$wire.set('report_time', dateStr);
                                            }
                                        });
                                    }
                                }" x-ref="wrapper"
                                    x-init="initFlatpickr();
                                    Livewire.hook('message.processed', () => {
                                        initFlatpickr();
                                    });">
                                    <input type="text" x-ref="tanggalInput" wire:model.live='report_time'
                                        placeholder="Pilih Tanggal dan Waktu..." readonly
                                        class="input input-bordered cursor-pointer w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs {{ $errors->has('report_time') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}" />
                                </div>
                            </div>
                            <x-label-error :messages="$errors->get('report_time')" />
                        </fieldset>

                        <x-form.searchable-dropdown label="Lokasi / Location" required modelsearch="searchLocation"
                            modelid="location" :options="$locations" :showdropdown="$show_location" clickaction="selectLocation"
                            namedb="name" />
                    </div>

                    <div class="space-y-4">
                        <fieldset>
                            <input id="department" value="department" wire:model="deptCont"
                                class="peer/department radio radio-xs radio-accent" type="radio" name="deptCont"
                                checked />
                            <x-form.label for="department" class="peer-checked/department:text-accent text-[10px]"
                                label="PT. MSM & PT. TTN" required />
                            <input id="company" value="company" wire:model="deptCont"
                                class="peer/company radio radio-xs radio-primary" type="radio" name="deptCont" />
                            <x-form.label for="company" class="peer-checked/company:text-primary" label="Kontraktor"
                                required />

                            <div class="hidden peer-checked/department:block">
                                {{-- Department --}}
                                <div class="relative mb-1 ">
                                    <x-form.searchable-dropdown-without-label modelsearch="search"
                                        modelid="department_id" placeholder="Cari Departemen..." :options="$departments"
                                        :showdropdown="$showDropdown" clickaction="selectDepartment" namedb="department_name" />
                                </div>
                            </div>
                            <div class="hidden peer-checked/company:block">
                                {{-- Contractor --}}
                                <div class="relative mb-1 ">
                                    <x-form.searchable-dropdown-without-label modelsearch="searchContractor"
                                        placeholder="Cari Kontraktor..." modelid="contractor_id" :options="$contractors"
                                        :showdropdown="$showContractorDropdown" clickaction="selectContractor" namedb="contractor_name" />
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="fieldset">
                            <x-form.label label="Lokasi Spesifik" required />
                            <input name="location_specific" type="text" wire:model.live="location_specific"
                                placeholder="Masukkan detail lokasi spesifik..." value="Tokatindung" disabled
                                class=" input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs {{ $errors->has('location_specific') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}" />
                            <x-label-error :messages="$errors->get('location_specific')" />
                        </fieldset>

                    </div>
                </div>
            </div>

            <div class="p-6 bg-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold tracking-wider text-gray-700 uppercase">Nama Petugas Inspeksi /
                        Inspector</h3>
                    <button type="button" wire:click="addInspector"
                        class="px-3 py-1 text-xs text-white transition bg-blue-600 rounded hover:bg-blue-700">
                        + Tambah Petugas
                    </button>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    @foreach ($inspectors as $index => $inspector)
                        <div class="flex items-start space-x-2 bg-blue-600" wire:key="ins-{{ $index }}">
                            <span class="mt-2 text-xs font-bold text-gray-400">{{ $index + 1 }}.</span>

                            <x-form.searchable-select-advanced label="Petugas Inspeksi" placeholder="Cari nama..."
                                modelsearch="searchPetugas.{{ $index }}"
                                modelid="inspectors.{{ $index }}.name" :options="$pelaporsAct" :showdropdown="$showDropdownPetugas[$index] ?? false"
                                :manualMode="$manualActPelaporMode" {{-- Cukup kirim nama method, index akan ditangani oleh helper select di backend --}} clickaction="selectActPelapor" />

                            {{-- Sembunyikan index di input tersembunyi agar bisa dibaca saat method dipanggil --}}
                            <input type="hidden" wire:model="currentLoopIndex" value="{{ $index }}">

                            {{-- Tombol Remove --}}
                            @if (count($inspectors) > 1)
                                <button type="button" wire:click="removeInspector({{ $index }})"
                                    class="mt-2 text-red-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="p-6 overflow-x-auto border-t border-gray-200">
                <table class="w-full text-xs text-left border border-collapse border-gray-300">
                    <thead class="italic text-white uppercase bg-gray-800">
                        <tr>
                            <th class="w-8 p-2 text-center border border-gray-300">#</th>
                            <th class="w-16 p-2 text-center border border-gray-300">OHS Risk</th>
                            <th class="p-2 border border-gray-300">Uraian Temuan & Foto</th>
                            <th class="p-2 border border-gray-300">Tindakan Pencegahan</th>
                            <th class="w-48 p-2 border border-gray-300">Follow Up (PIC & Due Date)</th>
                            <th class="w-12 p-2 text-center border border-gray-300">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($findings as $index => $finding)
                            <tr wire:key="find-{{ $index }}" class="align-top hover:bg-gray-50">
                                <td class="p-2 font-bold text-center border border-gray-300">{{ $index + 1 }}</td>
                                <td class="p-2 text-center border border-gray-300">
                                    <select wire:model="findings.{{ $index }}.ohs_risk"
                                        class="w-full p-1 text-xs border-gray-300 rounded">
                                        <option value="T">T</option>
                                        <option value="H">H</option>
                                        <option value="M">M</option>
                                        <option value="L">L</option>
                                    </select>
                                </td>
                                <td class="p-2 border border-gray-300">
                                    <textarea wire:model="findings.{{ $index }}.description" placeholder="Deskripsikan temuan..."
                                        class="w-full mb-2 text-xs border-gray-300 rounded" rows="3"></textarea>

                                    <div class="mt-1">
                                        {{-- Menggunakan komponen x-form.upload berdasarkan gambar 2 --}}
                                        <x-form.upload label="Lampirkan foto temuan"
                                            model="findings.{{ $index }}.new_photos" :file="$findings[$index]['new_photos'] ?? null" />

                                        <div class="mt-2" wire:loading.remove
                                            wire:target="findings.{{ $index }}.new_photos">
                                            @if (isset($findings[$index]['new_photos']) && count($findings[$index]['new_photos']) > 0)
                                                <div class="grid grid-cols-2 gap-2 mt-2">
                                                    @foreach ($findings[$index]['new_photos'] as $fileKey => $newFile)
                                                        {{-- Gunakan wire:key unik gabungan index finding dan index file --}}
                                                        <div class="relative p-1 border rounded bg-gray-50"
                                                            wire:key="preview-{{ $index }}-{{ $fileKey }}">

                                                            @php
                                                                // Pastikan file adalah objek UploadedFile sebelum panggil method
                                                                $isUploadedFile = method_exists(
                                                                    $newFile,
                                                                    'temporaryUrl',
                                                                );
                                                                $extension = $isUploadedFile
                                                                    ? strtolower($newFile->getClientOriginalExtension())
                                                                    : '';
                                                            @endphp

                                                            @if ($isUploadedFile && in_array($extension, ['jpg', 'jpeg', 'png', 'gif']))
                                                                <img src="{{ $newFile->temporaryUrl() }}"
                                                                    class="mt-2 {{ $newFile ? 'w-40' : '' }} h-auto rounded border" />
                                                            @else
                                                                {{-- Fallback jika bukan gambar (PDF/Word) --}}
                                                                <div
                                                                    class="flex flex-col items-center justify-center h-20 bg-gray-200 rounded">
                                                                    @if ($extension == 'pdf')
                                                                        <x-icon.pdf class="w-8 h-8" />
                                                                    @elseif(in_array($extension, ['doc', 'docx']))
                                                                        <x-icon.word class="w-8 h-8" />
                                                                    @endif
                                                                    <span
                                                                        class="text-[8px] mt-1 truncate w-full px-1 text-center">
                                                                        {{ $isUploadedFile ? $newFile->getClientOriginalName() : 'File Error' }}
                                                                    </span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="p-2 border border-gray-300">
                                    <textarea wire:model="findings.{{ $index }}.prevention_action" placeholder="Tindakan korektif..."
                                        class="w-full text-xs border-gray-300 rounded" rows="3"></textarea>
                                </td>
                                <td class="p-2 space-y-2 border border-gray-300">
                                    <input type="text" wire:model="findings.{{ $index }}.pic_responsible"
                                        placeholder="Nama PIC" class="w-full text-xs border-gray-300 rounded">
                                    <div class="flex items-center space-x-1 text-[10px]">
                                        <span class="text-gray-500">Due:</span>
                                        <input type="date" wire:model="findings.{{ $index }}.due_date"
                                            class="flex-1 p-1 text-xs border-gray-300 rounded">
                                    </div>
                                </td>
                                <td class="p-2 text-center border border-gray-300">
                                    <button type="button" wire:click="removeFinding({{ $index }})"
                                        class="text-red-400 hover:text-red-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z">
                                            </path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div
                class="flex flex-col items-center justify-between gap-4 p-6 border-t border-gray-200 bg-gray-50 md:flex-row">
                <button type="button" wire:click="addFinding"
                    class="w-full px-4 py-2 text-sm font-bold text-gray-700 transition bg-gray-200 rounded-md md:w-auto hover:bg-gray-300">
                    + Tambah Baris Temuan
                </button>

                <div class="flex items-center w-full space-x-3 md:w-auto">
                    <a href="/wpi-list"
                        class="flex-1 px-6 py-2 text-sm font-medium text-center text-gray-600 md:flex-none hover:text-gray-800">Batal</a>
                    <button type="submit"
                        class="flex items-center justify-center flex-1 px-8 py-2 text-sm font-bold text-white transition bg-green-600 rounded-md shadow-lg md:flex-none hover:bg-green-700">
                        <span wire:loading.remove wire:target="save italic">Simpan Laporan (Submit)</span>
                        <span wire:loading wire:target="save">Memproses...</span>
                    </button>
                </div>
            </div>
        </form>
    </x-tabs-wpi.layout>
</section>
