<section class="w-full px-4 mx-auto sm:px-6 lg:px-8">
    <x-toast />
    <div class="flex items-center gap-3 pb-4 mb-4 border-b border-base-200">
        <div class="p-2 rounded-lg bg-primary/10">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        </div>
        <h2 class="text-xl font-bold md:text-xl text-base-content">Buat Jadwal MCU Baru</h2>
    </div>

    <form wire:submit="generateJadwal" class="space-y-6">
        <div class="grid items-start grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <div class="border shadow-sm card bg-base-100 border-base-200">
                    <div class="p-2 card-body md:p-4">
                        <h3 class="flex items-center gap-2 mb-3 text-base font-semibold md:text-lg md:mb-0 text-base-content/80">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Informasi Jadwal
                        </h3>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-2">
                            <fieldset class="w-full fieldset">
                                <x-form.label label="Tanggal MCU" required />
                                <input type="text" readonly id="schedule_date" wire:model="schedule_date" placeholder="Pilih Tanggal MCU"
                                    class="w-full cursor-pointer input input-bordered input-xs focus-within:outline-none focus-within:border-info focus-within:ring-0 focus:border-primary transition-all {{ $errors->has('schedule_date') ? 'input-error focus:ring-error/20' : '' }}"
                                    x-data="{ fp: null }" x-init="
                                fp = flatpickr($refs.input, {
                                    altInput: true,
                                    altFormat: 'F j, Y',
                                    dateFormat: 'Y-m-d' ,
                                    static: true,
                                    inline: true,

                                    });
                                    $wire.on('dateLoaded', ()=> {
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
                    <div class="border shadow-sm card bg-base-100 border-base-200">
                        <div class="p-0 card-body md:p-4">

                            <div class="flex flex-col items-start justify-between gap-4 p-2 border-b md:flex-row md:items-center border-base-200 md:border-none md:mb-4">
                                <div>
                                    <h3 class="flex items-center gap-2 text-base font-semibold md:text-lg text-base-content">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        Daftar Peserta
                                    </h3>
                                    <p class="mt-1 text-xs md:text-sm text-base-content/60">Pilih karyawan yang akan dijadwalkan.</p>
                                </div>
                                <div class="w-full md:w-80">
                                    <x-form.input-floating label="Cari Nama Karyawan..." model="search" />
                                </div>
                            </div>

                            @error('participantsData')
                            <div class="py-2 mx-4 mb-4 text-sm alert alert-warning md:py-3 md:mx-0 rounded-xl">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 stroke-current shrink-0 md:h-5 md:w-5" fill="none" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <span>Pilih minimal 1 peserta.</span>
                            </div>
                            @enderror

                            <div class="md:overflow-x-auto md:border md:rounded-xl md:border-base-200 md:mb-2 bg-base-200/20 md:bg-transparent">

                                <div class="block w-full text-sm md:table">

                                    <div class="hidden md:table-header-group bg-base-200/50 text-base-content">
                                        <div class="table-row font-semibold">
                                            <div class="table-cell w-16 py-3 text-center">Pilih</div>
                                            <div class="table-cell py-3">Informasi Karyawan</div>
                                            <div class="table-cell py-3">Departemen & NIK</div>
                                            <div class="table-cell w-2/5 min-w-[300px] py-3">Detail Notifikasi</div>
                                        </div>
                                    </div>

                                    <div class="block divide-y md:table-row-group divide-base-200 md:divide-y-0">
                                        @forelse ($employees as $employee)

                                        <div wire:key="row-emp-{{ $employee->id }}" class="block md:table-row bg-base-100 p-4 md:p-0 md:hover:bg-base-200/40 transition-colors {{ isset($participantsData[$employee->id]['selected']) && $participantsData[$employee->id]['selected'] ? 'bg-primary/5 md:bg-primary/5 border-l-4 border-l-primary md:border-l-0' : '' }}">

                                            <div class="flex items-start gap-3 mb-3 md:table-cell md:pt-4 md:text-center md:align-top md:mb-0">
                                                <div class="pt-1 md:pt-0">
                                                    <input type="checkbox"
                                                        wire:model.live="participantsData.{{ $employee->id }}.selected"
                                                        value="true"
                                                        class="checkbox checkbox-primary checkbox-sm md:checkbox-md" />
                                                </div>

                                                <div class="flex-1 md:hidden">
                                                    <div class="text-sm font-bold">{{ $employee->name }}</div>
                                                    <div class="text-xs text-base-content/70">{{ $employee->email }}</div>
                                                    <div class="flex items-center gap-2 mt-1">
                                                        <div class="badge badge-ghost badge-sm text-[10px]">{{ $employee->department_name ?? 'Belum ada Dept' }}</div>
                                                        <span class="text-[10px] text-base-content/60 font-mono">NIK: {{ $employee->employee_id ?? '-' }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="hidden pt-3 align-top md:table-cell">
                                                <div class="text-base font-bold">{{ $employee->name }}</div>
                                                <div class="text-xs text-base-content/70 mt-0.5">{{ $employee->email }}</div>
                                            </div>

                                            <div class="hidden pt-4 align-top md:table-cell">
                                                <div class="mb-1 font-medium badge badge-ghost badge-sm md:badge-md">{{ $employee->department_name ?? 'Belum ada Dept' }}</div>
                                                <div class="mt-1 text-xs text-base-content/60">NIK: <span class="font-mono">{{ $employee->employee_id ?? '-' }}</span></div>
                                            </div>

                                            <div class="block pl-8 align-top md:table-cell md:pt-2 md:pb-3 md:pl-0">
                                                @if(isset($participantsData[$employee->id]['selected']) && $participantsData[$employee->id]['selected'])
                                                <div class="w-full p-3 mt-1 space-y-3 border shadow-sm bg-base-100 border-base-300 rounded-xl animate-fade-in-down">
                                                    <div class="text-[10px] md:text-xs font-bold text-primary tracking-wide uppercase mb-1 flex items-center gap-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 md:h-4 md:w-4" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
                                                        </svg>
                                                        Pengaturan WA
                                                    </div>

                                                    <div class="flex flex-col gap-3">
                                                        <x-form.input-text type="number" model="participantsData.{{ $employee->id }}.wa" placeholder="No WA (cth: 0812...)" />
                                                        <div class="pt-3 mt-1 border-t border-base-200" x-on:focusin="$wire.set('activeDeptRowId', {{ $employee->id }})">
                                                            <x-form.searchable-select-advanced
                                                                label="Dept Head (Opsional)"
                                                                placeholder="Cari Dept Head..."
                                                                modelsearch="searchDeptHead.{{ $employee->id }}"
                                                                modelid="participantsData.{{ $employee->id }}.dept_head_id"
                                                                :options="$deptHeads"
                                                                :showdropdown="$showDeptHeadDropdown[$employee->id] ?? false"
                                                                :manualMode="$manualDeptHeadMode[$employee->id] ?? false"
                                                                manualModelName="manualDeptHeadName.{{ $employee->id }}"
                                                                enableManualAction="enableManualDeptHead({{ $employee->id }})"
                                                                addManualAction="addDeptHeadManual({{ $employee->id }})"
                                                                clickaction="selectDeptHead" />
                                                        </div>

                                                        <div class="pt-3 mt-1 border-t border-base-200" x-on:focusin="$wire.set('activeSpvRowId', {{ $employee->id }})">
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
                                            <div class="block py-10 text-center md:table-cell md:col-span-4 md:py-12">
                                                <div class="flex flex-col items-center justify-center text-base-content/50">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mb-3 md:h-12 md:w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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

                            <div class="p-4 border-t md:px-0 md:pb-0 border-base-200 md:border-none">
                                {{ $employees->links() }}
                            </div>

                        </div>
                    </div>



                </x-mcu.layout>
            </div>
            <div class="lg:col-span-1">
                <div class="sticky border shadow-sm top-6 card bg-base-100 border-base-200">
                    <div class="p-4 card-body">
                        <h3 class="flex items-center gap-2 mb-2 text-lg font-bold">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            Peserta Dipilih
                        </h3>

                        <p class="mb-4 text-sm text-base-content/60">
                            Total: {{ collect($participantsData)->where('selected', true)->count() }} karyawan
                        </p>

                        <div class="max-h-[400px] overflow-y-auto space-y-2 pr-1">
                            @foreach($participantsData as $id => $data)
                            @if(isset($data['selected']) && $data['selected'])
                            <div class="flex items-center justify-between p-2 text-sm rounded-lg bg-base-200/50 group">
                                <span class="truncate">
                                    {{ $this->getEmployeeName($id) }}
                                </span>

                                <button type="button"
                                    wire:click="removeParticipant('{{ $id }}')"
                                    class="p-1 text-error hover:bg-error/20 rounded-full transition-all opacity-0 group-hover:opacity-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            @endif
                            @endforeach

                            @if(collect($participantsData)->where('selected', true)->isEmpty())
                            <div class="py-6 text-xs italic text-center text-base-content/40">
                                Belum ada peserta dipilih.
                            </div>
                            @endif
                        </div>
                        <div class="fixed bottom-0 left-0 right-0 p-4 bg-base-100 border-t border-base-200 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)] md:relative md:bg-transparent md:border-none md:shadow-none md:p-0 md:pt-4 flex justify-end z-50">
                            <button type="submit" class="w-full px-8 transition-all rounded-full shadow-md btn btn-primary md:w-auto hover:shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" wire:loading.remove wire:target="generateJadwal">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                </svg>
                                <span wire:loading.remove wire:target="generateJadwal">Generate Jadwal</span>

                                <span wire:loading wire:target="generateJadwal" class="hidden loading loading-spinner loading-sm"></span>
                                <span wire:loading wire:target="generateJadwal" class="hidden ml-2">Memproses...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>