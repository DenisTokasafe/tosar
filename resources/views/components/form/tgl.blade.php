@props([
'label' => null,
'placeholder' => 'Pilih Tanggal...',
'model' => null,
'size' => 'input-xs',
'required' => false,
'disabled' => false,
'dateFormat' => 'd F Y',
'format' => 'Y-m-d' // Perbaikan typo f -> d
])

<fieldset class="w-full fieldset">
    @if ($label)
    <x-form.label :label="$label" :required="$required" />
    @endif

    <div wire:ignore x-data="{
        reportDate: @entangle($model),
        fp: null,
        init() {
            this.fp = flatpickr(this.$refs.tanggalInput, {
                disableMobile: true,
                altInput: true,
                static: true, // Dipindahkan ke sini
                altFormat: '{{ $dateFormat }}',
                dateFormat: '{{ $format }}',
                defaultDate: this.reportDate,
                onChange: (selectedDates, dateStr) => {
                    this.reportDate = dateStr;
                }
            });

            this.$watch('reportDate', (newVal) => {
                if (newVal !== this.fp.currentSelectedDateString) {
                    this.fp.setDate(newVal, false);
                }
            });
        }
    }">
        <input
            x-ref="tanggalInput"
            type="text"
            readonly
            {{ $disabled ? 'disabled' : '' }}
            {{-- wire:model dihapus karena sudah dihandle @entangle --}}
            placeholder="{{ $placeholder ?: $label }}"
            {{ $attributes->merge([
                'class' => "input input-bordered w-full focus-within:outline-none focus-within:border-info focus-within:ring-0 $size border-gray-300 rounded " .
                ($errors->has($model) ? 'border-rose-500 focus:ring-rose-500 focus-within:border-rose-500' : '')
            ]) }} />
    </div>

    @if($model)
    @error($model)
    <x-label-error :messages="$errors->get($model)" />
    @enderror
    @endif
</fieldset>