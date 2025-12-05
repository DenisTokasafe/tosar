<div>
    {{-- 1. Tombol untuk membuka Modal --}}
     <flux:tooltip content="import manhours" position="top">
            <flux:button size="xs" wire:click='openModal' icon="import" variant="accent"></flux:button>
        </flux:tooltip>
    {{-- Notifikasi Sukses/Gagal (di luar modal agar terlihat setelah modal tertutup) --}}


    {{-- 2. Modal Daisy UI --}}
    {{-- Gunakan atribut :checked="$showModal" untuk mengontrol status buka/tutup --}}
    <input type="checkbox" id="manhours_import_modal" class="modal-toggle" :checked="$showModal" />
    <div class="modal" role="dialog">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Import Data Manhours</h3>
            <p class="py-4">Unggah file Excel (.xlsx, .xls) atau CSV yang berisi data manhours.</p>
            <form wire:submit="import" enctype="multipart/form-data">

                {{-- Input File --}}
                <div class="form-control w-full my-4">
                    <label class="label">
                        <span class="label-text">Pilih File Import</span>
                    </label>
                    <input
                        type="file"
                        wire:model="file"
                        class="file-input file-input-bordered w-full"
                    />
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
                    <button type="button" class="btn" wire:click="closeModal">Batal</button>

                    {{-- Tombol Submit --}}
                    <button
                        type="submit"
                        class="btn btn-success"
                        wire:loading.attr="disabled">

                        <span wire:loading.remove wire:target="import">Import Sekarang</span>
                        <span wire:loading wire:target="import">
                             <span class="loading loading-spinner"></span>
                             Memproses...
                        </span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Tombol Close di luar modal, klik di area gelap --}}
        <label class="modal-backdrop" wire:click="closeModal" for="manhours_import_modal"></label>
    </div>
</div>
