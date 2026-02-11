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

        <div
            class="flex flex-col items-center justify-between gap-4 md:shadow-md md:px-4 md:absolute md:inset-x-0 md:top-0 md:z-20 md:flex-row bg-base-100 md:inset-shadow-sm">

            <div class="w-full md:flex md:justify-start ">
                <div class="grid grid-cols-1 gap-2 md:grid-cols-3">
                    <fieldset class="w-full fieldset md:max-w-80">
                        <x-form.label label="Pilih Jenis Alat" required />
                        <select wire:model.live="search_type"
                            class="select select-xs select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden {{ $errors->has('type') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}">
                            <option value="">-- Pilih --</option>
                            @foreach ($availableTypes as $typeName)
                                <option value="{{ $typeName }}">{{ $typeName }}</option>
                            @endforeach
                        </select>
                        <x-label-error :messages="$errors->get('type')" />
                    </fieldset>
                    <fieldset class="w-full fieldset">
                        <x-form.label label="Pilih Bulan" required />

                        <div class="w-full" wire:ignore wire:key="manhours-month-picker-{{ time() }}"
                            x-data="{
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
                            }" x-init="initFlatpickr()"
                            x-effect="if(fp && dateValue) fp.setDate(dateValue, false)">

                            <input x-ref="input" type="text" readonly
                                class="w-full input input-bordered focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs"
                                placeholder="Pilih bulan" />
                        </div>
                        <x-label-error :messages="$errors->get('date')" />
                    </fieldset>
                    <x-form.searchable-dropdown label="Pilih Area" required modelsearch="searchLocation"
                        modelid="location_id" placeholder="Area..." :options="$locations" :showdropdown="$show_location"
                        clickaction="selectLocation" namedb="name" />
                </div>
            </div>
            <div class="flex flex-row items-center justify-start gap-2 md:justify-center">

                @if (count($selectedItems) > 0)
                    <div class="tooltip md:tooltip-left">
                        <div class="z-40 tooltip-content tooltip-error">
                            <div class="text-sm font-black animate-bounce">hapus data pilihan</div>
                        </div>
                        <label
                            onclick="confirm('Yakin ingin menghapus {{ count($selectedItems) }} data?') || event.stopImmediatePropagation()"
                            wire:click="deleteSelected" class="btn btn-square btn-xs btn-soft btn-error">
                            <x-icon.delete />
                        </label>
                    </div>
                @endif
                <div class="tooltip md:tooltip-left">
                    <div class="z-40 tooltip-content tooltip-primary">
                        <div class="text-sm font-black animate-bounce">Laporan Baru</div>
                    </div>
                    <a href="{{ route('fire-inspection') }}" class="text-xs uppercase btn btn-square btn-primary btn-xs btn-soft">
                       <x-icon.add />
                    </a>
                </div>
                <div class="tooltip md:tooltip-left">
                    <div class="z-40 tooltip-content tooltip-accent">
                        <div class="text-sm font-black animate-bounce">export PDF</div>
                    </div>
                    <label wire:click='clear_filter' for="my_modal_6" class="btn btn-square btn-xs btn-soft btn-accent">
                       <x-icon.file-download />
                    </label>
                </div>


            </div>
        </div>
        <input type="checkbox" id="my_modal_6" class="modal-toggle" />
        <div class="modal" role="dialog">
            <div class="modal-box">
                <h3 class="mb-2 text-lg font-bold">Export To PDF!</h3>
                <fieldset class="w-full fieldset ">
                    <x-form.label label="Pilih Jenis Alat" required />
                    <select wire:model.live="type"
                        class="select select-xs select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden {{ $errors->has('type') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}">
                        <option value="">-- Pilih --</option>
                        @foreach ($availableTypes as $typeName)
                            <option value="{{ $typeName }}">{{ $typeName }}</option>
                        @endforeach
                    </select>
                    <x-label-error :messages="$errors->get('type')" />
                </fieldset>
                {{-- Bulan --}}
                <fieldset class="w-full fieldset">
                    <x-form.label label="Bulan" required />

                    <div class="w-full" wire:ignore wire:key="manhours-month-picker-{{ time() }}"
                        x-data="{
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
                        }" x-init="initFlatpickr()"
                        x-effect="if(fp && dateValue) fp.setDate(dateValue, false)">

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

                    <span wire:loading.add.class='hidden' wire:target="exportPDF">Export to PDF</span>
                    <span class="hidden" wire:loading.remove.class='hidden' wire:target="exportPDF">Generating
                        PDF...</span>
                </label>
            </div>
            <label class="modal-backdrop" for="my_modal_6">Close</label>
        </div>

        <div class="my-10 overflow-x-auto">
            <table class="table table-xs table-zebra">
                <thead>
                    <tr class="text-center bg-gray-100">
                        <th class="border">#</th>
                        <th class="border">
                            <input type="checkbox" wire:model.live="selectAll"
                                class="checkbox checkbox-xs border-emerald-600 bg-emerald-500 checked:border-rose-500 checked:bg-rose-400 checked:text-rose-800" />
                        </th>
                        <th class="border">Reference</th>
                        <th class="border">Jenis Alat</th>
                        <th class="border">Area & Lokasi spesifik</th>
                        <th class="border">Data Teknis & Kondisi</th>
                        <th class="border">Pemeriksa</th>
                        <th class="border">Tanggal</th>
                        <th class="border">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($inspections as $index => $item)
                        <tr wire:key="row-{{ $item->id }}" wire:loading.add.class='hidden'
                            wire:target='location_id,search_type,date'
                            class="{{ in_array($item->id, $selectedItems) ? 'bg-error/10' : 'odd:bg-white even:bg-gray-100' }}">
                            <td class="text-center border">{{ $inspections->firstItem() + $index }}</td>
                            <td class="text-center border">
                                <input type="checkbox" wire:model.live="selectedItems" value="{{ $item->id }}"
                                    class="checkbox checkbox-xs border-emerald-600 bg-emerald-500 checked:border-rose-500 checked:bg-rose-400 checked:text-rose-800" />
                            </td>
                            <td class="text-center border">
                                <span
                                    class="text-xs">{{ $item->inspection_number ? $item->inspection_number : $item->inspectionSession->inspection_number }}</span>
                            </td>
                            <td class="text-center border">
                                <span class="w-32 font-semibold badge badge-soft badge-info"><span
                                        class="text-xs">{{ $item->equipmentMaster->type }}</span></span>
                            </td>
                            <td class="text-center border">
                                <div class="text-[10px] opacity-60">{{ $item->equipmentMaster->location->name }}
                                </div>
                                <div class="font-bold">{{ $item->equipmentMaster->specific_location }}</div>
                            </td>
                            <td>
                                <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-[10px]">
                                    @foreach ($item->conditions as $key => $value)
                                        <div class="flex justify-between py-1 border-b border-dotted">
                                            <span
                                                class="font-medium uppercase text-[10px]">{{ $key }}:</span>

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
                            <td class="p-2 border">
                                <div class="flex flex-col gap-2">
                                    @php
                                        $daftarNama = explode('|', $item->inspected_by ?? '');
                                    @endphp
                                    @foreach ($daftarNama as $namaOrang)
                                        @php
                                            if (empty(trim($namaOrang))) {
                                                continue;
                                            }
                                            // 1. Hapus tanda kutip (") DAN ubah koma (,) menjadi spasi
