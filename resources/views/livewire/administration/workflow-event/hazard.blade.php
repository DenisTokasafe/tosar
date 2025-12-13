<section class="w-full">
    <x-toast />
    <x-tabs-workflow-event.layout :activeTab="$activeTab" :heading="$heaading" :subheading="$subheading">
        <div class="overflow-hidden bg-white rounded-lg shadow-md">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th
                            class="px-5 py-3 text-xs font-semibold tracking-wider text-left text-gray-600 uppercase bg-gray-100 border-b-2 border-gray-200">
                            Dari Status (Key)
                        </th>
                        <th
                            class="px-5 py-3 text-xs font-semibold tracking-wider text-left text-gray-600 uppercase bg-gray-100 border-b-2 border-gray-200">
                            Dari Status (Nama)
                        </th>
                        <th
                            class="px-5 py-3 text-xs font-semibold tracking-wider text-left text-gray-600 uppercase bg-gray-100 border-b-2 border-gray-200">
                            Ke Status (Key)
                        </th>
                        <th
                            class="px-5 py-3 text-xs font-semibold tracking-wider text-left text-gray-600 uppercase bg-gray-100 border-b-2 border-gray-200">
                            Ke Status (Nama)
                        </th>
                        <th
                            class="px-5 py-3 text-xs font-semibold tracking-wider text-left text-gray-600 uppercase bg-gray-100 border-b-2 border-gray-200">
                            Role
                        </th>
                        <th
                            class="px-5 py-3 text-xs font-semibold tracking-wider text-left text-gray-600 uppercase bg-gray-100 border-b-2 border-gray-200">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($workflows as $workflow)
                        <tr wire:key="{{ $workflow->id }}">
                            <td class="px-5 py-5 text-sm bg-white border-b border-gray-200">{{ $workflow->from_status }}
                            </td>
                            <td class="px-5 py-5 text-sm bg-white border-b border-gray-200">
                                {{ $workflow->from_inisial }}</td>
                            <td class="px-5 py-5 text-sm bg-white border-b border-gray-200">{{ $workflow->to_status }}
                            </td>
                            <td class="px-5 py-5 text-sm bg-white border-b border-gray-200">{{ $workflow->to_inisial }}
                            </td>
                            <td class="px-5 py-5 text-sm bg-white border-b border-gray-200">
                                <span
                                    class="relative inline-block px-3 py-1 font-semibold leading-tight {{ $workflow->role == 'moderator' ? 'text-purple-900 bg-purple-200' : 'text-indigo-900 bg-indigo-200' }} rounded-full">
                                    {{ ucfirst($workflow->role) }}
                                </span>
                            </td>
                            <td class="px-5 py-5 text-sm bg-white border-b border-gray-200">
                                <button wire:click="edit({{ $workflow->id }})"
                                    class="mr-3 text-indigo-600 hover:text-indigo-900">Edit</button>
                                <button wire:click="delete({{ $workflow->id }})" class="text-red-600 hover:text-red-900"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus workflow ini?')">Hapus</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $workflows->links() }}
        </div>

        {{-- Karena Livewire V3 memiliki fitur 'Livewire Modals', kita bisa menggunakan properti $isModalOpen --}}
        @if ($isModalOpen)
            {{-- DaisyUI Modal - Note: Kita menggunakan 'fixed inset-0' untuk overlay background Livewire secara manual karena kita memicu modal melalui state $isModalOpen --}}
            <div class="modal modal-open" role="dialog">
                <div class="relative w-full max-w-lg modal-box" x-data="{}"
                    @click.away="window.livewire.find('{{ $this->getName() }}').closeModal()">

                    {{-- Tombol Close (x) di sudut kanan atas --}}
                    <form method="dialog">
                        <button type="button" wire:click="closeModal()"
                            class="absolute btn btn-sm btn-circle btn-ghost right-2 top-2">✕</button>
                    </form>

                    <h3 class="mb-4 text-lg font-bold">
                        {{ $workflowId ? 'Edit Hazard Workflow' : 'Tambah Hazard Workflow Baru' }}
                    </h3>

                    <form wire:submit.prevent="save">
                        <div class="space-y-4">

                            <div>
                                {{-- Menggunakan Flux Select Component --}}
                                <flux:select label="Dari Status (Key)" id="from_status" wire:model.defer="from_status" placeholder="Pilih Status Awal..."
                                    size="xs" {{-- Mengatur ukuran ke xs --}} class="w-full">
                                    @foreach ($statusOptions as $status)
                                        <flux:select.option value="{{ $status }}">{{ $status }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                                @error('from_status')
                                    <span class="mt-1 text-xs text-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                {{-- Menggunakan Flux Input Component --}}
                                <flux:input label="Dari Status (Nama)" id="from_inisial" type="text"
                                    wire:model.defer="from_inisial" placeholder="Cth: Submitted Event" size="xs"
                                    {{-- Mengatur ukuran ke xs --}} class="w-full" />
                                @error('from_inisial')
                                    <span class="mt-1 text-xs text-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                {{-- Menggunakan Flux Select Component --}}
                                <flux:select label="Ke Status (Key)" id="to_status" wire:model.defer="to_status" placeholder="Pilih Status Tujuan..."
                                    size="xs" {{-- Mengatur ukuran ke xs --}} class="w-full">
                                    @foreach ($statusOptions as $status)
                                        <flux:select.option value="{{ $status }}">{{ $status }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                                @error('to_status')
                                    <span class="mt-1 text-xs text-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                {{-- Menggunakan Flux Input Component --}}
                                <flux:input label="Ke Status (Nama)" id="to_inisial" type="text"
                                    wire:model.defer="to_inisial" placeholder="Cth: Moderator Review" size="xs"
                                    {{-- Mengatur ukuran ke xs --}} class="w-full" />
                                @error('to_inisial')
                                    <span class="mt-1 text-xs text-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                {{-- Menggunakan Flux Select Component --}}
                                <flux:select label="Role Yang Bertanggung Jawab" id="role" placeholder="Pilih Role..."
                                    wire:model.defer="role" size="xs" {{-- Mengatur ukuran ke xs --}} class="w-full">
                                    @foreach ($roleOptions as $r)
                                        <flux:select.option value="{{ $r }}">{{ ucfirst($r) }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                                @error('role')
                                    <span class="mt-1 text-xs text-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Modal Actions (Button) --}}
                        <div class="mt-6 modal-action">
                            <button type="button" wire:click="closeModal()" class="btn btn-sm">
                                Batal
                            </button>
                            <button type="submit" class="btn btn-success btn-sm">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </x-tabs-workflow-event.layout>
</section>
