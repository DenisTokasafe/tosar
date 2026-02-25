<div class="flex items-center space-x-2">

    <select wire:model.live="lang" name="" id=""class=" select select-xs select-bordered focus-within:outline-none focus-within:border-info focus-within:ring-0">
         <option value="">{{ __('Pilih Bahasa') }}</option>
         <option value="en"><i class="inline-block w-8 h-8 text-2xl fi fi-gb"></i>{{ __('English') }}</option>
         <option value="id"><i class="inline-block w-8 h-8 text-2xl fi fi-br-indonesia"></i></i>{{ __('Bahasa Indonesia') }}</option>
    </select>
</div>
