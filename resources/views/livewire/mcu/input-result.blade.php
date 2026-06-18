<section class="w-full max-w-4xl mx-auto px-4 py-6">
    <div class="card bg-base-100 shadow-sm border border-base-200">
        <div class="card-body">
            <h2 class="card-title text-xl mb-4">Input Hasil MCU (Medical Admin)</h2>

            @if (session()->has('message'))
            <div class="alert alert-success mb-4">{{ session('message') }}</div>
            @endif

            <form wire:submit="saveResult" class="space-y-4">

                <div class="form-control w-full">
                    <label class="label"><span class="label-text font-semibold">Pilih Karyawan</span></label>
                    <select wire:model="participant_id" class="select select-bordered w-full">
                        <option value="">-- Pilih Peserta MCU --</option>
                        @foreach($participants as $p)
                        <option value="{{ $p->id }}">
                            {{ $p->employee->name }} - {{ $p->schedule->schedule_date->format('d M Y') }}
                        </option>
                        @endforeach
                    </select>
                    @error('participant_id') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="form-control w-full">
                    <label class="label"><span class="label-text font-semibold">Unggah Dokumen Hasil (PDF/JPG)</span></label>
                    <input type="file" wire:model="result_document" class="file-input file-input-bordered w-full" accept=".pdf,.jpg,.jpeg,.png" />
                    @error('result_document') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="form-control w-full">
                    <label class="label"><span class="label-text font-semibold">Catatan Admin (Opsional)</span></label>
                    <textarea wire:model="admin_notes" class="textarea textarea-bordered h-24" placeholder="Ketik catatan jika ada..."></textarea>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="btn btn-primary">
                        <span wire:loading.remove wire:target="saveResult">Kirim ke Dokter</span>
                        <span wire:loading wire:target="saveResult" class="loading loading-spinner"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>