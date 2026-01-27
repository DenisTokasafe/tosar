<section class="w-full">
    <x-toast />
    <!-- Open the modal using ID.showModal() method -->
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
    @endpush
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
    @endpush
    <x-tabs-wpi.layout heading="Daftar Laporan Fire Protection" subheading="Site Tokatindung">
        <div  class="flex flex-col items-center justify-between gap-4 mb-6 md:flex-row">
            <label for="my_modal_6" class="btn btn-square btn-xs btn-soft btn-accent" >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </label>
            <input type="checkbox" id="my_modal_6" class="modal-toggle" />
            <div class="modal" role="dialog">
                <div class="modal-box">
                    <h3 class="mb-2 text-lg font-bold">Export To PDF!</h3>
                    <fieldset class="w-full fieldset ">
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
                    {{-- Bulan --}}
                    <fieldset class="w-full fieldset">
                        <x-form.label label="Bulan" required />

                        <div wire:ignore wire:key="manhours-month-picker-{{ time() }}" x-data="{
                            fp: null,
                            dateValue: @entangle('date').live,
                            initFlatpickr() {
                                // Gunakan nextTick untuk memastikan DOM input sudah render sempurna
                                this.$nextTick(() => {
                                    if (this.fp) {
                                        this.fp.destroy();
                                    }

                                    // Pastikan x-ref input tersedia
                                    if (!this.$refs.input) return;

                                    this.fp = flatpickr(this.$refs.input, {
                                    static: true,
                                        plugins: [
                                            new monthSelectPlugin({
                                                disableMobile: false,
                                                shorthand: true,
                                                dateFormat: 'M-Y',
                                                altFormat: 'F Y',
                                                theme: 'light'
                                            })
                                        ],
                                        defaultDate: this.dateValue,
                                        onChange: (selectedDates, dateStr) => {
                                            this.dateValue = dateStr;
                                        }
                                    });
                                });
                            }
                        }"
                            x-init="initFlatpickr()" x-effect="if(fp && dateValue) fp.setDate(dateValue, false)">

                            <input x-ref="input" type="text" readonly
                                class="w-full input input-bordered focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs"
                                placeholder="Pilih bulan" />
                        </div>
                        <x-label-error :messages="$errors->get('date')" />
                    </fieldset>
                    <x-form.searchable-dropdown label="Area" required modelsearch="searchLocation"
                        modelid="location_id" placeholder="Area..." :options="$locations" :showdropdown="$show_location"
                        clickaction="selectLocation" namedb="name" />
                    <label wire:click="exportPDF" wire:loading.attr="disabled"
                        class="flex items-center gap-2 text-white btn btn-error btn-sm">
                        {{-- Icon PDF --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>

                        <span wire:loading.remove wire:target="exportPDF">Export to PDF</span>
                        <span wire:loading wire:target="exportPDF">Generating PDF...</span>
                    </label>
                </div>
               <label class="modal-backdrop" for="my_modal_6">Close</label>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="table table-xs table-zebra">
                <thead>
                    <tr class="text-center bg-gray-100">
                        <th>No</th>
                        <th>Jenis Alat</th>
                        <th>Area & Lokasi spesifik</th>
                        <th>Data Teknis & Kondisi</th>
                        <th>Pemeriksa</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($inspections as $index => $item)
                        <tr wire:key="row-{{ $item->id }}" class=" odd:bg-white even:bg-gray-100">
                            <td class="text-center">{{ $inspections->firstItem() + $index }}</td>
                            <td class="text-center">
                                <span class="w-32 font-semibold badge badge-soft badge-info"><span
                                        class="text-xs">{{ $item->type }}</span></span>
                            </td>
                            <td class="text-center">
                                <div class="text-[10px] opacity-60">{{ $item->area }}</div>
                                <div class="font-bold">{{ $item->location }}</div>
                            </td>
                            <td>
                                <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-[10px]">
                                    @foreach ($item->conditions as $key => $value)
                                        <div class="flex justify-between py-1 border-b border-dotted">
                                            <span class="font-medium uppercase text-[10px]">{{ $key }}:</span>

                                            {{-- Hapus tanda petik karena di JSON datanya boolean murni --}}
                                            @if ($value === 'yes' || $value === true)
                                                <span class="text-success text-[10px] font-bold">✔</span>
                                            @elseif($value === false)
                                                <span class="font-bold text-error text-[10px]">✘</span>
                                            @else
                                                {{-- Ini untuk data seperti "01" atau "6.8 Kg" --}}
                                                <span
                                                    class="text-blue-600 font-semibold text-[10px]">{{ $value }}</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                {{-- Menampilkan pemeriksa yang digabung dengan '|' --}}
                                @php $pemeriksa = explode('|', $item->inspected_by); @endphp
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($pemeriksa as $nama)
                                        <span class="badge badge-ghost badge-xs">{{ $nama }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="text-center">
                                {{ \Carbon\Carbon::parse($item->inspection_date)->format('d/m/Y') }}
                            </td>
                            <td class="text-center">
                                <div class="flex gap-2">
                                    @if ($item->documentation_path)
                                        <a href="{{ Storage::url($item->documentation_path) }}" target="_blank"
                                            class="btn btn-ghost btn-xs text-info">Doc</a>
                                    @endif
                                    <button wire:click="edit({{ $item->id }})"
                                        class="btn btn-ghost btn-xs">Edit</button>


                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">
                {{ $inspections->links() }}
            </div>
        </div>
    </x-tabs-wpi.layout>
</section>
