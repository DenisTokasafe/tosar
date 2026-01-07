<section class="w-full">
    <x-toast />
    {{-- Breadcrumb di sebelah kanan --}}
    <div class="flex justify-start mb-2 " wire:ignore>
        {{ Breadcrumbs::render('hazard-form') }}
    </div>
    @include('partials.manhours-heading')
    <x-manhours.layout>
        <form wire:submit.prevent="submit">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
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
                            <option value="">-- Pilih Kategori Bahaya --</option>
                            @foreach ($ktas as $kta)
                                <option value="{{ $kta->id }}">{{ $kta->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="hidden peer-checked/tta:block ">
                        <select wire:model.live="tindakan_tidak_aman"
                            class="select select-xs mb-1 select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden {{ $errors->has('tindakan_tidak_aman') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}">
                            <option value="">-- Pilih Kategori Bahaya --</option>
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
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                <fieldset class="mb-4 fieldset lg:col-span-2">
                    <x-form.label label="Deskripsi" required />
                    <div wire:ignore>
                        <textarea id="ckeditor-description"></textarea>
                    </div>
                    <!-- Hidden input untuk binding Livewire -->
                    <input name="description" type="hidden" wire:model.live="description" id="description">
                    <x-label-error :messages="$errors->get('description')" />
                </fieldset>
                <fieldset class=" fieldset">
                    <x-form.upload label="Lampirkan Foto Dokumentasi Deskripsi" model="doc_deskripsi"
                        :file="$doc_deskripsi" />
                    <div wire:loading.remove wire:target="doc_deskripsi">
                        @if ($doc_deskripsi)
                            @if (in_array($doc_deskripsi->getClientOriginalExtension(), ['jpg', 'jpeg', 'png']))
                                <img src="{{ $doc_deskripsi->temporaryUrl() }}"
                                    class="mt-2 {{ $doc_deskripsi ? 'w-40' : '' }} h-auto rounded border" />
                            @elseif (in_array($doc_deskripsi->getClientOriginalExtension(), ['pdf', 'doc', 'docx']))
                                <div class="flex items-center gap-2 mt-2">
                                    @if ($doc_deskripsi->getClientOriginalExtension() == 'pdf')
                                        <x-icon.pdf class="w-8 h-8" />
                                        <span
                                            class="text-sm text-red-600">{{ $doc_deskripsi->getClientOriginalName() }}</span>
                                    @elseif (in_array($doc_deskripsi->getClientOriginalExtension(), ['doc', 'docx']))
                                        <x-icon.word class="w-8 h-8" />
                                        <span
                                            class="text-sm text-blue-600">{{ $doc_deskripsi->getClientOriginalName() }}</span>
                                    @else
                                        {{-- Ikon generik untuk file lain --}}
                                        <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zM6 20V4h7v4h4v12H6z" />
                                        </svg>
                                        <span class="text-sm text-gray-600">File:
                                            {{ $doc_deskripsi->getClientOriginalName() }}</span>
                                    @endif
                                @else
                                    <p class="mt-2 text-sm text-gray-600">File:
                                        {{ $doc_deskripsi->getClientOriginalName() }}</p>
                            @endif
                        @endif
                    </div>
                    <x-label-error :messages="$errors->get('doc_deskripsi')" />
                </fieldset>
            </div>
            <div class="grid grid-cols-1 gap-2 md:grid-cols-2 lg:grid-cols-3">
                <x-form.searchable-dropdown label="Lokasi" required modelsearch="searchLocation" modelid="location_id"
                    :options="$locations" :showdropdown="$show_location" clickaction="selectLocation" namedb="name" />
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
                <fieldset class="relative fieldset">
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
                                    time_24hr: true,
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
                <fieldset class="fieldset ">
                    <x-form.label label="Dilaporkan Oleh" required />
                    {{-- Induk harus memiliki class="relative" agar dropdown absolute berada di bawahnya --}}
                    <div class="relative">
                        <input name="searchPelapor" type="text" wire:model.live.debounce.300ms="searchPelapor"
                            placeholder="Cari Nama Pelapor..."
                            class="input input-bordered w-full max-w-sm focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs {{ $errors->has('pelapor_id') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}"
                            {{-- x-ref="searchInput" TIDAK LAGI DIBUTUHKAN --}} />
                        {{-- Menggunakan variabel Pelapor Anda: $showPelaporDropdown, $pelapors, selectPelapor --}}
                        @if ($showPelaporDropdown)
                            <ul
                                class="absolute z-10 w-full max-w-sm mt-1 overflow-auto border rounded-md shadow bg-base-100 max-h-60">

                                {{-- Spinner ketika klik --}}
                                <div wire:loading wire:target="selectPelapor" class="p-2 text-center">
                                    <span class="loading loading-spinner loading-sm text-secondary"></span>
                                    {{-- {{ $manualPelaporMode }} --}}
                                </div>

                                @if (count($pelapors) > 0)
                                    @foreach ($pelapors as $pelapor)
                                        <li wire:click="selectPelapor({{ $pelapor->id }}, '{{ $pelapor->name }}')"
                                            class="px-3 py-2 cursor-pointer hover:bg-base-200">
                                            {{ $pelapor->name }}
                                        </li>
                                    @endforeach
                                @else
                                    {{-- Logika untuk "Tambah pelapor manual" --}}
                                    @if (!$manualPelaporMode)
                                        <li class="px-3 py-2">
                                            <flux:button size="xs" wire:click="enableManualPelapor"
                                                icon="plus" class="w-full cursor-pointer text-warning"
                                                variant="primary" color="cyan">
                                                Tidak ditemukan, tambah pelapor manual
                                            </flux:button>
                                        </li>
                                    @else
                                        <li class="px-3 py-2 text-sm text-gray-500">
                                            Nama Pelapor Manual akan digunakan.
                                        </li>
                                    @endif
                                @endif
                            </ul>
                        @endif
                    </div>
                    @if ($manualPelaporMode)
                        <x-label-error :messages="$errors->get('manualPelaporName')" />
                    @else
                        <x-label-error :messages="$errors->get('pelapor_id')" />
                    @endif
                </fieldset>
            </div>
            <div class="grid grid-cols-1 gap-2 md:grid-cols-2 lg:grid-cols-3">
                <fieldset class="mb-4 fieldset md:col-span-2">
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
                    <x-form.upload label="Lampirkan foto atau dokumentasi" model="doc_corrective"
                        :file="$doc_corrective" />
                    <div wire:loading.remove wire:target="doc_corrective">
                        @if ($doc_corrective)
                            @if (in_array($doc_corrective->getClientOriginalExtension(), ['jpg', 'jpeg', 'png']))
                                <img src="{{ $doc_corrective->temporaryUrl() }}"
                                    class="mt-2 {{ $doc_corrective ? 'w-40' : '' }} h-auto rounded border" />
                            @elseif (in_array($doc_corrective->getClientOriginalExtension(), ['pdf', 'doc', 'docx']))
                                <div class="flex items-center gap-2 mt-2">
                                    @if ($doc_corrective->getClientOriginalExtension() == 'pdf')
                                        <x-icon.pdf class="w-8 h-8" />
                                        <span
                                            class="text-sm text-red-600">{{ $doc_corrective->getClientOriginalName() }}</span>
                                    @elseif (in_array($doc_corrective->getClientOriginalExtension(), ['doc', 'docx']))
                                        <x-icon.word class="w-8 h-8" />
                                        <span
                                            class="text-sm text-blue-600">{{ $doc_corrective->getClientOriginalName() }}</span>
                                    @else
                                        {{-- Ikon generik untuk file lain --}}
                                        <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zM6 20V4h7v4h4v12H6z" />
                                        </svg>
                                        <span class="text-sm text-gray-600">File:
                                            {{ $doc_corrective->getClientOriginalName() }}</span>
                                    @endif
                                </div>
                            @else
                                <p class="mt-2 text-sm text-gray-600">File:
                                    {{ $doc_corrective->getClientOriginalName() }}
                                </p>
                            @endif
                        @endif
                    </div>
                    <x-label-error :messages="$errors->get('doc_corrective')" />
                </fieldset>
            </div>
            <fieldset class="p-3 border border-gray-200 shadow-md fieldset card bg-base-100">
                <legend class="text-sm font-semibold card-title ">Penanggung Jawab</legend>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:mb-4 ">
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

                        <div class="hidden mt-2 peer-checked/department:block">
                            {{-- Department --}}
                            <div class="relative mb-1">
                                <x-form.searchable-dropdown-without-label modelsearch="search" modelid="department_id" placeholder="Cari Departemen..."
                                    :options="$departments" :showdropdown="$showDropdown" clickaction="selectDepartment"
                                    namedb="department_name" />
                            </div>
                        </div>
                        <div class="hidden mt-2 peer-checked/company:block">
                            {{-- Contractor --}}
                            <div class="relative mb-1">
                                <x-form.searchable-dropdown-without-label modelsearch="searchContractor" placeholder="Cari Kontraktor..."
                                    modelid="contractor_id" :options="$contractors" :showdropdown="$showContractorDropdown"
                                    clickaction="selectContractor" namedb="contractor_name" />
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="fieldset">
                        <x-form.label label="PIC" required />
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

            <fieldset class="p-3 border border-gray-200 shadow-md fieldset card bg-base-100">
                <legend class="text-sm font-semibold card-title "> Tindakan Lanjutan</legend>
                <!-- Deskripsi Tindakan -->
                <fieldset class="fieldset md:col-span-1">
                    <x-form.label label="Deskripsi Tindakan" required />
                    <div wire:ignore>
                        <textarea id="ckeditor-action_description" class="w-full h-20 textarea textarea-bordered"></textarea>
                    </div>
                    <input name="action_description" type="hidden" wire:model.live="action_description"
                        id="action_description">
                    <x-label-error :messages="$errors->get('action_description')" />
                </fieldset>
                <div class="grid items-end grid-cols-1 gap-4 md:grid-cols-3">
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
                    <x-form.searchable-select-advanced label="Dilaporkan Oleh" placeholder="Cari Nama Pelapor..."
                        modelsearch="searchActResponsibility" modelid="action_responsible_id" {{-- ID asli di DB --}}
                        :options="$pelaporsAct" :showdropdown="$showActPelaporDropdown" {{-- Logic Manual --}} :manualMode="$manualActPelaporMode"
                        manualModelName="manualActPelaporName" enableManualAction="enableManualActPelapor"
                        addManualAction="addActPelaporManual" clickaction="selectActPelapor" />
                </div>

                <!-- Tombol Tambah -->
                <div class="flex justify-end ">
                    <flux:button size="xs" wire:click="addAction" icon:trailing="add-icon" variant="primary">
                        Tambah</flux:button>
                </div>
                <!-- List Actions -->
                <div class="my-2 divider">Daftar Tindakan</div>
                <ul>
                    @forelse ($actions as $index => $action)
                        <li class="p-2 border rounded-md shadow-sm bg-base-100">
                            <div class="flex flex-col gap-1 md:flex-row md:justify-between">
                                <div>
                                    <span class="font-semibold">{!! $action['description'] !!}</span>
                                </div>
                                <div class="flex flex-col gap-1 md:flex-row md:items-center">
                                    <span class="text-sm badge badge-primary badge-outline">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-clock-check-icon lucide-clock-check">
                                            <path d="M12 6v6l4 2" />
                                            <path d="M22 12a10 10 0 1 0-11 9.95" />
                                            <path d="m22 16-5.5 5.5L14 19" />
                                        </svg>
                                        Batas Waktu:
                                        {{ $action['due_date'] ?? 'N/A' }}</span>
                                    <span class="text-sm badge badge-info badge-outline">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-clock-check-icon lucide-clock-check">
                                            <path d="M12 6v6l4 2" />
                                            <path d="M22 12a10 10 0 1 0-11 9.95" />
                                            <path d="m22 16-5.5 5.5L14 19" />
                                        </svg>
                                        Tgl Selesai:
                                        {{ $action['close_date'] ?? 'N/A' }}</span>
                                    <span class="text-sm badge badge-success badge-outline">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-user-check-icon lucide-user-check">
                                            <path d="m16 11 2 2 4-4" />
                                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                            <circle cx="9" cy="7" r="4" />
                                        </svg>
                                        PIC:
                                        {{ optional(\App\Models\User::find($action['responsible_id']))->name ?? ('-' ?? 'N/A') }}</span>
                                    <div class="flex gap-2 mt-1 md:mt-0">

                                        <flux:button variant="danger" size="xs"
                                            wire:click="removeAction({{ $index }})"
                                            wire:confirm="Yakin hapus tindakan ini?" icon="trash">
                                        </flux:button>
                                    </div>
                                </div>
                            </div>
                        </li>

                    @empty
                        <li class="p-2 border rounded-md shadow-sm bg-base-100">
                            <p class="text-sm text-center text-gray-500">Belum ada tindakan yang ditambahkan.</p>
                        </li>
                    @endforelse
                </ul>

            </fieldset>

            <div class="flex flex-col-reverse gap-2 mt-2 md:flex-row">
                {{-- Kolom Likelihood & Consequence --}}
                <div class="space-y-4 md:grow">
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
                                    class="h-20 p-2 mt-1 overflow-y-auto text-sm text-gray-600 border rounded bg-gray-50">
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
                                    class="h-20 p-2 mt-1 overflow-y-auto text-sm text-gray-600 border rounded bg-gray-50">
                                    {{ $selectedLikelihood->description ?? 'Tidak ada deskripsi' }}
                                </div>
                            @endif
                        @endif
                    </fieldset>


                </div>
                {{-- Kolom Risk Matrix --}}
                <div class="flex-none overflow-x-auto ">
                    <div role="tablist" class="flex">


                    </div>
                    <table class="table table-xs w-60">
                        <thead>
                            <tr class="text-center text-[9px]">
                                <td class=" border-1">Level</td>
                                <td class="rotate_text border-1 bg-emerald-500">Rendah</td>
                                <td class="bg-yellow-500 rotate_text border-1">Sedang</td>
                                <td class="bg-orange-500 rotate_text border-1">Tinggi</td>
                                <td class="rotate_text border-1 bg-rose-500">Ekstrem</td>
                                <td class="bg-gray-100 rotate_text border-1">Ditutup</td>
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
                                <tr class="w-32 text-xs text-center">

                                    <td class="w-1 font-bold border-1">{{ $l->name }}</td>
                                    @foreach ($consequences as $c)
                                        @php
                                            $cell =
                                                App\Models\RiskMatrixCell::where('likelihood_id', $l->id)
                                                    ->where('risk_consequence_id', $c->id)
                                                    ->first() ?? null;
                                            $score = $l->level * $c->level;
                                            $severity = $cell?->severity ?? '';
                                            $color = match ($severity) {
                                                'Rendah' => 'bg-emerald-500',
                                                'Sedang' => 'bg-yellow-500',
                                                'Tinggi' => 'bg-orange-500',
                                                'Ekstrem' => 'bg-rose-500',
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
                <table class="table mt-4 table-xs table-zebra">

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
            <div class="flex justify-end hidden mt-4 md:block">
                <flux:button size="xs" type="submit" icon:trailing="send" variant="primary">Kirim Laporan
                </flux:button>
            </div>
            <div class="block mt-4 md:hidden">
                <flux:button size="xs" class="w-full" type="submit" icon:trailing="send" variant="primary">
                    Kirim Laporan</flux:button>
            </div>
        </form>
    </x-manhours.layout>
    @push('scripts')
        <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
        {{-- action_description --}}
        <script>
            let ckAction_description = null;
            document.addEventListener('livewire:navigated', () => {
                ClassicEditor
                    .create(document.querySelector('#ckeditor-action_description'), {
                        toolbar: [
                            'bold', 'italic', 'bulletedList', 'numberedList', '|', 'undo', 'redo'
                        ],
                        removePlugins: ['ImageUpload', 'EasyImage']
                    })
                    .then(editor => {
                        ckAction_description = editor;
                        // Sinkronisasi ke Livewire menggunakan Livewire.dispatch
                        editor.model.document.on('change:data', () => {
                            const data = editor.getData();
                            document.querySelector('#ckeditor-action_description').value = data;

                            // 🛑 PERBAIKAN: Mengganti Livewire.find statis dengan dispatch
                            // Kita akan mengirimkan event khusus ke Livewire
                            Livewire.dispatch('updateActionDescription', {
                                actionData: data
                            });

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
            // RESET CKEDITOR
            Livewire.on('reset-ckeditor', () => {
                if (ckAction_description) {
                    ckAction_description.setData(''); // kosongkan editor
                }
                if (ckAction_description?.ui?.view?.editable?.element) {
                    ckAction_description.ui.view.editable.element.classList.remove('error');
                }
            });
        </script>

        {{-- immediate_corrective_action --}}
        <script>
            let ckImmediate_corrective_action = null;

            document.addEventListener('livewire:navigated', () => {
                ClassicEditor
                    .create(document.querySelector('#ckeditor-immediate_corrective_action'), {
                        toolbar: [
                            'bold', 'italic', 'bulletedList', 'numberedList', '|', 'undo', 'redo'
                        ],
                        removePlugins: ['ImageUpload', 'EasyImage']
                    })
                    .then(editor => {
                        ckImmediate_corrective_action = editor;

                        editor.model.document.on('change:data', () => {
                            const data = editor.getData();
                            document.querySelector('#ckeditor-immediate_corrective_action').value = data;

                            // 🛑 PERBAIKAN: Mengganti Livewire.find statis dengan dispatch
                            Livewire.dispatch('updateImmediateCorrectiveAction', {
                                actionData: data
                            });

                            if (data.trim() !== '') {
                                editor.ui.view.editable.element.classList.remove('error');
                            }
                        });
                    })
                    .catch(error => {
                        console.error(error);
                    });
            });

            Livewire.on('validateCkEditorImmediateCorrectiveAction', () => {
                if (ckImmediate_corrective_action) {
                    const data = ckImmediate_corrective_action.getData().trim();
                    if (data === '') {
                        ckImmediate_corrective_action.ui.view.editable.element.classList.add('error');
                        return false;
                    }
                }
                return true;
            });

            Livewire.on('reset-ckeditor-immediate-corrective-action', () => {
                if (ckImmediate_corrective_action) {
                    ckImmediate_corrective_action.setData('');
                }
                if (ckImmediate_corrective_action?.ui?.view?.editable?.element) {
                    ckImmediate_corrective_action.ui.view.editable.element.classList.remove('error');
                }
            });
        </script>

        {{-- DESCRIPTION --}}
        <script>
            let ckDescription = null;

            document.addEventListener('livewire:navigated', () => {
                ClassicEditor
                    .create(document.querySelector('#ckeditor-description'), {
                        toolbar: ['bold', 'italic', 'bulletedList', 'numberedList', '|', 'undo', 'redo'],
                        removePlugins: ['ImageUpload', 'EasyImage']
                    })
                    .then(editor => {
                        ckDescription = editor;

                        editor.model.document.on('change:data', () => {
                            const data = editor.getData();
                            document.querySelector('#ckeditor-description').value = data;

                            // 🛑 PERBAIKAN: Mengganti Livewire.find statis dengan dispatch
                            Livewire.dispatch('updateDescriptionData', {
                                descriptionData: data
                            });

                            if (data.trim() !== '') {
                                editor.ui.view.editable.element.classList.remove('error');
                            }
                        });
                    })
                    .catch(error => console.error(error));
            });

            Livewire.on('validateCkEditorDescription', () => {
                if (ckDescription) {
                    const data = ckDescription.getData().trim();
                    if (data === '') {
                        ckDescription.ui.view.editable.element.classList.add('error');
                        return false;
                    }
                }
                return true;
            });

            Livewire.on('reset-ckeditor-description', () => {
                if (ckDescription) {
                    ckDescription.setData('');
                }
                if (ckDescription?.ui?.view?.editable?.element) {
                    ckDescription.ui.view.editable.element.classList.remove('error');
                }
            });
        </script>
    @endpush
</section>
