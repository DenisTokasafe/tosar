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
            <div class="flex md:justify-start ">
                <div class="grid grid-cols-1 gap-2 mb-4 md:grid-cols-3">
                    <fieldset class="w-full fieldset md:max-w-80">

                        <select wire:model.live="type"
                            class="select select-xs select-bordered w-full focus-within:outline-none focus-within:border-info focus-within:ring-0 {{ $errors->has('type') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}">
                            <option value="">-- Pilih Jenis Alat --</option>
                            @foreach (array_keys($fields) as $key)
                                <option value="{{ $key }}">{{ $key }}</option>
                            @endforeach
                        </select>
                        <x-label-error :messages="$errors->get('type')" />
                    </fieldset>
                    <x-form.search-floating label="Area" required modelsearch="searchLocation" modelid="location_id"
                        placeholder="Area..." :options="$locations" :showdropdown="$show_location" clickaction="selectLocation"
                        namedb="name" />
                    <x-form.datepicker label="Tanggal / Date" model="inspection_date" />
                </div>
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

                <div class="overflow-x-auto max-h-[calc(100vh-25rem)] 2xl:max-h-[calc(100vh-37rem)] border rounded-lg ">
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
                                <th class="">Dokumentasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($allMasterData as $master)
                                <tr class="text-xs hover:bg-slate-50">
                                    <td class="w-40">{{ $master->specific_location }}</td>
                                    @foreach ($techKeys as $key)
                                        <td class="w-10 border border-slate-200 bg-slate-50/50"> {{-- Tambah sedikit background agar terlihat beda dengan input --}}
                                            <div class="w-full text-[10px] font-medium text-center text-slate-600">
                                                {{ $conditions[$master->id][$key] ?? '-' }}
                                            </div>
                                        </td>
                                    @endforeach

                                    @foreach ($checks as $field)
                                        <td class="text-center border border-slate-200">
                                            <input type="checkbox"
                                                wire:key="check-{{ $master->id }}-{{ $field }}"
                                                wire:model.live="conditions.{{ $master->id }}.{{ $field }}"
                                                class="checkbox checkbox-xs border-rose-600 bg-rose-500 checked:border-emerald-500 checked:bg-emerald-400" />
                                        </td>
                                    @endforeach

                                    <td class="p-1 border border-slate-200">
                                        <x-form.textarea row='1' model="conditions.{{ $master->id }}.remarks"
                                            placeholder="Remarks..." />
                                    </td>
                                    <td class="w-40 p-2 border border-slate-200">
                                        <div class="flex flex-col items-center gap-2">

                                            {{-- 1. Input yang di-hide --}}
                                            <div class="hidden">
                                                <x-form.upload id="file-upload-{{ $master->id }}"
                                                    {{-- ID Unik per baris --}} model="dokumentasi.{{ $master->id }}"
                                                    :file="$dokumentasi[$master->id] ?? null" />
                                            </div>

                                            {{-- 2. Tombol Pemicu (Menggunakan Label agar bisa klik input di atas) --}}
                                            @if (!isset($dokumentasi[$master->id]))
                                                <label for="file-upload-{{ $master->id }}"
                                                    class="gap-1 btn btn-xs btn-outline btn-info">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14"
                                                        height="14" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                                        <polyline points="17 8 12 3 7 8" />
                                                        <line x1="12" x2="12" y1="3"
                                                            y2="15" />
                                                    </svg>
                                                    Upload Foto
                                                </label>
                                            @endif

                                            {{-- 3. Preview (Tetap muncul setelah upload berhasil) --}}
                                            <div wire:loading.remove wire:target="dokumentasi.{{ $master->id }}">
                                                @if (isset($dokumentasi[$master->id]))
                                                    <div class="relative mt-1 group">
                                                        @php
                                                            $file = $dokumentasi[$master->id];
                                                            $extension = $file->getClientOriginalExtension();
                                                        @endphp

                                                        @if (in_array($extension, ['jpg', 'jpeg', 'png']))
                                                            <img src="{{ $file->temporaryUrl() }}"
                                                                class="object-cover w-16 h-16 border rounded-lg shadow-sm" />
                                                        @else
                                                            <div
                                                                class="flex items-center gap-1 text-[10px] bg-blue-50 p-1 rounded border border-blue-200">
                                                                <span
                                                                    class="font-bold uppercase">{{ $extension }}</span>
                                                            </div>
                                                        @endif

                                                        {{-- Tombol ganti foto (opsional) --}}
                                                        <label for="file-upload-{{ $master->id }}"
                                                            class="absolute p-1 bg-white border rounded-full shadow cursor-pointer -top-2 -right-2 hover:bg-slate-100">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="10"
                                                                height="10" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                class="lucide lucide-pencil">
                                                                <path
                                                                    d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                                                                <path d="m15 5 4 4" />
                                                            </svg>
                                                        </label>
                                                    </div>
                                                @endif
                                            </div>

                                            <x-label-error :messages="$errors->get('dokumentasi.' . $master->id)" />
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                @php
                                    // Hitung total kolom: Location (1) + TechKeys + Checks + Remarks (1)
                                    $totalColumns = 1 + count($techKeys) + count($checks) + 1 + 1;
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
                <div class="">
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

                    <div class="">
                        <button wire:click="save" wire:loading.attr="disabled"
                            class="btn btn-success btn-xs md:w-auto">
                            <span wire:loading.add.class='hidden' wire:target="save">🚀 Simpan Laporan Inspeksi</span>
                            <span wire:loading.remove.class="hidden" class="hidden"
                                wire:target="save">Menyimpan...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </x-tabs-wpi.layout>
</section>
