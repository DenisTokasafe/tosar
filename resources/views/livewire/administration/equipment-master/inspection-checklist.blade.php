<div class="p-6">
    @if (session()->has('message'))
        <div class="p-2 mb-4 bg-green-200">{{ session('message') }}</div>
    @endif

    <div class="p-4 mb-6 bg-white shadow">
        <h3 class="mb-4 font-bold">{{ $checklist_id ? 'Edit' : 'Tambah' }} Checklist</h3>
        <div class="grid grid-cols-2 gap-4 mb-4">
            <input type="text" wire:model="equipment_type" placeholder="Type" class="p-2 border">
            <input type="text" wire:model="location_keyword" placeholder="Location" class="p-2 border">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="font-bold">Inputs</label>
                @foreach($inputs as $index => $value)
                    <div class="flex mb-2">
                        <input type="text" wire:model="inputs.{{ $index }}" class="w-full p-2 border">
                        <button wire:click="removeInput({{ $index }})" class="px-2 ml-1 text-white bg-red-500">×</button>
                    </div>
                @endforeach
                <button wire:click="addInput" class="text-sm text-blue-500">+ Add Input Field</button>
            </div>

            <div>
                <label class="font-bold">Checks</label>
                @foreach($checks as $index => $value)
                    <div class="flex mb-2">
                        <input type="text" wire:model="checks.{{ $index }}" class="w-full p-2 border">
                        <button wire:click="removeCheck({{ $index }})" class="px-2 ml-1 text-white bg-red-500">×</button>
                    </div>
                @endforeach
                <button wire:click="addCheck" class="text-sm text-blue-500">+ Add Check Field</button>
            </div>
        </div>

        <button wire:click="save" class="px-4 py-2 mt-4 text-white bg-blue-600 rounded">Save Data</button>
        <button wire:click="resetForm" class="px-4 py-2 mt-4 text-white bg-gray-400 rounded">Cancel</button>
    </div>

    <table class="w-full bg-white shadow">
        <thead>
            <tr class="bg-gray-100">
                <th class="p-2 border">Type</th>
                <th class="p-2 border">Location</th>
                <th class="p-2 border">Inputs/Checks</th>
                <th class="p-2 border">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($checklists as $item)
            <tr>
                <td class="p-2 border">{{ $item->equipment_type }}</td>
                <td class="p-2 border">{{ $item->location_keyword }}</td>
                <td class="p-2 text-xs border">
                    <strong>Inputs:</strong> {{ implode(', ', $item->inputs) }} <br>
                    <strong>Checks:</strong> {{ implode(', ', $item->checks) }}
                </td>
                <td class="p-2 border">
                    <button wire:click="edit({{ $item->id }})" class="text-blue-500">Edit</button> |
                    <button wire:confirm="Yakin hapus?" wire:click="delete({{ $item->id }})" class="text-red-500">Hapus</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
