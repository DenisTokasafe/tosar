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
    <div wire:ignore
        x-data="{
        reportDate: @entangle($model),
        {{-- 1. Deteksi error secara reaktif dari Livewire --}}
        get hasError() { return @js($errors->has($model)) },
        fp: null,
        init() {
            this.fp = flatpickr(this.$refs.tanggalInput, {
                disableMobile: true,
                altInput: true,
                enableTime: true,
                time_24hr: true,
                altFormat: '{{ $dateFormat }}',
                dateFormat: 'Y-m-d H:i', {{-- Sesuaikan format jika enableTime aktif --}}
                defaultDate: this.reportDate,
                onChange: (selectedDates, dateStr) => {
                    this.reportDate = dateStr;
                }
            });

            {{-- 2. Sinkronisasi perubahan dari Livewire ke Flatpickr --}}
            this.$watch('reportDate', (newVal) => {
                if (newVal !== this.fp.currentSelectedDateString) {
                    this.fp.setDate(newVal, false);
                }
            });
        }
    }"
        {{-- 3. Gunakan Alpine untuk manipulasi class secara reaktif --}}
        :class="hasError ? 'flatpickr-error' : ''">
        <input
            x-ref="tanggalInput"
            type="text"
            readonly
            {{ $disabled ? 'disabled' : '' }}
            placeholder="{{ $placeholder ?: $label }}"
            {{ $attributes->merge([
            'class' => 'input input-bordered w-full input-xs focus:outline-none focus:border-info focus:ring-0',
        ]) }}
            {{-- CSS khusus untuk menangani border merah pada alt-input Flatpickr --}}
            :class="hasError ? '!border-rose-500 !ring-1 !ring-rose-500' : ''" />
    </div>

    <style>
        /* Paksa input yang dibuat flatpickr (alt-input) ikut berwarna merah */
        .flatpickr-error+.form-control,
        .flatpickr-error .flatpickr-mobile,
        .flatpickr-error input.altInput {
            border-color: #f43f5e !important;
            /* rose-500 */
            box-shadow: 0 0 0 1px #f43f5e !important;
        }
    </style>
    <div wire:ignore
        x-data="{
        reportDate: @entangle($model),
        {{-- 1. Deteksi error secara reaktif dari Livewire --}}
        get hasError() { return @js($errors->has($model)) },
        fp: null,
        init() {
            this.fp = flatpickr(this.$refs.tanggalInput, {
                disableMobile: true,
                altInput: true,
                enableTime: true,
                time_24hr: true,
                altFormat: '{{ $dateFormat }}',
                dateFormat: 'Y-m-d H:i', {{-- Sesuaikan format jika enableTime aktif --}}
                defaultDate: this.reportDate,
                onChange: (selectedDates, dateStr) => {
                    this.reportDate = dateStr;
                }
            });

            {{-- 2. Sinkronisasi perubahan dari Livewire ke Flatpickr --}}
            this.$watch('reportDate', (newVal) => {
                if (newVal !== this.fp.currentSelectedDateString) {
                    this.fp.setDate(newVal, false);
                }
            });
        }
    }"
        {{-- 3. Gunakan Alpine untuk manipulasi class secara reaktif --}}
        :class="hasError ? 'flatpickr-error' : ''">
        <input
            x-ref="tanggalInput"
            type="text"
            readonly
            {{ $disabled ? 'disabled' : '' }}
            placeholder="{{ $placeholder ?: $label }}"
            {{ $attributes->merge([
            'class' => 'input input-bordered w-full input-xs focus:outline-none focus:border-info focus:ring-0',
        ]) }}
            {{-- CSS khusus untuk menangani border merah pada alt-input Flatpickr --}}
            :class="hasError ? '!border-rose-500 !ring-1 !ring-rose-500' : ''" />
    </div>

    <style>
        /* Paksa input yang dibuat flatpickr (alt-input) ikut berwarna merah */
        .flatpickr-error+.form-control,
        .flatpickr-error .flatpickr-mobile,
        .flatpickr-error input.altInput {
            border-color: #f43f5e !important;
            /* rose-500 */
            box-shadow: 0 0 0 1px #f43f5e !important;
        }
    </style>

    {{-- Penanganan Error Otomatis --}}
    @if($model)
    @error($model)
    <x-label-error :messages="$errors->get($model)" />
    @enderror
    @endif
</fieldset>