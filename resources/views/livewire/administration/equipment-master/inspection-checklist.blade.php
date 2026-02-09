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
        <div class="flex flex-col w-11/12 max-w-3xl p-0 modal-box">
            {{-- p-0 digunakan agar scrollbar rapat ke pinggir --}}

            <div class="p-6 pb-2">
                <h3 class="text-lg font-bold">{{ $checklist_id ? 'Edit' : 'Tambah' }} Checklist</h3>
                <p class="text-sm text-gray-500">Kelola input dan poin pemeriksaan peralatan.</p>
            </div>

            <div class="p-6 pt-2 overflow-y-auto max-h-[70vh]">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="w-full form-control">
                        <label class="label"><span class="font-semibold label-text">Equipment Type</span></label>
                        <input type="text" wire:model="equipment_type"
                            class="w-full input input-bordered focus:input-primary">
                    </div>
                    <div class="w-full form-control">
                        <label class="label"><span class="font-semibold label-text">Location Keyword</span></label>
                        <input type="text" wire:model="location_keyword"
                            class="w-full input input-bordered focus:input-primary">
                    </div>
                </div>

                <hr class="my-4 border-gray-100">

                <div class="grid grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="text-sm font-bold tracking-wider text-gray-600 uppercase">Inputs Field</label>
                        @foreach ($inputs as $index => $value)
                            <div class="flex items-center gap-2 group">
                                <input type="text" wire:model="inputs.{{ $index }}"
                                    class="w-full input input-bordered input-sm">
                                <button wire:click="removeInput({{ $index }})"
                                    class="transition-opacity opacity-50 btn btn-square btn-xs btn-error btn-outline group-hover:opacity-100">×</button>
                            </div>
                        @endforeach
                        <button wire:click="addInput"
                            class="no-underline btn btn-ghost btn-xs text-primary hover:bg-primary/10">+ Add New
                            Input</button>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-bold tracking-wider text-gray-600 uppercase">Checkpoints</label>
                        @foreach ($checks as $index => $value)
                            <div class="flex items-center gap-2 group">
                                <input type="text" wire:model="checks.{{ $index }}"
                                    class="w-full input input-bordered input-sm">
                                <button wire:click="removeCheck({{ $index }})"
                                    class="transition-opacity opacity-50 btn btn-square btn-xs btn-error btn-outline group-hover:opacity-100">×</button>
                            </div>
                        @endforeach
                        <button wire:click="addCheck"
                            class="no-underline btn btn-ghost btn-xs text-primary hover:bg-primary/10">+ Add New
                            Check</button>
                    </div>
                </div>
            </div>

            <div class="p-4 modal-action bg-gray-50 rounded-b-2xl">
                <button wire:click="save" class="px-8 btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading wire:target="save" class="loading loading-spinner loading-xs"></span>
                    Save Changes
                </button>
                <form method="dialog">
                    <button class="btn btn-ghost" wire:click="resetForm">Cancel</button>
                </form>
            </div>
        </div>

        <form method="dialog" class="modal-backdrop bg-black/40">
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
