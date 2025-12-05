<div>
    {{-- 1. Tombol untuk membuka Modal --}}
    <flux:tooltip content="import manhours" position="top">
        <flux:button size="xs" wire:click='openModal' icon="import" variant="accent"></flux:button>
    </flux:tooltip>
    {{-- Notifikasi Sukses/Gagal (di luar modal agar terlihat setelah modal tertutup) --}}


    {{-- 2. Modal Daisy UI --}}
    {{-- Gunakan atribut :checked="$showModal" untuk mengontrol status buka/tutup --}}
    <div class="modal {{ $showModal }}" role="dialog">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Import Data Manhours</h3>
            <p class="py-4">Unggah file Excel (.xlsx, .xls) atau CSV yang berisi data manhours.</p>
            <form wire:submit="import" enctype="multipart/form-data">

                {{-- Input File --}}
                <div class="form-control w-full my-4">
                    <label class="label">
                        <span class="label-text">Pilih File Import</span>
                    </label>
                    <input type="file" wire:model.live="file" class="file-input file-input-bordered w-full" />
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
                    <flux:button size="xs" wire:click='closeModal' icon="import" variant="danger">Batal</flux:button>
                    {{-- Tombol Submit --}}
                    <flux:button type="submit" size="xs" wire:click='openModal' wire:loading.attr="disabled"
                        icon="import" variant="primary"><span wire:loading.class='hidden' wire:target="import,file">Import
                            Sekarang</span>
                        <span wire:loading class="hidden" wire:loading.class.remove="hidden" wire:target="import,file">
                            <span class="loading loading-spinner"></span>
                            Memproses...
                        </span>
                    </flux:button>

                </div>
            </form>
        </div>

        {{-- Tombol Close di luar modal, klik di area gelap --}}
        <label class="modal-backdrop" wire:click="closeModal" for="manhours_import_modal"></label>
    </div>
</div>
