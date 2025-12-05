<div>
    {{-- 1. Tombol untuk membuka Modal --}}
    <button
        class="btn btn-primary"
        wire:click="openModal">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
        Import Data Manhours
    </button>

    {{-- Notifikasi Sukses/Gagal (di luar modal agar terlihat setelah modal tertutup) --}}
    @if ($message && $status == 'success')
        <div class="toast toast-end mt-4">
            <div class="alert alert-success">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>{{ $message }}</span>
            </div>
        </div>
    @endif


    {{-- 2. Modal Daisy UI --}}
    {{-- Gunakan atribut :checked="$showModal" untuk mengontrol status buka/tutup --}}
    <input type="checkbox" id="manhours_import_modal" class="modal-toggle" :checked="$showModal" />
    <div class="modal" role="dialog">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Import Data Manhours</h3>
            <p class="py-4">Unggah file Excel (.xlsx, .xls) atau CSV yang berisi data manhours.</p>

            {{-- Pesan Error Validasi/Import --}}
            @if ($message && $status == 'error')
                <div class="alert alert-error my-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ $message }}</span>
                </div>
            @endif

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
