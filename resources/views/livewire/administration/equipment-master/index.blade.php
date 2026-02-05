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
                        <select wire:model.live="type"
                            class="w-full select select-bordered select-xs focus-within:outline-none focus-within:border-info focus-within:ring-0">
                            <option value="">-- Pilih --</option>
                            @foreach ($available_types as $t)
                                <option value="{{ $t }}">{{ $t }}</option>
                            @endforeach
                        </select>

                        <x-form.searchable-dropdown label="Area" required modelsearch="searchLocation"
                            modelid="location_id" :options="$locations" :showdropdown="$show_location" clickaction="selectLocation"
                            namedb="name" />

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
                        Pilih <b>Jenis Alat</b> & <b>Lokasi</b> di atas. Data akan diambil dari sheet:
                        <b>{{ $type ?: '...' }}</b>
                    </p>

                    <div class="flex flex-col gap-2">
                        <input type="file" wire:model="file_excel"
                            class="w-full file-input file-input-bordered file-input-info file-input-xs" />

                        <div wire:loading.remove.class='hidden' wire:target="file_excel"
                            class="text-[10px] text-blue-600 animate-pulse hidden">
                            Sedang mengunggah file...
                        </div>

                        {{-- Ganti fungsi ke previewExcel --}}
                        <button wire:click="previewExcel" wire:loading.attr="disabled" @disabled(!$file_excel || !$type || !$location_id)
                            class="w-full btn btn-xs btn-outline btn-info">
                            🔍 Preview Data (Sheet: {{ $type }})
                        </button>

                        @if (!$type || !$location_id)
                            <span class="text-[9px] text-error italic text-center">* Jenis alat & lokasi wajib
                                diisi</span>
                        @endif
                    </div>

                    {{-- MODAL / SECTION PREVIEW --}}
                    @if ($showPreview && count($previewData) > 0)
                        <div class="pt-4 mt-4 border-t">
                            <h5 class="mb-2 text-xs font-bold">Konfirmasi Data ({{ count($previewData) }} Baris)</h5>
                            <div class="mb-2 overflow-y-auto border rounded max-h-40">
                                <table class="table w-full table-zebra table-xs">
                                    <thead class="sticky top-0 bg-gray-200">
                                        <tr>
                                            {{-- Sesuaikan header ini dengan kolom excel Anda --}}
                                            @foreach (array_keys($previewData[0]) as $header)
                                                <th>{{ strtoupper(str_replace('_', ' ', $header)) }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach (array_slice($previewData, 0, 10) as $row)
                                            {{-- Tampilkan 10 baris saja untuk hemat ram --}}
                                            <tr>
                                                @foreach ($row as $value)
                                                    <td>{{ $value }}</td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if (count($previewData) > 10)
                                <p class="text-[9px] text-gray-400 mb-2 italic">* Menampilkan 10 baris pertama...</p>
                            @endif

                            <div class="flex gap-2">
                                <button wire:click="importExcel" class="flex-1 btn btn-xs btn-success">
                                    ✅ Simpan Ke Database
                                </button>
                                <button wire:click="$set('showPreview', false)" class="btn btn-xs btn-ghost text-error">
                                    Batal
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- KOLOM KANAN: TABEL DATA --}}
            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow md:col-span-2">
                <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2">
                    <fieldset class="fieldset">
                        <x-form.label label="Cari Tipe Alat" />
                        <select wire:model.live="search"
                            class="w-full select select-bordered select-xs focus-within:outline-none focus-within:border-info focus-within:ring-0">
                            <option value="">-- Cari Tipe Alat --</option>
                            @foreach ($available_types as $t)
                                <option value="{{ $t }}">{{ $t }}</option>
                            @endforeach
                        </select>
                    </fieldset>
                    <x-form.searchable-dropdown label="Cari Area" modelsearch="cari_searchLocation"
                        modelid="cari_location_id" :options="$cari_locations" :showdropdown="$cari_show_location" clickaction="selectCariLocation"
                        namedb="name" />
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
