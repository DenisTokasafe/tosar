{{-- Part 1: Tipe & Jenis Insiden --}}
<div @class([ 'grid gap-4 mb-4' , 'grid-cols-1 md:grid-cols-2'=> $this->hasSubTypes,
    'grid-cols-1' => !$this->hasSubTypes
    ])>
    <x-form.select label="Tipe Insiden" model="event_type_id" :options="$eventTypes" option-label="event_type_name" required />

    @if($this->hasSubTypes)
    <x-form.select label="Jenis Insiden" model="event_sub_type_id" :options="$eventSubTypes" option-label="event_sub_type_name" required />
    @endif
</div>

{{-- Part 2: Grid Utama (Responsive: Mobile 1, Tab 2, Desktop 3) --}}
<div class="grid grid-cols-1 gap-4 mb-8 sm:grid-cols-2 lg:grid-cols-3">
    <x-form.tgl-waktu label="Tanggal & Waktu Kejadian" model="date_time" required />

    <x-form.search-template label="Lokasi" required modelsearch="searchLocation" modelid="location_id" :options="$locations" :showdropdown="$show_location" clickaction="selectLocation" namedb="name" />

    @if ($location_id)
    <x-form.input-text label="Lokasi Spesifik" model="location_specific" placeholder="Detail lokasi..." required />
    @endif

    <x-form.department-contractor-selector
        :dept-cont="$deptCont" :departments="$departments" :contractors="$contractors"
        model_dept="department_id" model_cont="contractor_id"
        :showDropdown="$showDropdown" :showContractorDropdown="$showContractorDropdown"
        :required="true" />

    <x-form.select label="PIC" model="penanggungJawab" :options="$penanggungJawabOptions" optionValue="id" optionLabel="name" placeholder="-- Pilih PIC --" required="true" />

    <x-form.searchable-select-advanced label="Dilaporkan Oleh" placeholder="Cari Nama..."
        modelsearch="searchPelapor" modelid="pelapor_id" :options="$pelapors"
        :showdropdown="$showPelaporDropdown" :manualMode="$manualPelaporMode"
        manualModelName="manualPelaporName" enableManualAction="enableManualPelapor"
        addManualAction="addPelaporManual" clickaction="selectPelapor" />
</div>

{{-- Part 3: Risk Matrix --}}
<fieldset class="p-4 my-6 border shadow-md border-base-300 rounded-xl bg-base-100">
    <legend class="px-2 text-sm font-bold uppercase tracking-widest text-primary">{{ __('Integrasi Risk Matrix') }}</legend>

    <div class="flex flex-col gap-6 lg:flex-row">
        <div class="flex-1 space-y-4">
            {{-- Consequence --}}
            <div class="form-control">
                <x-form.label label="Consequence" required />
                <select wire:model.live="consequence_id" @class([ 'select select-sm select-bordered w-full focus:ring-1 focus:ring-info' , 'select-error'=> $errors->has('consequence_id')
                    ])>
                    <option value="">{{__('-- Pilih --')}}</option>
                    @foreach ($consequencess as $cons)
                    <option value="{{ $cons->id }}">{{ __($cons->name) }}</option>
                    @endforeach
                </select>
                @if ($consequence_id && $selectedConsequence = $consequencess->firstWhere('id', $consequence_id))
                <div class="p-3 mt-2 text-xs italic border-l-4 rounded bg-base-200 border-info">{{ __($selectedConsequence->description) }}</div>
                @endif
            </div>

            {{-- Likelihood --}}
            <div class="form-control">
                <x-form.label label="Likelihood" required />
                <select wire:model.live="likelihood_id" @class([ 'select select-sm select-bordered w-full focus:ring-1 focus:ring-info' , 'select-error'=> $errors->has('likelihood_id')
                    ])>
                    <option value="">{{__('-- Pilih --')}}</option>
                    @foreach ($likelihoodss as $like)
                    <option value="{{ $like->id }}">{{ __($like->name) }}</option>
                    @endforeach
                </select>
                @if ($likelihood_id && $selectedLikelihood = $likelihoodss->firstWhere('id', $likelihood_id))
                <div class="p-3 mt-2 text-xs italic border-l-4 rounded bg-base-200 border-info">{{ __($selectedLikelihood->description) }}</div>
                @endif
            </div>
        </div>

        {{-- Table Matrix (Scrollable di HP) --}}
        <div class="w-full overflow-x-auto lg:w-auto">
            <table class="table table-xs table-fixed border-separate border-spacing-px bg-base-300 rounded-lg overflow-hidden min-w-[300px]">
                <thead>
                    <tr class="text-center text-[10px] bg-base-100">
                        <th class="bg-base-200 w-20">Matrix</th>
                        @foreach ($consequences as $c)
                        <th class="p-1 truncate">{{ __($c->name) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($likelihoods as $l)
                    <tr class="text-center bg-base-100">
                        <td class="font-bold bg-base-200 text-[10px] p-1">{{ __($l->name) }}</td>
                        @foreach ($consequences as $c)
                        @php
                        $cell = App\Models\RiskMatrixCell::where('likelihood_id', $l->id)->where('risk_consequence_id', $c->id)->first();
                        $severity = $cell?->severity ?? '';
                        $color = match ($severity) {
                        'Rendah' => 'bg-emerald-500',
                        'Sedang' => 'bg-yellow-400',
                        'Tinggi' => 'bg-orange-500',
                        'Ekstrem' => 'bg-rose-600',
                        default => 'bg-gray-200',
                        };
                        $isActive = ($likelihood_id == $l->id && $consequence_id == $c->id);
                        @endphp
                        <td @class(['p-1', 'ring-2 ring-primary ring-inset z-10'=> $isActive])>
                            <button wire:click="edit({{ $l->id }}, {{ $c->id }})"
                                class="w-8 h-8 rounded flex items-center justify-center text-[10px] font-bold text-white transition-transform active:scale-90 {{ $color }}">
                                {{ Str::upper(substr(__($severity), 0, 1)) }}
                            </button>
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</fieldset>

{{-- Part 4: Injury / Damage --}}
<div class="mt-6">
    @if($this->isInjury)
    <fieldset class="p-4 border shadow-md border-base-300 rounded-xl bg-base-100">
        <legend class="px-2 text-sm font-bold uppercase tracking-widest text-primary">{{ __('Bagian Tubuh Terluka') }}</legend>
        <div @class([ 'grid gap-4' , 'grid-cols-1 md:grid-cols-2'=> $selectedBodyPartCategory,
            'grid-cols-1' => !$selectedBodyPartCategory
            ])>
            <x-form.select label="Kategori" model="selectedBodyPartCategory" :options="$this->existingCategory" option-value="category" option-label="category" required />
            @if ($selectedBodyPartCategory)
            <x-form.select label="Detail" model="selectedBodyPart" :options="$detailsBodyPart" option-label="display_name" required />
            @endif
        </div>
    </fieldset>
    @else
    <fieldset class="p-4 border shadow-md border-base-300 rounded-xl bg-base-100">
        <legend class="px-2 text-sm font-bold uppercase tracking-widest text-primary">{{ __('Dampak Lingkungan / Alat') }}</legend>
        <x-form.text_area label="Detail Kerusakan" model="damage_detail" rows="3" required />
    </fieldset>
    @endif
</div>