<section class="w-full">
    <x-toast />
    {{-- Breadcrumb di sebelah kanan --}}
    <div class="flex justify-start mb-2 " wire:ignore>
        {{ Breadcrumbs::render('hazard-form') }}
    </div>
    @include('partials.manhours-heading')
    <x-manhours.layout>
        <form wire:submit.prevent="submit">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <fieldset class="fieldset">
                    <x-form.label label="Tipe Bahaya" required />
                    <select wire:model.live="tipe_bahaya"
                        class="select select-xs select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden {{ $errors->has('tipe_bahaya') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}">
                        <option value="">-- Pilih --</option>
                        @foreach ($eventTypes as $et)
                            <option value="{{ $et->id }}">{{ $et->event_type_name }}</option>
                        @endforeach
                    </select>
                    <x-label-error :messages="$errors->get('tipe_bahaya')" />
                </fieldset>
                <fieldset class="fieldset">
                    <x-form.label label="Jenis Bahaya" required />
                    <select wire:model.live="sub_tipe_bahaya"
                        class="select select-xs select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden {{ $errors->has('sub_tipe_bahaya') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}"">
                        <option value="">-- Pilih --</option>
                        @if ($tipe_bahaya)
                            @foreach ($subTypes as $et)
                                <option value=" {{ $et->id }}">{{ $et->event_sub_type_name }}</option>
                            @endforeach
                        @endif
                    </select>
                    <x-label-error :messages="$errors->get('sub_tipe_bahaya')" />
                </fieldset>
                <fieldset>
                    <input id="kta" value="kta" wire:model.live="keyWord"
                        class="peer/kta radio radio-xs radio-accent" type="radio" name="keyWord" checked />
                    <x-form.label for="kta" class="peer-checked/kta:text-accent text-[10px]"
                        label="Kondisi Tidak Aman" required />
                    <input id="tta" value="tta" wire:model.live="keyWord"
                        class="peer/tta radio radio-xs radio-primary" type="radio" name="keyWord" />
                    <x-form.label for="tta" class="peer-checked/tta:text-primary text-[10px]"
                        label="Tindakan Tidak Aman" required />
                    <div class="hidden peer-checked/kta:block ">
                        <select wire:model.live="kondisi_tidak_aman"
                            class="select select-xs mb-1 select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden {{ $errors->has('kondisi_tidak_aman') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}">
                            <option value="">-- Pilih Kondisi Tidak Aman --</option>
                            @foreach ($ktas as $kta)
                                <option value="{{ $kta->id }}">{{ $kta->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="hidden peer-checked/tta:block ">
                        <select wire:model.live="tindakan_tidak_aman"
                            class="select select-xs mb-1 select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden {{ $errors->has('tindakan_tidak_aman') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}">
                            <option value="">-- Pilih Tidakan Tidak Aman --</option>
                            @foreach ($ttas as $tta)
                                <option value="{{ $tta->id }}">{{ $tta->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if ($keyWord === 'kta')
                        <x-label-error :messages="$errors->get('kondisi_tidak_aman')" />
                    @endif
                    @if ($keyWord === 'tta')
                        <x-label-error :messages="$errors->get('tindakan_tidak_aman')" />
                    @endif
                </fieldset>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <fieldset class="fieldset mb-4 lg:col-span-2">
                    <x-form.label label="Deskripsi" required />
                    <div wire:ignore>
                        <textarea id="ckeditor-description"></textarea>
                    </div>
                    <!-- Hidden input untuk binding Livewire -->
                    <input name="description" type="hidden" wire:model.live="description" id="description">
                    <x-label-error :messages="$errors->get('description')" />
                </fieldset>
                <fieldset class=" fieldset">
                    <x-form.label label="Lampirkan foto atau dokumentasi (optional)" />
                    <label wire:ignore for="upload-deskripsi"
                        class="flex items-center gap-2 cursor-pointer border border-info rounded  hover:ring-1 hover:border-info hover:ring-info hover:outline-hidden">
                        <!-- Tombol custom -->
                        <span class="btn btn-info btn-xs">
                            Pilih file atau gambar
                        </span>
                        <!-- Nama file -->
                        <span id="file-name" class="text-xs text-gray-500">
                            Belum ada file
                        </span>
                    </label>
                    <!-- Input asli (disembunyikan) -->
                    <input name="doc_deskripsi" id="upload-deskripsi" wire:model.live='doc_deskripsi' type="file"
                        class="hidden"
                        onchange="document.getElementById('file-name').textContent = this.files[0]?.name ?? 'Belum ada file'" />
                    @if ($doc_deskripsi)
                        @if (in_array($doc_deskripsi->getClientOriginalExtension(), ['jpg', 'jpeg', 'png']))
                            <img src="{{ $doc_deskripsi->temporaryUrl() }}"
                                class="mt-2 {{ $doc_deskripsi ? 'w-40' : '' }} h-auto rounded border" />
                        @else
                            <p class="mt-2 text-sm text-gray-600">File:
                                {{ $doc_deskripsi->getClientOriginalName() }}</p>
                        @endif
                    @endif
                    <x-label-error :messages="$errors->get('doc_deskripsi')" />
                </fieldset>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:grid-cols-3">
                <fieldset class="fieldset ">
                    <x-form.label label="Lokasi" required />
                    <div class="relative">
                        <!-- Input Search -->
                        <input type="text" wire:model.live.debounce.300ms="searchLocation"
                            placeholder="Cari Lokasi..."
                            class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs {{ $errors->has('location_id') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}" />
                        <!-- Dropdown hasil search -->
                        @if ($showLocationDropdown && count($locations) > 0)
                            <ul
                                class="absolute z-10 bg-base-100 border rounded-md w-full mt-1 max-h-60 overflow-auto shadow">
                                <!-- Spinner ketika klik -->
                                <div wire:loading wire:target="selectLocation" class="p-2 text-center">
                                    <span class="loading loading-spinner loading-sm text-secondary"></span>
                                </div>
                                @foreach ($locations as $loc)
                                    <li wire:click="selectLocation({{ $loc->id }}, '{{ $loc->name }}')"
                                        class="px-3 py-2 cursor-pointer hover:bg-base-200">
                                        {{ $loc->name }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                    <x-label-error :messages="$errors->get('location_id')" />
                </fieldset>
                {{-- Lokasi spesifik muncul hanya jika lokasi utama sudah dipilih --}}
                @if ($location_id)
                    <fieldset class="fieldset">
                        <x-form.label label="Lokasi Spesifik" required />
                        <input name="location_specific" type="text" wire:model.live="location_specific"
                            placeholder="Masukkan detail lokasi spesifik..."
                            class=" input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs {{ $errors->has('location_specific') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}" />
                        <x-label-error :messages="$errors->get('location_specific')" />
                    </fieldset>
                @endif
                <fieldset class="fieldset relative">
                    <x-form.label label="Tanggal & Waktu" required />
                    <div
                        class="{{ $errors->has('tanggal') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500 rounded' : 'ring-base-300 focus:ring-base-300 focus:border-base-300 rounded' }}">
                        <div class="relative " wire:ignore x-data="{
                            fp: null,
                            initFlatpickr() {
                                if (this.fp) this.fp.destroy();
                                this.fp = flatpickr(this.$refs.tanggalInput, {
                                    disableMobile: true,
                                    enableTime: true,
                                    defaultDate: this.$wire.entangle('tanggal').defer,
                                    dateFormat: 'd-m-Y H:i',
                                    clickOpens: true,
                                    // HAPUS ATAU KOMENTARI BARIS INI (appendTo)
                                    // appendTo: this.$refs.wrapper,

                                    // TAMBAHKAN ATAU UBAH OPSI POSITION
                                    position: 'auto-below', // Opsi ini akan memaksa kalender muncul di bawah input.

                                    onChange: (selectedDates, dateStr) => {
                                        this.$wire.set('tanggal', dateStr);
                                    }
                                });
                            }
                        }" x-ref="wrapper"
                            x-init="initFlatpickr();
                            Livewire.hook('message.processed', () => {
                                initFlatpickr();
                            });">
                            <input type="text" x-ref="tanggalInput" wire:model.live='tanggal'
                                placeholder="Pilih Tanggal dan Waktu..." readonly
                                class="input input-bordered cursor-pointer w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs {{ $errors->has('tanggal') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}" />
                        </div>
                    </div>
                    <x-label-error :messages="$errors->get('tanggal')" />
                </fieldset>
            </div>
            <div class="grid grid-cols-1 gap-4">
                <fieldset class="fieldset ">
                    <x-form.label label="Dilaporkan Oleh" required />
                    <div class="relative">
                        <!-- Input Search -->
                        <input name="searchPelapor" type="text" wire:model.live.debounce.300ms="searchPelapor"
                            placeholder="Cari Nama Pelapor..."
                            class="input input-bordered w-full max-w-sm focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs {{ $errors->has('pelapor_id') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}"
                            x-ref="searchInput" />
                        <!-- Dropdown hasil search (teleport keluar collapse) -->
                        @if ($showPelaporDropdown)
                            <template x-teleport="body">
                                <ul x-data x-init="// Posisikan dropdown tepat di bawah input
                                $el.style.position = 'absolute';
                                const rect = $refs.searchInput.getBoundingClientRect();
                                $el.style.top = rect.bottom + 'px';
                                $el.style.left = rect.left + 'px';
                                $el.style.width = rect.width + 'px';
                                $el.style.zIndex = 9999;"
                                    class="bg-base-100 border rounded-md mt-1 max-h-60 overflow-auto shadow">
                                    <div wire:loading wire:target="selectPelapor" class="p-2 text-center">
                                        <span class="loading loading-spinner loading-sm text-secondary"></span>
                                        {{ $manualPelaporMode }}
                                    </div>

                                    @if (count($pelapors) > 0)
                                        @foreach ($pelapors as $pelapor)
                                            <li wire:click="selectPelapor({{ $pelapor->id }}, '{{ $pelapor->name }}')"
                                                class="px-3 py-2 cursor-pointer hover:bg-base-200">
                                                {{ $pelapor->name }}
                                            </li>
                                        @endforeach
                                    @else
                                        @if (!$manualPelaporMode)
                                            <li class="px-3 py-2  ">
                                                <flux:button size="xs" wire:click="enableManualPelapor"
                                                    icon="plus" class="w-full cursor-pointer text-warning"
                                                    variant="primary" color="cyan">
                                                    Tidak ditemukan, tambah pelapor manual
                                                </flux:button>
                                            </li>
                                        @endif
                                    @endif

                                </ul>

                            </template>
                        @endif
                    </div>
                    <!-- Error Message -->
                    @if ($manualPelaporMode)
                        <x-label-error :messages="$errors->get('manualPelaporName')" />
                    @else
                        <x-label-error :messages="$errors->get('pelapor_id')" />
                    @endif
                </fieldset>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <fieldset class="fieldset mb-4 md:col-span-2">
                    <label class="block"></label>
                    <x-form.label label="kondisi atau tindakan yang sudah dilakukan" required />
                    <div wire:ignore>
                        <textarea id="ckeditor-immediate_corrective_action"></textarea>
                    </div>
                    <!-- Hidden input untuk binding Livewire -->
                    <input name="immediate_corrective_action" type="hidden"
                        wire:model.live="immediate_corrective_action" id="immediate_corrective_action">
                    <x-label-error :messages="$errors->get('immediate_corrective_action')" />
                </fieldset>
                <fieldset class=" fieldset">
                    <x-form.label label="Lampirkan foto atau dokumentasi (optional)" />
                    <label class="block"></label>
                    <label wire:ignore for="upload-corrective"
                        class="flex items-center gap-2 cursor-pointer border border-info rounded  hover:ring-1 hover:border-info hover:ring-info hover:outline-hidden">
                        <!-- Tombol custom -->
                        <span class="btn btn-info btn-xs">
                            Pilih file atau gambar
                        </span>
                        <!-- Nama file -->
                        <span id="file-name-corrective" class="text-xs text-gray-500">
                            Belum ada file
                        </span>
                    </label>
                    @if ($doc_corrective)
                        @if (in_array($doc_corrective->getClientOriginalExtension(), ['jpg', 'jpeg', 'png']))
                            <img src="{{ $doc_corrective->temporaryUrl() }}"
                                class="mt-2 {{ $doc_corrective ? 'w-40' : '' }} h-auto rounded border" />
                        @else
                            <p class="mt-2 text-sm text-gray-600">File: {{ $doc_corrective->getClientOriginalName() }}
                            </p>
                        @endif
                    @endif
                    <!-- Input asli (disembunyikan) -->
                    <input name="doc_corrective" id="upload-corrective" wire:model.live='doc_corrective'
                        type="file" class="hidden"
                        onchange="document.getElementById('file-name-corrective').textContent = this.files[0]?.name ?? 'Belum ada file'" />
                    <x-label-error :messages="$errors->get('doc_corrective')" />
                </fieldset>
            </div>
            <fieldset class="fieldset card bg-base-100 shadow-md border border-gray-200 p-3">
                <legend class="card-title text-sm font-semibold ">DiLaporkan ke</legend>
                <div class="grid grid-cols-1 md:grid-cols-2  gap-4 lg:mb-4 ">
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

                        <div class="hidden peer-checked/department:block mt-2">
                            {{-- Department --}}
                            <div class="relative mb-1">
                                <!-- Input Search -->

                                <input name="search" type="text" wire:model.live.debounce.300ms="search"
                                    placeholder="Cari departemen..."
                                    class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs {{ $errors->has('department_id') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}" />
                                <!-- Dropdown hasil search -->
                                @if ($showDropdown && count($departments) > 0)
                                    <ul
                                        class="absolute z-10 bg-base-100 border rounded-md w-full mt-1 max-h-60 overflow-auto shadow">
                                        <!-- Spinner ketika klik salah satu -->
                                        <div wire:loading wire:target="selectDepartment" class="p-2 text-center">
                                            <span class="loading loading-spinner loading-sm text-secondary"></span>
                                        </div>
                                        @foreach ($departments as $dept)
                                            <li wire:click="selectDepartment({{ $dept->id }}, '{{ $dept->department_name }}')"
                                                class="px-3 py-2 cursor-pointer hover:bg-base-200">
                                                {{ $dept->department_name }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                            @if ($deptCont === 'department')
                                <x-label-error :messages="$errors->get('department_id')" />
                            @endif
                        </div>
                        <div class="hidden peer-checked/company:block mt-2">
                            {{-- Contractor --}}
                            <div class="relative mb-1">
                                <!-- Input Search -->
                                <input name="searchContractor" type="text"
                                    wire:model.live.debounce.300ms="searchContractor" placeholder="Cari kontraktor..."
                                    class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs {{ $errors->has('contractor_id') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}" />
                                <!-- Dropdown hasil search -->
                                @if ($showContractorDropdown && count($contractors) > 0)
                                    <ul
                                        class="absolute z-10 bg-base-100 border rounded-md w-full mt-1 max-h-60 overflow-auto shadow">
                                        <!-- Spinner ketika klik -->
                                        <div wire:loading wire:target="selectContractor" class="p-2 text-center">
                                            <span class="loading loading-spinner loading-sm text-secondary"></span>
                                        </div>
                                        @foreach ($contractors as $contractor)
                                            <li wire:click="selectContractor({{ $contractor->id }}, '{{ $contractor->contractor_name }}')"
                                                class="px-3 py-2 cursor-pointer hover:bg-base-200">
                                                {{ $contractor->contractor_name }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                            @if ($deptCont === 'company')
                                <x-label-error :messages="$errors->get('contractor_id')" />
                            @endif
                        </div>
                    </fieldset>
                    <fieldset class="fieldset">
                        <x-form.label label="Penanggung Jawab Area" required />
                        <select wire:model.live="penanggungJawab"
                            class="select select-xs select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden {{ $errors->has('penanggungJawab') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}">
                            <option value="">-- Pilih --</option>
                            @foreach ($penanggungJawabOptions as $pj)
                                <option value="{{ $pj['id'] }}">{{ $pj['name'] }}</option>
                            @endforeach
                        </select>
                        <x-label-error :messages="$errors->get('penanggungJawab')" />
                    </fieldset>
                </div>
            </fieldset>
            <div class=" mx-auto mb-4">
                <fieldset class="fieldset card bg-base-100 shadow-md border border-gray-200 p-3">
                    <legend class="card-title text-sm font-semibold "> Tindakan Lanjutan</legend>
                    <div class="card-body ">

                        <!-- Deskripsi Tindakan -->
                        <fieldset class="fieldset md:col-span-1">
                            <x-form.label label="Deskripsi Tindakan" required />
                            <div wire:ignore>
                                <textarea id="ckeditor-action_description" class="textarea textarea-bordered w-full h-20"></textarea>
                            </div>
                            <input name="action_description" type="hidden" wire:model.live="action_description"
                                id="action_description">
                            <x-label-error :messages="$errors->get('action_description')" />
                        </fieldset>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                            <!-- Tanggal & Waktu -->
                            <fieldset class="fieldset md:col-span-1">
                                <x-form.label label="Batas Waktu Penyelesaian" />
                                <div class="relative" wire:ignore x-data="{
                                    fp: null,
                                    initFlatpickr() {
                                        if (this.fp) this.fp.destroy();
                                        this.fp = flatpickr(this.$refs.tanggalInput2, {
                                            disableMobile: true,
                                            enableTime: false,
                                            dateFormat: 'd-m-Y',
                                            onChange: (dates, str) => $wire.set('action_due_date', str),
                                        });
                                    }
                                }" x-init="initFlatpickr();
                                Livewire.hook('message.processed', () => initFlatpickr());"
                                    x-ref="wrapper">
                                    <input name="action_due_date" type="text" x-ref="tanggalInput2"
                                        wire:model.live="action_due_date" placeholder="Pilih Tanggal"
                                        class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs {{ $errors->has('action_due_date') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}"
                                        readonly />
                                </div>
                                <x-label-error :messages="$errors->get('action_due_date')" />
                            </fieldset>
                            <fieldset class="fieldset md:col-span-1">
                                <x-form.label label="Tanggal Penyelesaian Tindakan" />
                                <div class="relative" wire:ignore x-data="{
                                    fp: null,
                                    initFlatpickr() {
                                        if (this.fp) this.fp.destroy();
                                        this.fp = flatpickr(this.$refs.tanggalInput3, {
                                            disableMobile: true,
                                            enableTime: false,
                                            dateFormat: 'd-m-Y',
                                            onChange: (dates, str) => $wire.set('actual_close_date', str),
                                        });
                                    }
                                }" x-init="initFlatpickr();
                                Livewire.hook('message.processed', () => initFlatpickr());"
                                    x-ref="wrapper">
                                    <input name="actual_close_date" type="text" x-ref="tanggalInput3"
                                        wire:model.live="actual_close_date" placeholder="Pilih Tanggal"
                                        class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs {{ $errors->has('actual_close_date') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}"
                                        readonly />
                                </div>
                                <x-label-error :messages="$errors->get('actual_close_date')" />
                            </fieldset>
                            <!-- Dilaporkan Oleh -->
                            <fieldset class="fieldset md:col-span-1 relative">
                                <x-form.label label="Dilaporkan Oleh" />
                                <div class="relative">
                                    <input name="searchActResponsibility" type="text"
                                        wire:model.live.debounce.300ms="searchActResponsibility"
                                        placeholder="Cari Nama Pelapor..."
                                        class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs {{ $errors->has('pelapor_id') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}" />

                                    <!-- Dropdown hasil search -->
                                    @if ($showActPelaporDropdown)
                                        <ul
                                            class="absolute z-10 bg-base-100 border rounded-md w-full mt-1 max-h-60 overflow-auto shadow">
                                            <div wire:loading wire:target="selectPelapor" class="p-2 text-center">
                                                <span class="loading loading-spinner loading-sm text-secondary"></span>
                                            </div>
                                            @if (count($pelaporsAct) > 0)
                                                @foreach ($pelaporsAct as $pelapor)
                                                    <li wire:click="selectActPelapor({{ $pelapor->id }}, '{{ $pelapor->name }}')"
                                                        class="px-3 py-2 cursor-pointer hover:bg-base-200">
                                                        {{ $pelapor->name }}
                                                    </li>
                                                @endforeach
                                            @else
                                                @if (!$manualActPelaporMode)
                                                    <li wire:click="enableManualActPelapor"
                                                        class="px-3 py-2 cursor-pointer text-warning hover:bg-base-200">
                                                        Tidak ditemukan, tambah pelapor manual
                                                    </li>
                                                @endif
                                            @endif

                                            @if ($manualActPelaporMode)
                                                <li class="p-2">
                                                    <div class="relative w-full">
                                                        <input name="manualActPelaporName" type="text"
                                                            wire:model.live="manualActPelaporName"
                                                            placeholder="Masukkan nama..."
                                                            class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs {{ $errors->has('manualPelaporName') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}" />
                                                        <div class="absolute top-1/2 -translate-y-1/2 right-0">
                                                            <flux:button size="xs"
                                                                wire:click="addActPelaporManual" icon="plus"
                                                                variant="primary">
                                                                Tambah
                                                            </flux:button>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endif
                                        </ul>
                                    @endif
                                </div>
                                @if ($manualPelaporMode)
                                    <x-label-error :messages="$errors->get('manualPelaporName')" />
                                @else
                                    <x-label-error :messages="$errors->get('responsible_id')" />
                                @endif
                            </fieldset>
                        </div>

                        <!-- Tombol Tambah -->
                        <div class=" flex justify-end">
                            <flux:button size="xs" wire:click="addAction" icon:trailing="add-icon"
                                variant="primary">Tambah</flux:button>
                        </div>
                        <!-- List Actions -->
                        <div class="divider my-2">Daftar Tindakan</div>
                        <ul class="space-y-2">
                            @forelse($actions as $index => $act)
                                <li
                                    class="flex flex-col md:flex-row md:items-center justify-between border p-3 rounded bg-base-50">
                                    <div class="mb-2 md:mb-0">
                                        <p><strong>{!! $act['description'] !!}</strong></p>
                                        <p class="text-sm text-gray-500">
                                            Batas Waktu Penyelesaian: {{ $act['due_date'] }} |
                                            Tanggal Penyelesaian Tindakan: {{ $act['actual_close_date'] }} |
                                            PIC: {{ optional(\App\Models\User::find($act['responsible_id']))->name }}
                                        </p>
                                    </div>
                                    <button type="button" wire:click="removeAction({{ $index }})"
                                        class="btn btn-error btn-xs self-start md:self-center">Hapus</button>
                                </li>
                            @empty
                                <li class="text-gray-500 text-sm">Belum ada tindakan lanjutan ditambahkan.</li>
                            @endforelse
                        </ul>
                    </div>
                </fieldset>
            </div>
            <div class="flex flex-col-reverse md:flex-row gap-2 mt-2">
                {{-- Kolom Likelihood & Consequence --}}
                <div class=" space-y-4 md:grow">
                    {{-- Consequence --}}
                    <fieldset class="fieldset ">
                        <x-form.label label="Consequence" required />
                        <select wire:model.live="consequence_id"
                            class="select select-xs md:select-xs select-bordered w-full md:max-w-md focus:ring-1 focus:border-info focus:ring-info focus:outline-none {{ $errors->has('consequence_id') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}">
                            <option value="">-- Pilih --</option>
                            @foreach ($consequencess as $cons)
                                <option value="{{ $cons->id }}">{{ $cons->name }}</option>
                            @endforeach
                        </select>
                        <x-label-error :messages="$errors->get('consequence_id')" />

                        @if ($consequence_id)
                            @php
                                $selectedConsequence = $consequencess->firstWhere('id', $consequence_id);
                            @endphp
                            @if ($selectedConsequence)
                                <div
                                    class="mt-1 text-sm text-gray-600 h-20 overflow-y-auto border rounded p-2 bg-gray-50">
                                    {{ $selectedConsequence->description ?? 'Tidak ada deskripsi' }}
                                </div>
                            @endif
                        @endif
                    </fieldset>
                    {{-- Likelihood --}}
                    <fieldset class="fieldset ">
                        <x-form.label label="Likelihood" required />
                        <select wire:model.live="likelihood_id"
                            class="select select-xs md:select-xs select-bordered w-full md:max-w-md focus:ring-1 focus:border-info focus:ring-info focus:outline-none {{ $errors->has('likelihood_id') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}">
                            <option value="">-- Pilih --</option>
                            @foreach ($likelihoodss as $like)
                                <option value="{{ $like->id }}">{{ $like->name }}</option>
                            @endforeach
                        </select>
                        <x-label-error :messages="$errors->get('likelihood_id')" />

                        @if ($likelihood_id)
                            @php
                                $selectedLikelihood = $likelihoodss->firstWhere('id', $likelihood_id);
                            @endphp
                            @if ($selectedLikelihood)
                                <div
                                    class="mt-1 text-sm text-gray-600 h-20 overflow-y-auto border rounded p-2 bg-gray-50">
                                    {{ $selectedLikelihood->description ?? 'Tidak ada deskripsi' }}
                                </div>
                            @endif
                        @endif
                    </fieldset>


                </div>
                {{-- Kolom Risk Matrix --}}
                <div class="overflow-x-auto  flex-none ">
                    <div role="tablist" class="flex">


                    </div>
                    <table class="table table-xs w-60">
                        <thead>
                            <tr class="text-center text-[9px]">
                                <td class=" border-1">Level</td>
                                <td class="rotate_text border-1 bg-emerald-500">Low</td>
                                <td class="rotate_text border-1 bg-yellow-500">Moderate</td>
                                <td class="rotate_text border-1 bg-orange-500">High</td>
                                <td class="rotate_text border-1 bg-rose-500">Extreme</td>
                                <td class="rotate_text border-1 bg-gray-100">Closed</td>
                            </tr>
                            <tr class="text-center text-[9px]">
                                <th class="border-1">Likelihood ↓ / Consequence →</th>
                                @foreach ($consequences as $c)
                                    <th class="rotate_text border-1">{{ $c->name }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($likelihoods as $l)
                                <tr class="text-center text-xs w-32">

                                    <td class=" font-bold w-1 border-1">{{ $l->name }}</td>
                                    @foreach ($consequences as $c)
                                        @php
                                            $cell =
                                                App\Models\RiskMatrixCell::where('likelihood_id', $l->id)
                                                    ->where('risk_consequence_id', $c->id)
                                                    ->first() ?? null;
                                            $score = $l->level * $c->level;
                                            $severity = $cell?->severity ?? '';
                                            $color = match ($severity) {
                                                'Low' => 'bg-emerald-500',
                                                'Moderate' => 'bg-yellow-500',
                                                'High' => 'bg-orange-500',
                                                'Extreme' => 'bg-rose-500',
                                                default => 'bg-gray-100',
                                            };
                                        @endphp
                                        <td
                                            class="border cursor-pointer  @if ($likelihood_id == $l->id && $consequence_id == $c->id) border-2 border-stone-500 @endif">
                                            <span wire:click="edit({{ $l->id }}, {{ $c->id }})"
                                                class="btn btn-square btn-xs   {{ $color }}">{{ Str::upper(substr($severity, 0, 1)) }}</span>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($RiskAssessment != null)
                <table class="table table-xs mt-4 table-zebra">

                    <tr>
                        <th class="w-40 text-xs border-slate-400">Potential Risk Rating</th>
                        <td class="pl-2 text-xs border-slate-400">
                            {{ $RiskAssessment->name }}</td>
                    </tr>
                    <tr>
                        <th class="w-40 text-xs border-slate-400">Notify</th>
                        <td class="pl-2 text-xs border-slate-400">
                            {{ $RiskAssessment->reporting_obligation }}</td>
                    </tr>
                    <tr>
                        <th class="w-40 text-xs border-slate-400">Deadline</th>
                        <td class="pl-2 text-xs border-slate-400">{{ $RiskAssessment->notes }}</td>
                    </tr>
                    <tr>
                        <th class="w-40 text-xs border-slate-400">Coordinator</th>
                        <td class="pl-2 text-xs border-slate-400">
                            {{ $RiskAssessment->coordinator }}
                        </td>
                    </tr>


                </table>
            @endif
            <div class="hidden md:block flex justify-end mt-4">
                <flux:button size="xs" type="submit" icon:trailing="send" variant="primary">Kirim Laporan
                </flux:button>
            </div>
            <div class="block md:hidden  mt-4">
                <flux:button size="xs" class="w-full" type="submit" icon:trailing="send" variant="primary">
                    Kirim Laporan</flux:button>
            </div>
        </form>
    </x-manhours.layout>
</section>



@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>

    <script>
        let ckAction_description = null;

        document.addEventListener('livewire:navigated', () => {
            ClassicEditor
                .create(document.querySelector('#ckeditor-action_description'), {
                    toolbar: [
                        'bold', 'italic', 'bulletedList', 'numberedList', '|', 'undo', 'redo'
                    ],
                    removePlugins: ['ImageUpload', 'EasyImage', 'MediaEmbed']
                })
                .then(editor => {
                    ckAction_description = editor;

                    // sinkron ke Livewire
                    editor.model.document.on('change:data', () => {
                        const data = editor.getData();
                        document.querySelector('#ckeditor-action_description').value = data;
                        @this.set('action_description', data);

                        if (data.trim() !== '') {
                            editor.ui.view.editable.element.classList.remove('error');
                        }
                    });
                })
                .catch(error => console.error(error));
        });

        // Validasi untuk AddAction
        Livewire.on('validateCkEditorAddAction', () => {
            if (ckAction_description) {
                const data = ckAction_description.getData().trim();
                if (data === '') {
                    ckAction_description.ui.view.editable.element.classList.add('error');
                    return false;
                }
            }
            return true;
        });

        // RESET CKEDITOR setelah tombol TAMBAH ditekan
        Livewire.on('reset-ckeditor', () => {
            if (ckAction_description) {
                ckAction_description.setData(''); // kosongkan editor
            }

            // hilangkan warna error jika ada
            if (ckAction_description ? .ui ? .view ? .editable ? .element) {
                ckAction_description.ui.view.editable.element.classList.remove('error');
            }
        });
    </script>


    <script>
        let ckImmediate_corrective_action = null;
        document.addEventListener('livewire:navigated', () => {
            ClassicEditor
                .create(document.querySelector('#ckeditor-immediate_corrective_action'), {
                    toolbar: [, 'bold', 'italic', 'bulletedList', 'numberedList', '|', 'undo', 'redo'],
                    removePlugins: ['ImageUpload', 'EasyImage', 'MediaEmbed'] // buang plugin gambar
                })
                .then(editor => {
                    ckImmediate_corrective_action = editor;
                    editor.model.document.on('change:data', () => {
                        const data = editor.getData();
                        document.querySelector('#ckeditor-immediate_corrective_action').value = data;
                        @this.set('immediate_corrective_action', data);
                        if (data.trim() !== '') {
                            editor.ui.view.editable.element.classList.remove('error');
                        }
                    });
                })
                .catch(error => {
                    console.error(error);
                });
        });
        Livewire.on('validateCkEditor', event => {
            if (ckImmediate_corrective_action) {
                const data = ckImmediate_corrective_action.getData().trim();
                if (data === '') {
                    ckImmediate_corrective_action.ui.view.editable.element.classList.add('error');
                    return false; // cegah submit
                }
            }
            return true;
        });
    </script>

    <script>
        let ckDescription = null;
        document.addEventListener('livewire:navigated', () => {
            ClassicEditor
                .create(document.querySelector('#ckeditor-description'), {
                    toolbar: ['bold', 'italic', 'bulletedList', 'numberedList', '|', 'undo', 'redo'],
                    removePlugins: ['ImageUpload', 'EasyImage', 'MediaEmbed']
                })
                .then(editor => {
                    ckDescription = editor;
                    // Update hidden input dan Livewire
                    editor.model.document.on('change:data', () => {
                        const data = editor.getData();
                        document.querySelector('#description').value = data;
                        @this.set('description', data);
                        // Hapus error jika sudah diisi
                        if (data.trim() !== '') {
                            editor.ui.view.editable.element.classList.remove('error');
                        }
                    });
                })
                .catch(error => console.error(error));
        });
        // 🔴 Fungsi untuk validasi CKEditor sebelum submit
        Livewire.on('validateCkEditor', event => {
            if (ckDescription) {
                const data = ckDescription.getData().trim();
                if (data === '') {
                    ckDescription.ui.view.editable.element.classList.add('error');
                    return false; // cegah submit
                }
            }
            return true;
        });
    </script>
@endpush
