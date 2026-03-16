@props([
'model' => 'null', // wire:model untuk radio button
'model_dept'=> null,
'model_cont'=> null,
'required' => false,
'departments' => [],
'contractors' => [],
'label_dept' => 'Departemen Terkait',
'label_contractor' => 'Kontraktor Terkait',
'showDropdown' => false, // status dropdown department
'showContractorDropdown' => false, // status dropdown contractor
])


<fieldset>
    {{-- Bagian Department --}}
    <input id="department" value="department" wire:model.live="model" ... />
    <x-form.label for="department"
        label="{{ $label_dept }}"
        :required="$model === 'department' && $required" />

    {{-- Bagian Contractor --}}
    <input id="company" value="company" wire:model.live="model" ... />
    <x-form.label for="company"
        label="{{ $label_contractor }}"
        {{-- Ubah 'contractor' menjadi 'company' agar sesuai dengan value="company" di atas --}}
        :required="$model === 'company' && $required" />
    <div class="hidden peer-checked/department:block mt-1.5">
        {{-- Department --}}
        <div class="relative ">
            <x-form.searchable-dropdown-without-label modelsearch="search" modelid="{{ $model_dept ?$model_dept : '' }}"
                placeholder="Cari Departemen..." :options="$departments" :showdropdown="$showDropdown"
                clickaction="selectDepartment" namedb="department_name" />
        </div>
    </div>
    <div class="hidden peer-checked/company:block mt-1.5">
        {{-- Contractor --}}
        <div class="relative ">
            <x-form.searchable-dropdown-without-label modelsearch="searchContractor"
                placeholder="Cari Kontraktor..." modelid="{{ $model_cont ?$model_cont : '' }}" :options="$contractors"
                :showdropdown="$showContractorDropdown" clickaction="selectContractor" namedb="contractor_name" />
        </div>
    </div>

</fieldset>