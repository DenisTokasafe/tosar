<div class="grid grid-cols-1">
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

    </fieldset>
</div>