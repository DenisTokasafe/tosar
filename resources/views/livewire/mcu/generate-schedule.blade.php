<div class="container max-w-6xl mx-auto p-4 lg:p-8">

    @if (session()->has('message'))
    <div class="alert alert-success shadow-lg mb-6">
        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>{{ session('message') }}</span>
    </div>
    @endif

    @if (session()->has('error'))
    <div class="alert alert-error shadow-lg mb-6">
        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <div class="card bg-base-100 shadow-xl border border-base-200">
        <div class="card-body">
            <h2 class="card-title text-2xl font-bold border-b pb-4 mb-4">Buat Jadwal MCU Baru</h2>

            <form wire:submit="generateJadwal">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-semibold">Tanggal Pelaksanaan <span class="text-error">*</span></span>
                        </label>
                        <input type="date"
                            wire:model="schedule_date"
                            class="input input-bordered w-full @error('schedule_date') input-error @enderror" />
                        @error('schedule_date')
                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                        @enderror
                    </div>

                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-semibold">Lokasi MCU <span class="text-error">*</span></span>
                        </label>
                        <input type="text"
                            wire:model="location"
                            placeholder="Contoh: Klinik Perusahaan / RS Pelita"
                            class="input input-bordered w-full @error('location') input-error @enderror" />
                        @error('location')
                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                        @enderror
                    </div>
                </div>

                <div class="divider text-lg font-semibold">Daftar Peserta (Karyawan)</div>

                @error('participantsData')
                <div class="alert alert-warning py-2 mb-4 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>Anda harus memilih minimal 1 peserta untuk membuat jadwal.</span>
                </div>
                @enderror

                <div class="overflow-x-auto border border-base-200 rounded-box shadow-sm mb-6">
                    <table class="table table-zebra table-pin-rows w-full">
                        <thead class="bg-base-200 text-base-content text-sm">
                            <tr>
                                <th class="w-16 text-center">Pilih</th>
                                <th>Informasi Karyawan</th>
                                <th>Departemen & NIK</th>
                                <th class="w-1/4">Nomor WhatsApp</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($employees as $employee)
                            <tr class="hover">
                                <td class="text-center">
                                    <input type="checkbox"
                                        wire:model.live="participantsData.{{ $employee->id }}.selected"
                                        value="true"
                                        class="checkbox checkbox-primary" />
                                </td>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="avatar placeholder">
                                            <div class="bg-neutral text-neutral-content rounded-full w-10">
                                                <span class="text-xs">{{ $employee->initials() }}</span>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-bold">{{ $employee->name }}</div>
                                            <div class="text-sm opacity-70">{{ $employee->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="badge badge-ghost mb-1">{{ $employee->department_name ?? 'Belum ada Dept' }}</div>
                                    <br>
                                    <span class="text-xs opacity-70">NIK: {{ $employee->employee_id ?? '-' }}</span>
                                </td>
                                <td>
                                    @if(isset($participantsData[$employee->id]['selected']) && $participantsData[$employee->id]['selected'])
                                    <input type="text"
                                        wire:model="participantsData.{{ $employee->id }}.wa"
                                        placeholder="Format: 08123456..."
                                        class="input input-sm input-bordered input-primary w-full max-w-xs" />
                                    @else
                                    <span class="text-xs text-base-content/50 italic flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                        Pilih karyawan untuk input WA
                                    </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-8 text-base-content/60">
                                    Tidak ada data karyawan dengan Role 'User' yang ditemukan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $employees->links() }}</div>
                </div>

                <div class="card-actions justify-end pt-4 border-t border-base-200">
                    <button type="submit" class="btn btn-primary w-full sm:w-auto">
                        <span wire:loading.remove wire:target="generateJadwal">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                            Generate Jadwal MCU
                        </span>
                        <span wire:loading wire:target="generateJadwal" class="loading loading-spinner"></span>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>