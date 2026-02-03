<section class="w-full">
    <x-toast />

    <div class="flex justify-start mb-2" wire:ignore>
        @php $currentRoute = Route::currentRouteName(); @endphp
        @if (Breadcrumbs::exists($currentRoute))
            {!! Breadcrumbs::render($currentRoute, isset($reportId) ? $reportId : null) !!}
        @endif
    </div>

    <x-tabs-wpi.layout>
        <div class="px-6 mb-2 bg-white ">
            <div class="flex md:justify-start ">
                <div class="grid content-center max-w-lg grid-cols-3">
                    <div class="mt-1">

                        <select wire:model.live="type"
                            class="select select-xs select-bordered w-full max-w-sm focus-within:outline-none focus-within:border-info focus-within:ring-0 {{ $errors->has('type') ? 'border-rose-500' : '' }}">
                            <option value="">-- Pilih Jenis Alat --</option>
                            @foreach (array_keys($fields) as $key)
                                <option value="{{ $key }}">{{ $key }}</option>
                            @endforeach
                        </select>
                    </div>
                    <x-label-error :messages="$errors->get('type')" />

                    <x-form.search-floating label="Area" required modelsearch="searchLocation" modelid="location_id"
                        placeholder="Area..." :options="$locations" :showdropdown="$show_location" clickaction="selectLocation"
                        namedb="name" />
                    <x-form.datepicker label="Tanggal / Date" model="inspection_date" />
                </div>
            </div>

        </div>

        <div class="relative overflow-hidden border rounded-lg shadow-inner bg-slate-50">
            @php
                $allMasterData = \App\Models\EquipmentMaster::where('location_id', $location_id)
                    ->where('type', $type)
                    ->get();
                $checks = $fields[$type]['checks'] ?? [];
                $firstEquipment = $allMasterData->first();
                $techKeys =
                    $firstEquipment && $firstEquipment->technical_data
                        ? array_keys($firstEquipment->technical_data)
                        : [];
            @endphp

            <div class="overflow-x-auto max-h-[calc(100vh-25rem)] 2xl:max-h-[calc(100vh-37rem)]  border rounded-lg ">
                <table class="table border-separate table-fixed table-xs table-pin-rows border-spacing-0">
                    <thead>
                        <tr class="capitalize bg-slate-100 text-slate-700">
                            <th class="border-b border-r bg-slate-100 text-[10px]">Location</th>

                            @foreach ($techKeys as $techKey)
                                <th
                                    class="text-center text-blue-700 capitalize border-b border-r text-[10px] bg-blue-50/50"  style="width: 70px; min-width: 70px; white-space: normal; line-height: 1.2;">
                                    {{ $techKey }}
                                </th>
                            @endforeach

                            @foreach ($checks as $checkItem)
                                <th class="text-center border-b border-r bg-amber-50 text-amber-700 text-[10px] capitalize px-1"
                                    style="width: 60px; min-width: 60px; white-space: normal; line-height: 1.2;">
                                    {{ $checkItem }}
                                </th>
                            @endforeach

                            <th class=" text-center capitalize border-b border-r text-[10px]">Remarks</th>
                            <th class=" text-center capitalize border-b text-[10px]">Foto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($allMasterData as $master)
                            <tr class="transition-colors hover:bg-blue-50/30">
                                <td class="text-xs font-medium bg-white border-b border-r ">
                                    {{ $master->specific_location }}</td>

                                @foreach ($techKeys as $key)
                                    <td class="italic text-center border-b border-r bg-slate-50/50 text-slate-500">
                                        {{ $conditions[$master->id][$key] ?? '-' }}
                                    </td>
                                @endforeach

                                @foreach ($checks as $field)
                                    <td class="text-center bg-white border-b border-r">
                                        <input type="checkbox" wire:key="check-{{ $master->id }}-{{ $field }}"
                                            wire:model.live="conditions.{{ $master->id }}.{{ $field }}"
                                            class="checkbox checkbox-xs border-rose-600 bg-rose-500 checked:border-emerald-500 checked:bg-emerald-400 checked:text-emerald-800" />
                                    </td>
                                @endforeach

                                <td class="p-1 bg-white border-b border-r ">
                                    <x-form.textarea row='1' model="conditions.{{ $master->id }}.remarks"
                                        placeholder="Remarks" />
                                </td>

                                <td class="p-2 text-center bg-white border-b">
                                    <div class="flex flex-col items-center justify-center">
                                        <input type="file" id="file-{{ $master->id }}" class="hidden"
                                            wire:model="dokumentasi.{{ $master->id }}">

                                        @if (isset($dokumentasi[$master->id]))
                                            <div class="relative inline-block group">
                                                <img src="{{ $dokumentasi[$master->id]->temporaryUrl() }}"
                                                    class="object-cover w-10 h-10 border rounded-md shadow-sm">
                                                <label for="file-{{ $master->id }}"
                                                    class="absolute inset-0 flex items-center justify-center transition-opacity rounded-md opacity-0 cursor-pointer bg-black/40 group-hover:opacity-100">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2">
                                                        <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                                                    </svg>
                                                </label>
                                            </div>
                                        @else
                                            <label for="file-{{ $master->id }}"
                                                class="btn btn-ghost btn-xs text-info hover:bg-info/10">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2">
                                                    <rect width="18" height="18" x="3" y="3" rx="2"
                                                        ry="2" />
                                                    <circle cx="9" cy="9" r="2" />
                                                    <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21" />
                                                </svg>
                                            </label>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100" class="py-10 text-center bg-slate-50 text-slate-400">
                                    <p class="italic">Tidak ada data alat ditemukan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex md:justify-end ">


            <button wire:click="save" wire:loading.attr="disabled" class="btn btn-success btn-xs md:w-auto">

                <span wire:loading.add.class='hidden' wire:target="save">🚀 Simpan Laporan Inspeksi</span>

                <span wire:loading.remove.class="hidden" class="hidden" wire:target="save">Menyimpan...</span>

            </button>
        </div>
        </div>
    </x-tabs-wpi.layout>
</section>
