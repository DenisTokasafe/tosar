@props([
'label' => null,
'placeholder' => 'Pilih Tanggal...',
'model' => null,
'size' => 'input-xs',
'required' => false,
'disabled' => false,
'dateFormat' => 'd-m-Y H:i', // Format yang tampil ke user (altInput)
])

<fieldset class="w-full fieldset">
    {{-- Label dengan indikator required --}}
    @if ($label)
    <x-form.label :label="$label" :required="$required" />
    @endif

    {{-- Wrapper Alpine.js untuk Flatpickr --}}
    <div wire:ignore x-data="{
        reportDate: @entangle($model),
        fp: null,
        init() {
            this.fp = flatpickr(this.$refs.tanggalInput, {
                disableMobile: true,
                altInput: true,
                enableTime: true,
                time_24hr: true,
                altFormat: '{{ $dateFormat }}',
                dateFormat: 'Y-m-d',
                defaultDate: this.reportDate,
                onChange: (selectedDates, dateStr) => {
                    this.reportDate = dateStr;
                }
            });

            this.$watch('reportDate', (newVal) => {
                this.fp.setDate(newVal, false);
            });
        }
    }">
        {{-- Input Element --}}
        <input
            x-ref="tanggalInput"
            type="text"
            readonly
            {{ $disabled ? 'disabled' : '' }}
            {{ $model ? "wire:model.live.debounce.300ms=$model" : '' }}
            placeholder="{{ $placeholder ?: $label }}"
            {{ $attributes->merge([
                'class' => 'input input-bordered w-full focus-within:outline-none focus-within:border-info focus-within:ring-0 input-xs ' .
                    ($disabled ? 'bg-base-200 opacity-70 ' : '') .
                    ($errors->has($model) ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : ''),
            ]) }} />
    </div>

    {{-- Penanganan Error Otomatis --}}
    @if($model)
    @error($model)
    <x-label-error :messages="$errors->get($model)" />
    @enderror
    @endif
</fieldset>