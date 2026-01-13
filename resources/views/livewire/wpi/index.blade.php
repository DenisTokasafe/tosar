<section class="w-full">
    <x-toast />
    <x-tabs-wpi.layout heading="{{ $reportId ? 'Edit Laporan WPI' : 'Buat Laporan WPI Baru' }}"
        subheading="TT-MGT-FRS-024A">
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

                                            dateFormat: 'Y-m-d',
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
                                            time_24hr: true,
                                            defaultDate: this.$wire.entangle('report_time').defer,
                                            dateFormat: 'H:i:s',
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
                                    <x-form.searchable-dropdown-without-label modelsearch="search" modelid="dept_cont"
                                        placeholder="Cari Departemen..." :options="$departments" :showdropdown="$showDropdown"
                                        clickaction="selectDepartment" namedb="department_name" />
                                </div>
                            </div>
                            <div class="hidden peer-checked/company:block">
                                {{-- Contractor --}}
                                <div class="relative mb-1 ">
                                    <x-form.searchable-dropdown-without-label modelsearch="searchContractor"
                                        placeholder="Cari Kontraktor..." modelid="dept_cont" :options="$contractors"
                                        :showdropdown="$showContractorDropdown" clickaction="selectContractor" namedb="contractor_name" />
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="fieldset">
                            <x-form.label label="Nama Site " required />
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
                        <div class="flex items-center space-x-2 " wire:key="ins-{{ $index }}">
                            <input type="hidden" wire:model="currentLoopIndex" value="{{ $index }}">
                            <span
                                class="flex-none mt-2 text-xs font-bold text-gray-400 w-14">{{ $index + 1 }}.</span>
                            <div class="flex-1">
                                {{-- Menggunakan Grid untuk membagi menjadi 3 kolom pada layar sedang/besar --}}
                                <div class="grid grid-cols-1 gap-2 md:grid-cols-3 md:items-end">

                                    {{-- Kolom 1: Dropdown Pencarian --}}
                                    <div class="flex flex-col">
                                        <x-form.searchable-select-advanced label="Petugas Inspeksi {{ $index + 1 }}"
                                            placeholder="Cari nama..." modelsearch="searchPetugas.{{ $index }}"
                                            modelid="inspectors.{{ $index }}.name" :options="$pelaporsAct"
                                            :showdropdown="$showDropdownPetugas[$index] ?? false" :manualMode="$manualActPelaporMode" clickaction="selectActPelapor" />
                                    </div>

                                    {{-- Kolom 2: ID Number --}}
                                    <div class="flex flex-col pb-1">
                                        <span class="text-[10px] font-semibold uppercase text-gray-500">ID
                                            Number</span>
                                        <div
                                            class="px-2 py-1 text-xs italic border rounded  text-gray-600 min-h-[28px] flex items-center">
                                            {{ $inspectors[$index]['id_number'] ?: '-' }}
                                        </div>
                                    </div>

                                    {{-- Kolom 3: Department/Contractor --}}
                                    <div class="flex flex-col pb-1">
                                        <span
                                            class="text-[10px] font-semibold uppercase text-gray-500">Dept/Cont</span>
                                        <div
                                            class="px-2 py-1 text-xs italic border rounded  text-gray-600 min-h-[28px] flex items-center">
                                            {{ $inspectors[$index]['dept_con'] ?? ($inspectors[$index]['dept_con'] ?? '-') }}
                                        </div>
                                    </div>

                                </div>
                            </div>
                            {{-- Sembunyikan index di input tersembunyi agar bisa dibaca saat method dipanggil --}}
                            {{-- Tombol Remove --}}
                            <div class="flex-none w-14">
                                @if (count($inspectors) > 1)
                                    <button type="button" wire:click="removeInspector({{ $index }})"
                                        class="mt-2 text-red-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
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
                            <th class="p-2 border border-gray-300">Uraian Temuan & Foto / Descibe Unsafe Act & Photo
                            </th>
                            <th class="p-2 border border-gray-300">Tindakan Pencegahan & Foto / Prevention Action &
                                Photo</th>
                            <th class="w-48 p-2 border border-gray-300">Tindak Lanjut/ Follow Up</th>
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
                                        <option value="R">R</option>
                                        <option value="M">M</option>
                                        <option value="T">T</option>
                                        <option value="E">E</option>
                                    </select>
                                </td>
                                <td class="p-2 border border-gray-300">
                                    {{-- Input Textarea Deskripsi --}}
                                    <x-form.textarea label="Deskripsi Temuan" required
                                        model="findings.{{ $index }}.description" />

                                    <div class="mt-1">
                                        {{-- Komponen Upload --}}
                                        <x-form.upload label="Lampirkan foto temuan"
                                            model="findings.{{ $index }}.new_photos" :file="$findings[$index]['new_photos'] ?? null" />

                                        {{-- AREA PREVIEW FILE BARU (TEMPORARY) --}}
                                        <div class="mt-2" wire:loading.remove
                                            wire:target="findings.{{ $index }}.new_photos">
                                            @if (isset($findings[$index]['new_photos']) && count($findings[$index]['new_photos']) > 0)
                                                <div class="grid grid-cols-2 gap-2 mt-2">
                                                    @foreach ($findings[$index]['new_photos'] as $fileKey => $newFile)
                                                        <div class="relative p-1 border rounded bg-gray-50"
                                                            wire:key="preview-{{ $index }}-{{ $fileKey }}">

                                                            @php
                                                                $isUploadedFile = method_exists(
                                                                    $newFile,
                                                                    'temporaryUrl',
                                                                );
                                                                $extension = $isUploadedFile
                                                                    ? strtolower($newFile->getClientOriginalExtension())
                                                                    : '';
                                                            @endphp

                                                            {{-- Tombol Hapus Temporary --}}
                                                            <x-button.remove
                                                                click="removeTempPhoto({{ $index }}, {{ $fileKey }})"
                                                                key="btn-remove-temp-{{ $index }}-{{ $fileKey }}" />

                                                            @if ($isUploadedFile && in_array($extension, ['jpg', 'jpeg', 'png', 'gif']))
                                                                <img src="{{ $newFile->temporaryUrl() }}"
                                                                    class="object-cover w-full h-20 mt-2 border rounded" />
                                                            @else
                                                                <div
                                                                    class="flex flex-col items-center justify-center h-20 mt-2 bg-gray-200 rounded">
                                                                    @if ($extension == 'pdf')
                                                                        <x-icon.pdf class="w-8 h-8 text-red-500" />
                                                                    @elseif(in_array($extension, ['doc', 'docx']))
                                                                        <x-icon.word class="w-8 h-8 text-blue-500" />
                                                                    @elseif(in_array($extension, ['xls', 'xlsx', 'csv']))
                                                                        <x-icon.excel class="w-8 h-8 text-green-600" />
                                                                    @else
                                                                        <x-icon.file class="w-8 h-8 text-gray-400" />
                                                                    @endif
                                                                    <span
                                                                        class="text-[8px] mt-1 truncate w-full px-2 text-center text-gray-600">
                                                                        {{ $isUploadedFile ? $newFile->getClientOriginalName() : 'File Error' }}
                                                                    </span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>

                                        {{-- AREA FILE TERSIMPAN (PERMANENT DENGAN FITUR DOWNLOAD) --}}
                                        @if (!empty($finding['photos']))
                                            <div class="flex flex-wrap gap-2 pt-2 mt-2 border-t">
                                                <p class="text-[9px] text-gray-400 w-full mb-1 uppercase italic">File
                                                    Tersimpan:</p>
                                                @foreach ($finding['photos'] as $photoKey => $photoPath)
                                                    @php
                                                        $extension = strtolower(
                                                            pathinfo($photoPath, PATHINFO_EXTENSION),
                                                        );
                                                        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif']);
                                                    @endphp

                                                    <div class="relative group"
                                                        wire:key="saved-{{ $index }}-{{ $photoKey }}">

                                                        {{-- Jika Gambar: Klik untuk pratinjau di tab baru --}}
                                                        @if ($isImage)
                                                            <a href="{{ Storage::url($photoPath) }}" target="_blank">
                                                                <img src="{{ Storage::url($photoPath) }}"
                                                                    class="object-cover w-12 h-12 transition-opacity border rounded shadow-sm opacity-80 hover:opacity-100">
                                                            </a>

                                                            {{-- Jika Dokumen: Klik untuk memicu public function downloadFile --}}
                                                        @else
                                                            <button type="button"
                                                                wire:click="downloadFile('{{ $photoPath }}')"
                                                                class="flex flex-col items-center justify-center w-12 h-12 transition-colors border rounded bg-gray-50 hover:bg-gray-100"
                                                                title="Klik untuk unduh">

                                                                @if ($extension == 'pdf')
                                                                    <x-icon.pdf class="w-6 h-6 text-red-500" />
                                                                @elseif(in_array($extension, ['xls', 'xlsx', 'csv']))
                                                                    <x-icon.excel class="w-6 h-6 text-green-600" />
                                                                @else
                                                                    <x-icon.word class="w-6 h-6 text-blue-500" />
                                                                @endif
                                                                <span
                                                                    class="text-[6px] mt-0.5 uppercase">{{ $extension }}</span>
                                                            </button>
                                                        @endif

                                                        {{-- Tombol Hapus Permanent tetap di sini --}}
                                                        <x-button.remove
                                                            click="removeSavedPhoto({{ $index }}, {{ $photoKey }})"
                                                            key="btn-remove-saved-{{ $index }}-{{ $photoKey }}"
                                                            confirm="Hapus file ini secara permanen?"
                                                            class="transition-opacity scale-75 opacity-0 -top-1 -right-1 group-hover:opacity-100" />
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="p-2 border border-gray-300">
                                    {{-- Input Textarea --}}
                                    <x-form.textarea label="Tindakan pencegahan" required placeholder="Tindakan pencegahan..."
                                        model="findings.{{ $index }}.prevention_action" rows="3" />

                                    <div class="mt-1">
                                        {{-- Komponen Upload --}}
                                        <x-form.upload label="Lampirkan foto pencegahan"
                                            model="findings.{{ $index }}.new_photos_prevention"
                                            :file="$findings[$index]['new_photos_prevention'] ?? null" />

                                        {{-- Logika Preview Foto Baru (Temporary) --}}
                                        <div class="mt-2" wire:loading.remove
                                            wire:target="findings.{{ $index }}.new_photos_prevention">
                                            @if (isset($findings[$index]['new_photos_prevention']) && count($findings[$index]['new_photos_prevention']) > 0)
                                                <div class="grid grid-cols-2 gap-2 mt-2">
                                                    @foreach ($findings[$index]['new_photos_prevention'] as $fileKey => $newFile)
                                                        <div class="relative p-1 border rounded bg-gray-50"
                                                            wire:key="preview-prevention-{{ $index }}-{{ $fileKey }}">

                                                            {{-- Tombol Hapus Temp Photo --}}
                                                            <x-button.remove
                                                                click="removeTempPhotoPrevention({{ $index }}, {{ $fileKey }})"
                                                                key="btn-rm-temp-prev-{{ $index }}-{{ $fileKey }}" />

                                                            @php
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
                                                                    class="w-40 h-auto mt-2 border rounded" />
                                                            @else
                                                                <div
                                                                    class="flex flex-col items-center justify-center h-20 mt-2 bg-gray-200 rounded">
                                                                    @if ($extension == 'pdf')
                                                                        <x-icon.pdf class="w-8 h-8 text-red-500" />
                                                                    @elseif(in_array($extension, ['doc', 'docx']))
                                                                        <x-icon.word class="w-8 h-8 text-blue-500" />
                                                                    @elseif(in_array($extension, ['csv', 'xlsx', 'xls']))
                                                                        <x-icon.excel class="w-8 h-8 text-green-600" />
                                                                    @endif
                                                                    <span
                                                                        class="text-[8px] mt-1 truncate w-full px-1 text-center text-gray-600">
                                                                        {{ $isUploadedFile ? $newFile->getClientOriginalName() : 'File Error' }}
                                                                    </span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>

                                        {{-- AREA FILE TERSIMPAN (PERMANENT) DENGAN FITUR DOWNLOAD --}}
                                        @if (!empty($finding['photos_prevention']))
                                            <div class="flex flex-wrap gap-2 pt-2 mt-2 border-t">
                                                <p class="text-[9px] text-gray-400 w-full mb-1 uppercase italic">File
                                                    Pencegahan Tersimpan:</p>
                                                @foreach ($finding['photos_prevention'] as $photoKey => $photoPath)
                                                    @php
                                                        $extension = strtolower(
                                                            pathinfo($photoPath, PATHINFO_EXTENSION),
                                                        );
                                                        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif']);
                                                    @endphp
                                                    <div class="relative group"
                                                        wire:key="saved-{{ $index }}-{{ $photoKey }}">

                                                        {{-- Jika Gambar: Klik untuk pratinjau di tab baru --}}
                                                        @if ($isImage)
                                                            <a href="{{ Storage::url($photoPath) }}" target="_blank">
                                                                <img src="{{ Storage::url($photoPath) }}"
                                                                    class="object-cover w-12 h-12 transition-opacity border rounded shadow-sm opacity-80 hover:opacity-100">
                                                            </a>

                                                            {{-- Jika Dokumen: Klik untuk memicu public function downloadFile --}}
                                                        @else
                                                            <button type="button"
                                                                wire:click="downloadFile('{{ $photoPath }}')"
                                                                class="flex flex-col items-center justify-center w-12 h-12 transition-colors border rounded bg-gray-50 hover:bg-gray-100"
                                                                title="Klik untuk unduh">

                                                                @if ($extension == 'pdf')
                                                                    <x-icon.pdf class="w-6 h-6 text-red-500" />
                                                                @elseif(in_array($extension, ['xls', 'xlsx', 'csv']))
                                                                    <x-icon.excel class="w-6 h-6 text-green-600" />
                                                                @else
                                                                    <x-icon.word class="w-6 h-6 text-blue-500" />
                                                                @endif
                                                                <span
                                                                    class="text-[6px] mt-0.5 uppercase">{{ $extension }}</span>
                                                            </button>
                                                        @endif

                                                        {{-- Tombol Hapus Permanent --}}
                                                        <x-button.remove
                                                            click="removeSavedPhotoPrevention({{ $index }}, {{ $photoKey }})"
                                                            key="btn-rm-saved-prev-{{ $index }}-{{ $photoKey }}"
                                                            confirm="Hapus file pencegahan ini secara permanen?"
                                                            class="transition-opacity scale-75 opacity-0 -top-1 -right-1 group-hover:opacity-100" />
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="p-2 space-y-2 border border-gray-300">
                                    <x-form.searchable-select-advanced label="Person in charge (PIC)"
                                        placeholder="Cari dan klik nama..."
                                        modelsearch="search_pic.{{ $index }}"
                                        modelid="findings.{{ $index }}.pic_responsible" :options="$pelapors_pic"
                                        :showdropdown="$showDropdown_pic[$index] ?? false" :manualMode="$manualPICPelaporMode" clickaction="selectPicPelapor" />

                                    <div class="flex flex-wrap gap-1 mt-2">
                                        @if (isset($findings[$index]['pic_responsible']) && is_array($findings[$index]['pic_responsible']))
                                            @foreach ($findings[$index]['pic_responsible'] as $picKey => $picName)
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 text-[10px] font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded">
                                                    {{ $picName }}
                                                    <button type="button"
                                                        wire:click="removePic({{ $index }}, {{ $picKey }})"
                                                        class="ml-1 text-blue-400 hover:text-red-500">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </span>
                                            @endforeach
                                        @endif
                                    </div>
                                    <fieldset class="relative fieldset">
                                        <x-form.label label="Tanggal Jatuh Tempo:" required />
                                        <div
                                            class="{{ $errors->has('findings.' . $index . '.due_date') ? 'ring-1 ring-rose-500 rounded' : 'ring-base-300 rounded' }}">
                                            <div class="relative" wire:ignore x-data="{
                                                fp: null,
                                                initFlatpickr() {
                                                    if (this.fp) this.fp.destroy();
                                                    this.fp = flatpickr(this.$refs.tanggalInput, {
                                                        disableMobile: true,
                                                        enableTime: false,
                                                        // Gunakan entangle dengan format string PHP yang benar
                                                        defaultDate: @entangle('findings.' . $index . '.due_date'),

                                                        dateFormat: 'Y-m-d',
                                                        clickOpens: true,
                                                        position: 'auto-below',
                                                        onChange: (selectedDates, dateStr) => {
                                                            $wire.set('findings.{{ $index }}.due_date', dateStr);
                                                        }
                                                    });
                                                }
                                            }"
                                                x-init="initFlatpickr()">

                                                <input type="text" x-ref="tanggalInput"
                                                    wire:model.live="findings.{{ $index }}.due_date"
                                                    placeholder="Pilih Tanggal..." readonly
                                                    class="input input-bordered cursor-pointer w-full focus:ring-1 focus:border-info input-xs {{ $errors->has('findings.' . $index . '.due_date') ? 'border-rose-500' : '' }}" />
                                            </div>
                                        </div>
                                        <x-label-error :messages="$errors->get('findings.' . $index . '.due_date')" />
                                    </fieldset>
                                    <fieldset class="relative fieldset">
                                        <x-form.label label="Tanggal Selesai:" />
                                        <div
                                            class="{{ $errors->has('findings.' . $index . '.completion_date') ? 'ring-1 ring-rose-500 rounded' : 'ring-base-300 rounded' }}">
                                            <div class="relative" wire:ignore x-data="{
                                                fp: null,
                                                initFlatpickr() {
                                                    if (this.fp) this.fp.destroy();
                                                    this.fp = flatpickr(this.$refs.tanggalInput, {
                                                        disableMobile: true,
                                                        enableTime: false,
                                                        // Gunakan entangle dengan format string PHP yang benar
                                                        defaultDate: @entangle('findings.' . $index . '.completion_date'),

                                                        dateFormat: 'Y-m-d',
                                                        clickOpens: true,
                                                        position: 'auto-below',
                                                        onChange: (selectedDates, dateStr) => {
                                                            $wire.set('findings.{{ $index }}.completion_date', dateStr);
                                                        }
                                                    });
                                                }
                                            }"
                                                x-init="initFlatpickr()">

                                                <input type="text" x-ref="tanggalInput"
                                                    wire:model.live="findings.{{ $index }}.completion_date"
                                                    placeholder="Pilih Tanggal..." readonly
                                                    class="input input-bordered cursor-pointer w-full focus:ring-1 focus:border-info input-xs {{ $errors->has('findings.' . $index . '.completion_date') ? 'border-rose-500' : '' }}" />
                                            </div>
                                        </div>
                                        <x-label-error :messages="$errors->get('findings.' . $index . '.completion_date')" />
                                    </fieldset>
                                </td>
                                <td class="p-2 text-center border border-gray-300">
                                    @if (count($findings) > 1)
                                        <button type="button" wire:click="removeFinding({{ $index }})"
                                            class="text-red-400 hover:text-red-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z">
                                                </path>
                                            </svg>
                                        </button>
                                    @endif
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
                        <span wire:loading.remove
                            wire:target="save">{{ $reportId ? 'Perbarui Laporan' : 'Simpan Laporan' }}</span>
                        <span class="hidden" wire:loading.remove.class='hidden'
                            wire:target="save">Memproses...</span>
                    </button>
                </div>
            </div>
        </form>
    </x-tabs-wpi.layout>
</section>