$search = ['"', ','];
$replace = ['', ' '];
$cleanName = str_replace($search, $replace, $namaOrang);

// 2. Ambil inisial dari tiap kata yang sudah bersih
$initials = collect(preg_split('/\s+/', trim($cleanName)))
    ->filter()
    ->map(fn($word) => strtoupper(substr($word, 0, 1)))
    ->implode('');
                                        @endphp

                                        <div class="flex flex-col pb-1 border-b border-gray-100 last:border-0">
                                            <span class="text-xs font-bold text-primary">{{ $initials }}</span>

                                            {{-- Nama lengkap juga dibersihkan dari tanda kutip saat ditampilkan --}}
                                            <p class="text-[10px] text-gray-500 italic leading-tight">
                                                {{ trim(str_replace('"', '', $namaOrang)) }}
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="text-center border">
                                {{ \Carbon\Carbon::parse($item->inspectionSession->inspection_date)->format('d/m/Y') }}
                            </td>
                            <td class="text-center border">
                                <div class="flex gap-2">
                                    @if ($item->documentation_path)
                                        <a href="{{ Storage::url($item->documentation_path) }}" target="_blank"
                                            class="btn btn-ghost btn-xs text-info">Doc</a>
                                    @endif

                                    <flux:tooltip content="detail" position="top">
                                        <flux:button href="{{ route('fire-inspection-edit', $item->id) }}"
                                            size="xs" icon="pencil-square" variant="subtle">
                                        </flux:button>
                                    </flux:tooltip>

                                </div>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No inspections found.</td>
                        </tr>
                    @endforelse
                    <tr>
                        <td colspan="7" wire:loading.remove.class='hidden'
                            wire:target='location_id,search_type,date' class="hidden text-center"><span
                                class="loading loading-bars loading-xl"></span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="absolute inset-x-0 bottom-0 mt-4 shadow-md bg-base-100 inset-shadow-sm">
            {{ $inspections->links() }}
        </div>
    </x-tabs-wpi.layout>
</section>
