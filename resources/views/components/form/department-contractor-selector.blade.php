@props([
'model' => null, // wire:model untuk radio button
'departments' => [],
'contractors' => [],
'label_dept' => 'Departemen Terkait',
'label_contractor' => 'Kontraktor Terkait',
'showDropdown' => false, // status dropdown department
'showContractorDropdown' => false,
'required' => false, // status dropdown contractor
])


<fieldset>
    <div class="flex items-center gap-4">
        {{-- Radio Department --}}
        <div class="flex items-center gap-1">
            <input id="department" value="department" wire:model.live="model"
                class="peer/department radio radio-xs radio-accent" type="radio" name="dept_cont_toggle" />

            <x-form.label for="department" class="peer-checked/department:text-accent text-[10px] cursor-pointer"
                label="{{ $label_dept }}"
                :required="$model === 'department' && $required" />
        </div>

        {{-- Radio Company/Contractor --}}
        <div class="flex items-center gap-1">
            <input id="company" value="company" wire:model.live="model"
                class="peer/company radio radio-xs radio-primary" type="radio" name="dept_cont_toggle" />

            <x-form.label for="company" class="peer-checked/company:text-primary text-[10px] cursor-pointer"
                label="{{ $label_contractor }}"
                :required="$model === 'company' && $required" />
        </div>
    </div>

    <div wire:key="dropdown-department" class="{{ $model === 'department' ? 'block' : 'hidden' }} mb-1.5">
        {{-- Department --}}
        <div class="relative ">
            <x-form.searchable-dropdown-without-label modelsearch="search" modelid="department_id"
                placeholder="Cari Departemen..." :options="$departments" :showdropdown="$showDropdown"
                clickaction="selectDepartment" namedb="department_name" />
        </div>
    </div>
    <div wire:key="dropdown-company" class="{{ $model === 'company' ? 'block' : 'hidden' }} mb-1.5">
        {{-- Contractor --}}
        <div class="relative ">
            <x-form.searchable-dropdown-without-label modelsearch="searchContractor"
                placeholder="Cari Kontraktor..." modelid="contractor_id" :options="$contractors"
                :showdropdown="$showContractorDropdown" clickaction="selectContractor" namedb="contractor_name" />
        </div>
    </div>

</fieldset>