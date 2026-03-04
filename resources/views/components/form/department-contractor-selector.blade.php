@props([
    'model' => 'deptCont', // wire:model untuk radio button
    'departments' => [],
    'contractors' => [],
    'showDropdown' => false, // status dropdown department
    'showContractorDropdown' => false, // status dropdown contractor
])

<fieldset {{ $attributes->merge(['class' => 'fieldset']) }}>
    <div class="flex items-center gap-4">
        {{-- Radio: Department --}}
        <div class="flex items-center">
            <input
                id="department"
                value="department"
                wire:model.live="{{ $model }}"
                class="peer/department radio radio-xs radio-accent"
                type="radio"
                name="{{ $model }}"
                checked
            />
            <x-form.label
                for="department"
                class="peer-checked/department:text-accent text-[10px] ml-2"
                label="PT. MSM & PT. TTN"
                required
            />
        </div>

        {{-- Radio: Company/Contractor --}}
        <div class="flex items-center">
            <input
                id="company"
                value="company"
                wire:model.live="{{ $model }}"
                class="peer/company radio radio-xs radio-primary"
                type="radio"
                name="{{ $model }}"
            />
            <x-form.label
                for="company"
                class="peer-checked/company:text-primary text-[10px] ml-2"
                label="Kontraktor"
                required
            />
        </div>
    </div>

    {{-- Container Dropdown Department --}}
    <div class="hidden mt-2 peer-has-[#department:checked]:block">
        <div class="relative mb-1">
            <x-form.searchable-dropdown-without-label
                modelsearch="search"
                modelid="department_id"
                placeholder="Cari Departemen..."
                :options="$departments"
                :showdropdown="$showDropdown"
                clickaction="selectDepartment"
                namedb="department_name"
            />
        </div>
    </div>

    {{-- Container Dropdown Contractor --}}
    <div class="hidden mt-2 peer-has-[#company:checked]:block">
        <div class="relative mb-1">
            <x-form.searchable-dropdown-without-label
                modelsearch="searchContractor"
                placeholder="Cari Kontraktor..."
                modelid="contractor_id"
                :options="$contractors"
                :showdropdown="$showContractorDropdown"
                clickaction="selectContractor"
                namedb="contractor_name"
            />
        </div>
    </div>
</fieldset>
