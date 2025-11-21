<!-- resources/views/livewire/hazard-list.blade.php -->
<section class="w-full">
    <x-toast />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <div class="flex justify-start mb-2 " wire:ignore>
        @if (Breadcrumbs::exists('hazard-detail'))
        {!! Breadcrumbs::render('hazard-detail', $hazard_id) !!}
        @endif
    </div>
    <div class="card bg-base-100 shadow-md mb-2 ">
        <div class="card-body py-1 px-4 ">
            {{-- STATUS + Tombol Audit Trail --}}
            <div class="flex justify-between items-center">
                <div class="flex gap-2 items-center">
                    <label class="label">
                        <span class="label-text text-xs font-semibold">Status :</span>
                    </label>
                    <span class="text-green-600 italic text-xs capitalize">
                        {{ $hazard->status }}
                    </span>
                </div>

                {{-- Tombol buka modal --}}
                <flux:button size="xs" variant="accent" icon='clock' onclick="my_modal_2.showModal()"></flux:button>
            </div>

            @php
            $isDisabled = in_array(optional($hazard)->status, ['cancelled', 'closed']);
            @endphp

            {{-- Form Action --}}
            <div class="flex flex-col md:flex-row md:items-stretch gap-2 ">
                {{-- PROCEED TO --}}
                <div class="max-w-sm">
                    <label class="label">
                        <span class="label-text text-xs font-semibold">Lanjutkan Ke</span>
                    </label>
                    <select wire:model.live="proceedTo" class="select select-xs select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden">
                        <option value="">-- Pilih Aksi --</option>
                        @foreach ($availableTransitions as $label => $status)
                        <option value="{{ $status }}">
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- PILIH ERM --}}
                @if ($proceedTo === 'in_progress')
                <div class="max-w-sm">
                    <label class="label">
                        <span class="label-text font-semibold text-xs">Pilih ERM Utama</span>
                    </label>
                    <select wire:model="assignTo1" class="select select-xs select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden">
                        <option value="">-- Pilih --</option>
                        @foreach ($ermList as $erm)
                        <option value="{{ $erm['id'] }}">{{ $erm['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="max-w-sm">
                    <label class="label">
                        <span class="label-text font-semibold text-xs">Pilih ERM Tambahan (Opsional)</span>
                    </label>
                    <select wire:model="assignTo2" class="select select-xs select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden">
                        <option value="">-- Pilih --</option>
                        @foreach ($ermList as $erm)
                        <option value="{{ $erm['id'] }}">{{ $erm['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- TOMBOL SIMPAN --}}
                <div class="card-actions justify-end self-end mt-1">
                    <div x-data="{ proceedTo: @entangle('proceedTo') }" class="card-actions justify-end hidden md:block">
                        <div class="tooltip">
                            <div class="tooltip-content z-40">
                                <div class="animate-bounce text-orange-400 text-sm font-black">Kirim</div>
                            </div>
                            <flux:button size="xs" wire:click="processAction" icon:trailing="send" variant="primary"></flux:button>
                        </div>
                    </div>
                    <div x-data="{ proceedTo: @entangle('proceedTo') }" class="card-actions justify-end block md:hidden">
                        <div class="tooltip">
                            <div class="tooltip-content z-40">
                                <div class="animate-bounce text-orange-400 text-sm font-black">Kirim</div>
                            </div>
                            <flux:button size="xs" wire:click="processAction" icon:trailing="send" class="w-full" variant="primary">Kirim</flux:button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal DaisyUI --}}
            <dialog class="modal" id="my_modal_2" role="dialog">
                <div class="modal-box max-w-4xl ">
                    <form method="dialog">
                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                    </form>
                    <h3 class="text-lg font-bold mb-2">Audit Trail</h3>
                    <div class="max-h-[80vh] overflow-y-auto">
                        <table class="table table-xs table-pin-rows border">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border px-2 py-1">Tanggal</th>
                                    <th class="border px-2 py-1">User</th>
                                    <th class="border px-2 py-1">Perubahan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($report->activities as $activity)
                                <tr>
                                    <td class="border px-2 py-1">{{ $activity->created_at->format('d-m-Y H:i') }}</td>
                                    <td class="border px-2 py-1">{{ $activity->causer->name ?? 'System' }}</td>
                                    <td class="border px-2 py-1">
                                        @if(str_contains($activity->description, 'ActionHazard'))
                                        {{-- Log khusus ActionHazard --}}
                                        <div class="mb-1 text-blue-600">
                                            {{ $activity->description }}
                                        </div>
                                        @endif
                                        @foreach($activity->changes['attributes'] ?? [] as $field => $new)
                                        @continue($field === 'updated_at')
                                        @php
                                        $oldValue = $activity->changes['old'][$field] ?? '-';
                                        $newValue = $new;

                                        switch ($field) {
                                        case 'penanggung_jawab_id':
                                        $oldValue = $activity->subject->penanggungJawab?->name ?? $oldValue;
                                        $newValue = \App\Models\User::find($new)?->name ?? $newValue;
                                        break;
                                        case 'pelapor_id':
                                        $oldValue = $activity->subject->pelapor?->name ?? $oldValue;
                                        $newValue = \App\Models\User::find($new)?->name ?? $newValue;
                                        break;
                                        case 'department_id':
                                        $oldValue = $activity->subject->department?->department_name ?? $oldValue;
                                        $newValue = \App\Models\Department::find($new)?->department_name ?? $newValue;
                                        break;
                                        case 'contractor_id':
                                        $oldValue = $activity->subject->contractor?->contractor_name ?? $oldValue;
                                        $newValue = \App\Models\Contractor::find($new)?->contractor_name ?? $newValue;
                                        break;
                                        case 'location_id':
                                        $oldValue = $activity->subject->location?->name ?? $oldValue;
                                        $newValue = \App\Models\Location::find($new)?->name ?? $newValue;
                                        break;
                                        case 'event_type_id':
                                        $oldValue = $activity->subject->eventType?->event_type_name ?? $oldValue;
                                        $newValue = \App\Models\EventType::find($new)?->event_type_name ?? $newValue;
                                        break;
                                        case 'event_sub_type_id':
                                        $oldValue = $activity->subject->eventSubType?->event_sub_type_name ?? $oldValue;
                                        $newValue = \App\Models\EventSubType::find($new)?->event_sub_type_name ?? $newValue;
                                        break;
                                        }
                                        $label = ucfirst(str_replace('_', ' ', $field));
                                        @endphp

                                        <div class="mb-1">
                                            <strong>{{ $label }}</strong>:
                                            <span class="text-red-500">{{ $oldValue }}</span>
                                            →
                                            <span class="text-green-600">{{ $newValue }}</span>
                                        </div>
                                        @endforeach
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-gray-500 py-2">Belum ada perubahan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </dialog>

        </div>

    </div>
    <form wire:submit.prevent="submit">
        <div class="w-full bg-base-200 p-1 mb-2 rounded">
            <flux:button size="xs" class="{{ $isDisabled ? 'btn btn-disabled cursor-not-allowed' : '' }}" type="submit" icon:trailing="save" variant="primary">Simpan</flux:button>
            <flux:button size="xs" class="{{ $isDisabled ? 'btn btn-disabled cursor-not-allowed' : '' }}" wire:click="deleteHazard({{ $hazard_id }})" wire:confirm="Yakin hapus Laporan ini?" icon:trailing="trash" variant="danger">Hapus</flux:button>
        </div>
        <x-tab-hazard.layout>
            <div wire:loading.class="skeleton animate-pulse" wire:target="submit">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                    <fieldset class="fieldset">
                        <x-form.label label="Tipe Bahaya" required />
                        <select {{ $isDisabled ? 'disabled' : '' }} wire:model.live="tipe_bahaya" class="select select-xs select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden">
                            <option value="">-- Pilih --</option>
                            @foreach ($eventTypes as $et )
                            <option value="{{ $et->id }}">{{ $et->event_type_name }}</option>
                            @endforeach
                        </select>
                        <x-label-error :messages="$errors->get('tipe_bahaya')" />
                    </fieldset>
                    <fieldset class="fieldset">
                        <x-form.label label="Sub Tipe Bahaya" required />
                        <select {{ $isDisabled ? 'disabled' : '' }} wire:model.live="sub_tipe_bahaya" class="select select-xs select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden">
                            <option value="">-- Pilih --</option>
                            @if ($tipe_bahaya)
                            @foreach ($subTypes as $et )
                            <option value="{{ $et->id }}">{{ $et->event_sub_type_name }}</option>
                            @endforeach
                            @endif

                        </select>
                        <x-label-error :messages="$errors->get('sub_tipe_bahaya')" />
                    </fieldset>

                    <fieldset class="fieldset">
                        <x-form.label label="Dilaporkan Oleh" required />
                        <div class="relative">
                            <!-- Input Search -->
                            <input {{ $isDisabled ? 'disabled' : '' }} type="text" wire:model.live.debounce.300ms="searchPelapor" placeholder="Cari Nama Pelapor..." class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                            <!-- Dropdown hasil search -->
                            @if($showPelaporDropdown)
                            <ul class="absolute z-10 bg-base-100 border rounded-md w-full mt-1 max-h-60 overflow-auto shadow">
                                <!-- Spinner ketika klik -->
                                <div wire:loading wire:target="selectPelapor" class="p-2 text-center">
                                    <span class="loading loading-spinner loading-sm text-secondary"></span>
                                </div>
                                @if(count($pelapors) > 0)
                                @foreach($pelapors as $pelapor)
                                <li wire:click="selectPelapor({{ $pelapor->id }}, '{{ $pelapor->name }}')" class="px-3 py-2 cursor-pointer hover:bg-base-200">
                                    {{ $pelapor->name }}
                                </li>
                                @endforeach
                                @else
                                <!-- Jika tidak ada hasil & belum mode manual -->
                                @if(!$manualPelaporMode)
                                <li wire:click="enableManualPelapor" class="px-3 py-2 cursor-pointer text-warning hover:bg-base-200">
                                    Tidak ditemukan, tambah pelapor manual
                                </li>
                                @endif
                                @endif
                                <!-- Input manual jika mode manual aktif -->
                                @if($manualPelaporMode)
                                <li class="p-2">
                                    <div class="relative w-full">
                                        <input type="text" wire:model.live="manualPelaporName" placeholder="Masukkan nama pelapor..." class="input input-bordered w-full pr-20 focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                                        <div class="!absolute top-1/2 -translate-y-1/2 right-0 z-20">
                                            <flux:button size="xs" wire:click="addPelaporManual" icon="plus" variant="primary">
                                                Tambah
                                            </flux:button>
                                        </div>
                                    </div>
                                </li>

                                @endif
                            </ul>
                            @endif
                        </div>
                        <!-- Error Message -->
                        @if($manualPelaporMode)
                        <x-label-error :messages="$errors->get('manualPelaporName')" />
                        @else
                        <x-label-error :messages="$errors->get('pelapor_id')" />
                        @endif
                    </fieldset>

                    <fieldset>
                        <input {{ $isDisabled ? 'disabled' : '' }} id="department" value="department" wire:model="deptCont" class="peer/department radio radio-xs radio-accent" type="radio" name="deptCont" checked />
                        <x-form.label for="department" class="peer-checked/department:text-accent text-[10px]" label="PT. MSM & PT. TTN" required />
                        <input {{ $isDisabled ? 'disabled' : '' }} id="company" value="company" wire:model="deptCont" class="peer/company radio radio-xs radio-primary" type="radio" name="deptCont" />
                        <x-form.label for="company" class="peer-checked/company:text-primary" label="Kontraktor" required />
                        <div class="hidden peer-checked/department:block mt-0.5">
                            {{-- Department --}}
                            <div class="relative mb-1">
                                <!-- Input Search -->

                                <input {{ $isDisabled ? 'disabled' : '' }} type="text" wire:model.live.debounce.300ms="search" placeholder="Cari departemen..." class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs " />
                                <!-- Dropdown hasil search -->
                                @if($showDropdown && count($departments) > 0)
                                <ul class="absolute z-10 bg-base-100 border rounded-md w-full mt-1 max-h-60 overflow-auto shadow">
                                    <!-- Spinner ketika klik salah satu -->
                                    <div wire:loading wire:target="selectDepartment" class="p-2 text-center">
                                        <span class="loading loading-spinner loading-sm text-secondary"></span>
                                    </div>
                                    @foreach($departments as $dept)
                                    <li wire:click="selectDepartment({{ $dept->id }}, '{{ $dept->department_name }}')" class="px-3 py-2 cursor-pointer hover:bg-base-200">
                                        {{ $dept->department_name }}
                                    </li>
                                    @endforeach
                                </ul>
                                @endif
                            </div>
                            @if($deptCont === 'department')
                            <x-label-error :messages="$errors->get('department_id')" />
                            @endif
                        </div>
                        <div class="hidden peer-checked/company:block mt-1">
                            {{-- Contractor --}}
                            <div class="relative mb-1">
                                <!-- Input Search -->
                                <input {{ $isDisabled ? 'disabled' : '' }} type="text" wire:model.live.debounce.300ms="searchContractor" placeholder="Cari kontraktor..." class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                                <!-- Dropdown hasil search -->
                                @if($showContractorDropdown && count($contractors) > 0)
                                <ul class="absolute z-10 bg-base-100 border rounded-md w-full mt-1 max-h-60 overflow-auto shadow">
                                    <!-- Spinner ketika klik -->
                                    <div wire:loading wire:target="selectContractor" class="p-2 text-center">
                                        <span class="loading loading-spinner loading-sm text-secondary"></span>
                                    </div>
                                    @foreach($contractors as $contractor)
                                    <li wire:click="selectContractor({{ $contractor->id }}, '{{ $contractor->contractor_name }}')" class="px-3 py-2 cursor-pointer hover:bg-base-200">
                                        {{ $contractor->contractor_name }}
                                    </li>
                                    @endforeach
                                </ul>
                                @endif
                            </div>
                            @if($deptCont === 'company')
                            <x-label-error :messages="$errors->get('contractor_id')" />
                            @endif
                        </div>
                    </fieldset>
                    <fieldset class="fieldset">
                        <x-form.label label="Penanggung Jawab Area" required />
                        <select {{ $isDisabled ? 'disabled' : '' }} wire:model.live="penanggungJawab" class="select select-xs select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden">
                            <option value="">-- Pilih --</option>
                            @foreach($penanggungJawabOptions as $pj)
                            <option value="{{ $pj['id'] }}">{{ $pj['name'] }}</option>
                            @endforeach
                        </select>
                        <x-label-error :messages="$errors->get('penanggungJawab')" />
                    </fieldset>

                    <fieldset class="fieldset ">
                        <x-form.label label="Lokasi" required />
                        <div class="relative">
                            <!-- Input Search -->
                            <input {{ $isDisabled ? 'disabled' : '' }} type="text" wire:model.live.debounce.300ms="searchLocation" placeholder="Cari Lokasi..." class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                            <!-- Dropdown hasil search -->
                            @if($showLocationDropdown && count($locations) > 0)
                            <ul class="absolute z-10 bg-base-100 border rounded-md w-full mt-1 max-h-60 overflow-auto shadow">
                                <!-- Spinner ketika klik -->
                                <div wire:loading wire:target="selectLocation" class="p-2 text-center">
                                    <span class="loading loading-spinner loading-sm text-secondary"></span>
                                </div>
                                @foreach($locations as $loc)
                                <li wire:click="selectLocation({{ $loc->id }}, '{{ $loc->name }}')" class="px-3 py-2 cursor-pointer hover:bg-base-200">
                                    {{ $loc->name }}
                                </li>
                                @endforeach
                            </ul>
                            @endif
                        </div>
                        <x-label-error :messages="$errors->get('location_id')" />
                    </fieldset>

                    {{-- Lokasi spesifik muncul hanya jika lokasi utama sudah dipilih --}}
                    @if($location_id)
                    <fieldset class="fieldset">
                        <x-form.label label="Lokasi Spesifik" required />
                        <input {{ $isDisabled ? 'disabled' : '' }} type="text" wire:model.live="location_specific" placeholder="Masukkan detail lokasi spesifik..." class=" input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                        <x-label-error :messages="$errors->get('location_specific')" />
                    </fieldset>
                    @endif

                    <fieldset class="fieldset relative">
                        <x-form.label label="Tanggal & Waktu" required />
                        <div class="relative" wire:ignore x-data="{
                            fp: null,
                            initFlatpickr() {
                                if (this.fp) this.fp.destroy();
                                this.fp = flatpickr(this.$refs.tanggalInput, {
                                    disableMobile: true,
                                    enableTime: true,
                                    defaultDate: this.$wire.entangle('tanggal').defer,
                                    dateFormat: 'd-m-Y H:i',
                                    clickOpens: true,
                                    appendTo: this.$refs.wrapper,
                                    onChange: (selectedDates, dateStr) => {
                                        this.$wire.set('tanggal', dateStr);
                                    }
                                });
                            }
                        }" x-ref="wrapper" x-init="
                            initFlatpickr();
                            Livewire.hook('message.processed', () => {
                                initFlatpickr();
                            });
                        ">
                            <input {{ $isDisabled ? 'disabled' : '' }} type="text" x-ref="tanggalInput" wire:model.live='tanggal' placeholder="Pilih Tanggal dan Waktu..." readonly class="input input-bordered cursor-pointer w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                        </div>
                        <x-label-error :messages="$errors->get('tanggal')" />
                    </fieldset>

                </div>
                <fieldset class="fieldset mb-4">
                    <x-form.label label="Deskripsi" required />
                    <div wire:ignore>
                        <textarea id="ckeditor-description">{{ $description }}</textarea>
                    </div>
                    <!-- Hidden input untuk binding Livewire -->
                    <input type="hidden" wire:model.live="description" id="description">
                    <x-label-error :messages="$errors->get('description')" />
                </fieldset>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4 ">
                    <fieldset class=" fieldset">
                        <x-form.label label="Dokumentasi Sebelum Tidakan perbaikan langsung" />
                        <label wire:ignore for="upload-deskripsi" class="flex items-center gap-2  {{ $isDisabled ? 'cursor-not-allowed' : 'cursor-pointer' }} border border-info rounded  hover:ring-1 hover:border-info hover:ring-info hover:outline-hidden">
                            <!-- Tombol custom -->
                            <span class="btn btn-info btn-xs {{ $isDisabled ? 'btn btn-disabled' : '' }}">
                                Pilih file atau gambar
                            </span>
                            <!-- Nama file -->
                            <span id="file-name" class="text-[9px] text-gray-500 truncate max-w-sm">
                                {!! $new_doc_deskripsi? $new_doc_deskripsi->getClientOriginalName():$doc_deskripsi !!}
                            </span>
                        </label>
                        @if ($new_doc_deskripsi)
                        <div class="text-xs text-green-600">Preview file baru:</div>
                        <img src="{{ $new_doc_deskripsi->temporaryUrl() }}" class="h-24 rounded border mt-1">
                        @elseif($doc_deskripsi)
                        <div class="text-xs text-gray-600">File lama:</div>
                        <img src="{{ asset('storage/' . $doc_deskripsi) }}" class="h-24 rounded border mt-1">
                        @else
                        <span class="text-xs text-gray-400">Belum ada file</span>
                        @endif
                        <!-- Input asli (disembunyikan) -->
                        <input {{ $isDisabled ? 'disabled' : '' }} id="upload-deskripsi" wire:model.live='new_doc_deskripsi' type="file" class="hidden" onchange="document.getElementById('file-name').textContent = this.files[0]?.name ?? 'Belum ada file'" />
                        <x-label-error :messages="$errors->get('new_doc_deskripsi')" />
                    </fieldset>
                </div>
                <fieldset class="fieldset mb-4">
                    <x-form.label label="Tindakan perbaikan langsung" required />
                    <div wire:ignore>
                        <textarea id="ckeditor-immediate_corrective_action">{{ $immediate_corrective_action }}</textarea>
                    </div>
                    <!-- Hidden input untuk binding Livewire -->
                    <input type="hidden" wire:model.live="immediate_corrective_action" id="immediate_corrective_action">
                    <x-label-error :messages="$errors->get('immediate_corrective_action')" />
                </fieldset>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4 ">
                    <fieldset class=" fieldset">
                        <x-form.label label="Dokumentasi Sesudah Tidakan perbaikan langsung" />
                        <label wire:ignore for="upload-corrective" class="flex items-center gap-2  {{ $isDisabled ? 'cursor-not-allowed' : 'cursor-pointer' }} border border-info rounded  hover:ring-1 hover:border-info hover:ring-info hover:outline-hidden">
                            <!-- Tombol custom -->
                            <span class="btn btn-info btn-xs {{ $isDisabled ? 'btn btn-disabled' : '' }}">
                                Pilih file atau gambar
                            </span>
                            <!-- Nama file -->
                            <span id="file-name-corrective" class="text-[9px] text-gray-500 truncate max-w-sm">
                                {!! $new_doc_corrective? $new_doc_corrective->getClientOriginalName():$doc_corrective !!}
                            </span>
                        </label>
                        @if ($new_doc_corrective)
                        <div class="text-xs text-green-600">Preview file baru:</div>
                        <img src="{{ $new_doc_corrective->temporaryUrl() }}" class="h-24 rounded border mt-1">
                        @elseif($doc_corrective)
                        <div class="text-xs text-gray-600">File lama:</div>
                        <img src="{{ asset('storage/' . $doc_corrective) }}" class="h-24 rounded border mt-1">
                        @else
                        <span class="text-xs text-gray-400">Belum ada file</span>
                        @endif
                        <!-- Input asli (disembunyikan) -->
                        <input {{ $isDisabled ? 'disabled' : '' }} id="upload-corrective" wire:model.live='new_doc_corrective' type="file" class="hidden" onchange="document.getElementById('file-name-corrective').textContent = this.files[0]?.name ?? 'Belum ada file'" />
                        <x-label-error :messages="$errors->get('new_doc_corrective')" />
                    </fieldset>
                </div>

                <fieldset class="fieldset  bg-base-100 shadow-md border border-gray-200 p-3 mb-4">
                    <legend class="card-title text-sm font-semibold ">Tambah Tindakan Lanjutan</legend>
                    <!-- Deskripsi Tindakan -->
                    <fieldset class="fieldset md:col-span-1">
                        <x-form.label label="Deskripsi Tindakan" required />
                        <div wire:ignore>
                            <textarea id="ckeditor-action_description" class="textarea textarea-bordered w-full h-20">{{ $action_description }}</textarea>
                        </div>
                        <input name="action_description" type="hidden" wire:model.live="action_description" id="action_description">
                        <x-label-error :messages="$errors->get('action_description')" />
                    </fieldset>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        <!-- Tanggal & Waktu -->
                        <fieldset class="fieldset md:col-span-1">
                            <x-form.label label="Batas Waktu Penyelesaian" required />
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
                                    }" x-init="initFlatpickr(); Livewire.hook('message.processed', () => initFlatpickr());" x-ref="wrapper">
                                <input name="action_due_date" type="text" x-ref="tanggalInput2" wire:model.live="action_due_date" placeholder="Pilih Tanggal" class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs {{ $errors->has('action_due_date') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}" readonly />
                            </div>
                            <x-label-error :messages="$errors->get('action_due_date')" />
                        </fieldset>
                        <fieldset class="fieldset md:col-span-1">
                            <x-form.label label="Tanggal Penyelesaian Tindakan" required />
                            <div class="relative" wire:ignore x-data="{
                                        fp: null,
                                        initFlatpickr() {
                                            if (this.fp) this.fp.destroy();
                                            this.fp = flatpickr(this.$refs.tanggalInput3, {
                                                disableMobile: true,
                                                enableTime: false,
                                                dateFormat: 'd-m-Y',
                                                onChange: (dates, str) => $wire.set('action_actual_close_date', str),
                                            });
                                        }
                                    }" x-init="initFlatpickr(); Livewire.hook('message.processed', () => initFlatpickr());" x-ref="wrapper">
                                <input name="action_actual_close_date" type="text" x-ref="tanggalInput3" wire:model.live="action_actual_close_date" placeholder="Pilih Tanggal" class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs {{ $errors->has('action_actual_close_date') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}" readonly />
                            </div>
                            <x-label-error :messages="$errors->get('action_actual_close_date')" />
                        </fieldset>
                        <!-- Dilaporkan Oleh -->
                        <fieldset class="fieldset md:col-span-1 relative">
                            <x-form.label label="Dilaporkan Oleh" required />
                            <div class="relative">
                                <input name="searchActResponsibility" type="text" wire:model.live.debounce.300ms="searchActResponsibility" placeholder="Cari Nama Pelapor..." class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs {{ $errors->has('pelapor_id') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}" />

                                <!-- Dropdown hasil search -->
                                @if($showActPelaporDropdown)
                                <ul class="absolute z-10 bg-base-100 border rounded-md w-full mt-1 max-h-60 overflow-auto shadow">
                                    <div wire:loading wire:target="selectPelapor" class="p-2 text-center">
                                        <span class="loading loading-spinner loading-sm text-secondary"></span>
                                    </div>
                                    @if(count($pelaporsAct) > 0)
                                    @foreach($pelaporsAct as $pelapor)
                                    <li wire:click="selectActPelapor({{ $pelapor->id }}, '{{ $pelapor->name }}')" class="px-3 py-2 cursor-pointer hover:bg-base-200">
                                        {{ $pelapor->name }}
                                    </li>
                                    @endforeach
                                    @else
                                    @if(!$manualActPelaporMode)
                                    <li wire:click="enableManualActPelapor" class="px-3 py-2 cursor-pointer text-warning hover:bg-base-200">
                                        Tidak ditemukan, tambah pelapor manual
                                    </li>
                                    @endif
                                    @endif

                                    @if($manualActPelaporMode)
                                    <li class="p-2">
                                        <div class="relative w-full">
                                            <input name="manualActPelaporName" type="text" wire:model.live="manualActPelaporName" placeholder="Masukkan nama..." class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs {{ $errors->has('manualPelaporName') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}" />
                                            <div class="absolute top-1/2 -translate-y-1/2 right-0">
                                                <flux:button size="xs" wire:click="addActPelaporManual" icon="plus" variant="primary">
                                                    Tambah
                                                </flux:button>
                                            </div>
                                        </div>
                                    </li>
                                    @endif
                                </ul>
                                @endif
                            </div>
                            @if($manualPelaporMode)
                            <x-label-error :messages="$errors->get('manualActPelaporName')" />
                            @else
                            <x-label-error :messages="$errors->get('action_responsible_id')" />
                            @endif
                        </fieldset>
                    </div>
                    <!-- Tombol Tambah -->
                    <div class=" flex justify-end">
                        <flux:button size="xs" wire:click="addActionHazard" icon:trailing="add-icon" variant="primary">Tambah</flux:button>
                    </div>
                    <!-- List Actions -->
                    <div class="divider my-2">Daftar Tindakan</div>
                    <div class="overflow-x-auto ">
                        <table class="table table-xs border text-sm px-2">
                            <thead class="bg-base-200">
                                <tr>
                                    <th class="whitespace-nowrap">Deskripsi</th>
                                    <th class="whitespace-nowrap">Batas Waktu</th>
                                    <th class="whitespace-nowrap">Tgl Selesai</th>
                                    <th class="whitespace-nowrap">PIC</th>
                                    <th class="text-center whitespace-nowrap">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($actionHazards as $act)
                                <tr>
                                    {{-- Deskripsi --}}
                                    <td class="align-top">
                                        <span class="font-semibold">{!! $act['description'] !!}</span>
                                    </td>

                                    {{-- Due Date --}}
                                    <td class="align-top">
                                        {{ \Carbon\Carbon::parse($act['due_date'])->timezone('Asia/Makassar')->format('d-m-Y') }}
                                    </td>

                                    {{-- Actual Close --}}
                                    <td class="align-top">
                                        {{ $act['actual_close_date']? \Carbon\Carbon::parse($act['actual_close_date'])->timezone('Asia/Makassar')->format('d-m-Y'): '-' }}
                                    </td>

                                    {{-- PIC --}}
                                    <td class="align-top">
                                        {{ optional(\App\Models\User::find($act['responsible_id']))->name ?? '-' }}
                                    </td>

                                    {{-- Action Buttons --}}
                                    <td class="align-top text-center">
                                        <div class="flex justify-center gap-2">
                                            <flux:button variant="subtle" size="xs" wire:click="loadEditAction({{ $act['id'] }})" icon="pencil-square">
                                            </flux:button>

                                            <flux:button variant="danger" size="xs" wire:click="removeAction({{ $act['id'] }})" wire:confirm="Yakin hapus tindakan ini?" icon="trash">
                                            </flux:button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-sm text-gray-500">
                                        Belum ada tindakan lanjutan ditambahkan.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </fieldset>

                <div class="grid grid-cols-1 md:grid-cols-2  gap-4 mb-4 border border-gray-300 p-4 rounded">
                    {{-- KEY WORD --}}
                    <fieldset>
                        <input {{ $isDisabled ? 'disabled' : '' }} id="kta" value="kta" wire:model.live="keyWord" class="peer/kta radio radio-xs radio-accent" type="radio" name="keyWord" checked />
                        <x-form.label for="kta" class="peer-checked/kta:text-accent text-[10px]" label="Kondisi Tidak Aman" required />
                        <input {{ $isDisabled ? 'disabled' : '' }} id="tta" value="tta" wire:model.live="keyWord" class="peer/tta radio radio-xs radio-primary" type="radio" name="keyWord" />
                        <x-form.label for="tta" class="peer-checked/tta:text-primary text-[10px]" label="Tindakan Tidak Aman" required />

                        <div class="hidden peer-checked/kta:block mt-1">
                            <select {{ $isDisabled ? 'disabled' : '' }} wire:model.live="kondisi_tidak_aman" class="select select-xs mb-1 select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden">
                                <option value="">-- Pilih Kondisi Tidak Aman --</option>
                                @foreach ($ktas as $kta)
                                <option value="{{ $kta->id }}">{{ $kta->name }}</option>
                                @endforeach
                            </select>

                        </div>
                        <div class="hidden peer-checked/tta:block mt-1">
                            <select {{ $isDisabled ? 'disabled' : '' }} wire:model.live="tindakan_tidak_aman" class="select select-xs mb-1 select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden">
                                <option value="">-- Pilih Tindakan Tidak Aman --</option>
                                @foreach ($ttas as $tta)
                                <option value="{{ $tta->id }}">{{ $tta->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if($keyWord === 'kta')
                        <x-label-error :messages="$errors->get('kondisi_tidak_aman')" />
                        @endif
                        @if($keyWord === 'tta')
                        <x-label-error :messages="$errors->get('tindakan_tidak_aman')" />
                        @endif

                    </fieldset>
                </div>
                <div class="flex flex-col md:flex-row gap-2">

                    {{-- Kolom Likelihood & Consequence --}}
                    <div class=" space-y-4 md:grow">
                        {{-- Consequence --}}
                        <fieldset class="fieldset ">
                            <x-form.label label="Consequence" required />
                            <select {{ $isDisabled ? 'disabled' : '' }} wire:model.live="consequence_id" class="select select-xs md:select-xs select-bordered w-full md:max-w-md focus:ring-1 focus:border-info focus:ring-info focus:outline-none">
                                <option value="">-- Pilih --</option>
                                @foreach ($consequencess as $cons)
                                <option value="{{ $cons->id }}">{{ $cons->name }}</option>
                                @endforeach
                            </select>
                            <x-label-error :messages="$errors->get('consequence_id')" />

                            @if($consequence_id)
                            @php
                            $selectedConsequence = $consequencess->firstWhere('id', $consequence_id);
                            @endphp
                            @if($selectedConsequence)
                            <div class="mt-1 text-sm text-gray-600 h-20 overflow-y-auto border rounded p-2 bg-gray-50">
                                {{ $selectedConsequence->description ?? 'Tidak ada deskripsi' }}
                            </div>
                            @endif
                            @endif
                        </fieldset>
                        {{-- Likelihood --}}
                        <fieldset class="fieldset ">
                            <x-form.label label="Likelihood" required />
                            <select {{ $isDisabled ? 'disabled' : '' }} wire:model.live="likelihood_id" class="select select-xs md:select-xs select-bordered w-full md:max-w-md focus:ring-1 focus:border-info focus:ring-info focus:outline-none">
                                <option value="">-- Pilih --</option>
                                @foreach ($likelihoodss as $like)
                                <option value="{{ $like->id }}">{{ $like->name }}</option>
                                @endforeach
                            </select>
                            <x-label-error :messages="$errors->get('likelihood_id')" />

                            @if($likelihood_id)
                            @php
                            $selectedLikelihood = $likelihoodss->firstWhere('id', $likelihood_id);
                            @endphp
                            @if($selectedLikelihood)
                            <div class="mt-1 text-sm text-gray-600 h-20 overflow-y-auto border rounded p-2 bg-gray-50">
                                {{ $selectedLikelihood->description ?? 'Tidak ada deskripsi' }}
                            </div>
                            @endif
                            @endif
                        </fieldset>


                    </div>

                    {{-- Kolom Risk Matrix --}}
                    <div class="overflow-x-auto  flex-none ">
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
                                    <th class="border-1">Likelihooc ↓ / Consequence →</th>
                                    @foreach ($consequences as $c)
                                    <th class="rotate_text border-1">{{ $c->name }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($likelihoods as $l)
                                <tr class="text-center text-[9px]">

                                    <td class=" font-bold w-1 border-1">{{ $l->name }}</td>
                                    @foreach ($consequences as $c)
                                    @php
                                    $cell = App\Models\RiskMatrixCell::where('likelihood_id', $l->id)->where('risk_consequence_id', $c->id)->first() ?? null;
                                    $score = $l->level * $c->level;
                                    $severity = $cell?->severity ?? '';
                                    $color = match($severity) {
                                    'Low' => 'bg-emerald-500',
                                    'Moderate' => 'bg-yellow-500',
                                    'High' => 'bg-orange-500',
                                    'Extreme' => 'bg-rose-500',
                                    default => 'bg-gray-100',
                                    };
                                    @endphp
                                    <td class="border cursor-pointer  @if($likelihood_id == $l->id && $consequence_id == $c->id) border-2 border-stone-500 @endif">
                                        <span wire:click="edit({{ $l->id }}, {{ $c->id }})" class="btn btn-square btn-xs   {{ $isDisabled ? 'btn btn-disabled' : "$color" }}">{{ Str::upper(substr($severity, 0, 1)) }}</span>
                                    </td>

                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
                @if ($RiskAssessment !=null)
                <table class="table table-xs mb-4">

                    <tr>
                        <th class="w-40 text-xs border-2 border-slate-400">Potential Risk Rating</th>
                        <td class="pl-2 text-xs border-2 border-slate-400">
                            {{ $RiskAssessment->name }}</td>
                    </tr>
                    <tr>
                        <th class="w-40 text-xs border-2 border-slate-400">Notify</th>
                        <td class="pl-2 text-xs border-2 border-slate-400">
                            {{ $RiskAssessment->reporting_obligation }}</td>
                    </tr>
                    <tr>
                        <th class="w-40 text-xs border-2 border-slate-400">Deadline</th>
                        <td class="pl-2 text-xs border-2 border-slate-400">{{ $RiskAssessment->notes }}</td>
                    </tr>
                    <tr>
                        <th class="w-40 text-xs border-2 border-slate-400">Coordinator</th>
                        <td class="pl-2 text-xs border-2 border-slate-400">
                            {{ $RiskAssessment->coordinator }}
                        </td>
                    </tr>


                </table>
                @endif
            </div>
        </x-tab-hazard.layout>
    </form>
    <!-- Modal Edit ActionHazard -->
    <div x-data="{ open: false }" x-on:open-edit-action.window="open = true" x-show="open" x-transition class="modal modal-open fixed inset-0 z-50 flex items-center justify-center bg-black/50" style="display:none;">
        <div class="modal-box w-11/12 max-w-4xl">
            <h3 class="font-bold text-lg mb-4">Edit Tindakan Lanjutan </h3>

            {{-- === Form Update === --}}
            <fieldset class="fieldset md:col-span-1">
                <x-form.label label="Deskripsi Tindakan" required />
                <div wire:ignore>
                    <textarea id="ckeditor-edit-action" class="textarea textarea-bordered w-full h-20">{{ $edit_action_description }}</textarea>
                </div>
                <input type="hidden" wire:model.live="edit_action_description" id="edit_action_description">
                <x-label-error :messages="$errors->get('edit_action_description')" />
            </fieldset>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end mt-4">
                {{-- Batas Waktu --}}
                <fieldset class="fieldset">
                    <x-form.label label="Batas Waktu Penyelesaian" required />
                    <div class="relative" wire:ignore x-data="{
                                fp:null,
                                initFlatpickr(){
                                    if(this.fp) this.fp.destroy();
                                    this.fp = flatpickr(this.$refs.dueEdit,{
                                        disableMobile:true,
                                        dateFormat:'d-m-Y',
                                        onChange:(dates,str)=>$wire.set('edit_action_due_date',str),
                                    });
                                }
                            }" x-init="
                                initFlatpickr();
                                Livewire.hook('message.processed', ()=>initFlatpickr());

                                // ==== Tambahan: isi ulang saat modal dibuka ====
                                Livewire.on('open-edit-action', () => {
                                    // Ambil value terbaru dari Livewire
                                    const val = @this.get('edit_action_due_date');
                                    // setDate akan menyesuaikan input + kalender
                                    if (val && this.fp) {
                                        this.fp.setDate(val, true, 'd-m-Y');
                                    }
                                });
                            " x-ref="wrapper">
                        <input type="text" x-ref="dueEdit" wire:model.live="edit_action_due_date" class="input input-bordered w-full input-xs" placeholder="Pilih Tanggal" readonly />
                    </div>
                    <x-label-error :messages="$errors->get('edit_action_due_date')" />
                </fieldset>


                {{-- Actual Close Date --}}
                <fieldset class="fieldset">
                    <x-form.label label="Tanggal Penyelesaian Tindakan" required />
                    <div class="relative" wire:ignore x-data="{
                         fp:null,
                         initFlatpickr(){
                             if(this.fp) this.fp.destroy();
                             this.fp = flatpickr(this.$refs.closeEdit,{
                                 disableMobile:true,
                                 dateFormat:'d-m-Y',
                                 
                                 onChange:(dates,str)=>$wire.set('edit_action_actual_close_date',str),
                             });
                         }
                     }" x-init="initFlatpickr(); Livewire.hook('message.processed', ()=>initFlatpickr());" x-ref="wrapper">
                        <input type="text" x-ref="closeEdit" wire:model.live="edit_action_actual_close_date" class="input input-bordered w-full input-xs" placeholder="Pilih Tanggal" readonly />
                    </div>
                    <x-label-error :messages="$errors->get('edit_action_actual_close_date')" />
                </fieldset>

                {{-- Responsible Person --}}
                <fieldset class="fieldset md:col-span-1 relative">
                    <x-form.label label="PIC" required />
                    <div class="relative">
                        <input name="searchActResponsibilityEdit" type="text" wire:model.live.debounce.300ms="searchActResponsibilityEdit" placeholder="Cari Nama Pelapor..." class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs {{ $errors->has('pelapor_id') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}" />

                        <!-- Dropdown hasil search -->
                        @if($showActPelaporDropdownEdit)
                        <ul class="absolute z-10 bg-base-100 border rounded-md w-full mt-1 max-h-60 overflow-auto shadow">
                            <div wire:loading wire:target="selectActPelaporEdit" class="p-2 text-center">
                                <span class="loading loading-spinner loading-sm text-secondary"></span>
                            </div>
                            @if(count($pelaporsActEdit) > 0)
                            @foreach($pelaporsActEdit as $pelapor)
                            <li wire:click="selectActPelaporEdit({{ $pelapor->id }}, '{{ $pelapor->name }}')" class="px-3 py-2 cursor-pointer hover:bg-base-200">
                                {{ $pelapor->name }}
                            </li>
                            @endforeach
                            @else
                            @if(!$manualActPelaporModeEdit)
                            <li wire:click="enableManualActPelapor" class="px-3 py-2 cursor-pointer text-warning hover:bg-base-200">
                                Tidak ditemukan, tambah pelapor manual
                            </li>
                            @endif
                            @endif

                            @if($manualActPelaporModeEdit)
                            <li class="p-2">
                                <div class="relative w-full">
                                    <input name="manualActPelaporNameEdit" type="text" wire:model.live="manualActPelaporNameEdit" placeholder="Masukkan nama..." class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs {{ $errors->has('manualPelaporName') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}" />
                                    <div class="absolute top-1/2 -translate-y-1/2 right-0">
                                        <flux:button size="xs" wire:click="addActPelaporManualEdit" icon="plus" variant="primary">
                                            Tambah
                                        </flux:button>
                                    </div>
                                </div>
                            </li>
                            @endif
                        </ul>
                        @endif
                    </div>
                    @if($manualPelaporMode)
                    <x-label-error :messages="$errors->get('manualActPelaporName')" />
                    @else
                    <x-label-error :messages="$errors->get('action_responsible_id')" />
                    @endif
                </fieldset>
            </div>

            <!-- Aksi -->
            <div class="modal-action mt-4 flex justify-end gap-2">
                <!-- Update tidak menutup modal -->
                <flux:button variant="primary" size="xs" type="button" wire:click="updateAction" x-on:click="$wire.call('updateAction').then(() => { open = false })">
                    Update
                </flux:button>
                <!-- Batal -->
                <flux:button variant="outline" size="xs" type="button" x-on:click="open = false">
                    Batal
                </flux:button>

            </div>
        </div>
    </div>
</section>
@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<script>
    const isDisabled = @json($isDisabled);
    console.log('Initial isDisabled:', isDisabled);

    document.addEventListener('livewire:navigated', () => {
        ClassicEditor
            .create(document.querySelector('#ckeditor-description'), {
                toolbar: ['bold', 'italic', 'bulletedList', 'numberedList', '|', 'undo', 'redo']
                , removePlugins: ['ImageUpload', 'EasyImage', 'MediaEmbed']
            })
            .then(editor => {
                // Set awal read-only jika isDisabled true
                if (isDisabled) {
                    editor.enableReadOnlyMode('hazard-description');
                }
                Livewire.on('hazardStatusChanged', event => {
                    data = event[0];
                    const bekukan = data.isDisabled;
                    if (bekukan === true) {
                        editor.enableReadOnlyMode('hazard-description');
                    } else {
                        editor.disableReadOnlyMode('hazard-description');
                    }

                });

                // Update hidden input dan Livewire
                editor.model.document.on('change:data', () => {
                    const data = editor.getData();
                    document.querySelector('#description').value = data;
                    @this.set('description', data);
                });
            })
            .catch(error => console.error(error));
    });

</script>

<script>
    document.addEventListener('livewire:navigated', () => {
        ClassicEditor
            .create(document.querySelector('#ckeditor-immediate_corrective_action'), {
                toolbar: [
                    // 'heading', '|'
                    , 'bold', 'italic', 'bulletedList', 'numberedList', '|'
                    , 'undo', 'redo'
                ]
                , removePlugins: ['ImageUpload', 'EasyImage', 'MediaEmbed'] // buang plugin gambar
            })
            .then(editor => {
                // Set awal read-only jika isDisabled true
                if (isDisabled) {
                    editor.enableReadOnlyMode('hazard-immediate_corrective_action');
                }
                Livewire.on('hazardStatusChanged', event => {
                    data = event[0];
                    const bekukan = data.isDisabled;
                    if (bekukan === true) {
                        editor.enableReadOnlyMode('hazard-immediate_corrective_action');
                    } else {
                        editor.disableReadOnlyMode('hazard-immediate_corrective_action');
                    }

                });

                editor.model.document.on('change:data', () => {
                    // Update ke hidden input
                    const data = editor.getData();
                    document.querySelector('#ckeditor-immediate_corrective_action').value = data;

                    // Kirim ke Livewire
                    @this.set('immediate_corrective_action', data);
                });
            })
            .catch(error => {
                console.error(error);
            });
    });

</script>
<script>
    document.addEventListener('livewire:navigated', () => {
        ClassicEditor
            .create(document.querySelector('#ckeditor-action_description'), {
                toolbar: [
                    // 'heading', '|'
                    , 'bold', 'italic', 'bulletedList', 'numberedList', '|'
                    , 'undo', 'redo'
                ]
                , removePlugins: ['ImageUpload', 'EasyImage', 'MediaEmbed'] // buang plugin gambar
            })
            .then(editor => {
                // Set awal read-only jika isDisabled true
                if (isDisabled) {
                    editor.enableReadOnlyMode('hazard-action_description');
                }
                Livewire.on('hazardStatusChanged', event => {
                    data = event[0];
                    const bekukan = data.isDisabled;
                    if (bekukan === true) {
                        editor.enableReadOnlyMode('hazard-action_description');
                    } else {
                        editor.disableReadOnlyMode('hazard-action_description');
                    }

                });

                editor.model.document.on('change:data', () => {
                    // Update ke hidden input
                    const data = editor.getData();
                    document.querySelector('#ckeditor-action_description').value = data;

                    // Kirim ke Livewire
                    @this.set('action_description', data);
                });
            })
            .catch(error => {
                console.error(error);
            });
    });

</script>
<script>
    document.addEventListener('livewire:navigated', () => {
        ClassicEditor
            .create(document.querySelector('#ckeditor-edit-action'), {
                toolbar: [
                    // 'heading', '|'
                    , 'bold', 'italic', 'bulletedList', 'numberedList', '|'
                    , 'undo', 'redo'
                ]
                , removePlugins: ['ImageUpload', 'EasyImage', 'MediaEmbed'] // buang plugin gambar
            })
            .then(editor => {
                // Set awal read-only jika isDisabled true
                if (isDisabled) {
                    editor.enableReadOnlyMode('hazard-action_description');
                }
                Livewire.on('hazardStatusChanged', event => {
                    data = event[0];
                    const bekukan = data.isDisabled;
                    if (bekukan === true) {
                        editor.enableReadOnlyMode('hazard-action_description');
                    } else {
                        editor.disableReadOnlyMode('hazard-action_description');
                    }

                });

                editor.model.document.on('change:data', () => {
                    // Update ke hidden input
                    const data = editor.getData();
                    document.querySelector('#ckeditor-edit-action').value = data;

                    // Kirim ke Livewire
                    @this.set('edit_action_description', data);
                });
                // ==== **ISI ULANG SAAT MODAL DIBUKA** ====
                Livewire.on('open-edit-action', () => {
                    // Ambil value terbaru dari property Livewire
                    const newValue = @this.get('edit_action_description');
                    editor.setData(newValue);
                });
            })
            .catch(error => {
                console.error(error);
            });
    });

</script>
@endpush
