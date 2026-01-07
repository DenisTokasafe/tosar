<!-- resources/views/livewire/hazard-list.blade.php -->
<section class="w-full">
    <x-toast />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <div class="flex justify-start mb-2 " wire:ignore>
        @if (Breadcrumbs::exists('hazard-detail'))
            {!! Breadcrumbs::render('hazard-detail', $hazard_id) !!}
        @endif
    </div>
    <div class="mb-2 shadow-md card bg-base-100 ">
        <div class="px-4 py-1 card-body ">
            {{-- STATUS + Tombol Audit Trail --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <label class="label">
                        <span class="text-xs font-semibold label-text">Status :</span>
                    </label>
                    <span class="badge-xs italic badge {{ $this->getRandomBadgeColor($hazard->status) }} capitalize">
                        {{ $hazard->status }}
                    </span>
                </div>

                {{-- Tombol buka modal --}}
                <flux:button size="xs" variant="accent" icon='clock' onclick="my_modal_2.showModal()">
                </flux:button>
            </div>

            @php
                $isDisabled = in_array(optional($hazard)->status, ['cancelled', 'closed']);
            @endphp

            {{-- Form Action --}}
            <div class="flex flex-col gap-2 md:flex-row md:items-stretch ">
                {{-- PROCEED TO --}}
                <div class="max-w-sm">
                    <label class="label">
                        <span class="text-xs font-semibold label-text">Lanjutkan Ke</span>
                    </label>
                    <select wire:model.live="proceedTo"
                        class="w-full select select-xs select-bordered focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden">
                        <option value="">-- Pilih Aksi --</option>
                        @foreach ($availableTransitions as $label => $status)
                            <option class="text-{{ $this->getTextColor($status) }}" value="{{ $status }}">
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- PILIH ERM --}}
                @if ($proceedTo === 'in_progress')
                    <div class="max-w-sm">
                        <label class="label">
                            <span class="text-xs font-semibold label-text">Pilih ERM Utama</span>
                        </label>
                        <select wire:model="assignTo1"
                            class="w-full select select-xs select-bordered focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden">
                            <option value="">-- Pilih --</option>
                            @foreach ($ermList as $erm)
                                <option value="{{ $erm['id'] }}">{{ $erm['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="max-w-sm">
                        <label class="label">
                            <span class="text-xs font-semibold label-text">Pilih ERM Tambahan (Opsional)</span>
                        </label>
                        <select wire:model="assignTo2"
                            class="w-full select select-xs select-bordered focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden">
                            <option value="">-- Pilih --</option>
                            @foreach ($ermList as $erm)
                                <option value="{{ $erm['id'] }}">{{ $erm['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- TOMBOL SIMPAN --}}
                <div class="self-end justify-end mt-1 card-actions">
                    <div x-data="{ proceedTo: @entangle('proceedTo') }" class="justify-end hidden card-actions md:block">
                        <div class="tooltip">
                            <div class="z-40 tooltip-content">
                                <div class="text-sm font-black text-orange-400 animate-bounce">Kirim</div>
                            </div>
                            <flux:button size="xs" wire:click="processAction" icon:trailing="send"
                                variant="primary"></flux:button>
                        </div>
                    </div>
                    <div x-data="{ proceedTo: @entangle('proceedTo') }" class="justify-end block card-actions md:hidden">
                        <div class="tooltip">
                            <div class="z-40 tooltip-content">
                                <div class="text-sm font-black text-orange-400 animate-bounce">Kirim</div>
                            </div>
                            <flux:button size="xs" wire:click="processAction" icon:trailing="send" class="w-full"
                                variant="primary">Kirim</flux:button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal DaisyUI --}}
            <dialog class="modal" id="my_modal_2" role="dialog">
                <div class="max-w-4xl modal-box ">
                    <form method="dialog">
                        <button class="absolute btn btn-sm btn-circle btn-ghost right-2 top-2">✕</button>
                    </form>
                    <h3 class="mb-2 text-lg font-bold">Audit Trail</h3>
                    <div class="max-h-[80vh] overflow-y-auto">
                        <table class="table border table-xs table-pin-rows">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-2 py-1 border">Tanggal</th>
                                    <th class="px-2 py-1 border">User</th>
                                    <th class="px-2 py-1 border">Perubahan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($report->activities as $activity)
                                    <tr>
                                        <td class="px-2 py-1 border">{{ $activity->created_at->format('d-m-Y H:i') }}
                                        </td>
                                        <td class="px-2 py-1 border">
                                            {{ $activity->causer->name ?? $manualPelaporName }}</td>
                                        <td class="px-2 py-1 border">
                                            @if (str_contains($activity->description, 'ActionHazard'))
                                                {{-- Log khusus ActionHazard --}}
                                                <div class="mb-1 text-blue-600">
                                                    {{ $activity->description }}
                                                </div>
                                            @endif
                                            @foreach ($activity->changes['attributes'] ?? [] as $field => $new)
                                                @continue($field === 'updated_at')
                                                @php
                                                    $oldValue = $activity->changes['old'][$field] ?? '-';
                                                    $newValue = $new;

                                                    switch ($field) {
                                                        case 'penanggung_jawab_id':
                                                            $oldValue =
                                                                $activity->subject->penanggungJawab?->name ?? $oldValue;
                                                            $newValue =
                                                                \App\Models\User::find($new)?->name ?? $newValue;
                                                            break;
                                                        case 'pelapor_id':
                                                            $oldValue = $activity->subject->pelapor?->name ?? $oldValue;
                                                            $newValue =
                                                                \App\Models\User::find($new)?->name ?? $newValue;
                                                            break;
                                                        case 'department_id':
                                                            $oldValue =
                                                                $activity->subject->department?->department_name ??
                                                                $oldValue;
                                                            $newValue =
                                                                \App\Models\Department::find($new)?->department_name ??
                                                                $newValue;
                                                            break;
                                                        case 'contractor_id':
                                                            $oldValue =
                                                                $activity->subject->contractor?->contractor_name ??
                                                                $oldValue;
                                                            $newValue =
                                                                \App\Models\Contractor::find($new)?->contractor_name ??
                                                                $newValue;
                                                            break;
                                                        case 'location_id':
                                                            $oldValue =
                                                                $activity->subject->location?->name ?? $oldValue;
                                                            $newValue =
                                                                \App\Models\Location::find($new)?->name ?? $newValue;
                                                            break;
                                                        case 'event_type_id':
                                                            $oldValue =
                                                                $activity->subject->eventType?->event_type_name ??
                                                                $oldValue;
                                                            $newValue =
                                                                \App\Models\EventType::find($new)?->event_type_name ??
                                                                $newValue;
                                                            break;
                                                        case 'event_sub_type_id':
                                                            $oldValue =
                                                                $activity->subject->eventSubType
                                                                    ?->event_sub_type_name ?? $oldValue;
                                                            $newValue =
                                                                \App\Models\EventSubType::find($new)
                                                                    ?->event_sub_type_name ?? $newValue;
                                                            break;
                                                        case 'kondisi_tidak_aman_id':
                                                            $oldValue =
                                                                $activity->subject->hazardKondisiTidakAman?->name ??
                                                                $oldValue;
                                                            $newValue =
                                                                \App\Models\UnsafeCondition::find($new)?->name ??
                                                                $newValue;
                                                            break;
                                                        case 'tindakan_tidak_aman_id':
                                                            $oldValue =
                                                                $activity->subject->hazardTindakanTidakAman?->name ??
                                                                $oldValue;
                                                            $newValue =
                                                                \App\Models\UnsafeAct::find($new)?->name ?? $newValue;
                                                            break;
                                                        case 'consequence_id':
                                                            $oldValue =
                                                                $activity->subject->consequence?->name ?? $oldValue;
                                                            $newValue =
                                                                \App\Models\RiskConsequence::find($new)?->name ??
                                                                $newValue;
                                                            break;
                                                        case 'likelihood_id':
                                                            $oldValue =
                                                                $activity->subject->likelihood?->name ?? $oldValue;
                                                            $newValue =
                                                                \App\Models\Likelihood::find($new)?->name ?? $newValue;
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
                                        <td colspan="3" class="py-2 text-center text-gray-500">Belum ada perubahan
                                        </td>
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
        <div class="w-full p-1 mb-2 rounded bg-base-200">
            <flux:button size="xs" class="{{ $isDisabled ? 'btn btn-disabled cursor-not-allowed' : '' }}"
                type="submit" icon:trailing="save" variant="primary">Update Laporan</flux:button>
            <flux:button size="xs" class="{{ $isDisabled ? 'btn btn-disabled cursor-not-allowed' : '' }}"
                wire:click="deleteHazard({{ $hazard_id }})" wire:confirm="Yakin hapus Laporan ini?"
                icon:trailing="trash" variant="danger">Hapus Laporan</flux:button>
        </div>
        <x-tab-hazard.layout>
            <div wire:loading.class="skeleton animate-pulse skeleton-text" wire:target="submit">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <fieldset class="fieldset">
                        <x-form.label label="Tipe Bahaya" required />
                        <select {{ $isDisabled ? 'disabled' : '' }} wire:model.live="tipe_bahaya"
                            class="w-full select select-xs select-bordered focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden">
                            <option value="">-- Pilih --</option>
                            @foreach ($eventTypes as $et)
                                <option value="{{ $et->id }}">{{ $et->event_type_name }}</option>
                            @endforeach
                        </select>
                        <x-label-error :messages="$errors->get('tipe_bahaya')" />
                    </fieldset>

                    <fieldset class="fieldset">
                        <x-form.label label="Jenis Bahaya" required />
                        <select {{ $isDisabled ? 'disabled' : '' }} wire:model.live="sub_tipe_bahaya"
                            class="w-full select select-xs select-bordered focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden">
                            <option value="">-- Pilih --</option>
                            @if ($tipe_bahaya)
                                @foreach ($subTypes as $et)
                                    <option value="{{ $et->id }}">{{ $et->event_sub_type_name }}</option>
                                @endforeach
                            @endif

                        </select>
                        <x-label-error :messages="$errors->get('sub_tipe_bahaya')" />
                    </fieldset>

                    <fieldset>
                        <input {{ $isDisabled ? 'disabled' : '' }} id="kta" value="kta"
                            wire:model.live="keyWord" class="peer/kta radio radio-xs radio-accent" type="radio"
                            name="keyWord" checked />
                        <x-form.label for="kta" class="peer-checked/kta:text-accent text-[10px]"
                            label="Kondisi Tidak Aman" required />
                        <input {{ $isDisabled ? 'disabled' : '' }} id="tta" value="tta"
                            wire:model.live="keyWord" class="peer/tta radio radio-xs radio-primary" type="radio"
                            name="keyWord" />
                        <x-form.label for="tta" class="peer-checked/tta:text-primary text-[10px]"
                            label="Tindakan Tidak Aman" required />
                        <div class="hidden peer-checked/kta:block ">
                            <select {{ $isDisabled ? 'disabled' : '' }} wire:model.live="kondisi_tidak_aman"
                                class="select select-xs mb-1 select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden {{ $errors->has('kondisi_tidak_aman') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}">
                                <option value="">-- Pilih Kategori Bahaya --</option>
                                @foreach ($ktas as $kta)
                                    <option value="{{ $kta->id }}">{{ $kta->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="hidden peer-checked/tta:block ">
                            <select {{ $isDisabled ? 'disabled' : '' }} wire:model.live="tindakan_tidak_aman"
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
                            <textarea id="ckeditor-description">{{ $description }}</textarea>
                        </div>
                        <!-- Hidden input untuk binding Livewire -->
                        <input type="hidden" wire:model.live="description" id="description">
                        <x-label-error :messages="$errors->get('description')" />
                    </fieldset>
                    <x-form.file-upload label="Lampirkan foto atau dokumentasi" model="new_doc_deskripsi"
                        :existingFile="$doc_deskripsi" :newFile="$new_doc_deskripsi" :isDisabled="$isDisabled" />
                </div>
                <div class="grid grid-cols-1 gap-2 md:grid-cols-2 lg:grid-cols-3">
                    <x-form.searchable-dropdown label="Lokasi" required modelsearch="searchLocation"
                        modelid="location_id" :options="$locations" :showdropdown="$showLocationDropdown" clickaction="selectLocation"
                        :disabled="$isDisabled" namedb="name" />

                    {{-- Lokasi spesifik muncul hanya jika lokasi utama sudah dipilih --}}
                    @if ($location_id)
                        <fieldset class="fieldset">
                            <x-form.label label="Lokasi Spesifik" required />
                            <input {{ $isDisabled ? 'disabled' : '' }} type="text"
                                wire:model.live="location_specific" placeholder="Masukkan detail lokasi spesifik..."
                                class="w-full input input-bordered focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                            <x-label-error :messages="$errors->get('location_specific')" />
                        </fieldset>
                    @endif
                    <fieldset class="relative fieldset">
                        <x-form.label label="Tanggal & Waktu" required />
                        <div class="relative " wire:ignore x-data="{
                            fp: null,
                            // Properti Alpine.js untuk menampung nilai awal dari Livewire
                            tanggalValue: '{{ $this->tanggal }}',
                            initFlatpickr() {
                                if (this.fp) this.fp.destroy();
                                this.fp = flatpickr(this.$refs.tanggalInput, {
                                    disableMobile: true,
                                    enableTime: true,
                                    time_24hr: true,
                                    defaultDate: @js($this->tanggal),
                                    dateFormat: 'd-m-Y H:i',
                                    clickOpens: true,
                                    position: 'auto-below',

                                    onChange: (selectedDates, dateStr) => {
                                        this.$wire.set('tanggal', dateStr);
                                    }
                                });
                            }
                        }" x-ref="wrapper"
                            x-init="initFlatpickr();
                            Livewire.hook('message.processed', () => {
                                // Re-initialize hanya jika Anda yakin properti 'tanggal' di Livewire berubah
                                // dan perlu diperbarui tanpa interaksi user.
                                // initFlatpickr();
                            });">
                            <input {{ $isDisabled ? 'disabled' : '' }} type="text" x-ref="tanggalInput"
                                wire:model.live='tanggal' placeholder="Pilih Tanggal dan Waktu..." readonly
                                class="w-full cursor-pointer input input-bordered focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                        </div>
                        <x-label-error :messages="$errors->get('tanggal')" />
                    </fieldset>

                    <x-form.searchable-select-advanced label="Dilaporkan Oleh" placeholder="Cari Nama Pelapor..."
                        modelsearch="searchPelapor" modelid="pelapor_id" {{-- ID asli di DB --}} :options="$pelapors"
                        :showdropdown="$showPelaporDropdown" {{-- Logic Manual --}} :manualMode="$manualPelaporMode"
                        manualModelName="manualPelaporName" enableManualAction="enableManualPelapor"
                        addManualAction="addPelaporManual" clickaction="selectPelapor" :disabled="$isDisabled" />
                </div>
                <div class="grid grid-cols-1 gap-2 md:grid-cols-2 lg:grid-cols-3">
                    <fieldset class="mb-4 fieldset md:col-span-2">
                        <x-form.label label="Tindakan perbaikan langsung" required />
                        <div wire:ignore>
                            <textarea id="ckeditor-immediate_corrective_action">{{ $immediate_corrective_action }}</textarea>
                        </div>
                        <!-- Hidden input untuk binding Livewire -->
                        <input type="hidden" wire:model.live="immediate_corrective_action"
                            id="immediate_corrective_action">
                        <x-label-error :messages="$errors->get('immediate_corrective_action')" />
                    </fieldset>
                    <x-form.file-upload label="Dokumentasi Sesudah Tidakan perbaikan langsung (Optional)" model="new_doc_corrective"
                        :existingFile="$doc_corrective" :newFile="$new_doc_corrective" :isDisabled="$isDisabled" />

                </div>
                <fieldset class="p-3 border border-gray-200 shadow-md fieldset card bg-base-100">
                    <legend class="text-sm font-semibold card-title ">Penanggung Jawab</legend>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:mb-4 ">
                        {{-- workgroup --}}
                        <fieldset>
                            <input {{ $isDisabled ? 'disabled' : '' }} id="department" value="department"
                                wire:model="deptCont" class="peer/department radio radio-xs radio-accent"
                                type="radio" name="deptCont" checked />
                            <x-form.label for="department" class="peer-checked/department:text-accent text-[10px]"
                                label="PT. MSM & PT. TTN" required />
                            <input {{ $isDisabled ? 'disabled' : '' }} id="company" value="company"
                                wire:model="deptCont" class="peer/company radio radio-xs radio-primary"
                                type="radio" name="deptCont" />
                            <x-form.label for="company" class="peer-checked/company:text-primary" label="Kontraktor"
                                required />
                            <div class="hidden mt-2 peer-checked/department:block">
                                {{-- Department --}}
                                <div class="relative mb-1">
                                    <!-- Input Search -->

                                    <input {{ $isDisabled ? 'disabled' : '' }} type="text"
                                        wire:model.live.debounce.300ms="search" placeholder="Cari departemen..."
                                        class="w-full input input-bordered focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs " />
                                    <!-- Dropdown hasil search -->
                                    @if ($showDropdown && count($departments) > 0)
                                        <ul
                                            class="absolute z-10 w-full mt-1 overflow-auto border rounded-md shadow bg-base-100 max-h-60">
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
                            <div class="hidden mt-2 peer-checked/company:block">
                                {{-- Contractor --}}
                                <div class="relative mb-1">
                                    <!-- Input Search -->
                                    <input {{ $isDisabled ? 'disabled' : '' }} type="text"
                                        wire:model.live.debounce.300ms="searchContractor"
                                        placeholder="Cari kontraktor..."
                                        class="w-full input input-bordered focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                                    <!-- Dropdown hasil search -->
                                    @if ($showContractorDropdown && count($contractors) > 0)
                                        <ul
                                            class="absolute z-10 w-full mt-1 overflow-auto border rounded-md shadow bg-base-100 max-h-60">
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
                            <x-form.label label="PIC" required />
                            <select {{ $isDisabled ? 'disabled' : '' }} wire:model.live="penanggungJawab"
                                class="w-full select select-xs select-bordered focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden">
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
                    <legend class="text-sm font-semibold card-title ">Tindakan Lanjutan</legend>

                    <!-- Deskripsi Tindakan -->
                    <fieldset class="fieldset md:col-span-1">
                        <x-form.label label="Deskripsi Tindakan" required />
                        <div wire:ignore>
                            <textarea id="ckeditor-action_description" class="w-full h-20 textarea textarea-bordered">{{ $action_description }}</textarea>
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
                                <input {{ $isDisabled ? 'disabled' : '' }} name="action_due_date" type="text"
                                    x-ref="tanggalInput2" wire:model.live="action_due_date"
                                    placeholder="Pilih Tanggal"
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
                                        onChange: (dates, str) => $wire.set('action_actual_close_date', str),
                                    });
                                }
                            }" x-init="initFlatpickr();
                            Livewire.hook('message.processed', () => initFlatpickr());"
                                x-ref="wrapper">
                                <input {{ $isDisabled ? 'disabled' : '' }} name="action_actual_close_date"
                                    type="text" x-ref="tanggalInput3" wire:model.live="action_actual_close_date"
                                    placeholder="Pilih Tanggal"
                                    class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs {{ $errors->has('action_actual_close_date') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}"
                                    readonly />
                            </div>
                            <x-label-error :messages="$errors->get('action_actual_close_date')" />
                        </fieldset>
                        <!-- Dilaporkan Oleh -->
                        <x-form.searchable-select-advanced label="PIC" placeholder="Cari Nama Pelapor..."
                            modelsearch="searchActResponsibility" modelid="action_responsible_id"
                            {{-- ID asli di DB --}} :options="$pelaporsAct" :showdropdown="$showActPelaporDropdown" {{-- Logic Manual --}}
                            :manualMode="$manualActPelaporMode" manualModelName="manualActPelaporName"
                            enableManualAction="enableManualActPelapor" addManualAction="addActPelaporManual"
                            clickaction="selectActPelapor" :disabled="$isDisabled" />
                    </div>
                    <!-- Tombol Tambah -->
                    <div class="flex justify-end ">
                        <flux:button size="xs" wire:click="addActionHazard"
                            class="{{ $isDisabled ? 'btn btn-disabled cursor-not-allowed' : '' }}"
                            icon:trailing="add-icon" variant="primary">Tambah</flux:button>
                    </div>
                    <!-- List Actions -->
                    <div class="my-2 divider">Daftar Tindakan</div>
                    <ul class="space-y-2">
                        @forelse($actionHazards as $act)
                            <li class="p-2 border rounded-md shadow-sm bg-base-100">
                                <div class="flex flex-col gap-1 md:flex-row md:justify-between">
                                    <div class="w-full rounded md:maxw-96 xl:max-w-1/2 bg-base-200">
                                        <span class="font-semibold">{!! $act['description'] !!}</span>
                                    </div>
                                    <div class="flex flex-col gap-1 md:flex-row md:items-center">
                                        <span class="text-[9px] badge badge-primary badge-outline">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="lucide lucide-clock-check-icon lucide-clock-check">
                                                <path d="M12 6v6l4 2" />
                                                <path d="M22 12a10 10 0 1 0-11 9.95" />
                                                <path d="m22 16-5.5 5.5L14 19" />
                                            </svg>
                                            Batas Waktu:
                                            {{ $act['due_date'] ? \Carbon\Carbon::parse($act['due_date'])->timezone('Asia/Makassar')->format('d-m-Y') : '' }}</span>
                                        <span class="text-[9px] badge badge-info badge-outline">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="lucide lucide-clock-check-icon lucide-clock-check">
                                                <path d="M12 6v6l4 2" />
                                                <path d="M22 12a10 10 0 1 0-11 9.95" />
                                                <path d="m22 16-5.5 5.5L14 19" />
                                            </svg>
                                            Tgl Selesai:
                                            {{ $act['actual_close_date'] ? \Carbon\Carbon::parse($act['actual_close_date'])->timezone('Asia/Makassar')->format('d-m-Y') : '-' }}</span>
                                        <span class="text-[9px] badge badge-success badge-outline">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="lucide lucide-user-check-icon lucide-user-check">
                                                <path d="m16 11 2 2 4-4" />
                                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                                <circle cx="9" cy="7" r="4" />
                                            </svg>
                                            PIC:
                                            {{ optional(\App\Models\User::find($act['responsible_id']))->name ?? '-' }}</span>
                                        <div class="flex gap-2 mt-1 md:mt-0">

                                            <flux:button variant="subtle" size="xs"
                                                class="{{ $isDisabled ? 'btn btn-disabled cursor-not-allowed' : '' }}"
                                                wire:click="loadEditAction({{ $act['id'] }})"
                                                icon="pencil-square">
                                            </flux:button>

                                            <flux:button variant="danger" size="xs"
                                                class="{{ $isDisabled ? 'btn btn-disabled cursor-not-allowed' : '' }}"
                                                wire:click="removeAction({{ $act['id'] }})"
                                                wire:confirm="Yakin hapus tindakan ini?" icon="trash">
                                            </flux:button>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="p-2 border rounded-md shadow-sm bg-base-100">
                                <div class="text-center text-gray-500">Tidak ada tindakan</div>
                            </li>
                        @endforelse
                    </ul>

                </fieldset>

                <div class="flex flex-col-reverse gap-2 my-2 md:flex-row">

                    {{-- Kolom Likelihood & Consequence --}}
                    <div class="space-y-4 md:grow">
                        {{-- Consequence --}}
                        <fieldset class="fieldset ">
                            <x-form.label label="Consequence" required />
                            <select {{ $isDisabled ? 'disabled' : '' }} wire:model.live="consequence_id"
                                class="w-full select select-xs md:select-xs select-bordered md:max-w-md focus:ring-1 focus:border-info focus:ring-info focus:outline-none">
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
                            <select {{ $isDisabled ? 'disabled' : '' }} wire:model.live="likelihood_id"
                                class="w-full select select-xs md:select-xs select-bordered md:max-w-md focus:ring-1 focus:border-info focus:ring-info focus:outline-none">
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
                                    <th class="border-1">Likelihooc ↓ / Consequence →</th>
                                    @foreach ($consequences as $c)
                                        <th class="rotate_text border-1">{{ $c->name }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($likelihoods as $l)
                                    <tr class="text-center text-[9px]">

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
                                                    class="btn btn-square btn-xs   {{ $isDisabled ? 'btn btn-disabled' : "$color" }}">{{ Str::upper(substr($severity, 0, 1)) }}</span>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
                @if ($RiskAssessment != null)
                    <table class="table mb-4 table-xs">

                        <tr>
                            <th class="w-40 text-xs border border-slate-200">Potential Risk Rating</th>
                            <td class="pl-2 text-xs border border-slate-200">
                                {{ $RiskAssessment->name }}</td>
                        </tr>
                        <tr>
                            <th class="w-40 text-xs border border-slate-200">Notify</th>
                            <td class="pl-2 text-xs border border-slate-200">
                                {{ $RiskAssessment->reporting_obligation }}</td>
                        </tr>
                        <tr>
                            <th class="w-40 text-xs border border-slate-200">Deadline</th>
                            <td class="pl-2 text-xs border border-slate-200">{{ $RiskAssessment->notes }}</td>
                        </tr>
                        <tr>
                            <th class="w-40 text-xs border border-slate-200">Coordinator</th>
                            <td class="pl-2 text-xs border border-slate-200">
                                {{ $RiskAssessment->coordinator }}
                            </td>
                        </tr>


                    </table>
                @endif
            </div>
        </x-tab-hazard.layout>
    </form>
    <!-- Modal Edit ActionHazard -->
    <div x-data="{ open: false }" x-on:open-edit-action.window="open = true" x-show="open" x-transition
        class="fixed inset-0 z-50 flex items-center justify-center modal modal-open bg-black/50"
        style="display:none;">
        <div class="w-11/12 max-w-4xl modal-box !overflow-visible">
            <h3 class="mb-4 text-lg font-bold">Edit Tindakan Lanjutan </h3>

            {{-- === Form Update === --}}
            <fieldset class="fieldset md:col-span-1">
                <x-form.label label="Deskripsi Tindakan" required />
                <div wire:ignore>
                    <textarea id="ckeditor-edit-action" class="w-full h-20 textarea textarea-bordered">{{ $edit_action_description }}</textarea>
                </div>
                <input type="hidden" wire:model.live="edit_action_description" id="edit_action_description">
                <x-label-error :messages="$errors->get('edit_action_description')" />
            </fieldset>

            <div class="grid items-end grid-cols-1 gap-4 mt-4 md:grid-cols-3">
                {{-- Batas Waktu --}}
                <fieldset class="fieldset">
                    <x-form.label label="Batas Waktu Penyelesaian" required />
                    <div class="relative" wire:ignore x-data="{
                        fp: null,
                        initFlatpickr() {
                            if (this.fp) this.fp.destroy();
                            this.fp = flatpickr(this.$refs.dueEdit, {
                                disableMobile: true,
                                dateFormat: 'd-m-Y',
                                onChange: (dates, str) => $wire.set('edit_action_due_date', str),
                            });
                        }
                    }" x-init="initFlatpickr();
                    Livewire.hook('message.processed', () => initFlatpickr());

                    // ==== Tambahan: isi ulang saat modal dibuka ====
                    Livewire.on('open-edit-action', () => {
                        // Ambil value terbaru dari Livewire
                        const val = @this.get('edit_action_due_date');
                        // setDate akan menyesuaikan input + kalender
                        if (val && this.fp) {
                            this.fp.setDate(val, true, 'd-m-Y');
                        }
                    });"
                        x-ref="wrapper">
                        <input type="text" x-ref="dueEdit" wire:model.live="edit_action_due_date"
                            class="w-full input input-bordered input-xs" placeholder="Pilih Tanggal" readonly />
                    </div>
                    <x-label-error :messages="$errors->get('edit_action_due_date')" />
                </fieldset>


                {{-- Actual Close Date --}}
                <fieldset class="fieldset">
                    <x-form.label label="Tanggal Penyelesaian Tindakan" required />
                    <div class="relative" wire:ignore x-data="{
                        fp: null,
                        initFlatpickr() {
                            if (this.fp) this.fp.destroy();
                            this.fp = flatpickr(this.$refs.closeEdit, {
                                disableMobile: true,
                                dateFormat: 'd-m-Y',

                                onChange: (dates, str) => $wire.set('edit_action_actual_close_date', str),
                            });
                        }
                    }" x-init="initFlatpickr();
                    Livewire.hook('message.processed', () => initFlatpickr());"
                        x-ref="wrapper">
                        <input type="text" x-ref="closeEdit" wire:model.live="edit_action_actual_close_date"
                            class="w-full input input-bordered input-xs" placeholder="Pilih Tanggal" readonly />
                    </div>
                    <x-label-error :messages="$errors->get('edit_action_actual_close_date')" />
                </fieldset>

                {{-- Responsible Person --}}
                <x-form.searchable-select-advanced label="PIC" placeholder="Cari Nama Pelapor..."
                    modelsearch="searchActResponsibilityEdit" modelid="action_responsible_id" {{-- ID asli di DB --}}
                    :options="$pelaporsActEdit" :showdropdown="$showActPelaporDropdownEdit" {{-- Logic Manual --}} :manualMode="$manualActPelaporModeEdit"
                    manualModelName="manualActPelaporNameEdit" enableManualAction="manualActPelaporModeEdit"
                    addManualAction="addActPelaporManualEdit" clickaction="selectActPelaporEdit" :disabled="$isDisabled" />
            </div>

            <!-- Aksi -->
            <div class="flex justify-end gap-2 mt-4 modal-action">
                <!-- Update tidak menutup modal -->
                <flux:button variant="primary" size="xs" type="button" wire:click="updateAction"
                    x-on:click="$wire.call('updateAction').then(() => { open = false })">
                    Update
                </flux:button>
                <!-- Batal -->
                <flux:button variant="outline" size="xs" type="button" x-on:click="open = false">
                    Batal
                </flux:button>

            </div>
        </div>
    </div>
    @push('scripts')
        <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
        <script>
            const isDisabled = @json($isDisabled);
            console.log('Initial isDisabled:', isDisabled);

            document.addEventListener('livewire:navigated', () => {
                ClassicEditor
                    .create(document.querySelector('#ckeditor-description'), {
                        toolbar: ['bold', 'italic', 'bulletedList', 'numberedList', '|', 'undo', 'redo'],
                        removePlugins: ['ImageUpload', 'EasyImage', 'MediaEmbed']
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
                            , 'bold', 'italic', 'bulletedList', 'numberedList', '|', 'undo', 'redo'
                        ],
                        removePlugins: ['ImageUpload', 'EasyImage', 'MediaEmbed'] // buang plugin gambar
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
                            , 'bold', 'italic', 'bulletedList', 'numberedList', '|', 'undo', 'redo'
                        ],
                        removePlugins: ['ImageUpload', 'EasyImage', 'MediaEmbed'] // buang plugin gambar
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
                            , 'bold', 'italic', 'bulletedList', 'numberedList', '|', 'undo', 'redo'
                        ],
                        removePlugins: ['ImageUpload', 'EasyImage', 'MediaEmbed'] // buang plugin gambar
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
</section>
