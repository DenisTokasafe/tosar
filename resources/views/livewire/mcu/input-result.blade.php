<section class="w-full max-w-4xl mx-auto px-4 py-6">
    <div class="card bg-base-100 shadow-sm border border-base-200">
        <div class="card-body">
            <h2 class="card-title text-xl mb-4">Input Hasil MCU (Medical Admin)</h2>

            @if (session()->has('message'))
            <div class="alert alert-success mb-4">{{ session('message') }}</div>
            @endif

            <form wire:submit="saveResult" class="space-y-4">

                <div class="form-control w-full">
                    <x-form.label label="Pilih Karyawan" />
                    <flux:select size="xs" wire:model.live='participant_id' placeholder="Choose Status...">
                        <option value="">-- Pilih Peserta MCU --</option>
                        @foreach($participants as $p)
                        <option value="{{ $p->id }}">
                            {{ $p->employee->name }} - {{ $p->schedule->schedule_date->format('d M Y') }}
                        </option>
                        @endforeach
                    </flux:select>
                    <x-label-error :messages="$errors->get('participant_id')" />
                </div>

                <div class="form-control w-full">
                    <label class="label"><span class="label-text font-semibold">Unggah Dokumen Hasil (PDF/JPG)</span></label>
                    <input type="file" wire:model="result_document" class="file-input file-input-bordered focus-within:outline-none focus-within:border-info focus-within:ring-0 focus:border-primary w-full input-xs " accept=".pdf,.jpg,.jpeg,.png" />
                    @error('result_document') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <fieldset class="mb-4 fieldset md:col-span-2" wire:key="box-admin_notes">
                    <x-form.label label="Catatan Admin (Opsional)" />
                    <div x-data="ckeditorHelper('admin_notes')" wire:ignore>
                        <div x-ref="editorElement" data-placeholder="{{ __('Masukkan Catatan Admin...') }}"></div>
                    </div>
                    <x-label-error :messages="$errors->get('admin_notes')" />
                </fieldset>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <span wire:loading.remove wire:target="saveResult">Kirim ke Dokter</span>
                        <span wire:loading.remove.class="hidden" wire:target="saveResult" class="loading loading-spinner hidden"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>