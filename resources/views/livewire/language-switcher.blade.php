<div class="flex items-center space-x-2">
    <div class="join">
         <label class="rounded-r-full btn btn-xs btn-primary join-item">
            @if (session('lang') == 'en')
                <i class="inline-block w-4 h-4 text-2xl fi fi-gb"></i>
            @elseif (session('lang') == 'id')
                <i class="inline-block w-4 h-4 text-2xl fi fi-id"></i>
            @else
                {{ __('Pilih Bahasa') }}
            @endif
         </label>
        <select wire:model.live="lang" name="" id="" class=" select select-xs select-bordered focus-within:outline-none focus-within:border-info focus-within:ring-0">
            <option value="">{{ __('Pilih Bahasa') }}</option>
            <option value="en"><i class="inline-block w-4 h-4 text-2xl fi fi-gb"></i>{{ __('English') }}</option>
            <option value="id"><i class="inline-block w-4 h-4 text-2xl fi fi-id"></i></i>{{ __('Bahasa Indonesia') }}</option>
        </select>
    </div>
</div>
