<div class="">


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