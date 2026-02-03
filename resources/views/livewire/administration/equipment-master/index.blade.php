<section class="w-full">
    <x-toast />

    <x-tabs-wpi.layout>
        <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-3">
            {{-- KOLOM KIRI: FORM INPUT & IMPORT --}}
            <div class="space-y-6">
                {{-- Form Manual --}}
                <div class="p-4 bg-white border border-gray-200 rounded-lg shadow">
                    <h3 class="mb-4 font-bold">{{ $isEdit ? 'Edit Alat' : 'Tambah Alat Baru' }}</h3>

                    <div class="space-y-3">
                        <x-form.label label="Jenis Alat" />
                        <select wire:model.live="type" class="w-full select select-bordered select-sm">
                            <option value="">-- Pilih --</option>
                            @foreach ($available_types as $t)
                                <option value="{{ $t }}">{{ $t }}</option>
                            @endforeach
                        </select>

                         <x-form.searchable-dropdown label="Area" required modelsearch="searchLocation"
                            :disabled="$isDisabled" modelid="location_id" :options="$locations" :showdropdown="$show_location"
                            clickaction="selectLocation" namedb="name" />

                        <x-form.input-floating label="Lokasi Spesifik" model="specific_location" />

                        <div class="p-3 mt-4 border rounded bg-gray-50">
                            <p class="mb-2 text-xs font-bold">Spesifikasi (FE No, Capacity, dll)</p>
                            <div class="flex gap-1 mb-2">
                                <input type="text" wire:model.live="newKey" placeholder="Label"
                                    class="w-1/2 input input-xs input-bordered">
                                <input type="text" wire:model.live="newValue" placeholder="Value"
                                    class="w-1/2 input input-xs input-bordered">
                                <button wire:click="addTechnicalField" class="btn btn-xs btn-primary">+</button>
                            </div>

                            <div class="space-y-1">
                                @foreach ($technical_data as $key => $val)
                                    <div class="flex items-center justify-between p-1 text-xs bg-white border rounded">
                                        <span><strong>{{ $key }}:</strong> {{ $val }}</span>
                                        <button wire:click="removeTechnicalField('{{ $key }}')"
                                            class="font-bold text-red-500">×</button>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex gap-2 mt-4">
                            <button wire:click="save" class="flex-1 btn btn-success btn-sm">Simpan</button>
                            @if ($isEdit)
                                <button wire:click="resetForm" class="btn btn-ghost btn-sm">Batal</button>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- FITUR IMPORT EXCEL --}}
                <div class="p-4 border-2 border-gray-300 border-dashed rounded-lg shadow-sm bg-gray-50">
                    <h4 class="mb-2 text-sm font-bold">Import dari Excel</h4>
                    <p class="text-[10px] text-gray-500 mb-3 uppercase tracking-wider leading-tight">
                        Pilih <b>Jenis Alat</b> & <b>Lokasi</b> di atas sebelum memproses file Excel.
                    </p>

                    <div class="flex flex-col gap-2">
                        <input type="file" wire:model="file_excel"
                            class="w-full file-input file-input-bordered file-input-info file-input-xs" />

                        <div wire:loading.remove.class='hidden' wire:target="file_excel"
                            class="text-[10px] text-blue-600 animate-pulse hidden">
                            Sedang mengunggah file...
                        </div>

                        <button wire:click="importExcel" wire:loading.attr="disabled" {{-- Tombol mati jika file belum dipilih atau Jenis Alat/Lokasi masih kosong --}}
                            @disabled(!$file_excel || !$type || !$location_id) class="w-full btn btn-xs btn-outline btn-info">
                            🚀 Proses Import Data
                        </button>

                        @if (!$type || !$location_id)
                            <span class="text-[9px] text-error italic text-center">* Jenis alat & lokasi wajib
                                diisi</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: TABEL DATA --}}
            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow md:col-span-2">
                <div class="flex flex-row gap-4 mb-4">
                    <select wire:model.live="search" class="w-full select select-bordered select-sm">
                    <option value="">-- Cari Tipe Alat --</option>
                    @foreach ($available_types as $t)
                        <option value="{{ $t }}">{{ $t }}</option>
                    @endforeach
                </select>
                <select wire:model.live="search_area" class="w-full select select-bordered select-sm">
                    <option value="">-- Cari Area --</option>
                    @foreach ($locations as $loc)
                        <option value="{{ $loc->name }}">{{ $loc->name }}</option>
                    @endforeach
                </select>
                </div>
                <div class="overflow-x-auto ">
                    <table class="table table-xs">
                        <thead class="bg-gray-100">
                            <tr>
                                <th>Tipe</th>
                                <th>Lokasi</th>
                                <th>Spesifikasi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($equipments as $item)
                                <tr>
                                    <td><strong>{{ $item->type }}</strong></td>
                                    <td>{{ $item->location->name }} <br> <span
                                            class="text-[10px] text-gray-500">{{ $item->specific_location }}</span>
                                    </td>
                                    <td>
                                        <div class="flex flex-wrap gap-1">
                                            @foreach ($item->technical_data as $k => $v)
                                                <span class="badge badge-ghost text-[9px]">{{ $k }}:
                                                    {{ $v }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="flex gap-1">
                                        <button wire:click="edit({{ $item->id }})"
                                            class="btn btn-xs btn-info">Edit</button>
                                        <button onclick="confirm('Hapus?') || event.stopImmediatePropagation()"
                                            wire:click="delete({{ $item->id }})"
                                            class="btn btn-xs btn-error">Hapus</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-4 text-center text-gray-400">Data tidak ditemukan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $equipments->links() }}</div>
            </div>
        </div>
    </x-tabs-wpi.layout>
</section>
