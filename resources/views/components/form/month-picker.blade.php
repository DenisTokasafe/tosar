@props([
    'label' => null,
    'required' => false,
    'model' => null,
    'placeholder' => 'Pilih bulan',
    'id' => 'month-picker-' . uniqid()
])

<fieldset {{ $attributes->merge(['class' => 'w-full fieldset']) }}>
    @if($label)
        <x-form.label :label="$label" :required="$required" />
    @endif

    <div class="w-full"
         wire:ignore
         wire:key="{{ $id }}"
         x-data="{
            fp: null,
            dateValue: @entangle($model).live,
            initFlatpickr() {
                this.$nextTick(() => {
                    if (this.fp) {
                        this.fp.destroy();
                    }

                    if (!this.$refs.input) return;

                    this.fp = flatpickr(this.$refs.input, {
                        static: true,
                        plugins: [
                            new monthSelectPlugin({
                                disableMobile: false,
                                shorthand: true,
                                dateFormat: 'M-Y',
                                altFormat: 'F Y',
                                theme: 'light'
                            })
                        ],
                        defaultDate: this.dateValue,
                        onChange: (selectedDates, dateStr) => {
                            this.dateValue = dateStr;
                        }
                    });
                });
            }
         }"
         x-init="initFlatpickr()"
         x-effect="if(fp && dateValue) fp.setDate(dateValue, false)">

        <input x-ref="input"
               type="text"
               readonly
               class="w-full input input-bordered focus-within:outline-none focus-within:border-info focus-within:ring-0 input-xs"
               placeholder="{{ $placeholder }}" />
    </div>

    @if($model)
        <x-label-error :messages="$errors->get($model)" />
    @endif
</fieldset>
