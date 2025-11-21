<section class="w-full">
    <x-toast />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
    @include('partials.manhours')
    <div class="flex justify-between">
        <!-- You can open the modal using ID.showModal() method -->
        @can('create', \App\Models\Manhour::class)
        <flux:tooltip content="tambah data" position="top">
            <flux:button size="xs" wire:click='open_modal' icon="add-icon" variant="primary"></flux:button>
        </flux:tooltip>
        @endif
    </div>
    <x-manhours.layout>
        <div class="overflow-x-auto ">
            <table class="table table-xs">
                <thead>
                    <tr>
                        <th></th>
                        <th>Tanggal</th>
                        <th>Jenis Entitas</th>
                        <th>Perusahaan</th>
                        <th>Departemen</th>
                        <th>Departemen Group</th>
                        <th>Job Class</th>
                        <th>Manhour</th>
                        <th>Manpower</th>
                        @can('create', \App\Models\Manhour::class)<th>Aksi</th>@endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data_manhours as $no => $manhour)
                    <tr>
                        <th>{{ $data_manhours->firstItem() + $no }}</th>
                        <td>{{ \Carbon\Carbon::parse($manhour->date)->translatedFormat('M-Y') }}</td>
                        <td>{{ $manhour->company_category }}</td>
                        <td>{{ $manhour->company }}</td>
                        <td>{{ $manhour->department }}</td>
                        <td>{{ $manhour->dept_group }}</td>
                        <td>{{ $manhour->job_class }}</td>
                        <td>{{ $manhour->manhours }}</td>
                        <td>{{ $manhour->manpower }}</td>
                        @can('create', \App\Models\Manhour::class)
                        <th class='flex justify-center flex-row gap-2'>
                            <flux:tooltip content="edit" position="top">
                                <flux:button wire:click="open_modal({{ $manhour->id }})" size="xs" icon="pencil-square" variant="subtle"></flux:button>
                            </flux:tooltip>
                            <flux:modal.trigger name="delete-bu">
                                <flux:tooltip content="hapus" position="top">
                                    <flux:button wire:click="showDelete({{ $manhour->id }})" size="xs" icon="trash" variant="danger"></flux:button>
                                </flux:tooltip>
                            </flux:modal.trigger>
                        </th>
                        @endif
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $data_manhours->links() }}</div>
        <div class="modal {{ $modalOpen }}">
            <div class="modal-box max-w-4xl w-11/12 max-h-[90vh] md:max-h-[85vh] lg:max-h-[80vh] overflow-y-auto">
                <form wire:submit.prevent="{{ $selectedId ? "update($selectedId)" : 'store' }}">
                    <fieldset class="fieldset bg-base-200 border-base-300 rounded-box border p-4 overflow-y-auto">
                        <legend class="fieldset-legend">Formulir {{ $form }} Manhours & Manpower</legend>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            {{-- Bulan --}}
                            <fieldset class="fieldset">
                                <x-form.label label="Bulan" required />
                                <div x-data="{
                                    fp: null,
                                    initFlatpickr() {
                                        this.fp = flatpickr(this.$refs.input, {
                                            plugins: [
                                                new monthSelectPlugin({
                                                    disableMobile: true,
                                                    shorthand: true,  // Jan, Feb, ...
                                                    dateFormat: 'M-Y', // format yang dikirim ke Livewire
                                                    altFormat: 'F Y',  // format yang ditampilkan ke user (September 2025)
                                                    theme: 'light'
                                                })
                                            ],
                                            onChange: (selectedDates, dateStr) => {
                                                $wire.set('date', dateStr)
                                            }
                                        })
                                    }
                                }" x-init="initFlatpickr()" x-effect="if($wire.date) fp.setDate($wire.date, true)" wire:ignore>
                                    <input x-ref="input" type="text" wire:model.live="date" class="input input-bordered w-full md:max-w-md focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" placeholder="Pilih bulan" />
                                </div>
                                <x-label-error :messages="$errors->get('date')" />
                            </fieldset>

                            {{-- Kategori Perusahaan --}}
                            <fieldset class="fieldset">
                                <x-form.label label="Jenis Entitas" required />
                                <select wire:model.live="entityType" class="select select-xs md:select-xs select-bordered w-full md:max-w-md focus:ring-1 focus:border-info focus:ring-info focus:outline-none">
                                    <option value="">-- Pilih --</option>
                                    <option value="owner">Perusahaan (Owner)</option>
                                    <option value="contractor">Kontraktor</option>

                                </select>

                                <x-label-error :messages="$errors->get('entityType')" />
                            </fieldset>

                            {{-- Perusahaan --}}
                            <fieldset class="fieldset">
                                <x-form.label label="Perusahaan" required />
                                <select wire:model.live="company" class="select select-xs md:select-xs select-bordered w-full md:max-w-md focus:ring-1 focus:border-info focus:ring-info focus:outline-none">
                                    <option value="">-- Pilih --</option>

                                    {{-- kalau ada owners --}}
                                    @isset($companies['owners'])
                                    @foreach ($companies['owners'] as $comp)
                                    <option value="{{ $comp->company_name }}" @selected($company===$comp->company_name)>
                                        {{ $comp->company_name }}
                                    </option>
                                    @endforeach
                                    @endisset

                                    @isset($companies['contractors'])
                                    @foreach ($companies['contractors'] as $cont)
                                    <option value="{{ $cont->contractor_name }}" @selected($company===$cont->contractor_name)>
                                        {{ $cont->contractor_name }}
                                    </option>
                                    @endforeach
                                    @endisset
                                </select>
                                <x-label-error :messages="$errors->get('company')" />
                            </fieldset>


                            {{-- Departemen --}}
                            <fieldset class="fieldset">
                                <x-form.label label="Department" required />
                                <select wire:model.live="department" class="select select-xs md:select-xs select-bordered w-full md:max-w-md focus:ring-1 focus:border-info focus:ring-info focus:outline-none">
                                    <option value="">-- Pilih --</option>
                                    @if($entityType === "contractor")
                                    @foreach ($custodian as $cust)
                                    <option value="{{ $cust->Departemen->department_name }}" @selected($department===$cust->Departemen->department_name)>
                                        {{ $cust->Departemen->department_name }}
                                    </option>
                                    @endforeach
                                    @else
                                    @foreach ($deptGroup as $dg)
                                    <option value="{{ $dg->Departemen->department_name }}" @selected($department===$dg->Departemen->department_name)>
                                        {{ $dg->Departemen->department_name }}
                                    </option>
                                    @endforeach
                                    @endif
                                </select>
                                <x-label-error :messages="$errors->get('department')" />
                            </fieldset>
                        </div>

                        {{-- Job Class --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            @foreach($jobclasses as $key => $label)
                            <fieldset class="fieldset border border-base-300 p-3 rounded-lg">
                                <legend class="text-xs font-semibold flex gap-2">
                                    <span>{{ $label }}</span>
                                    <label class="flex items-center space-x-1">
                                        <input type="checkbox" wire:model.live="hide.{{ $key }}" class="checkbox checkbox-xs">
                                        <span class="text-[8px] text-rose-500 capitalize">tidak ada {{ $label }}</span>
                                    </label>
                                </legend>

                                {{-- Manhours --}}
                                <fieldset class="fieldset">
                                    <x-form.label label="Manhours" :required="!$hide[$key]" />
                                    <input type="number" wire:model.live="manhours.{{ $key }}" placeholder="Masukkan manhours..." class="input input-bordered w-full input-xs focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden" @disabled($hide[$key]) />
                                    <x-label-error :messages="$errors->get('manhours.'.$key)" />
                                </fieldset>

                                {{-- Manpower --}}
                                <fieldset class="fieldset mt-2">
                                    <x-form.label label="Manpower" :required="!$hide[$key]" />
                                    <input type="number" wire:model.live="manpower.{{ $key }}" placeholder="Masukkan manpower..." class="input input-bordered w-full input-xs focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden" @disabled($hide[$key]) />
                                    <x-label-error :messages="$errors->get('manpower.'.$key)" />
                                </fieldset>
                            </fieldset>
                            @endforeach
                        </div>

                    </fieldset>

                    {{-- Tombol Aksi --}}
                    <div class="flex justify-end gap-2 mt-4">
                        <flux:button size="xs" variant="danger" wire:click="close_modal">Batal</flux:button>
                        @if ($selectedId)
                        <flux:button size="xs" variant="primary" type="submit">Update</flux:button>
                        @else
                        <flux:button size="xs" variant="primary" type="submit">Simpan</flux:button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
        {{-- Modal konfirmasi --}}
        <flux:modal name="delete-bu" wire:model="confirmingDelete">
            <div class="p-4 space-y-4">
                <h2 class="text-lg font-semibold">Konfirmasi Hapus</h2>
                <p>Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak bisa dibatalkan.</p>

                <div class="flex justify-end gap-2">
                    <flux:button wire:click="$set('confirmingDelete', false)" variant="subtle">Batal</flux:button>
                    <flux:button wire:click="delete" variant="danger">Hapus</flux:button>
                </div>
            </div>
        </flux:modal>
    </x-manhours.layout>



</section>
