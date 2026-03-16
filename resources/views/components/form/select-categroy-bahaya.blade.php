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
        {{-- Radio Kondisi Tidak Aman (KTA) --}}
        <div class="flex items-center gap-1">
            <input id="kta" value="kta" wire:model.live="keyWord"
                class="peer/kta radio radio-xs radio-accent" type="radio" name="keyWord" />

            <x-form.label for="kta" class="peer-checked/kta:text-accent text-[10px] cursor-pointer"
                label="Kondisi Tidak Aman"
                :required="$keyWord === 'kta' && $required" />
        </div>

        {{-- Radio Tindakan Tidak Aman (TTA) --}}
        <div class="flex items-center gap-1">
            <input id="tta" value="tta" wire:model.live="keyWord"
                class="peer/tta radio radio-xs radio-primary" type="radio" name="keyWord" />

            <x-form.label for="tta" class="peer-checked/tta:text-primary text-[10px] cursor-pointer"
                label="Tindakan Tidak Aman"
                :required="$keyWord === 'tta' && $required" />
        </div>
    </div>
    {{-- Dropdown KTA --}}
    <div wire:key="dropdown-kta-{{ $model_kta }}" class="{{ $keyWord === 'kta' ? 'block' : 'hidden' }} mb-1.5">
        <select {{ $model_kta ? "wire:model.live=$model_kta" : '' }}
            class="select select-xs mb-1 select-bordered w-full focus-within:outline-none focus-within:border-info focus-within:ring-0
            {{ $errors->has($model_kta) ? 'ring-1 ring-rose-500 border-rose-500' : '' }}">
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
            {{ $errors->has($model_tta) ? 'ring-1 ring-rose-500 border-rose-500' : '' }}">
            <option value="">{{__($options_label)}}</option>
            @foreach ($ttas as $tta)
            <option value="{{ $tta->id }}">{{ __($tta->name) }}</option>
            @endforeach
        </select>
        {{-- Menampilkan error berdasarkan variabel model_tta --}}
        <x-label-error :messages="$errors->get($model_tta)" />
    </div>
</fieldset>