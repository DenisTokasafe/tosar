<div class="p-6">
    @if (session()->has('message'))
        <div class="p-2 mb-4 bg-green-200">{{ session('message') }}</div>
    @endif
    <button class="mb-4 btn btn-primary" onclick="checklist_modal.showModal()" wire:click="resetForm">
        + Tambah Checklist
    </button>


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
            @foreach ($checklists as $item)
                <tr>
                    <td class="p-2 border">{{ $item->equipment_type }}</td>
                    <td class="p-2 border">{{ $item->location_keyword }}</td>
                    <td class="p-2 text-xs border">
                        <strong>Inputs:</strong> {{ implode(', ', $item->inputs) }} <br>
                        <strong>Checks:</strong> {{ implode(', ', $item->checks) }}
                    </td>
                    <td class="p-2 border">
                        <button wire:click="edit({{ $item->id }})" class="text-blue-500">Edit</button> |
                        <button wire:confirm="Yakin hapus?" wire:click="delete({{ $item->id }})"
                            class="text-red-500">Hapus</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <dialog id="checklist_modal" class="modal" wire:ignore.self>
        <div class="w-11/12 max-w-3xl modal-box">
            <h3 class="mb-4 text-lg font-bold">{{ $checklist_id ? 'Edit' : 'Tambah' }} Checklist</h3>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="w-full form-control">
                    <label class="label"><span class="label-text">Equipment Type</span></label>
                    <input type="text" wire:model="equipment_type" placeholder="Ex: Fire Extinguisher"
                        class="w-full input input-bordered">
                </div>
                <div class="w-full form-control">
                    <label class="label"><span class="label-text">Location Keyword</span></label>
                    <input type="text" wire:model="location_keyword" placeholder="Ex: Default"
                        class="w-full input input-bordered">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block mb-2 font-bold">Inputs (JSON)</label>
                    @foreach ($inputs as $index => $value)
                        <div class="flex gap-2 mb-2">
                            <input type="text" wire:model="inputs.{{ $index }}"
                                class="w-full input input-bordered input-sm">
                            <button wire:click="removeInput({{ $index }})"
                                class="text-white btn btn-square btn-sm btn-error">×</button>
                        </div>
                    @endforeach
                    <button wire:click="addInput" class="text-blue-500 btn btn-ghost btn-xs">+ Add Input Field</button>
                </div>

                <div>
                    <label class="block mb-2 font-bold">Checks (JSON)</label>
                    @foreach ($checks as $index => $value)
                        <div class="flex gap-2 mb-2">
                            <input type="text" wire:model="checks.{{ $index }}"
                                class="w-full input input-bordered input-sm">
                            <button wire:click="removeCheck({{ $index }})"
                                class="text-white btn btn-square btn-sm btn-error">×</button>
                        </div>
                    @endforeach
                    <button wire:click="addCheck" class="text-blue-500 btn btn-ghost btn-xs">+ Add Check Field</button>
                </div>
            </div>

            <div class="mt-8 modal-action">
                <button wire:click="save" class="text-white btn btn-primary">
                    <span wire:loading class="loading loading-spinner"></span> Save Data
                </button>
                <form method="dialog">
                    <button class="btn btn-ghost" wire:click="resetForm">Cancel</button>
                </form>
            </div>
        </div>

        <form method="dialog" class="modal-backdrop">
            <button wire:click="resetForm">close</button>
        </form>
    </dialog>

</div>
<script>
    // Listener untuk menutup modal setelah save berhasil
    window.addEventListener('close-modal', event => {
        document.getElementById('checklist_modal').close();
    });
</script>
