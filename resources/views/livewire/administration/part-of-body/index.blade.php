<section class="w-full">
    <x-toast />

    <x-body.layout heading=" {{ __('Manajemen Bagian Tubuh') }}" subheading="{{ __('Kelola bagian tubuh yang terkait dengan insiden, termasuk nama, kategori, dan kode.') }}">

        <div class="flex justify-end w-full p-6">
            <x-button.btn-tooltip wireClick="openModal" color="primary" icon="add" tooltip="Tambah Baru" />
        </div>



        <div class="border shadow-xl card bg-base-100 border-base-200">
            <div class="p-4 bg-base-200/50">
                <input type="text" wire:model.live="search" placeholder="Cari nama atau kategori..." class="w-full max-w-xs input input-bordered input-sm" />
            </div>
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama (ID)</th>
                            <th>Name (EN)</th>
                            <th>Kategori</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bodyParts as $part)
                        <tr>
                            <td class="font-mono text-xs">{{ $part->code }}</td>
                            <td>{{ $part->name }}</td>
                            <td class="italic text-base-content/70">{{ $part->name_en ?? '-' }}</td>
                            <td><span class="badge badge-ghost">{{ $part->category }}</span></td>
                            <td class="flex justify-center gap-2">
                                <button wire:click="edit({{ $part->id }})" class="btn btn-square btn-ghost btn-sm text-info">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                                <button onclick="confirm('Hapus data ini?') || event.stopImmediatePropagation()" wire:click="delete({{ $part->id }})" class="btn btn-square btn-ghost btn-sm text-error">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-4 text-center">Data tidak ditemukan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4">
                {{ $bodyParts->links() }}
            </div>
        </div>

        <dialog id="body_modal" class="modal modal-bottom sm:modal-middle" wire:ignore.self>
            <div class="modal-box">
                <h3 class="mb-4 text-lg font-bold">{{ $isEditing ? 'Edit Bagian Tubuh' : 'Tambah Bagian Tubuh Baru' }}</h3>

                <form wire:submit.prevent="{{ $isEditing ? 'update' : 'store' }}" class="space-y-4">

                    <x-form.input-text label="Nama (Bahasa Indonesia)" model="name" placeholder="Masukkan Nama (Bahasa Indonesia)"
                        required />
                    <x-form.input-text label="Name (English)" model="name_en" placeholder="Contoh: Right Eye"
                        required />



                    <div class="grid grid-cols-2 gap-4">
                        <fieldset class="fieldset">
                            <x-form.label label="Pilih Kategori" required />
                            <select wire:model.live="category"
                                class="w-full select select-bordered select-xs focus-within:outline-none focus-within:border-info focus-within:ring-0">
                                <option value="">-- Select Existing Category --</option>

                                {{-- Loop data category yang unik dari database --}}
                                @foreach($this->existing_categories as $item)
                                <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach

                                {{-- Opsi jika ingin menambah kategori baru secara manual (opsional) --}}
                                <option value="new_category">+ Add New Category...</option>
                            </select>
                            <x-label-error :messages="$errors->get('category')" />
                        </fieldset>
                        @if($category === 'new_category')
                            <x-form.input-text
                                label="New Category Name"
                                model="category"
                                placeholder="Type new category name here..." required/>
                        @endif
                        <x-form.input-text label="Kode (Slug)" model="code" placeholder="Contoh: head_eye_right" class="font-mono text-sm"
                        required />


                    </div>

                    <div class="modal-action">
                        <button type="button" class="btn btn-error btn-xs" onclick="body_modal.close()">Batal</button>
                        <button type="submit" class="btn btn-primary btn-xs" wire:loading.attr="disabled">
                            <span wire:loading.remove.class="hidden" class="hidden loading loading-spinner loading-xs"></span>
                            {{ $isEditing ? 'Simpan Perubahan' : 'Simpan Data' }}
                        </button>
                    </div>
                </form>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>

        <script>
            window.addEventListener('open-body-modal', event => {
                document.getElementById('body_modal').showModal();
            });
            window.addEventListener('close-body-modal', event => {
                document.getElementById('body_modal').close();
            });
        </script>
        </div>

    </x-body.layout>
</section>
