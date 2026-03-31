<section class="w-full">
    <div class="flex items-center justify-start pb-2 border-b">
        {{-- Kontrol di sisi kanan tanpa judul --}}
        <div class="flex gap-2">
            <x-button.btn-tooltip color="primary" icon="add" href="{{ route('incident-create') }}" tooltip="Tambah Data" position="top md:right" />
        </div>
    </div>
    <x-incident.layout>

        <div class="overflow-x-auto border rounded-xl border-base-300 bg-base-100">
            <table class="table table-sm table-pin-rows">
                <thead>
                    <tr class="border-b bg-base-200 text-base-content border-base-300">
                        <th class="w-10 text-center">No</th>

                        {{-- Nomor Referensi --}}
                        <th class="w-40">
                            {{ __('Nomor Referensi') }}
                            <button class="btn btn-ghost btn-xs" popovertarget="my-popover">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="{{ !empty($search) ? 'text-blue-600' : '' }}">
                                    <circle cx="11" cy="11" r="8" />
                                    <path d="m21 21-4.3-4.3" />
                                </svg>
                            </button>
                            <div popover id="my-popover" class="p-3 shadow-xl opacity-0 rounded-box bg-base-100 w-60 starting:open:opacity-0" ">
                                <x-form.input-text type=" text" wire:model.live.debounce.300ms="search" placeholder="Cari nomor..." class="w-full input input-bordered input-sm" />

                        </th>

                        {{-- Tanggal & Waktu --}}
                        <th class="w-48">{{ __('Tanggal & Waktu') }}</th>

                        {{-- Deskripsi --}}
                        <th>{{ __('Deskripsi Insiden') }}</th>

                        {{-- Divisi Penanggung Jawab (Department) --}}
                        <th>
                            <div class="flex items-center gap-1">
                                {{ __('Divisi') }}
                                <button class="btn btn-ghost btn-xs" popovertarget="pop_dept" style="anchor-name:--pop_dept">
                                    <x-icon.icon-filter :active="!empty($filterDept)" />
                                </button>
                                <ul popover id="pop_dept" class="p-2 overflow-y-auto shadow-lg menu w-60 rounded-box bg-base-100 max-h-60" style="position-anchor:--pop_dept; inset-area: bottom span-right;">
                                    @foreach ($filterOptions['departments'] as $dept)
                                    <li>
                                        <label class="flex items-center p-1 cursor-pointer">
                                            <input type="checkbox" wire:model.live="filterDept" value="{{ $dept->id }}" class="checkbox checkbox-xs checkbox-primary">
                                            <span class="ml-2 text-xs">{{ $dept->name }}</span>
                                        </label>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </th>

                        {{-- Tipe Insiden (Event Type) --}}
                        <th class="w-40">
                            <div class="flex items-center gap-1">
                                {{ __('Tipe') }}
                                <button class="btn btn-ghost btn-xs" popovertarget="pop_event" style="anchor-name:--pop_event">
                                    <x-icon.icon-filter :active="!empty($filterEventType)" />
                                </button>
                                <ul popover id="pop_event" class="p-2 overflow-y-auto shadow-lg menu w-52 rounded-box bg-base-100 max-h-60" style="position-anchor:--pop_event; inset-area: bottom span-right;">
                                    @foreach ($filterOptions['eventTypes'] as $type)
                                    <li>
                                        <label class="flex items-center p-1 cursor-pointer">
                                            <input type="checkbox" wire:model.live="filterEventType" value="{{ $type->id }}" class="checkbox checkbox-xs checkbox-primary">
                                            <span class="ml-2 text-xs">{{ $type->name }}</span>
                                        </label>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </th>

                        {{-- Klasifikasi (Event Sub Type) --}}
                        <th class="w-32 text-center">
                            <div class="flex items-center justify-center gap-1">
                                {{ __('Klasifikasi') }}
                                <button class="btn btn-ghost btn-xs" popovertarget="pop_subtype" style="anchor-name:--pop_subtype">
                                    <x-icon.icon-filter :active="!empty($filterEventSubType)" />
                                </button>
                                <ul popover id="pop_subtype" class="p-2 overflow-y-auto shadow-lg menu w-52 rounded-box bg-base-100 max-h-60" style="position-anchor:--pop_subtype; inset-area: bottom span-right;">
                                    @foreach ($filterOptions['eventSubTypes'] as $sub)
                                    <li>
                                        <label class="flex items-center p-1 cursor-pointer">
                                            <input type="checkbox" wire:model.live="filterEventSubType" value="{{ $sub->id }}" class="checkbox checkbox-xs checkbox-primary">
                                            <span class="ml-2 text-xs">{{ $sub->name }}</span>
                                        </label>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </th>

                        {{-- Status (Enum) --}}
                        <th class="w-32 text-center">
                            <div class="flex items-center justify-center gap-1">
                                {{ __('Status') }}
                                <button class="btn btn-ghost btn-xs" popovertarget="pop_status" style="anchor-name:--pop_status">
                                    <x-icon.icon-filter :active="!empty($filterStatus)" />
                                </button>
                                <ul popover id="pop_status" class="p-2 shadow-lg menu w-44 rounded-box bg-base-100" style="position-anchor:--pop_status; inset-area: bottom span-right;">
                                    @foreach (['Open', 'In Progress', 'Action Required', 'Closed'] as $status)
                                    <li>
                                        <label class="flex items-center p-1 cursor-pointer">
                                            <input type="checkbox" wire:model.live="filterStatus" value="{{ $status }}" class="checkbox checkbox-xs checkbox-primary">
                                            <span class="ml-2 text-xs">{{ $status }}</span>
                                        </label>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-base-200">
                    @forelse($incidents as $index => $item)
                    <tr class="transition-colors hover:bg-base-200/50">
                        <td class="font-mono text-xs text-center opacity-50">
                            {{ ($incidents->currentPage() - 1) * $incidents->perPage() + $loop->iteration }}
                        </td>

                        {{-- Nomor Referensi --}}
                        <td class="text-xs font-bold tracking-wider uppercase text-primary">
                            <button class="btn btn-sm btn-info btn-link" wire:click="editIncident({{ $item->id }})">{{ $item->report_number }}</button>
                        </td>

                        {{-- Tanggal & Waktu --}}
                        <td>
                            <div class="flex flex-col">
                                <span class="text-xs font-semibold">{{ $item->date_time->format('d M Y') }}</span>
                                <span class="text-[10px] opacity-50">{{ $item->date_time->format('H:i') }} WITA</span>
                            </div>
                        </td>

                        {{-- Judul & Lokasi --}}
                        <td>
                            <div class="flex flex-col max-w-xs md:max-w-md">
                                <span class="text-xs font-bold truncate" title="{{ $item->title }}">
                                    {{ Str::limit($item->description, 50) }}
                                </span>
                                <span class="text-[10px] italic opacity-60 flex items-center gap-1">
                                    <x-icon name="map-pin" class="w-3 h-3" />
                                    {{ $item->location?->name}} ->spesifik: {{ $item->location_specific }}
                                </span>
                            </div>
                        </td>
                        {{-- Divisi Penanggung Jawab --}}
                        <td>
                            <div class="badge badge-outline badge-xs py-2 px-2 font-medium text-[10px] uppercase">
                                {{ $item->department?->department_name ?? $item->contractor?->contractor_name }}
                            </div>
                        </td>

                        {{-- Kategori --}}
                        <td>
                            <div class="badge badge-outline badge-xs py-2 px-2 font-medium text-[10px] uppercase">
                                {{ $item->EventType?->event_type_name ?? 'N/A' }}
                            </div>
                        </td>

                        {{-- Klasifikasi Warna --}}
                        <td class="text-center">
                            @php
                            $riskColor = match($item->risk?->rating_name) {
                            'Ekstrem' => 'bg-error text-error-content',
                            'Tinggi' => 'bg-secondary text-secondary-content',
                            'Sedang' => 'bg-warning text-warning-content',
                            default => 'bg-success text-success-content',
                            };
                            @endphp
                            <div class="tooltip" data-tip="Actual Risk: {{ $item->risk?->rating_name }}">
                                <span class="inline-block w-3 h-3 rounded-full {{ $riskColor }}"></span>
                            </div>
                        </td>

                        {{-- Status --}}
                        <td class="text-center">
                            <div @class([ 'badge badge-xs py-2 px-2 font-bold text-[9px] uppercase' , 'badge-success'=> $item->status === 'Closed',
                                'badge-error' => $item->status === 'Open',
                                'badge-warning' => $item->status === 'Action Required',
                                'badge-info' => $item->status === 'In Progress',
                                ])>
                                {{ $item->status }}
                            </div>
                        </td>


                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-20 text-center">
                            <div class="flex flex-col items-center opacity-30">
                                <x-icon name="clipboard-list" class="w-16 h-16 mb-2" />
                                <p class="text-lg font-bold">Data Insiden Tidak Ditemukan</p>
                                <p class="text-xs">Coba sesuaikan filter atau tambahkan laporan baru.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $incidents->links() }}
        </div>
    </x-incident.layout>
</section>