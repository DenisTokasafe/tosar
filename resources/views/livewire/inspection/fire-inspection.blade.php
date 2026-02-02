<section class="w-full">
    <x-toast />
    <div class="flex justify-start " wire:ignore>
        @php
            $currentRoute = Route::currentRouteName();
        @endphp

        @if (Breadcrumbs::exists($currentRoute))
            {!! Breadcrumbs::render($currentRoute, isset($reportId) ? $reportId : null) !!}
        @endif
    </div>

    <x-tabs-wpi.layout>
        <div class="p-6 bg-white rounded-lg shadow">
            {{-- HEADER SELECTION --}}
            <div class="mb-4">
                <fieldset class="w-full fieldset md:max-w-80">
                    <x-form.label label="Pilih Jenis Alat" required />
                    <select wire:model.live="type"
                        class="select select-xs select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden {{ $errors->has('type') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}">
                        <option value="">-- Pilih --</option>
                        @foreach (array_keys($fields) as $key)
                            <option value="{{ $key }}">{{ $key }}</option>
                        @endforeach
                    </select>
                    <x-label-error :messages="$errors->get('type')" />
                </fieldset>
            </div>

            <div class="grid grid-cols-1 gap-2 mb-4 md:grid-cols-3">
                <x-form.search-floating label="Area" required modelsearch="searchLocation" modelid="location_id"
                    placeholder="Area..." :options="$locations" :showdropdown="$show_location" clickaction="selectLocation"
                    namedb="name" />


                <x-form.datepicker label="Tanggal / Date" model="inspection_date" />
            </div>

            {{-- TABLE SPREADSHEET STYLE --}}
            <div class="rounded-lg shadow-md ">
                @php
                    // Ambil semua data alat di area tersebut dengan tipe yang sama
                    $allMasterData = \App\Models\EquipmentMaster::where('location_id', $location_id)
                        ->where('type', $type)
                        ->get();

                    $checks = $fields[$type]['checks'] ?? [];

                    // Ambil sample technical data dari data pertama untuk header tabel
                    $firstEquipment = $allMasterData->first();
                    $techKeys =
                        $firstEquipment && $firstEquipment->technical_data
                            ? array_keys($firstEquipment->technical_data)
                            : [];
                @endphp
                <div class="overflow-x-auto  max-h-[calc(100vh-20rem)] border rounded-lg ">
                    <table class="table text-xs border-collapse table-xs table-pin-rows">
                        <thead>
                            <tr class="text-xs text-center text-black ">
                                <th class="">Location</th>

                                @foreach ($techKeys as $techKey)
                                    <th class="">
                                        {{ $techKey }}
                                    </th>
                                @endforeach

                                @foreach ($checks as $checkItem)
                                    <th class="">
                                        {{ $checkItem }}
                                    </th>
                                @endforeach

                                <th class="">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($allMasterData as $master)
                                <tr class="text-xs hover:bg-slate-50">
                                    <td class="">{{ $master->specific_location }}</td>
                                    @foreach ($techKeys as $key)
                                        <td class="border border-slate-200">
                                            <input type="text" value="{{ $key }}"
                                                wire:model="conditions.{{ $master->id }}.{{ $key }}" readonly
                                                class="w-full text-xs text-center bg-transparent border-none focus:ring-0">
                                        </td>
                                    @endforeach

                                    @foreach ($checks as $field)
                                        <td class="text-center border border-slate-200">
                                            <input type="checkbox"
                                                wire:key="check-{{ $master->id }}-{{ $field }}"
                                                wire:model="conditions.{{ $master->id }}.{{ $field }}"
                                                class="checkbox checkbox-xs border-rose-600 bg-rose-500 checked:border-emerald-500 checked:bg-emerald-400" />
                                        </td>
                                    @endforeach

                                    <td class="p-1 border border-slate-200">
                                        <x-form.textarea model="conditions.{{ $master->id }}.remarks"
                                            placeholder="Remarks..." />
                                    </td>
                                </tr>
                            @empty
                                @php
                                    // Hitung total kolom: Location (1) + TechKeys + Checks + Remarks (1)
                                    $totalColumns = 1 + count($techKeys) + count($checks) + 1;
                                @endphp
                                <td colspan="{{ $totalColumns }}" class="py-12 text-center bg-slate-50">
                                    <div class="flex flex-col items-center justify-center space-y-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-circle-x-icon lucide-circle-x">
                                            <circle cx="12" cy="12" r="10" />
                                            <path d="m15 9-6 6" />
                                            <path d="m9 9 6 6" />
                                        </svg>
                                        <span class="font-medium text-slate-400">Tidak ada data alat ditemukan untuk
                                            area dan tipe ini.</span>
                                        <p class="text-xs italic text-slate-400">Silahkan periksa kembali filter atau
                                            Master Data Anda.</p>
                                    </div>
                                </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- REMARKS & UPLOAD SECTIONS --}}
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="space-y-4">
                    <fieldset class="fieldset">
                        <x-form.upload label="Lampirkan foto atau dokumentasi" model="dokumentasi" :file="$dokumentasi" />
                        <div wire:loading.remove wire:target="dokumentasi">
                            @if ($dokumentasi)
                                <div class="p-2 mt-2 border border-dashed rounded-lg bg-slate-50">
                                    @if (in_array($dokumentasi->getClientOriginalExtension(), ['jpg', 'jpeg', 'png']))
                                        <img src="{{ $dokumentasi->temporaryUrl() }}"
                                            class="h-auto border rounded w-44" />
                                    @else
                                        <div class="flex items-center gap-2 text-sm text-info">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                            {{ $dokumentasi->getClientOriginalName() }}
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <x-label-error :messages="$errors->get('dokumentasi')" />
                    </fieldset>
                </div>

                <div class="space-y-4">
                    <fieldset class="fieldset">
                        <x-form.searchable-select-advanced label="Dilaporkan Oleh" placeholder="Cari Nama Pelapor..."
                            modelsearch="searchResponsibility" modelid="action_responsible_id" :options="$pelapors"
                            :showdropdown="$showPelaporDropdown" :manualMode="$manualPelaporMode" manualModelName="manualPelaporName"
                            enableManualAction="enableManualPelapor" addManualAction="addPelaporManual"
                            clickaction="selectPelapor" />

                        <div class="flex flex-wrap gap-2 mt-3">
                            @foreach ($inspected_users as $index => $user)
                                <div
                                    class="flex items-center gap-1 px-3 py-1 text-xs font-semibold transition-all border rounded-full bg-slate-100 text-slate-700 border-slate-300 hover:bg-slate-200">
                                    <span>{{ $user['name'] }}</span>
                                    <button type="button" wire:click="removeInspectedUser({{ $index }})"
                                        class="text-slate-400 hover:text-red-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </fieldset>

                    <div class="pt-4 border-t">
                        <button wire:click="save" wire:loading.attr="disabled"
                            class="w-full btn btn-success btn-sm md:w-auto">
                            <span wire:loading.remove wire:target="save">🚀 Simpan Laporan Inspeksi</span>
                            <span wire:loading wire:target="save">Menyimpan...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </x-tabs-wpi.layout>
</section>
