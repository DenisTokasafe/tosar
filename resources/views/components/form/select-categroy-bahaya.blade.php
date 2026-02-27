@props([
    'keyWord' => null,
    'ktas' => [],
    'ttas' => [],
    'model_kta' => null,
    'model_tta' => null,
])

<fieldset {{ $attributes->merge(['class' => 'fieldset']) }}>
    <div class="flex items-center gap-4 mb-2">
        <div class="flex items-center gap-1">
            <input id="kta" value="kta" wire:model.live="keyWord"
                class="peer/kta radio radio-xs radio-accent" type="radio" name="keyWord" />
            <x-form.label for="kta" class="peer-checked/kta:text-accent text-[10px] cursor-pointer"
                label="Kondisi Tidak Aman" required />
        </div>

        <div class="flex items-center gap-1">
            <input id="tta" value="tta" wire:model.live="keyWord"
                class="peer/tta radio radio-xs radio-primary" type="radio" name="keyWord" />
            <x-form.label for="tta" class="peer-checked/tta:text-primary text-[10px] cursor-pointer"
                label="Tindakan Tidak Aman" required />
        </div>
    </div>

    <div class="{{ $keyWord === 'kta' ? 'block' : 'hidden' }}">
        <select {{ $model_kta ? "wire:model.live=$model_kta" : '' }}
            class="select select-xs mb-1 select-bordered w-full focus-within:outline-none focus-within:border-info focus-within:ring-0 {{ $errors->has('kondisi_tidak_aman') ? 'ring-1 ring-rose-500 border-rose-500' : '' }}">
            <option value="">{{__('-- Pilih Kategori Bahaya KTA --')}}</option>
            @foreach ($ktas as $kta)
                <option value="{{ $kta->id }}">{{ __($kta->name) }}</option>
            @endforeach
        </select>
        <x-label-error :messages="$errors->get('model_kta')" />
    </div>

    <div class="{{ $keyWord === 'tta' ? 'block' : 'hidden' }}">
        <select {{ $model_tta ? "wire:model.live=$model_tta" : '' }}
            class="select select-xs mb-1 select-bordered w-full focus-within:outline-none focus-within:border-info focus-within:ring-0 {{ $errors->has('tindakan_tidak_aman') ? 'ring-1 ring-rose-500 border-rose-500' : '' }}">
            <option value="">{{__('-- Pilih Kategori Bahaya TTA --')}}</option>
            @foreach ($ttas as $tta)
                <option value="{{ $tta->id }}">{{ __($tta->name) }}</option>
            @endforeach
        </select>
        <x-label-error :messages="$errors->get('model_tta')" />
    </div>
</fieldset>
