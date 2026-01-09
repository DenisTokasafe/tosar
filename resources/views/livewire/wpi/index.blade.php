<section class="w-full">
    <x-toast />
    <x-tabs-wpi.layout heading="Work Permit to Install" subheading="Form WPI">
        <form wire:submit.prevent="save" class="overflow-hidden bg-white border border-gray-200 rounded-lg shadow-xl">

            <div class="p-6 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl italic font-bold text-gray-800 uppercase">Formulir Laporan WPI KPLH</h2>
                    <span class="text-sm font-semibold text-gray-500">TT-MGT-FRS-024A</span>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <label class="w-32 text-sm font-medium text-gray-600">Tanggal / Date</label>
                            <input type="date" wire:model="report_date"
                                class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="flex items-center">
                            <label class="w-32 text-sm font-medium text-gray-600">Jam / Time</label>
                            <input type="time" wire:model="report_time"
                                class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="flex items-center">
                            <label class="w-32 text-sm font-medium text-gray-600">Lokasi / Location</label>
                            <input type="text" wire:model="location" placeholder="e.g. Toka Pit, Araren Pit"
                                class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center">
                            <label class="w-32 text-sm font-medium text-gray-600">Department</label>
                            <select wire:model="department"
                                class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih Department</option>
                                <option value="Geoteknik & Hidrogeologi">Geoteknik & Hidrogeologi</option>
                                <option value="Mining Operation">Mining Operation</option>
                                <option value="OHS Operational">OHS Operational</option>
                            </select>
                        </div>
                        <div class="flex items-center">
                            <label class="w-32 text-sm font-medium text-gray-600">Site Name</label>
                            <input type="text" value="Tokatindung" disabled
                                class="flex-1 text-gray-500 bg-gray-100 border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 bg-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold tracking-wider text-gray-700 uppercase">Nama Petugas Inspeksi /
                        Inspector</h3>
                    <button type="button" wire:click="addInspector"
                        class="px-3 py-1 text-xs text-white transition bg-blue-600 rounded hover:bg-blue-700">
                        + Tambah Petugas
                    </button>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    @foreach ($inspectors as $index => $inspector)
                        <div class="flex items-start space-x-2" wire:key="ins-{{ $index }}">
                            <span class="mt-2 text-xs font-bold text-gray-400">{{ $index + 1 }}.</span>
                            <input type="text" wire:model="inspectors.{{ $index }}.name"
                                placeholder="Nama Lengkap" class="flex-1 text-sm border-gray-300 rounded">
                            <input type="text" wire:model="inspectors.{{ $index }}.id_number"
                                placeholder="ID/NIK" class="w-24 text-sm border-gray-300 rounded">
                            @if (count($inspectors) > 1)
                                <button type="button" wire:click="removeInspector({{ $index }})"
                                    class="mt-2 text-red-500 hover:text-red-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="p-6 overflow-x-auto border-t border-gray-200">
                <table class="w-full text-xs text-left border border-collapse border-gray-300">
                    <thead class="italic text-white uppercase bg-gray-800">
                        <tr>
                            <th class="w-8 p-2 text-center border border-gray-300">#</th>
                            <th class="w-16 p-2 text-center border border-gray-300">OHS Risk</th>
                            <th class="p-2 border border-gray-300">Uraian Temuan & Foto</th>
                            <th class="p-2 border border-gray-300">Tindakan Pencegahan</th>
                            <th class="w-48 p-2 border border-gray-300">Follow Up (PIC & Due Date)</th>
                            <th class="w-12 p-2 text-center border border-gray-300">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($findings as $index => $finding)
                            <tr wire:key="find-{{ $index }}" class="align-top hover:bg-gray-50">
                                <td class="p-2 font-bold text-center border border-gray-300">{{ $index + 1 }}</td>
                                <td class="p-2 text-center border border-gray-300">
                                    <select wire:model="findings.{{ $index }}.ohs_risk"
                                        class="w-full p-1 text-xs border-gray-300 rounded">
                                        <option value="T">T</option>
                                        <option value="H">H</option>
                                        <option value="M">M</option>
                                        <option value="L">L</option>
                                    </select>
                                </td>
                                <td class="p-2 border border-gray-300">
                                    <textarea wire:model="findings.{{ $index }}.description" placeholder="Deskripsikan temuan..."
                                        class="w-full mb-2 text-xs border-gray-300 rounded" rows="3"></textarea>

                                    <div class="mt-2" wire:loading.remove
                                        wire:target="findings.{{ $index }}.new_photos">
                                        @if (isset($findings[$index]['new_photos']) && count($findings[$index]['new_photos']) > 0)
                                            <div class="grid grid-cols-2 gap-2 mt-2">
                                                @foreach ($findings[$index]['new_photos'] as $fileKey => $newFile)
                                                    {{-- Gunakan wire:key unik gabungan index finding dan index file --}}
                                                    <div class="relative p-1 border rounded bg-gray-50"
                                                        wire:key="preview-{{ $index }}-{{ $fileKey }}">

                                                        @php
                                                            // Pastikan file adalah objek UploadedFile sebelum panggil method
                                                            $isUploadedFile = method_exists($newFile, 'temporaryUrl');
                                                            $extension = $isUploadedFile
                                                                ? strtolower($newFile->getClientOriginalExtension())
                                                                : '';
                                                        @endphp

                                                        @if ($isUploadedFile && in_array($extension, ['jpg', 'jpeg', 'png', 'gif']))
                                                            <img src="{{ $newFile->temporaryUrl() }}"
                                                                class="object-cover w-full h-20 rounded shadow-sm" />
                                                        @else
                                                            {{-- Fallback jika bukan gambar (PDF/Word) --}}
                                                            <div
                                                                class="flex flex-col items-center justify-center h-20 bg-gray-200 rounded">
                                                                @if ($extension == 'pdf')
                                                                    <x-icon.pdf class="w-8 h-8" />
                                                                @elseif(in_array($extension, ['doc', 'docx']))
                                                                    <x-icon.word class="w-8 h-8" />
                                                                @endif
                                                                <span
                                                                    class="text-[8px] mt-1 truncate w-full px-1 text-center">
                                                                    {{ $isUploadedFile ? $newFile->getClientOriginalName() : 'File Error' }}
                                                                </span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="p-2 border border-gray-300">
                                    <textarea wire:model="findings.{{ $index }}.prevention_action" placeholder="Tindakan korektif..."
                                        class="w-full text-xs border-gray-300 rounded" rows="3"></textarea>
                                </td>
                                <td class="p-2 space-y-2 border border-gray-300">
                                    <input type="text" wire:model="findings.{{ $index }}.pic_responsible"
                                        placeholder="Nama PIC" class="w-full text-xs border-gray-300 rounded">
                                    <div class="flex items-center space-x-1 text-[10px]">
                                        <span class="text-gray-500">Due:</span>
                                        <input type="date" wire:model="findings.{{ $index }}.due_date"
                                            class="flex-1 p-1 text-xs border-gray-300 rounded">
                                    </div>
                                </td>
                                <td class="p-2 text-center border border-gray-300">
                                    <button type="button" wire:click="removeFinding({{ $index }})"
                                        class="text-red-400 hover:text-red-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z">
                                            </path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div
                class="flex flex-col items-center justify-between gap-4 p-6 border-t border-gray-200 bg-gray-50 md:flex-row">
                <button type="button" wire:click="addFinding"
                    class="w-full px-4 py-2 text-sm font-bold text-gray-700 transition bg-gray-200 rounded-md md:w-auto hover:bg-gray-300">
                    + Tambah Baris Temuan
                </button>

                <div class="flex items-center w-full space-x-3 md:w-auto">
                    <a href="/wpi-list"
                        class="flex-1 px-6 py-2 text-sm font-medium text-center text-gray-600 md:flex-none hover:text-gray-800">Batal</a>
                    <button type="submit"
                        class="flex items-center justify-center flex-1 px-8 py-2 text-sm font-bold text-white transition bg-green-600 rounded-md shadow-lg md:flex-none hover:bg-green-700">
                        <span wire:loading.remove wire:target="save italic">Simpan Laporan (Submit)</span>
                        <span wire:loading wire:target="save">Memproses...</span>
                    </button>
                </div>
            </div>
        </form>
    </x-tabs-wpi.layout>
</section>
