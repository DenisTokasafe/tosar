<section class="w-full">
    <x-toast />
    <x-tabs-wpi.layout heading="Edit Laporan Inspeksi Kebakaran" subheading="Fire Inspection - KPLH Site Tokatindung">
        <div class="p-6 bg-white rounded-lg shadow">
            <div class="mb-4">
                <label class="font-bold">Pilih Jenis Alat:</label>
                <select wire:model.live="type" class="w-full p-2 border rounded">
                    @foreach (array_keys($fields) as $key)
                        <option value="{{ $key }}">{{ $key }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2">
                <input type="text" wire:model="location" placeholder="Lokasi (Contoh: Area 23)"
                    class="p-2 border rounded">
                <input type="date" wire:model="inspection_date" class="p-2 border rounded">
            </div>

            <div class="p-4 border rounded-lg bg-gray-50">
                <h3 class="mb-3 font-bold">Kondisi Checklist ({{ $type }}):</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    @foreach ($fields[$type] as $field)
                        <fieldset
                            class="flex items-center p-2 font-sans text-xs bg-white border border-gray-100 rounded shadow-sm">
                            <div class="text-black font-medium min-w-[80px]">
                                {{ $field }}
                            </div>



                            <div class="flex items-center space-x-4">
                                <label class="flex items-center space-x-2 cursor-pointer group">
                                    <span class="text-gray-600">yes</span>
                                    <input type="radio" wire:model="conditions.{{ $field }}" value="yes"
                                        class="shadow-sm radio radio-primary radio-xs" />
                                </label>

                                <label class="flex items-center space-x-2 cursor-pointer group">
                                    <span class="text-gray-600">No</span>
                                    <input type="radio" wire:model="conditions.{{ $field }}" value="no"
                                        class="shadow-sm radio radio-primary radio-xs" />
                                </label>
                            </div>
                        </fieldset>
                    @endforeach
                </div>
            </div>

            <textarea wire:model="remarks" placeholder="Remarks/Catatan..." class="w-full p-2 mt-4 border rounded"></textarea>

            <button wire:click="save"
                class="w-full py-2 mt-4 font-bold text-white bg-blue-600 rounded hover:bg-blue-700">
                Simpan Laporan
            </button>
        </div>
    </x-tabs-wpi.layout>
