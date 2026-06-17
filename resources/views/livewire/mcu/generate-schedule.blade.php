<section class="w-full">

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

    <h2 class="card-title text-2xl font-bold border-b  mb-4">Buat Jadwal MCU Baru</h2>
    <form wire:submit="generateJadwal">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-2">

            <fieldset class="fieldset">
                <x-form.label label="Tanggal Lahir" required />
                <input type="text" readonly id="schedule_date" wire:model="schedule_date"
                    class="w-full cursor-pointer input input-bordered focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs"
                    placeholder="Pilih tanggal lahir {{ $errors->has('schedule_date') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}"
                    x-data="{ fp: null }" x-init="
                                    fp = flatpickr($refs.input, {
                                        dateFormat: 'Y-m-d',
                                    });

                                    // Dengarkan event 'dateLoaded' dari Livewire
                                    $wire.on('dateLoaded', () => {
                                        // Set tanggal menggunakan nilai Livewire saat event dipanggil
                                        if ($wire.schedule_date) {
                                            fp.setDate($wire.schedule_date);
                                        }
                                    });" x-ref="input" />
                <x-label-error :messages="$errors->get('schedule_date')" />
            </fieldset>
            <x-form.input-text label="Lokasi MCU" model="location" placeholder="Lokasi MCU" />


        </div>
        <x-manhours.layout>
            <div class="divider text-lg font-semibold">Daftar Peserta (Karyawan)</div>
            <div class="w-full md:max-w-sm"><x-form.input-floating label="Cari Nama Karyawan" model="search" /></div>
            @error('participantsData')
            <div class="alert alert-warning py-2 mb-4 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>Anda harus memilih minimal 1 peserta untuk membuat jadwal.</span>
            </div>
            @enderror

            <div class="overflow-x-auto border border-base-200 rounded-box shadow-sm mb-6">
                <table class="table table-zebra table-pin-rows w-full table-xs">
                    <thead class="bg-base-200 text-base-content text-sm">
                        <tr>
                            <th class="w-16 text-center">Pilih</th>
                            <th>Informasi Karyawan</th>
                            <th>Departemen & NIK</th>
                            <th class="w-1/4">No WhatsApp & Supervisor</th>
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
                                <div class="flex flex-col gap-2">
                                    <x-form.input-text type="number" model="participantsData.{{ $employee->id }}.wa" placeholder="No WA" />

                                    <div x-on:focusin="$wire.set('activeRowId', {{ $employee->id }})">

                                        <x-form.searchable-select-advanced
                                            label="Supervisor"
                                            placeholder="Cari Nama Supervisor..."
                                            modelsearch="searchSupervisor.{{ $employee->id }}"
                                            modelid="participantsData.{{ $employee->id }}.spv_id"
                                            :options="$managers"

                                            {{-- Pastikan diberi nilai default true/false jika array belum terisi --}}
                                            :showdropdown="$showSupervisorDropdown[$employee->id] ?? false"
                                            :manualMode="$manualSupervisorMode[$employee->id] ?? false"

                                            manualModelName="manualSupervisorName.{{ $employee->id }}"

                                            {{-- GANTI TITIK MENJADI KURUNG PARAMETER --}}
                                            enableManualAction="enableManualSupervisor({{ $employee->id }})"
                                            addManualAction="addSupervisorManual({{ $employee->id }})"

                                            clickaction="selectSupervisor" />

                                    </div>
                                    <x-form.input-text type="number" model="participantsData.{{ $employee->id }}.wa_spv" placeholder="No WA Supervisor" />


                                </div>
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

            <div class=" justify-end pt-4 border-t border-base-200">
                <button type="submit" class="btn btn-primary btn-xs">

                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" wire:loading.remove wire:target="generateJadwal">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    Generate Jadwal MCU

                    <span wire:loading.remove.class="hidden" wire:target="generateJadwal" class="loading loading-spinner hidden"></span>
                </button>
            </div>
        </x-manhours.layout>
    </form>


</section>