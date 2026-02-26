<div class="flex items-center space-x-2">
    <div class="join">
        <label class="rounded-l-md btn btn-xs btn-primary join-item">
            @if (session('lang') == 'en')
            <i class="inline-block w-4 h-4 text-2xl fi fi-gb"></i>
            @elseif (session('lang') == 'id')
            <i class="inline-block w-4 h-4 text-2xl fi fi-id"></i>
            @else
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-languages-icon lucide-languages">
                <path d="m5 8 6 6" />
                <path d="m4 14 6-6 2-3" />
                <path d="M2 5h12" />
                <path d="M7 2h1" />
                <path d="m22 22-5-10-5 10" />
                <path d="M14 18h6" />
            </svg>{{ __('Pilih Bahasa') }}
            @endif
        </label>
        <select wire:model.live="lang" name="" id="" class=" select select-xs select-bordered focus-within:outline-none focus-within:border-info focus-within:ring-0 join-item">
            <option value="">{{ __('Pilih Bahasa') }}</option>
            <option value="en"><i class="inline-block w-4 h-4 text-2xl fi fi-gb"></i>{{ __('English') }}</option>
            <option value="id"><i class="inline-block w-4 h-4 text-2xl fi fi-id"></i>{{ __('Bahasa Indonesia') }}</option>
        </select>
    </div>
</div>
