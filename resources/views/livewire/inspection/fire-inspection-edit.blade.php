<section class="w-full">
    <x-toast />

    {{-- Header & Breadcrumbs --}}
    <div class="flex justify-start" wire:ignore>
        @php
            // Menggunakan layout edit jika ID sudah ada
            $layoutComponent = $inspectionId ? 'tabs-fire.layout-edit' : 'tabs-fire.layout';
            $currentRoute = Route::currentRouteName();
            $currentStatus = strtolower($status ?? 'draft');
            $isDisabled = in_array($currentStatus, ['closed', 'cancelled']);
        @endphp

        @if (Breadcrumbs::exists($currentRoute))
            {!! Breadcrumbs::render($currentRoute, isset($inspectionId) ? $inspectionId : null) !!}
        @endif
    </div>

    {{-- Info Card: Status & Workflow Actions --}}
    @if ($inspectionId)
        <div class="mb-2 border border-gray-200 shadow-md card bg-base-100">
            <div class="px-4 py-2 card-body">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <label class="label">
                            <span class="text-xs font-semibold label-text">Status :</span>
                        </label>
                        <span class="badge-xs italic badge {{ $this->getRandomBadgeColor($status) }} capitalize px-3 py-2">
                            {{ $status }}
                        </span>
                    </div>

                    <div wire:ignore class="flex items-center gap-2">
                        <flux:tooltip content="Download PDF" position="top">
                            <flux:button wire:click="exportPDF({{ $inspectionId }})" size="xs" icon="document-arrow-down" variant="primary" color="blue" />
                        </flux:tooltip>
                        <flux:tooltip content="Lihat Riwayat" position="left">
                            <flux:button size="xs" variant="accent" icon='clock' onclick="audit_modal.showModal()" />
                        </flux:tooltip>
                    </div>
                </div>

                {{-- Workflow Transitions --}}
                <div class="flex flex-col gap-4 mt-2 md:flex-row md:items-end">
                    <div class="w-full max-w-xs">
                        <label class="py-1 label">
                            <span class="text-[10px] font-bold uppercase text-gray-500">Transition To</span>
                        </label>
                        <select wire:model.live="proceedTo" class="w-full select select-xs select-bordered">
                            <option value="">-- Pilih Aksi --</option>
                            @foreach ($availableTransitions as $label => $targetStatus)
                                <option value="{{ $targetStatus }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if (in_array($proceedTo, ['Review', 'Approved']))
                        <div class="w-full max-w-xs">
                            <label class="py-1 label">
                                <span class="text-[10px] font-bold uppercase text-gray-500">Assign To</span>
                            </label>
                            <select wire:model="assignTo" class="w-full select select-xs select-bordered">
                                <option value="">-- Pilih User --</option>
                                @foreach ($approverList as $user)
                                    <option value="{{ $user['id'] }}">{{ $user['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="flex items-end">
                        <flux:button size="xs" wire:click="processStatusChange('{{ $proceedTo }}')" icon-trailing="paper-airplane" variant="primary" wire:loading.attr="disabled">
                            Kirim Aksi
                        </flux:button>
                    </div>
                </div>

                {{-- Modal Audit Trail (Spatie Activity Log) --}}
                <dialog class="modal" id="audit_modal" role="dialog">
                    <div class="max-w-4xl modal-box">
                        <form method="dialog"><button class="absolute btn btn-sm btn-circle btn-ghost right-2 top-2">✕</button></form>
                        <h3 class="mb-4 text-lg font-bold">Audit Trail / Riwayat Laporan</h3>
                        <div class="max-h-[60vh] overflow-y-auto">
                            <table class="table border table-xs table-pin-rows">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="border">Tanggal</th>
                                        <th class="border">User</th>
                                        <th class="border">Perubahan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $activities = Spatie\Activitylog\Models\Activity::where('subject_type', \App\Models\FireProtection::class)
                                            ->where('subject_id', $inspectionId)->latest()->get();
                                    @endphp
                                    @forelse($activities as $activity)
                                        <tr>
                                            <td class="text-[10px]">{{ $activity->created_at->format('d-m-Y H:i') }}</td>
                                            <td class="italic font-semibold">{{ $activity->causer->name ?? 'System' }}</td>
                                            <td>
                                                <span class="text-blue-600 text-[10px] block font-bold uppercase">{{ $activity->description }}</span>
                                                @foreach ($activity->changes['attributes'] ?? [] as $field => $new)
                                                    @continue(in_array($field, ['updated_at', 'id']))
                                                    <div class="text-[10px] border-l-2 border-gray-200 pl-2 mb-1">
                                                        <strong>{{ ucfirst($field) }}</strong>:
                                                        <span class="text-red-500 line-through">{{ $activity->changes['old'][$field] ?? '-' }}</span>
                                                        <span> → </span>
                                                        <span class="text-green-600">{{ $new }}</span>
                                                    </div>
                                                @endforeach
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="py-4 text-center">Belum ada riwayat.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </dialog>
            </div>
        </div>
    @endif

    {{-- Main Content --}}
    <x-dynamic-component :component="$layoutComponent" :heading="$inspectionId ? '' : 'Buat Laporan Inspeksi Baru'" :subheading="$inspectionId ? '' : 'Fire Protection Checklist'">
        <form wire:submit.prevent="update" class="overflow-hidden bg-white border border-gray-200 rounded-lg shadow-xl">

            {{-- Bagian Header Form --}}
            <div class="p-6 border-b border-gray-200 bg-gray-50">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="space-y-4">
                        {{-- Datepicker Flatpickr --}}
                        <fieldset class="relative fieldset">
                            <x-form.label label="Tanggal / Date" required />
                            <div wire:ignore x-data="{
                                date: @entangle('inspection_date'),
                                init() {
                                    flatpickr($refs.dateInput, {
                                        altInput: true, altFormat: 'd F Y', dateFormat: 'Y-m-d',
                                        defaultDate: this.date,
                                        onChange: (selectedDates, dateStr) => { this.date = dateStr }
                                    })
                                }
                            }">
                                <input {{ $isDisabled ? 'disabled' : '' }} type="text" x-ref="dateInput" class="w-full input input-bordered input-xs" readonly />
                            </div>
                        </fieldset>

                        <x-form.searchable-dropdown label="Lokasi / Location" required modelsearch="searchLocation"
                            :disabled="$isDisabled" modelid="area" :options="$locations" :showdropdown="$show_location"
                            clickaction="selectLocation" namedb="name" />
                    </div>

                    <div class="space-y-4">
                        <x-form.input-text :disabled="$isDisabled" label="Lokasi Spesifik" model="location" placeholder="Detail lokasi..." required />

                        <fieldset class="fieldset">
                            <x-form.label label="Jenis Alat" />
                            <input type="text" class="w-full bg-gray-100 input input-bordered input-xs" value="{{ $type }}" disabled />
                        </fieldset>
                    </div>
                </div>
            </div>

            {{-- Bagian Inspector --}}
            <div class="p-6 bg-white border-b">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-gray-700 uppercase">Petugas Inspeksi</h3>
                    <button type="button" wire:click="addInspector" class="btn btn-xs btn-info {{ $isDisabled ? 'btn-disabled' : '' }}">
                        + Tambah Petugas
                    </button>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    @foreach ($inspectors as $index => $inspector)
                        <div class="flex items-center space-x-2" wire:key="ins-{{ $index }}">
                            <span class="w-8 text-xs font-bold text-gray-400">{{ $index + 1 }}.</span>
                            <div class="flex-1">
                                <x-form.searchable-select-advanced
                                    :disabled="$isDisabled"
                                    label="Nama Petugas"
                                    modelsearch="searchPetugas.{{ $index }}"
                                    modelid="inspectors.{{ $index }}.name"
                                    :options="$pelaporsAct"
                                    :showdropdown="$showDropdownPetugas[$index] ?? false"
                                    clickaction="selectActPelapor" />
                            </div>
                            @if (count($inspectors) > 1)
                                <flux:button wire:click="removeInspector({{ $index }})" size="xs" icon="trash" variant="danger" :disabled="$isDisabled" />
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Bagian Item Check (Conditions) --}}
            <div class="p-6 bg-white">
                <h3 class="mb-4 text-sm font-bold text-gray-700 uppercase">Kondisi Alat / Checklist</h3>
                <div class="grid grid-cols-1 gap-4 p-4 border rounded-lg md:grid-cols-2 lg:grid-cols-3 bg-gray-50">
                    @if(isset($fields[$type]))
                        @foreach($fields[$type]['checks'] as $check)
                            <div class="flex items-center justify-between p-2 border-b">
                                <span class="text-xs font-medium">{{ $check }}</span>
                                <div class="flex gap-2">
                                    <label class="gap-1 cursor-pointer label">
                                        <input type="radio" wire:model="conditions.{{ $check }}" value="Good" class="radio radio-xs radio-success" {{ $isDisabled ? 'disabled' : '' }}>
                                        <span class="label-text text-[10px]">Good</span>
                                    </label>
                                    <label class="gap-1 cursor-pointer label">
                                        <input type="radio" wire:model="conditions.{{ $check }}" value="Bad" class="radio radio-xs radio-error" {{ $isDisabled ? 'disabled' : '' }}>
                                        <span class="label-text text-[10px]">Bad</span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- Footer / Submit --}}
            <div class="flex justify-end gap-2 p-6 bg-gray-50">
                <a href="{{ route('fire-protection.index') }}" class="btn btn-ghost btn-xs">Batal</a>
                @if(!$isDisabled)
                    <button type="submit" class="px-6 btn btn-primary btn-xs">Simpan Perubahan</button>
                @endif
            </div>
        </form>
    </x-dynamic-component>
</section>
