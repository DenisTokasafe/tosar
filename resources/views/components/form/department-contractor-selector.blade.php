@props([
'model' => null, // wire:model untuk radio button (misal: $deptCont)
'model_dept' => 'department_id', // nama property untuk ID department
'model_cont' => 'contractor_id', // nama property untuk ID contractor
'departments' => [],
'contractors' => [],
'label_dept' => 'Departemen Terkait',
'label_contractor' => 'Kontraktor Terkait',
'showDropdown' => false,
'showContractorDropdown' => false,
'required' => false,
])

<fieldset {{ $attributes->merge(['class' => 'fieldset']) }}>
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

    {{-- Dropdown Department --}}
    <div wire:key="dropdown-dept-{{ $model_dept }}" class="{{ $model === 'department' ? 'block' : 'hidden' }} mt-1.5">
        <div class="{{ $errors->has($model_dept) ? 'rounded-lg ring-1 ring-rose-500 border-rose-500' : '' }}">
            <x-form.searchable-dropdown-without-label
                modelsearch="search"
                :modelid="$model_dept"
                placeholder="Cari Departemen..."
                :options="$departments"
                :showdropdown="$showDropdown"
                clickaction="selectDepartment"
                namedb="department_name" />
        </div>
        {{-- Menampilkan error untuk department_id --}}
        <x-label-error :messages="$errors->get($model_dept)" />
    </div>

    {{-- Dropdown Contractor --}}
    <div wire:key="dropdown-cont-{{ $model_cont }}" class="{{ $model === 'company' ? 'block' : 'hidden' }} mt-1.5">
        <div class="{{ $errors->has($model_cont) ? 'rounded-lg ring-1 ring-rose-500 border-rose-500' : '' }}">
            <x-form.searchable-dropdown-without-label
                modelsearch="searchContractor"
                placeholder="Cari Kontraktor..."
                :modelid="$model_cont"
                :options="$contractors"
                :showdropdown="$showContractorDropdown"
                clickaction="selectContractor"
                namedb="contractor_name" />
        </div>
        {{-- Menampilkan error untuk contractor_id --}}
        <x-label-error :messages="$errors->get($model_cont)" />
    </div>
</fieldset>