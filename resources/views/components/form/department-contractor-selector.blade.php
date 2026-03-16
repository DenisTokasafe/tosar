@props([
'model' => null, // wire:model untuk radio button
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
    <input id="department" value="department" wire:model.live="model"
        class="peer/department radio radio-xs radio-accent " type="radio" name="{{ $model }}"
        checked />
    <x-form.label for="department" class="peer-checked/department:text-accent text-[10px]"
        label="{{ $label_dept }}" :required="$model === 'department' && $required" />
    <input id="company" value="company" wire:model.live="model"
        class="peer/company radio radio-xs radio-primary" type="radio" name="{{ $model }}" />
    <x-form.label for="company" class="peer-checked/company:text-primary text-[10px]" label="{{ $label_contractor }}"
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