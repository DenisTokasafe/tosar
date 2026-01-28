<section class="w-full">
    <x-toast />

    <x-tabs-wpi.layout >
        <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-3">
    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow">
        <h3 class="mb-4 font-bold">{{ $isEdit ? 'Edit Alat' : 'Tambah Alat Baru' }}</h3>

        <div class="space-y-3">
            <x-form.label label="Jenis Alat" />
            <select wire:model="type" class="w-full select select-bordered select-sm">
                <option value="">-- Pilih --</option>
                @foreach($available_types as $t) <option value="{{ $t }}">{{ $t }}</option> @endforeach
            </select>

            <x-form.label label="Area / Lokasi" />
            <select wire:model="location_id" class="w-full select select-bordered select-sm">
                <option value="">-- Pilih Lokasi --</option>
                @foreach($locations as $loc) <option value="{{ $loc->id }}">{{ $loc->name }}</option> @endforeach
            </select>

            <x-form.input-floating label="Lokasi Spesifik" model="specific_location" />

            <div class="p-3 mt-4 border rounded bg-gray-50">
                <p class="mb-2 text-xs font-bold">Spesifikasi (FE No, Capacity, dll)</p>
                <div class="flex gap-1 mb-2">
                    <input type="text" wire:model="newKey" placeholder="Label" class="w-1/2 input input-xs input-bordered">
                    <input type="text" wire:model="newValue" placeholder="Value" class="w-1/2 input input-xs input-bordered">
                    <button wire:click="addTechnicalField" class="btn btn-xs btn-primary">+</button>
                </div>

                <div class="space-y-1">
                    @foreach($technical_data as $key => $val)
                        <div class="flex items-center justify-between p-1 text-xs bg-white border rounded">
                            <span><strong>{{ $key }}:</strong> {{ $val }}</span>
                            <button wire:click="removeTechnicalField('{{ $key }}')" class="font-bold text-red-500">×</button>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex gap-2 mt-4">
                <button wire:click="save" class="flex-1 btn btn-success btn-sm">Simpan</button>
                @if($isEdit) <button wire:click="resetForm" class="btn btn-ghost btn-sm">Batal</button> @endif
            </div>
        </div>
    </div>

    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow md:col-span-2">
        <input type="text" wire:model.live="search" placeholder="Cari tipe alat..." class="w-full mb-4 input input-sm input-bordered">

        <table class="table w-full table-xs">
            <thead class="bg-gray-100">
                <tr>
                    <th>Tipe</th>
                    <th>Lokasi</th>
                    <th>Spesifikasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($equipments as $item)
                <tr>
                    <td><strong>{{ $item->type }}</strong></td>
                    <td>{{ $item->location->name }} <br> <span class="text-[10px] text-gray-500">{{ $item->specific_location }}</span></td>
                    <td>
                        @foreach($item->technical_data as $k => $v)
                            <span class="badge badge-ghost text-[9px]">{{ $k }}: {{ $v }}</span>
                        @endforeach
                    </td>
                    <td class="flex gap-1">
                        <button wire:click="edit({{ $item->id }})" class="btn btn-xs btn-info">Edit</button>
                        <button onclick="confirm('Hapus?') || event.stopImmediatePropagation()" wire:click="delete({{ $item->id }})" class="btn btn-xs btn-error">Hapus</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">{{ $equipments->links() }}</div>
    </div>
</div>
    </x-tabs-wpi.layout>
