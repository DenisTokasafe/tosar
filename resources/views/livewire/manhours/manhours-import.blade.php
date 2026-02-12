<div>
    {{-- 1. Tombol untuk membuka Modal --}}


     <x-button.btn-tooltip modalId="import_modal" color="accent" icon="file-import"  tooltip="Import Data" />
    {{-- Notifikasi Sukses/Gagal (di luar modal agar terlihat setelah modal tertutup) --}}


    {{-- 2. Modal Daisy UI --}}
    {{-- Gunakan atribut :checked="$showModal" untuk mengontrol status buka/tutup --}}
    <dialog id="import_modal" class="modal"   wire:ignore.self>
        <div class="modal-box">
            <h3 class="text-lg font-bold">Import Data Manhours</h3>
            <p class="text-[9px] font-bold text-gray-400 uppercase">Unggah file Excel (.xlsx, .xls) atau CSV yang berisi data manhours.</p>
            <form wire:submit="import" enctype="multipart/form-data">

                {{-- Input File --}}
                <div class="w-full my-4 form-control">
                    <label class="label">
                        <span class="label-text text-[9px]">Pilih File Import</span>
                    </label>
                    <input type="file" wire:model.live="file" class="w-full file-input-xs focus-within:outline-none focus-within:border-info focus-within:ring-0 file-input file-input-bordered" />
                    {{-- Menampilkan error validasi Livewire --}}
                    @error('file')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                <div class="modal-action">
                    {{-- Tombol Tutup --}}
                    {{-- Gunakan wire:click="closeModal" agar state Livewire ($showModal) ikut di-update --}}
                  <button class="btn btn-xs btn-soft btn-error" wire:click='closeModal' onclick="import_modal.close()">Batal</button>
                    {{-- Tombol Submit --}}
                    <button class="btn btn-xs btn-soft btn-primary" wire:loading.attr="disabled"
                       > <x-icon.file-import><span wire:loading.class='hidden'
                            wire:target="import,file">Import
                            Sekarang</span>
                        <span wire:loading class="hidden" wire:loading.class.remove="hidden" wire:target="import,file">
                            <span class="loading loading-spinner"></span>
                            Memproses...
                        </span>
                    </button>

                </div>
            </form>
        </div>
        {{-- Tombol Close di luar modal, klik di area gelap --}}

    </dialog>
</div>
