<section class="w-full">
    <x-toast />

    <x-body.layout heading=" {{ __('Manajemen Bagian Tubuh') }}" subheading="{{ __('Kelola bagian tubuh yang terkait dengan insiden, termasuk nama, kategori, dan kode.') }}">

        <div class="flex justify-between w-full p-6">
            <div class="gap-4">
                <x-form.input-text label="Nama (Bahasa Indonesia)" model="search" placeholder="Masukkan Nama (Bahasa Indonesia)"
                         />
                <fieldset class="fieldset">
                            <x-form.label label="{{ __('Pilih Kategori') }}"  />
                            <select wire:model.live="filterCategory"
                                class="w-full select select-bordered select-xs focus-within:outline-none focus-within:border-info focus-within:ring-0">
                                <option value="">-- Select Existing Category --</option>

                                {{-- Loop data category yang unik dari database --}}
                                @foreach($this->existing_categories as $item)
                                <option value="{{ $item }}">{{ __($item) }}</option>
                                @endforeach

                                {{-- Opsi jika ingin menambah kategori baru secara manual (opsional) --}}
                                <option value="new_category">+ {{ __('Add New Category') }}...</option>
                            </select>
                            <x-label-error :messages="$errors->get('category')" />
                        </fieldset>
            </div>
            <div class="gap-4 ">
                <x-button.btn-tooltip wireClick="openModal" color="primary" icon="add" tooltip="Tambah Baru" />
                 @livewire('administration.part-of-body.import-data')
            </div>
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
                                <x-button.btn-tooltip wireClick="edit({{ $part->id }})" color="Warning" icon="edit" tooltip="Edit Data" />
                                <x-button.btn-tooltip wireClick="delete({{ $part->id }})" color="Error" icon="trash" tooltip="Hapus Data" onclick="confirm('Hapus data ini?') || event.stopImmediatePropagation()" wire:click="delete({{ $part->id }})"/>

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
