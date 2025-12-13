<section class="w-full">
    <x-toast />
    <x-tabs-workflow-event.layout :activeTab="$activeTab" :heading="$heaading" :subheading="$subheading">
    <div class="overflow-hidden bg-white rounded-lg shadow-md">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 text-xs font-semibold tracking-wider text-left text-gray-600 uppercase bg-gray-100 border-b-2 border-gray-200">
                        Dari Status (Key)
                    </th>
                    <th class="px-5 py-3 text-xs font-semibold tracking-wider text-left text-gray-600 uppercase bg-gray-100 border-b-2 border-gray-200">
                        Dari Status (Nama)
                    </th>
                    <th class="px-5 py-3 text-xs font-semibold tracking-wider text-left text-gray-600 uppercase bg-gray-100 border-b-2 border-gray-200">
                        Ke Status (Key)
                    </th>
                    <th class="px-5 py-3 text-xs font-semibold tracking-wider text-left text-gray-600 uppercase bg-gray-100 border-b-2 border-gray-200">
                        Ke Status (Nama)
                    </th>
                    <th class="px-5 py-3 text-xs font-semibold tracking-wider text-left text-gray-600 uppercase bg-gray-100 border-b-2 border-gray-200">
                        Role
                    </th>
                    <th class="px-5 py-3 text-xs font-semibold tracking-wider text-left text-gray-600 uppercase bg-gray-100 border-b-2 border-gray-200">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($workflows as $workflow)
                    <tr wire:key="{{ $workflow->id }}">
                        <td class="px-5 py-5 text-sm bg-white border-b border-gray-200">{{ $workflow->from_status }}</td>
                        <td class="px-5 py-5 text-sm bg-white border-b border-gray-200">{{ $workflow->from_inisial }}</td>
                        <td class="px-5 py-5 text-sm bg-white border-b border-gray-200">{{ $workflow->to_status }}</td>
                        <td class="px-5 py-5 text-sm bg-white border-b border-gray-200">{{ $workflow->to_inisial }}</td>
                        <td class="px-5 py-5 text-sm bg-white border-b border-gray-200">
                            <span class="relative inline-block px-3 py-1 font-semibold leading-tight {{ $workflow->role == 'moderator' ? 'text-purple-900 bg-purple-200' : 'text-indigo-900 bg-indigo-200' }} rounded-full">
                                {{ ucfirst($workflow->role) }}
                            </span>
                        </td>
                        <td class="px-5 py-5 text-sm bg-white border-b border-gray-200">
                            <button wire:click="edit({{ $workflow->id }})" class="mr-3 text-indigo-600 hover:text-indigo-900">Edit</button>
                            <button wire:click="delete({{ $workflow->id }})" class="text-red-600 hover:text-red-900" onclick="return confirm('Apakah Anda yakin ingin menghapus workflow ini?')">Hapus</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $workflows->links() }}
    </div>

    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-600 bg-opacity-75">
            <div class="w-full max-w-lg p-6 bg-white rounded-lg shadow-xl" @click.away="closeModal()">
                <div class="flex items-center justify-between pb-3">
                    <h3 class="text-xl font-semibold text-gray-900">
                        {{ $workflowId ? 'Edit Workflow' : 'Tambah Workflow Baru' }}
                    </h3>
                    <button wire:click="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form wire:submit.prevent="save">
                    <div class="space-y-4">
                        <div>
                            <label for="from_status" class="block text-sm font-medium text-gray-700">Dari Status (Key)</label>
                            <select wire:model.defer="from_status" id="from_status" class="block w-full p-2 mt-1 border border-gray-300 rounded-md shadow-sm">
                                <option value="">Pilih Status Awal</option>
                                @foreach($statusOptions as $status)
                                    <option value="{{ $status }}">{{ $status }}</option>
                                @endforeach
                            </select>
                            @error('from_status') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="from_inisial" class="block text-sm font-medium text-gray-700">Dari Status (Nama)</label>
                            <input type="text" wire:model.defer="from_inisial" id="from_inisial" class="block w-full p-2 mt-1 border border-gray-300 rounded-md shadow-sm" placeholder="Cth: Submitted Event">
                            @error('from_inisial') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="to_status" class="block text-sm font-medium text-gray-700">Ke Status (Key)</label>
                            <select wire:model.defer="to_status" id="to_status" class="block w-full p-2 mt-1 border border-gray-300 rounded-md shadow-sm">
                                <option value="">Pilih Status Tujuan</option>
                                @foreach($statusOptions as $status)
                                    <option value="{{ $status }}">{{ $status }}</option>
                                @endforeach
                            </select>
                            @error('to_status') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="to_inisial" class="block text-sm font-medium text-gray-700">Ke Status (Nama)</label>
                            <input type="text" wire:model.defer="to_inisial" id="to_inisial" class="block w-full p-2 mt-1 border border-gray-300 rounded-md shadow-sm" placeholder="Cth: Moderator Review">
                            @error('to_inisial') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700">Role Yang Bertanggung Jawab</label>
                            <select wire:model.defer="role" id="role" class="block w-full p-2 mt-1 border border-gray-300 rounded-md shadow-sm">
                                <option value="">Pilih Role</option>
                                @foreach($roleOptions as $r)
                                    <option value="{{ $r }}">{{ ucfirst($r) }}</option>
                                @endforeach
                            </select>
                            @error('role') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end mt-6 space-x-3">
                        <button type="button" wire:click="closeModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
    </x-tabs-workflow-event.layout>
</section>
