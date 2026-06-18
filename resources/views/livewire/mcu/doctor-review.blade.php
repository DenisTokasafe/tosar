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

    <flux:modal wire:model="showReviewModal" class="w-full max-w-2xl">
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
                <div class="form-control w-full">
                    <label class="label"><span class="label-text font-bold">Status MCU</span></label>
                    <select wire:model.live="fit_status" class="select select-bordered w-full border-primary">
                        <option value="">-- Pilih Status Kebugaran --</option>
                        <option value="fit_to_work">✅ Fit To Work</option>
                        <option value="fit_with_notes">⚠️ Fit With Notes (Restriction)</option>
                        <option value="temporary_unfit">⏳ Temporary Unfit</option>
                        <option value="unfit">❌ Unfit</option>
                    </select>
                    @error('fit_status') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                @if($fit_status === 'fit_with_notes')
                <div wire:key="box-restriction" class="form-control w-full animate-fade-in-down">
                    <label class="label"><span class="label-text font-semibold text-warning">Catatan Batasan Kerja (Restriction Monitoring)</span></label>
                    <textarea wire:model="restriction_notes" class="textarea textarea-bordered textarea-warning h-24" placeholder="Sebutkan batasan kerjanya (contoh: Tidak boleh mengangkat beban >10kg)"></textarea>
                    @error('restriction_notes') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                @endif

                @if($fit_status === 'temporary_unfit')
                <div wire:key="box-followup" class="form-control w-full animate-fade-in-down">
                    <label class="label"><span class="label-text font-semibold text-info">Jadwal Follow Up MCU</span></label>
                    <input type="date" wire:model="follow_up_date" class="input input-bordered input-info w-full" />
                    @error('follow_up_date') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                @endif

                <div class="form-control w-full">
                    <label class="label"><span class="label-text font-semibold">Catatan Tambahan Dokter</span></label>
                    <textarea wire:model="doctor_notes" class="textarea textarea-bordered h-20" placeholder="Opsional..."></textarea>
                    @error('doctor_notes') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                </div>

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

</section>