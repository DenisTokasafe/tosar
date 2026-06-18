<section class="w-full max-w-7xl mx-auto pb-8">

    @if (session()->has('message'))
    <div class="alert alert-success shadow-sm mb-6 rounded-xl">
        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span class="font-medium">{{ session('message') }}</span>
    </div>
    @endif

    @if (session()->has('error'))
    <div class="alert alert-error shadow-sm mb-6 rounded-xl">
        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span class="font-medium">{{ session('error') }}</span>
    </div>
    @endif

    <div class="flex items-center gap-2 mb-6 border-b border-base-200 pb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <h2 class="text-2xl font-bold text-base-content">Buat Jadwal MCU Baru</h2>
    </div>

    <form wire:submit="generateJadwal" class="space-y-6">

        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body p-5 md:p-6">
                <h3 class="text-lg font-semibold mb-4 text-base-content/80">Informasi Jadwal</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <fieldset class="fieldset w-full">
                        <x-form.label label="Tanggal MCU" required />
                        <input type="text" readonly id="schedule_date" wire:model="schedule_date" placeholder="Pilih Tanggal MCU"
                            class="w-full cursor-pointer input input-bordered input-md focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all {{ $errors->has('schedule_date') ? 'input-error focus:ring-error/20' : '' }}"
                            x-data="{ fp: null }" x-init="
                                fp = flatpickr($refs.input, {
                                    dateFormat: 'Y-m-d',
                                });
                                $wire.on('dateLoaded', () => {
                                    if ($wire.schedule_date) {
                                        fp.setDate($wire.schedule_date);
                                    }
                                });" x-ref="input" />
                        <x-label-error :messages="$errors->get('schedule_date')" />
                    </fieldset>

                    <fieldset class="fieldset w-full">
                        <div class="mt-1 md:mt-0">
                            <x-form.input-text label="Lokasi MCU" model="location" placeholder="Masukkan Lokasi MCU" />
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>

        <x-manhours.layout>
            <div class="card bg-base-100 border border-base-200 shadow-sm">
                <div class="card-body p-0 sm:p-5 md:p-6">

                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6 p-4 sm:p-0">
                        <div>
                            <h3 class="text-lg font-semibold text-base-content">Daftar Peserta (Karyawan)</h3>
                            <p class="text-sm text-base-content/60">Pilih karyawan yang akan dijadwalkan MCU.</p>
                        </div>
                        <div class="w-full md:w-80">
                            <x-form.input-floating label="Cari Nama Karyawan..." model="search" />
                        </div>
                    </div>

                    @error('participantsData')
                    <div class="alert alert-warning py-3 mb-4 mx-4 sm:mx-0 rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>Anda harus memilih minimal 1 peserta untuk membuat jadwal.</span>
                    </div>
                    @enderror

                    <div class="overflow-x-auto border-y sm:border sm:rounded-xl border-base-200 mb-2">
                        <table class="table table-zebra w-full text-sm">
                            <thead class="bg-base-200/50 text-base-content">
                                <tr>
                                    <th class="w-16 text-center">Pilih</th>
                                    <th>Informasi Karyawan</th>
                                    <th>Departemen & NIK</th>
                                    <th class="w-2/5 min-w-[300px]">Detail Notifikasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($employees as $employee)
                                <tr class="hover:bg-base-200/40 transition-colors {{ isset($participantsData[$employee->id]['selected']) && $participantsData[$employee->id]['selected'] ? 'bg-primary/5' : '' }}">
                                    <td class="text-center align-top pt-4">
                                        <input type="checkbox"
                                            wire:model.live="participantsData.{{ $employee->id }}.selected"
                                            value="true"
                                            class="checkbox checkbox-primary checkbox-sm md:checkbox-md" />
                                    </td>
                                    <td class="align-top pt-3">
                                        <div class="flex items-center gap-3">
                                            <div class="avatar placeholder">
                                                <div class="bg-neutral text-neutral-content rounded-full w-10 md:w-12 shadow-sm">
                                                    <span class="text-xs md:text-sm font-semibold">{{ $employee->initials() }}</span>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="font-bold text-base">{{ $employee->name }}</div>
                                                <div class="text-xs text-base-content/70 mt-0.5">{{ $employee->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-top pt-4">
                                        <div class="badge badge-ghost badge-sm md:badge-md font-medium mb-1">{{ $employee->department_name ?? 'Belum ada Dept' }}</div>
                                        <div class="text-xs text-base-content/60 mt-1">NIK: <span class="font-mono">{{ $employee->employee_id ?? '-' }}</span></div>
                                    </td>
                                    <td class="align-top pt-2 pb-3">
                                        @if(isset($participantsData[$employee->id]['selected']) && $participantsData[$employee->id]['selected'])
                                        <div class="p-3 bg-base-100 border border-base-300 rounded-xl shadow-sm space-y-3 mt-1 animate-fade-in-down">
                                            <div class="text-xs font-bold text-primary tracking-wide uppercase mb-1">Pengaturan WhatsApp</div>

                                            <div class="flex flex-col gap-3">
                                                <x-form.input-text type="number" model="participantsData.{{ $employee->id }}.wa" placeholder="No WA Karyawan (cth: 0812...)" />

                                                <div class="border-t border-base-200 pt-3 mt-1" x-on:focusin="$wire.set('activeRowId', {{ $employee->id }})">
                                                    <x-form.searchable-select-advanced
                                                        label="Supervisor (Opsional)"
                                                        placeholder="Cari Nama Supervisor..."
                                                        modelsearch="searchSupervisor.{{ $employee->id }}"
                                                        modelid="participantsData.{{ $employee->id }}.spv_id"
                                                        :options="$managers"
                                                        :showdropdown="$showSupervisorDropdown[$employee->id] ?? false"
                                                        :manualMode="$manualSupervisorMode[$employee->id] ?? false"
                                                        manualModelName="manualSupervisorName.{{ $employee->id }}"
                                                        enableManualAction="enableManualSupervisor({{ $employee->id }})"
                                                        addManualAction="addSupervisorManual({{ $employee->id }})"
                                                        clickaction="selectSupervisor" />
                                                </div>

                                                <x-form.input-text type="number" model="participantsData.{{ $employee->id }}.wa_spv" placeholder="No WA Supervisor (Opsional)" />
                                            </div>
                                        </div>
                                        @else
                                        <div class="text-xs text-base-content/40 italic pt-2">Centang untuk mengatur notifikasi...</div>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-12">
                                        <div class="flex flex-col items-center justify-center text-base-content/50">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                            <span class="text-sm font-medium">Tidak ada data karyawan yang ditemukan.</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="px-4 pb-4 sm:px-0">
                        {{ $employees->links() }}
                    </div>

                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="btn btn-primary px-8 btn-sm md:btn-md rounded-full shadow-md hover:shadow-lg transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" wire:loading.remove wire:target="generateJadwal">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    <span wire:loading.remove wire:target="generateJadwal">Generate Jadwal MCU</span>

                    <span wire:loading wire:target="generateJadwal" class="loading loading-spinner loading-sm hidden"></span>
                    <span wire:loading wire:target="generateJadwal" class="ml-2 hidden">Memproses...</span>
                </button>
            </div>
        </x-manhours.layout>
    </form>
</section>