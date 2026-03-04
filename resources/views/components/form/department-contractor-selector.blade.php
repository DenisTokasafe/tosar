@props([
    'selectedType' => 'department', // Default value untuk radio
    'model' => 'deptCont',          // wire:model untuk radio

    // Props untuk Department Dropdown
    'departments' => [],
    'showDeptDropdown' => false,
    'deptSearchModel' => 'search',
    'deptIdModel' => 'department_id',

    // Props untuk Contractor Dropdown
    'contractors' => [],
    'showContDropdown' => false,
    'contSearchModel' => 'searchContractor',
    'contIdModel' => 'contractor_id',
])

<fieldset class="fieldset">
    <div class="flex items-center gap-4 mb-2">
        {{-- Radio: Department (MSM & TTN) --}}
        <div class="flex items-center gap-2">
            <input
                id="type_dept"
                value="department"
                wire:model.live="{{ $model }}"
                class="peer/dept radio radio-xs radio-accent"
                type="radio"
                name="{{ $model }}"
            />
            <x-form.label
                for="type_dept"
                class="peer-checked/dept:text-accent text-[10px] cursor-pointer"
                label="Departemen (TTN & MSM) Terkait"
                required
            />
        </div>

        {{-- Radio: Contractor --}}
        <div class="flex items-center gap-2">
            <input
                id="type_cont"
                value="company"
                wire:model.live="{{ $model }}"
                class="peer/comp radio radio-xs radio-primary"
                type="radio"
                name="{{ $model }}"
            />
            <x-form.label
                for="type_cont"
                class="peer-checked/comp:text-primary text-[10px] cursor-pointer"
                label="Kontraktor Terkait"
                required
            />
        </div>
    </div>

    {{-- Container untuk Dropdown Departemen --}}
    {{-- Menggunakan peer-checked dari radio type_dept --}}
    <div class="hidden peer-has-[#type_dept:checked]:block mt-2">
        <div class="relative mb-1">
            <x-form.searchable-dropdown-without-label
                :modelsearch="$deptSearchModel"
                :modelid="$deptIdModel"
                placeholder="Cari Departemen..."
                :options="$departments"
                :showdropdown="$showDeptDropdown"
                clickaction="selectDepartment"
                namedb="department_name"
            />
        </div>
    </div>

    {{-- Container untuk Dropdown Kontraktor --}}
    {{-- Menggunakan peer-checked dari radio type_cont --}}
    <div class="hidden peer-has-[#type_cont:checked]:block mt-2">
        <div class="relative mb-1">
            <x-form.searchable-dropdown-without-label
                :modelsearch="$contSearchModel"
                :modelid="$contIdModel"
                placeholder="Cari Kontraktor..."
                :options="$contractors"
                :showdropdown="$showContDropdown"
                clickaction="selectContractor"
                namedb="contractor_name"
            />
        </div>
    </div>
</fieldset>
