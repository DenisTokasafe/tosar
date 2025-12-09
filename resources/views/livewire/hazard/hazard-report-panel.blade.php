<section class="w-full">
    <x-toast />
    @include('partials.header-hazard')
    <div class="flex flex-col md:flex-row md:justify-between items-start md:items-center">
        <div class="mb-4 md:mb-0 flex items-center gap-2">
            <div class="tooltip tooltip-right  mb-0.5" data-tip="Tambah Hazard">
                <a href="{{ route('hazard-form') }}" class="btn btn-square btn-primary btn-xs">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                        <path fill-rule="evenodd" d="M12 3.75a.75.75 0 0 1 .75.75v6.75h6.75a.75.75 0 0 1 0 1.5h-6.75v6.75a.75.75 0 0 1-1.5 0v-6.75H4.5a.75.75 0 0 1 0-1.5h6.75V4.5a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd" />
                    </svg>
                </a>
            </div>
            @livewire('hazard.import-hazard-reports-modal')
        </div>
        <div class="w-full flex flex-col md:flex-row md:max-w-md  gap-4">
            <fieldset class="fieldset w-full">
                <x-form.label label="Cari Pelapor" required />
                <div class="relative">
                    <!-- Input Search -->
                    <input name="searchLocation" type="text" wire:model.live.debounce.300ms="searchPelapor" placeholder="Cari Pelapor..." class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs {{ $errors->has('location_id') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}" />
                    <!-- Dropdown hasil search -->
                    @if($showPelaporDropdown && count($pelapors) > 0)
                    <ul class="absolute z-10 bg-base-100 border rounded-md w-full mt-1 max-h-60 overflow-auto shadow">
                        <!-- Spinner ketika klik -->
                        <div wire:loading wire:target="selectPelapor" class="p-2 text-center">
                            <span class="loading loading-spinner loading-sm text-secondary"></span>
                        </div>
                        @foreach($pelapors as $user)
                        <li wire:click="selectPelapor({{ $user->id }}, '{{ $user->name }}')" class="px-3 py-2 cursor-pointer hover:bg-base-200">
                            {{ $user->name }}
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </fieldset>
            <fieldset class="fieldset w-full">
                <x-form.label label="rentang tanggal" required />
                <div class="relative" wire:ignore x-data="{
                                fp: null,
                                initFlatpickr() {
                                    if (this.fp) this.fp.destroy();
                                    this.fp = flatpickr(this.$refs.tanggalInput2, {
                                        disableMobile: true,
                                        enableTime: false,
                                        altInput: true,
                                        altFormat: 'd-M-Y',
                                        dateFormat: 'd-m-Y',
                                        mode: 'range', // 👈 Tambahkan opsi ini
                                        onChange: (dates, str) => $wire.set('action_due_date', str),
                                    });
                                }
                            }" x-init="initFlatpickr(); Livewire.hook('message.processed', () => initFlatpickr());" x-ref="wrapper">
                    <input name="action_due_date" type="text" x-ref="tanggalInput2" wire:model.live="action_due_date" placeholder="Pilih Tanggal" class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs {{ $errors->has('action_due_date') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}" readonly />
                </div>
                <x-label-error :messages="$errors->get('action_due_date')" />
            </fieldset>
        </div>
    </div>
    <div class="overflow-x-auto mt-4">
        <table class="table table-xs border text-xs px-2">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border">#</th>
                    <th class="border">reference</th>
                    <th class="border">Tipe Bahaya</th>
                    <th class="border">Jenis Bahaya
                        <button class="btn btn-ghost btn-xs" popovertarget="eventSubType" style="anchor-name:--eventSubType">
                            <span class="text-blue-600 text-xs">
                                @if(empty($filterEventSubType))
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-list-filter-icon lucide-list-filter">
                                    <path d="M2 5h20" />
                                    <path d="M6 12h12" />
                                    <path d="M9 19h6" />
                                </svg>
                                @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-down-wide-narrow-icon lucide-arrow-down-wide-narrow">
                                    <path d="m3 16 4 4 4-4" />
                                    <path d="M7 20V4" />
                                    <path d="M11 4h10" />
                                    <path d="M11 8h7" />
                                    <path d="M11 12h4" />
                                </svg>
                                @endif
                            </span>
                        </button>
                        <ul class="dropdown menu w-52 rounded-box bg-base-100 shadow-lg p-2 max-h-60 overflow-y-auto" popover id="eventSubType" style="position-anchor:--eventSubType; inset-area: bottom span-right;">
                            {{-- Loop Department --}}
                            @foreach ($filterOptions['EventSubType'] as $event_sub_type)
                            <li>
                                <label class="flex items-center cursor-pointer hover:bg-gray-100 p-1 rounded">
                                    <input type="checkbox" wire:model.live="filterEventSubType" value="{{ $event_sub_type->id }}" class="form-checkbox text-blue-600 rounded">
                                    <span class="ml-2 text-xs capitalize">{{ $event_sub_type->event_sub_type_name }}</span>
                                </label>
                            </li>
                            @endforeach
                        </ul>
                    </th>
                    <th class="border">Divisi Penanggung Jawab
                        <button class="btn btn-ghost btn-xs" popovertarget="divisi_dept" style="anchor-name:--divisi_dept">
                            {{-- Ikon Filter: Tampilkan jika filterDepartment tidak kosong --}}
                            <span class="text-blue-600 text-xs">
                                @if(empty($filterDepartment) && empty($filterContractor))
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-list-filter-icon lucide-list-filter">
                                    <path d="M2 5h20" />
                                    <path d="M6 12h12" />
                                    <path d="M9 19h6" />
                                </svg>
                                @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-down-wide-narrow-icon lucide-arrow-down-wide-narrow">
                                    <path d="m3 16 4 4 4-4" />
                                    <path d="M7 20V4" />
                                    <path d="M11 4h10" />
                                    <path d="M11 8h7" />
                                    <path d="M11 12h4" />
                                </svg>
                                @endif
                            </span>
                        </button>
                        <ul class="dropdown menu w-52 rounded-box bg-base-100 shadow-lg p-2 max-h-60 overflow-y-auto" popover id="divisi_dept" style="position-anchor:--divisi_dept; inset-area: bottom span-right;">
                            {{-- Loop Department --}}
                            @foreach ($filterOptions['Department'] as $dept)
                            <li>
                                <label class="flex items-center cursor-pointer hover:bg-gray-100 p-1 rounded">
                                    <input type="checkbox" wire:model.live="filterDepartment" value="{{ $dept->id }}" class="form-checkbox text-blue-600 rounded">
                                    <span class="ml-2 text-xs capitalize">{{ $dept->department_name }}</span>
                                </label>
                            </li>
                            @endforeach
                            {{-- Loop Contractor --}}
                            @foreach ($filterOptions['Contractors'] as $cont)
                            <li>
                                <label class="flex items-center cursor-pointer hover:bg-gray-100 p-1 rounded">
                                    <input type="checkbox" wire:model.live="filterContractor" value="{{ $cont->id }}" class="form-checkbox text-blue-600 rounded">

                                    <span class="ml-2 text-xs capitalize">{{ $cont->contractor_name }}</span>
                                </label>
                            </li>
                            @endforeach
                        </ul>
                    </th>
                    <th class="border relative">
                        Status
                        <button class="btn btn-ghost btn-xs" popovertarget="popover-1" style="anchor-name:--anchor-1">

                            <span class="text-blue-600 text-xs">
                                @if(empty($filterStatus))
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-list-filter-icon lucide-list-filter">
                                    <path d="M2 5h20" />
                                    <path d="M6 12h12" />
                                    <path d="M9 19h6" />
                                </svg>
                                @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-down-wide-narrow-icon lucide-arrow-down-wide-narrow">
                                    <path d="m3 16 4 4 4-4" />
                                    <path d="M7 20V4" />
                                    <path d="M11 4h10" />
                                    <path d="M11 8h7" />
                                    <path d="M11 12h4" />
                                </svg>
                                @endif
                            </span>
                        </button>
                        {{-- Dropdown Menu --}}
                        <ul class="dropdown menu w-52 rounded-box bg-base-100 shadow-sm" popover id="popover-1" style="position-anchor:--anchor-1">

                            {{-- Loop Isi Dropdown --}}
                            @foreach ($availableStatuses as $status)
                            <li>
                                <label class="flex items-center mb-1 cursor-pointer hover:bg-gray-100 p-1 rounded">
                                    <input type="checkbox" wire:model.live="filterStatus" value="{{ $status }}" class="form-checkbox text-blue-600 rounded">
                                    <span class="ml-2 text-xs capitalize">{{ str_replace('_', ' ', $status) }}</span>
                                </label>
                            </li>
                            @endforeach
                        </ul>
                    </th>
                    <th class="border">Pelapor</th>
                    <th class="border">Tanggal</th>
                    <th class="flex-col border text-center">
                        <p>Action</p>
                        <p>Total/Open</p>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reports as $no => $report)
                <tr class="hover:bg-gray-50">
                    <td class="border">{{ $reports->firstItem()+$no }}</td>
                    <td class="border">
                        @can('view', $report)
                        <a href="{{ route('hazard-detail', $report) }}" class="text-blue-600 text-xs hover:underline">{{ $report->no_referensi  ?? '-' }}</a>
                        @else
                        <span class="text-gray-400 text-xs cursor-not-allowed">{{ $report->no_referensi  ?? '-' }}</span>
                        @endcan
                    </td>
                    <td class="border">{{ $report->eventType->event_type_name  ?? '-' }}</td>
                    <td class="border">{{ $report->eventSubType->event_sub_type_name  ?? '-' }}</td>
                    <td class="border">{{ $report->department->department_name ?? $report->contractor->contractor_name }}</td>
                    <td class="border">
                        <span class="text-xs uppercase px-2  rounded
                                @if($report->status == 'submitted') bg-yellow-100 text-yellow-800
                                @elseif($report->status == 'in_progress') bg-blue-100 text-blue-800
                                @elseif($report->status == 'pending') bg-orange-100 text-orange-800
                                @elseif($report->status == 'closed') bg-green-100 text-green-800
                                @endif">
                            {{ str_replace('_', ' ', $report->status) }}
                        </span>
                    </td>
                    <td class="border">{{ $report->pelapor->name ?? $report->manualPelaporName }}</td>
                    <td class="border">{{ \Carbon\Carbon::parse($report->tanggal)->format('d M Y') }}</td>
                    <td class="border text-center">
                        {{ $report->total_due_dates }} / {{ $report->pending_actual_closes }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-gray-500 py-4">Tidak ada laporan ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $reports->links() }}
</section>
