<x-form.input-text label="Judul Insiden" model="title" required :disabled="!$canEdit" />
<div class="grid grid-cols-1 gap-2 md:grid-cols-2">
    <fieldset class="p-0 my-4 border shadow-md md:p-3 border-base-300 fieldset card bg-base-100">
        <legend class="text-sm font-semibold card-title ">{{ __('Klasifikasi Insiden') }}</legend>
        <div @class([ 'grid grid-cols-1 gap-2' , 'md:grid-cols-2'=> $this->hasSubTypes,
            'md:grid-cols-1' => !$this->hasSubTypes,])>
            <x-form.select label="Tipe Insiden" model="event_type_id" :options="$eventTypes" option-label="event_type_name" required :disabled="!$canEdit" />
            @if($this->hasSubTypes)
            <x-form.select label="Jenis Insiden" model="event_sub_type_id" :options="$eventSubTypes" option-label="event_sub_type_name" required :disabled="!$canEdit" />
            @endif

        </div>
    </fieldset>
    <fieldset class="p-0 my-4 border shadow-md md:p-3 border-base-300 fieldset card bg-base-100">
        <legend class="text-sm font-semibold card-title ">{{ __('Klasifikasi Insiden Lingkungan') }}</legend>
        <div class="">
            <x-form.select
                label="Tingkat Keparahan Lingkungan"
                model="env_classification"
                :options="$EnvironmentalIncidentClassification"
                option-label="name"
                placeholder="Pilih Klasifikasi Lingkungan"
                required :disabled="!$canEdit" />
        </div>
    </fieldset>
</div>

<div class="grid grid-cols-1 gap-2 mb-8 md:grid-cols-2 lg:grid-cols-3">
    <x-form.tgl-waktu label="Tanggal & Waktu Kejadian" model="date_time" required :disabled="!$canEdit" />

    <x-form.search-template label="Lokasi" required modelsearch="searchLocation" modelid="location_id" :options="$locations" :showdropdown="$show_location" clickaction="selectLocation" namedb="name" :disabled="!$canEdit" />

    @if ($location_id)
    <x-form.input-text label="Lokasi Spesifik" model="location_specific" placeholder="Masukkan detail lokasi spesifik..." required :disabled="!$canEdit" />
    <x-form.select
        label="Area Kontrak Karya"
        model="contract_area_name"
        :options="$contractAreas"
        option-label="name"
        placeholder="Pilih Area Kontrak Karya"
        required :disabled="!$canEdit" />
    @endif

    <x-form.department-contractor-selector
        :dept-cont="$deptCont"
        :departments="$departments"
        :contractors="$contractors"
        model_dept="department_id"
        model_cont="contractor_id"
        :showDropdown="$showDropdown"
        :showContractorDropdown="$showContractorDropdown"
        :required="true"
        :disabled="!$canEdit" />

    <x-form.select
        label="PIC"
        model="penanggungJawab"
        :options="$penanggungJawabOptions"
        optionValue="id"
        optionLabel="name"
        placeholder="-- Pilih Penanggung Jawab --"
        required="true"
        :disabled="!$canEdit" />

    <x-form.searchable-select-advanced label="Dilaporkan Oleh" placeholder="Cari Nama Pelapor..."
        modelsearch="searchPelapor" modelid="pelapor_id" :options="$pelapors"
        :showdropdown="$showPelaporDropdown" :manualMode="$manualPelaporMode"
        manualModelName="manualPelaporName" enableManualAction="enableManualPelapor"
        addManualAction="addPelaporManual" clickaction="selectPelapor" :disabled="!$canEdit" />
</div>

