<section class="w-full">
    <div class="flex items-center justify-start pb-2 border-b">
        {{-- Kontrol di sisi kanan tanpa judul --}}
        <div class="flex gap-2">
            <x-button.btn-tooltip color="primary" icon="add" href="{{ route('incident-create') }}" tooltip="Tambah Data" position="top md:right" />
        </div>
    </div>
    <x-incident.layout>

        <div class="overflow-x-auto border rounded-xl border-base-300 bg-base-100 shadow-sm">
            <table class="table table-sm table-pin-rows w-full">
                <thead>
                    <tr class="bg-base-200 text-base-content border-b border-base-300">
                        <th class="text-center w-12">No</th>

                        {{-- Kolom Nomor Referensi - Lebar Tetap --}}
                        <th class="whitespace-nowrap w-40">
                            <div class="flex items-center gap-1">
                                {{ __('Nomor Referensi') }}
                                <button class="btn btn-ghost btn-xs btn-circle" popovertarget="popover-1" style="anchor-name:--anchor-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="{{ !empty($search) ? 'text-blue-600' : '' }}">
                                        <circle cx="11" cy="11" r="8" />
                                        <path d="m21 21-4.3-4.3" />
                                    </svg>
                                </button>
                            </div>
                            <div class="p-3 border shadow-lg w-60 rounded-box bg-base-100 border-base-300" popover id="popover-1" style="position-anchor: --anchor-1; position-area: bottom; margin-top: 5px;">
                                <x-form.input-text type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nomor..." class="w-full input-sm" />
                            </div>
                        </th>

                        <th class="whitespace-nowrap w-32">{{ __('Tanggal & Waktu') }}</th>

                        {{-- Kolom Judul - Fleksibel tapi punya min-width agar tidak terlalu sempit --}}
                        <th class="min-w-[250px]">{{ __('Judul Insiden') }}</th>

                        {{-- Kolom Divisi - Lebar Terkontrol --}}
                        <th class="whitespace-nowrap w-44">
                            <div class="flex items-center gap-1">
                                {{ __('Divisi') }}
                                <button class="btn btn-ghost btn-xs btn-circle" popovertarget="pop_dept" style="anchor-name:--pop_dept">
                                    <x-icon.icon-filter :active="!empty($filterDept)" />
                                </button>
                            </div>
                            {{-- Popover Filter Dept Tetap Sama --}}
                        </th>

                        <th class="whitespace-nowrap w-36">
                            <div class="flex items-center gap-1">
                                {{ __('Tipe Insiden') }}
                                <button class="btn btn-ghost btn-xs btn-circle" popovertarget="pop_event_type" style="anchor-name:--pop_event_type">
                                    <x-icon.icon-filter :active="!empty($filterEventType)" />
                                </button>
                            </div>
                        </th>

                        <th class="whitespace-nowrap w-36">
                            <div class="flex items-center gap-1">
                                {{ __('Jenis Insiden') }}
                                <button class="btn btn-ghost btn-xs btn-circle" popovertarget="pop_event_sub_type" style="anchor-name:--pop_event_sub_type">
                                    <x-icon.icon-filter :active="!empty($filterEventSubType)" />
                                </button>
                            </div>
                        </th>

                        <th class="text-center whitespace-nowrap w-24">{{ __('Klasifikasi') }}</th>

                        <th class="text-center whitespace-nowrap w-32">
                            <div class="flex items-center justify-center gap-1">
                                {{ __('Status') }}
                                <button class="btn btn-ghost btn-xs btn-circle" popovertarget="pop_status" style="anchor-name:--pop_status">
                                    <x-icon.icon-filter :active="!empty($filterStatus)" />
                                </button>
                            </div>
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-base-200">
                    @forelse($incidents as $index => $item)
                    <tr class="hover:bg-base-200/50 transition-colors">
                        <td class="text-center font-mono text-[10px] opacity-50 w-12">
                            {{ ($incidents->currentPage() - 1) * $incidents->perPage() + $loop->iteration }}
                        </td>

                        <td class="w-40">
                            <button class="link link-primary no-underline text-xs font-bold" wire:click="editIncident({{ $item->id }})">
                                {{ $item->report_number }}
                            </button>
                        </td>

                        <td class="whitespace-nowrap w-32">
                            <div class="flex flex-col">
                                <span class="text-xs font-semibold">{{ $item->date_time->format('d M Y') }}</span>
                                <span class="text-[10px] opacity-50">{{ $item->date_time->format('H:i') }} WITA</span>
                            </div>
                        </td>

                        <td class="min-w-[250px]">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold leading-tight line-clamp-2" title="{{ $item->title }}">
                                    {{ $item->title }}
                                </span>
                                <span class="text-[10px] italic opacity-60 mt-1 flex items-start gap-1">
                                    <svg class="w-3 h-3 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ $item->location?->name}} ({{ $item->location_specific }})
                                </span>
                            </div>
                        </td>

                        <td class="w-44">
                            <div class="badge badge-ghost border-base-300 whitespace-nowrap text-[10px] uppercase px-2 max-w-[160px] truncate block overflow-hidden">
                                {{ $item->department?->department_name ?? $item->contractor?->contractor_name }}
                            </div>
                        </td>

                        <td class="w-36">
                            <div class="badge badge-outline badge-info text-[10px] uppercase whitespace-nowrap">
                                {{ $item->EventType?->event_type_name ?? 'N/A' }}
                            </div>
                        </td>

                        <td class="w-36">
                            <div class="badge badge-outline badge-info text-[10px] uppercase whitespace-nowrap">
                                {{ $item->eventSubType?->event_sub_type_name ?? 'N/A' }}
                            </div>
                        </td>

                        <td class="text-center w-24">
                            {{-- Logika Risk Color Tetap Sama --}}
                            <div class="tooltip" data-tip="{{ $item->risk?->rating_name }}">
                                <span class="inline-block w-3 h-3 rounded-full {{ $riskColor }} shadow-inner"></span>
                            </div>
                        </td>

                        <td class="text-center w-32">
                            <div @class([ 'badge badge-sm font-bold text-[9px] uppercase w-20 py-3 mx-auto flex justify-center' , 'badge-success'=> $item->status === 'Closed',
                                'badge-error' => $item->status === 'Open',
                                'badge-warning' => $item->status === 'Action Required',
                                'badge-info' => $item->status === 'In Progress',
                                ])>
                                {{ $item->status }}
                            </div>
                        </td>
                    </tr>
                    @empty
                    {{-- Data Kosong --}}
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