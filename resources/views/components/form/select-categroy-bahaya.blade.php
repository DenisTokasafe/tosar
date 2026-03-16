@props([
'keyWord' => null,
'required' => false,
'ktas' => [],
'ttas' => [],
'options_label' => '-- Pilih Kategori Bahaya --',
'model_kta' => null,
'model_tta' => null,
])

<fieldset {{ $attributes->merge(['class' => 'fieldset']) }}>
    <div class="flex items-center gap-4 ">
        {{-- Radio Department --}}
        <div class="flex items-center gap-1">
            <input id="department" value="department" wire:model.live="model"
                class="peer/department radio radio-xs radio-accent" type="radio" name="{{ $model }}" />

            <x-form.label for="department" class="peer-checked/department:text-accent text-[10px] cursor-pointer"
                label="{{ $label_dept }}"
                :required="$model === 'department' && $required" />
        </div>

        {{-- Radio Company/Contractor --}}
        <div class="flex items-center gap-1">
            <input id="company" value="company" wire:model.live="model"
                class="peer/company radio radio-xs radio-primary" type="radio" name="{{ $model }}" />

            <x-form.label for="company" class="peer-checked/company:text-primary text-[10px] cursor-pointer"
                label="{{ $label_contractor }}"
                :required="$model === 'company' && $required" />
        </div>
    </div>
    {{-- Dropdown KTA --}}
    <div wire:key="dropdown-kta-{{ $model_kta }}" class="{{ $keyWord === 'kta' ? 'block' : 'hidden' }} mb-1.5">
        <select {{ $model_kta ? "wire:model.live=$model_kta" : '' }}
            class="select select-xs mb-1 select-bordered w-full focus-within:outline-none focus-within:border-info focus-within:ring-0
            {{ $errors->has($model_kta) ? 'focus:ring-rose-500  focus-within:outline-none focus-within:border-rose-500  focus-within:ring-0' : '' }}">
            <option value="">{{__($options_label)}}</option>
            @foreach ($ktas as $kta)
            <option value="{{ $kta->id }}">{{ __($kta->name) }}</option>
            @endforeach
        </select>
        {{-- Menampilkan error berdasarkan variabel model_kta --}}
        <x-label-error :messages="$errors->get($model_kta)" />
    </div>

    {{-- Dropdown TTA --}}
    <div wire:key="dropdown-tta-{{ $model_tta }}" class="{{ $keyWord === 'tta' ? 'block' : 'hidden' }} mb-1.5">
        <select {{ $model_tta ? "wire:model.live=$model_tta" : '' }}
            class="select select-xs mb-1 select-bordered w-full focus-within:outline-none focus-within:border-info focus-within:ring-0
            {{ $errors->has($model_tta) ? 'focus:ring-rose-500  focus-within:outline-none focus-within:border-rose-500  focus-within:ring-0' : '' }}">
            <option value="">{{__($options_label)}}</option>
            @foreach ($ttas as $tta)
            <option value="{{ $tta->id }}">{{ __($tta->name) }}</option>
            @endforeach
        </select>
        {{-- Menampilkan error berdasarkan variabel model_tta --}}
        <x-label-error :messages="$errors->get($model_tta)" />
    </div>
</fieldset>