<section class="w-full max-w-7xl mx-auto  px-4 sm:px-6 lg:px-8">

    @if (session()->has('message'))
    <div class="alert alert-success shadow-sm mb-6 rounded-xl">
        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span class="font-medium text-sm md:text-base">{{ session('message') }}</span>
    </div>
    @endif

    @if (session()->has('error'))
    <div class="alert alert-error shadow-sm mb-6 rounded-xl">
        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span class="font-medium text-sm md:text-base">{{ session('error') }}</span>
    </div>
    @endif

    <div class="flex items-center gap-3 mb-4 border-b border-base-200 pb-4">
        <div class="p-2 bg-primary/10 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6   text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        </div>
        <h2 class="text-xl md:text-xl font-bold text-base-content">Buat Jadwal MCU Baru</h2>
    </div>

    <form wire:submit="generateJadwal" class="space-y-6">

        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body p-2 md:p-4">
                <h3 class="text-base md:text-lg font-semibold mb-3 md:mb-0 text-base-content/80 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Informasi Jadwal
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-2">
                    <fieldset class="fieldset w-full">
                        <x-form.label label="Tanggal MCU" required />
                        <input type="text" readonly id="schedule_date" wire:model="schedule_date" placeholder="Pilih Tanggal MCU"
                            class="w-full cursor-pointer input input-bordered input-xs focus-within:outline-none focus-within:border-info focus-within:ring-0 focus:border-primary transition-all {{ $errors->has('schedule_date') ? 'input-error focus:ring-error/20' : '' }}"
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
                    <div class="w-full">
                        <x-form.input-text label="Lokasi MCU" model="location" placeholder="Masukkan Lokasi MCU" required />
                    </div>
                </div>
            </div>
        </div>

        <x-mcu.layout>
            <div class="card bg-base-100 border border-base-200 shadow-sm">
                <div class="card-body p-0 md:p-4">

                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 p-2 border-b border-base-200 md:border-none md:mb-4">
                        <div>
                            <h3 class="text-base md:text-lg font-semibold text-base-content flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                Daftar Peserta
                            </h3>
                            <p class="text-xs md:text-sm text-base-content/60 mt-1">Pilih karyawan yang akan dijadwalkan.</p>
                        </div>
                        <div class="w-full md:w-80">
                            <x-form.input-floating label="Cari Nama Karyawan..." model="search" />
                        </div>
                    </div>

                    @error('participantsData')
                    <div class="alert alert-warning py-2 md:py-3 mb-4 mx-4 md:mx-0 rounded-xl text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-4 w-4 md:h-5 md:w-5" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>Pilih minimal 1 peserta.</span>
                    </div>
                    @enderror

                    <div class="md:overflow-x-auto md:border md:rounded-xl md:border-base-200 md:mb-2 bg-base-200/20 md:bg-transparent">

                        <div class="block md:table w-full text-sm">

                            <div class="hidden md:table-header-group bg-base-200/50 text-base-content">
                                <div class="table-row font-semibold">
                                    <div class="table-cell w-16 text-center py-3">Pilih</div>
                                    <div class="table-cell py-3">Informasi Karyawan</div>
                                    <div class="table-cell py-3">Departemen & NIK</div>
                                    <div class="table-cell w-2/5 min-w-[300px] py-3">Detail Notifikasi</div>
                                </div>
                            </div>

                            <div class="block md:table-row-group divide-y divide-base-200 md:divide-y-0">
                                @forelse ($employees as $employee)

                                <div class="block md:table-row bg-base-100 p-4 md:p-0 md:hover:bg-base-200/40 transition-colors {{ isset($participantsData[$employee->id]['selected']) && $participantsData[$employee->id]['selected'] ? 'bg-primary/5 md:bg-primary/5 border-l-4 border-l-primary md:border-l-0' : '' }}">

                                    <div class="flex md:table-cell items-start gap-3 md:pt-4 md:text-center md:align-top mb-3 md:mb-0">
                                        <div class="pt-1 md:pt-0">
                                            <input type="checkbox"
                                                wire:model.live="participantsData.{{ $employee->id }}.selected"
                                                value="true"
                                                class="checkbox checkbox-primary checkbox-sm md:checkbox-md" />
                                        </div>

                                        <div class="md:hidden flex-1">
                                            <div class="font-bold text-sm">{{ $employee->name }}</div>
                                            <div class="text-xs text-base-content/70">{{ $employee->email }}</div>
                                            <div class="flex items-center gap-2 mt-1">
                                                <div class="badge badge-ghost badge-sm text-[10px]">{{ $employee->department_name ?? 'Belum ada Dept' }}</div>
                                                <span class="text-[10px] text-base-content/60 font-mono">NIK: {{ $employee->employee_id ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="hidden md:table-cell align-top pt-3">
                                        <div class="font-bold text-base">{{ $employee->name }}</div>
                                        <div class="text-xs text-base-content/70 mt-0.5">{{ $employee->email }}</div>
                                    </div>

                                    <div class="hidden md:table-cell align-top pt-4">
                                        <div class="badge badge-ghost badge-sm md:badge-md font-medium mb-1">{{ $employee->department_name ?? 'Belum ada Dept' }}</div>
                                        <div class="text-xs text-base-content/60 mt-1">NIK: <span class="font-mono">{{ $employee->employee_id ?? '-' }}</span></div>
                                    </div>

                                    <div class="block md:table-cell align-top md:pt-2 md:pb-3 pl-8 md:pl-0">
                                        @if(isset($participantsData[$employee->id]['selected']) && $participantsData[$employee->id]['selected'])
                                        <div class="p-3 bg-base-100 border border-base-300 rounded-xl shadow-sm space-y-3 mt-1 animate-fade-in-down w-full">
                                            <div class="text-[10px] md:text-xs font-bold text-primary tracking-wide uppercase mb-1 flex items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 md:h-4 md:w-4" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
                                                </svg>
                                                Pengaturan WA
                                            </div>

                                            <div class="flex flex-col gap-3">
                                                <x-form.input-text type="number" model="participantsData.{{ $employee->id }}.wa" placeholder="No WA (cth: 0812...)" />

                                                <div class="border-t border-base-200 pt-3 mt-1" x-on:focusin="$wire.set('activeRowId', {{ $employee->id }})">
                                                    <x-form.searchable-select-advanced
                                                        label="Supervisor (Opsional)"
                                                        placeholder="Cari SPV..."
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

                                                <x-form.input-text type="number" model="participantsData.{{ $employee->id }}.wa_spv" placeholder="No WA SPV (Opsional)" />
                                            </div>
                                        </div>
                                        @else
                                        <div class="text-[10px] md:text-xs text-base-content/40 italic pt-1 md:pt-2">Centang untuk atur notifikasi...</div>
                                        @endif
                                    </div>
                                </div>
                                @empty
                                <div class="block md:table-row">
                                    <div class="block md:table-cell md:col-span-4 text-center py-10 md:py-12">
                                        <div class="flex flex-col items-center justify-center text-base-content/50">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 md:h-12 md:w-12 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                            <span class="text-sm font-medium">Tidak ada karyawan ditemukan.</span>
                                        </div>
                                    </div>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="p-4 md:px-0 md:pb-0 border-t border-base-200 md:border-none">
                        {{ $employees->links() }}
                    </div>

                </div>
            </div>

            <div class="fixed bottom-0 left-0 right-0 p-4 bg-base-100 border-t border-base-200 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)] md:relative md:bg-transparent md:border-none md:shadow-none md:p-0 md:pt-4 flex justify-end z-50">
                <button type="submit" class="btn btn-primary w-full md:w-auto px-8 rounded-full shadow-md hover:shadow-lg transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" wire:loading.remove wire:target="generateJadwal">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    <span wire:loading.remove wire:target="generateJadwal">Generate Jadwal</span>

                    <span wire:loading wire:target="generateJadwal" class="loading loading-spinner loading-sm hidden"></span>
                    <span wire:loading wire:target="generateJadwal" class="ml-2 hidden">Memproses...</span>
                </button>
            </div>

            <div class="h-16 md:hidden"></div>

        </x-mcu.layout>
    </form>
</section>