@props([
    'label' => null,
    'placeholder' => 'Pilih Tanggal...',
    'model' => null,
    'size' => 'input-xs',
    'dateFormat' => 'd F Y', // Format yang tampil ke user
])

<div class="w-full">
    <label {{ $attributes->merge(['class' => 'floating-label w-full']) }} wire:ignore x-data="{
        reportDate: @entangle($model),
        fp: null,
        init() {
            this.fp = flatpickr(this.$refs.tanggalInput, {
                disableMobile: true,
                altInput: true,
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
        <input x-ref="tanggalInput" type="text" {{ $model ? "wire:model.live=$model" : '' }} readonly
            placeholder="{{ $placeholder ?: $label }}" {{ $attributes->whereDoesntStartWith('class') }}
            class="input input-bordered {{ $size }} w-full cursor-pointer focus:border-info focus:ring-info focus:outline-hidden
                border-gray-300 rounded
                {{ $errors->has($model) ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}" />

        @if ($label)
            <span>{{ $label }}</span>
        @endif
    </label>

    {{-- Tampilkan Error --}}
    @if ($model)
        @error($model)
               <x-label-error :messages="$errors->get($model)" />
        @enderror
    @endif
</div>
