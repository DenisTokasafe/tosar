@props([
'model' => 'deptCont', // wire:model untuk radio button
'departments' => [],
'contractors' => [],
'showDropdown' => false, // status dropdown department
'showContractorDropdown' => false, // status dropdown contractor
])


<fieldset>
    <input id="department" value="department" wire:model="{{ $model }}"
        class="peer/department radio radio-xs radio-accent" type="radio" name="{{ $model }}"
        checked />
    <x-form.label for="department" class="peer-checked/department:text-accent text-[10px]"
        label="PT. MSM & PT. TTN" required />
    <input id="company" value="company" wire:model="{{ $model }}"
        class="peer/company radio radio-xs radio-primary" type="radio" name="{{ $model }}" />
    <x-form.label for="company" class="peer-checked/company:text-primary" label="Kontraktor"
        required />

    <div class="hidden peer-checked/department:block">
        {{-- Department --}}
        <div class="relative mb-1">
            <x-form.searchable-dropdown-without-label modelsearch="search" modelid="department_id"
                placeholder="Cari Departemen..." :options="$departments" :showdropdown="$showDropdown"
                clickaction="selectDepartment" namedb="department_name" />
        </div>
    </div>
    <div class="hidden peer-checked/company:block">
        {{-- Contractor --}}
        <div class="relative mb-1">
            <x-form.searchable-dropdown-without-label modelsearch="searchContractor"
                placeholder="Cari Kontraktor..." modelid="contractor_id" :options="$contractors"
                :showdropdown="$showContractorDropdown" clickaction="selectContractor" namedb="contractor_name" />
        </div>
    </div>

</fieldset>
