<div class="mt-12 mb-8">
    <div @class([ 'grid grid-cols-1 gap-2' , 'md:grid-cols-3'=> $this->hasSubTypes,
        'md:grid-cols-2' => !$this->hasSubTypes,])>
        <x-form.select label="Tipe Insiden" model="event_type_id" :options="$eventTypes" option-label="event_type_name" required />
        @if($this->hasSubTypes)
        <x-form.select label="Jenis Insiden" model="event_sub_type_id" :options="$eventSubTypes" option-label="event_sub_type_name" required />
        @endif
        <x-form.select-categroy-bahaya options_label="-- Pilih Kategori Insiden --" :key-word="$keyWord" :ktas="$ktas" :ttas="$ttas" model_kta="kondisi_tidak_aman" model_tta="tindakan_tidak_aman" />
    </div>
    <fieldset class="p-3 my-4 border shadow-md border-base-300 fieldset card bg-base-100">
        <legend class="text-sm font-semibold card-title ">{{ __('Integrasi Risk Matrix') }}</legend>
        <div class="flex flex-col-reverse gap-2 mt-2 md:flex-row">
            {{-- Kolom Likelihood & Consequence --}}
            <div class="space-y-4 md:grow">
                {{-- Consequence --}}
                <fieldset class="fieldset ">
                    <x-form.label label="Consequence" required />
                    <select wire:model.live="consequence_id"
                        class="select select-xs md:select-xs select-bordered w-full md:max-w-md focus-within:outline-none focus-within:border-info focus-within:ring-0 {{ $errors->has('consequence_id') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}">
                        <option value="">{{__('-- Pilih --')}}</option>
                        @foreach ($consequencess as $cons)
                        <option value="{{ $cons->id }}">{{ __($cons->name) }}</option>
                        @endforeach
                    </select>
                    <x-label-error :messages="$errors->get('consequence_id')" />

                    @if ($consequence_id)
                    @php
                    $selectedConsequence = $consequencess->firstWhere('id', $consequence_id);
                    @endphp
                    @if ($selectedConsequence)
                    <div
                        class="h-20 p-2 mt-1 overflow-y-auto text-sm text-gray-600 border rounded bg-gray-50">
                        {{ __($selectedConsequence->description) ?? 'Tidak ada deskripsi' }}
                    </div>
                    @endif
                    @endif
                </fieldset>
                {{-- Likelihood --}}
                <fieldset class="fieldset ">
                    <x-form.label label="Likelihood" required />
                    <select wire:model.live="likelihood_id"
                        class="select select-xs md:select-xs select-bordered w-full md:max-w-md focus-within:outline-none focus-within:border-info focus-within:ring-0 {{ $errors->has('likelihood_id') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}">
                        <option value="">{{__('-- Pilih --')}}</option>
                        @foreach ($likelihoodss as $like)
                        <option value="{{ $like->id }}">{{ __($like->name) }}</option>
                        @endforeach
                    </select>
                    <x-label-error :messages="$errors->get('likelihood_id')" />

                    @if ($likelihood_id)
                    @php
                    $selectedLikelihood = $likelihoodss->firstWhere('id', $likelihood_id);
                    @endphp
                    @if ($selectedLikelihood)
                    <div
                        class="h-20 p-2 mt-1 overflow-y-auto text-sm text-gray-600 border rounded bg-gray-50">
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
                            <td class="text-white rotate_text border-1 bg-emerald-500">{{ __('Rendah') }}</td>
                            <td class="text-white bg-yellow-500 rotate_text border-1">{{ __('Sedang') }}</td>
                            <td class="text-white bg-orange-500 rotate_text border-1">{{ __('Tinggi') }}</td>
                            <td class="text-white rotate_text border-1 bg-rose-500">{{ __('Ekstrem') }}</td>
                            <td class="text-black bg-gray-100 rotate_text border-1">{{ __('Ditutup') }}</td>
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
                        <tr class="w-32 text-xs text-center">

                            <td class="w-1 font-bold border-1">{{ __($l->name) }}</td>
                            @foreach ($consequences as $c)
                            @php
                            $cell =
                            App\Models\RiskMatrixCell::where('likelihood_id', $l->id)
                            ->where('risk_consequence_id', $c->id)
                            ->first() ?? null;
                            $score = $l->level * $c->level;
                            $severity = $cell?->severity ?? '';
                            $color = match ($severity) {
                            'Rendah' => 'bg-emerald-500',
                            'Sedang' => 'bg-yellow-500',
                            'Tinggi' => 'bg-orange-500',
                            'Ekstrem' => 'bg-rose-500',
                            default => 'bg-gray-100',
                            };
                            @endphp
                            <td
                                class="border cursor-pointer   @if ($likelihood_id == $l->id && $consequence_id == $c->id) border-2 bg-primary border-primary-content @endif">
                                <span wire:click="edit({{ $l->id }}, {{ $c->id }})"
                                    class="btn btn-square btn-xs   {{ $color }}">{{ Str::upper(substr(__($severity), 0, 1)) }}</span>
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if ($RiskAssessment != null)
        <table class="table mt-4 table-xs table-zebra">

            <tr>
                <th class="w-40 text-xs border-base-300">Potential Risk Rating</th>
                <td class="pl-2 text-xs border-base-300">
                    {{ $RiskAssessment->name }}
                </td>
            </tr>
            <tr>
                <th class="w-40 text-xs border-base-300">Notify</th>
                <td class="pl-2 text-xs border-base-300">
                    {{ $RiskAssessment->reporting_obligation }}
                </td>
            </tr>
            <tr>
                <th class="w-40 text-xs border-base-300">Deadline</th>
                <td class="pl-2 text-xs border-base-300">{{ $RiskAssessment->notes }}</td>
            </tr>
            <tr>
                <th class="w-40 text-xs border-base-300">Coordinator</th>
                <td class="pl-2 text-xs border-base-300">
                    {{ $RiskAssessment->coordinator }}
                </td>
            </tr>
        </table>
        @endif
    </fieldset>
    <div class="grid grid-cols-1 gap-4 space-y-6 md:grid-cols-2">
        <fieldset class="p-3 my-4 border shadow-md border-base-300 fieldset card bg-base-100">
            <legend class="text-sm font-semibold card-title ">{{ __('Personel Terlibat / Korban') }}</legend>
            <x-form.searchable-select-advanced
                label="Nama Personel Terlibat/Korban"
                placeholder="Cari Nama..."
                modelsearch="searchName"
                :options="$involved_personnel_options"
                :showdropdown="$showinvolvedPersonnelDropdown"
                enableManualAction="enableInvolvedPersonnelManual"
                clickaction="selectInvolvedPersonnel" />

            <div class="flex flex-wrap gap-2 mt-2">
                @foreach($selected_personnel as $index => $person)
                <span class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md shadow-sm bg-info text-info-content">
                    <div class="flex flex-col leading-tight">
                        <span class="font-bold uppercase">
                            {{ $person['name'] }}
                            @if($person['is_manual']) <span class="text-[10px] italic opacity-75">(Manual)</span> @endif
                        </span>

                        <span class="text-[10px] opacity-90 mt-0.5">
                            {{ $person['employee_id'] ?? '-' }} • {{ $person['department_name'] ?? 'No Department' }}
                        </span>
                    </div>

                    <button
                        type="button"
                        wire:click="removePersonnel({{ $index }})"
                        class="inline-flex items-center justify-center w-4 h-4 ml-3 transition-colors duration-200 rounded-full bg-black/10 hover:bg-black/20 focus:outline-none"
                        title="Hapus">
                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </span>
                @endforeach
            </div>
            @foreach($selected_personnel as $person)
            <input type="hidden" name="involved_ids[]" value="{{ $person['id'] }}">
            <input type="hidden" name="involved_names[]" value="{{ $person['name'] }}">
            <input type="hidden" name="involved_employee_ids[]" value="{{ $person['employee_id'] }}">
            <input type="hidden" name="involved_department_names[]" value="{{ $person['department_name'] }}">
            @endforeach
        </fieldset>
        @if($this->isInjury)
        <fieldset class="p-3 my-4 border shadow-md border-base-300 fieldset card bg-base-100">
            <legend class="text-sm font-semibold card-title ">{{ __('Bagian Tubuh yang Terluka') }}</legend>
            <div @class([ 'grid grid-cols-1 gap-2' , 'md:grid-cols-2'=> $selectedBodyPartCategory,
                'md:grid-cols-1' => !$selectedBodyPartCategory,
                ])>
                <x-form.select
                    label="Kategori Bagian Tubuh"
                    model="selectedBodyPartCategory"
                    :options="$this->existingCategory"
                    option-value="category"
                    option-label="category"
                    placeholder="-- {{__('Pilih Kategori Bagian Tubuh')}} --"
                    required />

                @if ($selectedBodyPartCategory)
                <x-form.select
                    label="Detail Bagian Tubuh"
                    model="selectedBodyPart"
                    :options="$detailsBodyPart"
                    option-label="display_name"
                    placeholder="-- {{__('Pilih Detail Bagian Tubuh')}} --"
                    required />
                @endif
            </div>
        </fieldset>
        @else
        <fieldset class="p-3 my-4 border shadow-md border-base-300 fieldset card bg-base-100">
            <legend class="text-sm font-semibold card-title ">{{ __('Kerusakan alat atau dampak lingkungan') }}</legend>
            <x-form.text_area label="Detail Kerusakan Alat / Lingkungan" model="damage_detail" placeholder="{{ __('Jelaskan kerusakan alat atau dampak lingkungan...')}}" required />
            @endif
        </fieldset>
    </div>
    <x-form.text_area label="Narasi detail mengenai urutan kejadian (5W+1H)" model="description" placeholder="{{ __('Contoh: Siapa yang terlibat, Apa yang terjadi, Dimana, Kapan, Mengapa, dan Bagaimana urutannya.')}}" required />
    <x-form.text_area label="Tindakan Darurat" model="emergency_action" placeholder="{{ __('Jelaskan tindakan segera yang dilakukan setelah kejadian...')}}" required />
</div>