<fieldset class="p-0 my-4 border shadow-md md:p-3 border-base-300 fieldset card bg-base-100">
    <legend class="text-sm font-semibold card-title ">{{ __('Integrasi Risk Matrix') }}</legend>
    <div class="flex flex-col-reverse gap-2 mt-2 md:flex-row">
        <div class="space-y-4 md:grow">
            {{-- Consequence --}}
            <fieldset class="fieldset ">
                <x-form.label label="Consequence" required />
                <select wire:model.live="consequence_id" @disabled(!$canEdit)
                    class="select select-xs md:select-xs select-bordered w-full md:max-w-md focus-within:outline-none focus-within:border-info focus-within:ring-0 {{ $errors->has('consequence_id') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }} {{ !$canEdit ? 'bg-base-200 cursor-not-allowed' : '' }}">
                    <option value="">{{__('-- Pilih --')}}</option>
                    @foreach ($consequencess as $cons)
                    <option value="{{ $cons->id }}">{{ __($cons->name) }}</option>
                    @endforeach
                </select>
                <x-label-error :messages="$errors->get('consequence_id')" />

                @if ($consequence_id)
                @php $selectedConsequence = $consequencess->firstWhere('id', $consequence_id); @endphp
                @if ($selectedConsequence)
                <div class="h-20 p-2 mt-1 overflow-y-auto text-sm text-gray-600 border rounded bg-gray-50">
                    {{ __($selectedConsequence->description) ?? 'Tidak ada deskripsi' }}
                </div>
                @endif
                @endif
            </fieldset>

            {{-- Likelihood --}}
            <fieldset class="fieldset ">
                <x-form.label label="Likelihood" required />
                <select wire:model.live="likelihood_id" @disabled(!$canEdit)
                    class="select select-xs md:select-xs select-bordered w-full md:max-w-md focus-within:outline-none focus-within:border-info focus-within:ring-0 {{ $errors->has('likelihood_id') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }} {{ !$canEdit ? 'bg-base-200 cursor-not-allowed' : '' }}">
                    <option value="">{{__('-- Pilih --')}}</option>
                    @foreach ($likelihoodss as $like)
                    <option value="{{ $like->id }}">{{ __($like->name) }}</option>
                    @endforeach
                </select>
                <x-label-error :messages="$errors->get('likelihood_id')" />

                @if ($likelihood_id)
                @php $selectedLikelihood = $likelihoodss->firstWhere('id', $likelihood_id); @endphp
                @if ($selectedLikelihood)
                <div class="h-20 p-2 mt-1 overflow-y-auto text-sm text-gray-600 border rounded bg-gray-50">
                    {{ __($selectedLikelihood->description) ?? 'Tidak ada deskripsi' }}
                </div>
                @endif
                @endif
            </fieldset>
        </div>

        {{-- Kolom Risk Matrix --}}
        <div class="flex-none overflow-x-auto ">
            <table class="table table-xs w-60">
                <thead>
                    <tr class="text-center text-[9px]">
                        <td class=" border-1">{{ __('Level') }}</td>
                        <td class="rotate_text border-1 bg-emerald-500">{{ __('Rendah') }}</td>
                        <td class="bg-yellow-500 rotate_text border-1">{{ __('Sedang') }}</td>
                        <td class="bg-orange-500 rotate_text border-1">{{ __('Tinggi') }}</td>
                        <td class="rotate_text border-1 bg-rose-500">{{ __('Ekstrem') }}</td>
                        <td class="bg-gray-100 rotate_text border-1">{{ __('Ditutup') }}</td>
                    </tr>
                    <tr class="text-center text-[9px]">
                        <th class="border-1">Likelihood ↓ / Consequence →</th>
                        @foreach ($consequences as $c)
                        <th class="rotate_text border-1">{{ __($c->name) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($likelihoods as $l)
                    <tr class="text-center text-[9px]">
                        <td class="w-1 font-bold border-1">{{ __($l->name) }}</td>
                        @foreach ($consequences as $c)
                        @php
                        $cell = App\Models\RiskMatrixCell::where('likelihood_id', $l->id)
                        ->where('risk_consequence_id', $c->id)
                        ->first() ?? null;
                        $severity = $cell?->severity ?? '';
                        $color = match ($severity) {
                        'Rendah' => 'bg-emerald-500',
                        'Sedang' => 'bg-yellow-500',
                        'Tinggi' => 'bg-orange-500',
                        'Ekstrem' => 'bg-rose-500',
                        default => 'bg-gray-100',
                        };
                        @endphp
                        <td class="cursor-pointer @if ($likelihood_id == $l->id && $consequence_id == $c->id) border-2 bg-primary border-primary-content @endif">
                            <span
                                @if($canEdit) wire:click="edit({{ $l->id }}, {{ $c->id }})" @endif
                                class="btn btn-square btn-xs {{ $color }} {{ !$canEdit ? 'opacity-50 btn-disabled cursor-not-allowed' : '' }}">
                                {{ Str::upper(substr(__( $severity ), 0, 1)) }}
                            </span>
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    {{-- ... Risk Assessment Info Table (Read Only by nature) ... --}}
</fieldset>

<x-form.text_area label="Narasi detail mengenai urutan kejadian (5W+1H)" :deskripsi="true" deskripsi_value="deskripsi_insident" model="description" placeholder="{{ __('Contoh: Siapa yang terlibat...')}}" required :disabled="!$canEdit" />
<x-form.text_area label="Tindakan Darurat" :deskripsi="true" deskripsi_value="deskripsi_darurat" model="emergency_action" placeholder="{{ __('Jelaskan tindakan segera...')}}" required :disabled="!$canEdit" />

@if($this->isInjury)
<fieldset class="p-3 my-4 border shadow-md border-base-300 fieldset card bg-base-100">
    <legend class="text-sm font-semibold card-title ">{{ __('Bagian Tubuh yang Terluka') }}</legend>
    <div @class([ 'grid grid-cols-1 gap-2' , 'md:grid-cols-2'=> $selectedBodyPartCategory, 'md:grid-cols-1' => !$selectedBodyPartCategory ])>
        <x-form.select label="Kategori Bagian Tubuh" model="selectedBodyPartCategory" :options="$this->existingCategory" option-value="category" option-label="category" placeholder="-- Pilih --" required :disabled="!$canEdit" />

        @if ($selectedBodyPartCategory)
        <x-form.select label="Detail Bagian Tubuh" model="selectedBodyPart" :options="$detailsBodyPart" option-label="display_name" placeholder="-- Pilih --" required :disabled="!$canEdit" />
        @endif
    </div>
</fieldset>
@else
<fieldset class="p-3 my-4 border shadow-md border-base-300 fieldset card bg-base-100">
    <legend class="text-sm font-semibold card-title ">{{ __('Kerusakan alat atau dampak lingkungan') }}</legend>
    <x-form.text_area label="Detail Kerusakan Alat / Lingkungan" :deskripsi="true" deskripsi_value="ket_insident" model="damage_detail" placeholder="{{ __('Jelaskan kerusakan...')}}" required :disabled="!$canEdit" />
</fieldset>
@endif