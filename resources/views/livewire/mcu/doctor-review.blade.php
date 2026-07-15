<section class="w-full max-w-7xl mx-auto px-4 py-6">

    @if (session()->has('message'))
    <div class="alert alert-success mb-4">{{ session('message') }}</div>
    @endif

    <div class="card bg-base-100 shadow-sm border border-base-200">
        <div class="card-body">
            <h2 class="card-title text-xl mb-4">Daftar Antrean Review Dokter</h2>

            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th>Nama Karyawan</th>
                            <th>Tanggal MCU</th>
                            <th>Dokumen Admin</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingReviews as $result)
                        <tr>
                            <td class="font-bold">{{ $result->participant->employee->name }}</td>
                            <td>{{ $result->participant->schedule->schedule_date->format('d M Y') }}</td>
                            <td>
                                <a href="{{ Storage::url($result->result_document) }}" target="_blank" class="btn btn-sm btn-outline btn-info">Lihat Dokumen</a>
                            </td>
                            <td>
                                <button wire:click="openReviewModal({{ $result->id }})" class="btn btn-sm btn-primary">Review Sekarang</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 italic text-base-content/50">Tidak ada data yang perlu di-review.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <flux:modal wire:model="showReviewModal" class="w-full">
        <div class="space-y-2">
            <flux:heading size="lg" class="border-b pb-2">Review Status MCU</flux:heading>

            <form wire:submit="saveReview" class="py-4 space-y-4">
                @if ($errors->any())
                <div class="p-3 text-sm text-red-600 bg-red-50 rounded-lg border border-red-200">
                    <p class="font-bold mb-1">Gagal menyimpan karena:</p>
                    <ul class="list-disc pl-4 space-y-1">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <flux:select size="xs" wire:model.live='fit_status' placeholder="Choose Status...">
                    <option value="">-- Pilih Status Kebugaran --</option>
                    <option value="fit_to_work">✅ Fit To Work</option>
                    <option value="fit_with_notes">⚠️ Fit With Notes (Restriction)</option>
                    <option value="temporary_unfit">⏳ Temporary Unfit</option>
                    <option value="unfit">❌ Unfit</option>
                </flux:select>
                <x-label-error :messages="$errors->get('fit_status')" />

                <fieldset class="fieldset border border-base-300 p-3 rounded-lg bg-base-50/50">
                    <div class="flex justify-between items-center mb-2">
                        <x-form.label label="Kategori Penyakit Temuan (Bisa pilih > 1)" />
                        <button type="button" wire:click="openAddDiseaseModal" class="btn btn-xs btn-outline btn-secondary">
                            + Penyakit Lainnya
                        </button>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 max-h-48 overflow-y-auto p-2 border border-base-200 rounded bg-base-100">
                        @foreach($diseaseCategories as $category)
                        <label class="cursor-pointer label justify-start space-x-2 py-1">
                            <input type="checkbox"
                                value="{{ $category->id }}"
                                wire:model="selectedDiseaseCategories"
                                class="checkbox checkbox-xs checkbox-primary">
                            <span class="label-text text-xs font-medium">{{ $category->name }}</span>
                        </label>
                        @endforeach
                    </div>
                    <x-label-error :messages="$errors->get('selectedDiseaseCategories')" />
                </fieldset>

                @if($fit_status === 'fit_with_notes')
                <fieldset class="mb-4 fieldset md:col-span-2" wire:key="box-restriction">
                    <x-form.label label="Catatan Batasan Kerja (Restriction Monitoring)" required />
                    <div x-data="ckeditorHelper('restriction_notes')" wire:ignore>
                        <div x-ref="editorElement" data-placeholder="{{ __('Masukkan Catatan Batasan Kerja (Restriction Monitoring)...') }}"></div>
                    </div>
                    <x-label-error :messages="$errors->get('restriction_notes')" />
                </fieldset>
                @endif

                @if($fit_status === 'temporary_unfit')
                <fieldset class="w-full fieldset">
                    <x-form.label label="Jadwal Follow Up MCU" required />
                    <input type="text" readonly id="follow_up_date" wire:model="follow_up_date" placeholder="Pilih Jadwal Follow Up MCU"
                        class="w-full cursor-pointer input input-bordered input-xs focus-within:outline-none focus-within:border-info focus-within:ring-0 focus:border-primary transition-all {{ $errors->has('follow_up_date') ? 'input-error focus:ring-error/20' : '' }}"
                        x-data="{ fp: null }" x-init="
                                fp = flatpickr($refs.input, {
                                    dateFormat: 'Y-m-d',
                                     static: true,
                                });
                                $wire.on('dateLoaded', () => {
                                    if ($wire.follow_up_date) {
                                        fp.setDate($wire.follow_up_date);
                                    }
                                });" x-ref="input" />
                    <x-label-error :messages="$errors->get('follow_up_date')" />
                </fieldset>
                @endif

                <fieldset class="mb-4 fieldset md:col-span-2" wire:key="box-doctor_notes">
                    <x-form.label label="Catatan Tambahan Dokter" required />
                    <div x-data="ckeditorHelper('doctor_notes')" wire:ignore>
                        <div x-ref="editorElement" data-placeholder="{{ __('Masukkan Catatan Tambahan Dokter...') }}"></div>
                    </div>
                    <x-label-error :messages="$errors->get('doctor_notes')" />
                </fieldset>

                <div class="flex justify-end space-x-2 pt-4 border-t border-base-200">
                    <flux:button variant="ghost" wire:click="$set('showReviewModal', false)">Batal</flux:button>
                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                        <span wire:loading wire:target="saveReview" class="loading loading-spinner loading-sm mr-1"></span>
                        Simpan Review
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <input type="checkbox" id="modal-add-disease" class="modal-toggle" wire:model="showAddDiseaseModal" />
    <div class="modal" role="dialog">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Tambah Kategori Penyakit Baru</h3>
            <p class="py-2 text-xs text-base-content/70">Penyakit yang ditambahkan akan otomatis tercentang di daftar temuan MCU ini.</p>

            <div class="py-4">
                <label class="form-control w-full">
                    <div class="label">
                        <span class="label-text font-medium">Nama Penyakit</span>
                    </div>
                    <input type="text" wire:model="new_disease_name" placeholder="Contoh: Asma, Diabetes Type 2..." class="input input-bordered w-full input-sm" />
                    @error('new_disease_name') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                </label>
            </div>

            <div class="modal-action">
                <button type="button" wire:click="closeAddDiseaseModal" class="btn btn-sm btn-ghost">Batal</button>
                <button type="button" wire:click="saveNewDisease" class="btn btn-sm btn-primary">
                    <span wire:loading wire:target="saveNewDisease" class="loading loading-spinner loading-xs"></span>
                    Simpan & Centang
                </button>
            </div>
        </div>
        <label class="modal-backdrop" wire:click="closeAddDiseaseModal">Close</label>
    </div>

</section>