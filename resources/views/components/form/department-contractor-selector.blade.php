@props([
'deptCont' => null, // wire:model untuk radio button
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
    <!-- Departemen -->
    <input id="department" value="department" wire:model.live="deptCont"
        class="peer/department radio radio-xs radio-accent " type="radio" name="deptCont"
        checked />
    <x-form.label for="department" class="peer-checked/department:text-accent text-[10px]"
        label="{{ $label_dept }}"
        :required="$deptCont === 'department' && $required" />
    <!--  Kontraktor -->
    <input id="kontraktor" value="kontraktor" wire:model.live="deptCont"
        class="peer/kontraktor radio radio-xs radio-primary" type="radio" name="deptCont" />
    <x-form.label for="kontraktor" class="peer-checked/kontraktor:text-primary text-[10px]"
        label="{{ $label_contractor }}"
        :required="$deptCont === 'kontraktor' && $required" />
    <div class="hidden peer-checked/department:block mt-1.5">
        {{-- Department --}}
        <div class="relative ">
            <x-form.searchable-dropdown-without-label modelsearch="search" modelid="{{ $model_dept ?$model_dept : '' }}"
                placeholder="Cari Departemen..." :options="$departments" :showdropdown="$showDropdown"
                clickaction="selectDepartment" namedb="department_name" />
        </div>
    </div>
    <div class="hidden peer-checked/kontraktor:block mt-1.5">
        {{-- Contractor --}}
        <div class="relative ">
            <x-form.searchable-dropdown-without-label modelsearch="searchContractor"
                placeholder="Cari Kontraktor..." modelid="{{ $model_cont ?$model_cont : '' }}" :options="$contractors"
                :showdropdown="$showContractorDropdown" clickaction="selectContractor" namedb="contractor_name" />
        </div>
    </div>

</fieldset>