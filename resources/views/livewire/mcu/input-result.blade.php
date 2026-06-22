<section class="w-full max-w-4xl mx-auto px-4 py-6">
    <div class="card bg-base-100 shadow-sm border border-base-200">
        <div class="card-body">
            <h2 class="card-title text-xl mb-4">Input Hasil MCU (Medical Admin)</h2>

            @if (session()->has('message'))
            <div class="alert alert-success mb-4">{{ session('message') }}</div>
            @endif

            <form wire:submit="saveResult" class="space-y-4">

                <fieldset class="fieldset">
                    <x-form.label label="Pilih Karyawan" />
                    <flux:select size="xs" wire:model.live='participant_id' placeholder="Pilih Peserta MCU...">
                        @foreach($participants as $p)
                        <option value="{{ $p->id }}">
                            {{ $p->employee->name }} - {{ $p->schedule->schedule_date->format('d M Y') }}
                        </option>
                        @endforeach
                    </flux:select>
                    <x-label-error :messages="$errors->get('participant_id')" />
                </fieldset>
                <fieldset class="fieldset">
                    <x-form.upload label="Unggah Dokumen Hasil (PDF/JPG)" model="result_document" :file="$result_document" required />
                </fieldset>
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