<section class="w-full">
    <x-toast />
    <x-tabs-wpi.layout heading="Buat Laporan Inspeksi Kebakaran" subheading="Inspeksi Kebakaran - Site Tokatindung">
        <div class="p-6 bg-white rounded-lg shadow">
            <div class="mb-4">
                <fieldset class="w-full fieldset md:max-w-80">
                    <x-form.label label="Pilih Jenis Alat" required />
                    <select wire:model.live="type"
                        class="select select-xs select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden {{ $errors->has('type') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}">
                        <option value="">-- Pilih --</option>
                        @foreach (array_keys($fields) as $key)
                            <option value="{{ $key }}">{{ $key }}</option>
                        @endforeach
                    </select>
                    <x-label-error :messages="$errors->get('type')" />
                </fieldset>
            </div>

            <div class="grid grid-cols-1 gap-2 mb-4 md:grid-cols-3">
                <x-form.input-floating label="Area" wire:model="area" required />
                <x-form.input-floating label="Lokasi" wire:model="location" required />
                <fieldset class="relative fieldset">
                            <x-form.label label="Tanggal / Date" required />
                            <div class="{{ $errors->has('inspection_date') ? 'ring-1 ring-rose-500 rounded' : '' }}">
                                <div class="relative" wire:ignore x-data="{

                                    reportDate: @entangle('inspection_date'),
                                    fp: null,
                                    init() {
                                        this.fp = flatpickr(this.$refs.tanggalInput, {
                                            disableMobile: true,
                                            altInput: true,
                                            altFormat: 'd F Y',
                                            dateFormat: 'Y-m-d',
                                            // Set nilai awal dari Livewire ke Flatpickr
                                            defaultDate: this.reportDate,
                                            onChange: (selectedDates, dateStr) => {
                                                this.reportDate = dateStr;
                                            }
                                        });

                                        // Pantau perubahan dari sisi Livewire (misal: saat reset form atau edit data)
                                        this.$watch('reportDate', (newVal) => {
                                            this.fp.setDate(newVal, false);
                                        });
                                    }
                                }">

                                    <input  type="text" x-ref="tanggalInput"
                                        placeholder="Pilih Tanggal..." readonly
                                        class="input input-bordered cursor-pointer w-full focus:ring-1 focus:border-info input-xs  {{ $errors->has('inspection_date') ? 'ring-1 ring-rose-500' : '' }}" />
                                </div>
                            </div>
                            <x-label-error :messages="$errors->get('inspection_date')" />
                        </fieldset>
                <input type="date" wire:model="inspection_date" class="p-2 border rounded">
            </div>

            <div class="p-4 border rounded-lg bg-gray-50">
                <h3 class="mb-3 font-bold">Kondisi Checklist ({{ $type }}):</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3 lg:grid-cols-4">
                    @foreach ($fields[$type] as $field)
                        <fieldset class="p-2 border rounded-md fieldset">
                            <label class="label">
                                {{ $field }}
                                <input type="checkbox" wire:key="condition-{{ $field }}"
                                    wire:model="conditions.{{ $field }}"
                                    class="checkbox checkbox-xs border-rose-600 bg-rose-500 checked:border-emerald-500 checked:bg-emerald-400 checked:text-emerald-800" />
                            </label>
                        </fieldset>
                    @endforeach
                </div>
            </div>

            <textarea wire:model="remarks" placeholder="Remarks/Catatan..." class="w-full p-2 mt-4 border rounded"></textarea>

            <button wire:click="save"
                class="w-full py-2 mt-4 font-bold text-white bg-blue-600 rounded hover:bg-blue-700">
                Simpan Laporan
            </button>
        </div>
    </x-tabs-wpi.layout>
