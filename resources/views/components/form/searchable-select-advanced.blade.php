@props([
    'label' => null,
    'placeholder' => 'Cari...',
    'modelsearch' => null,
    'modelid' => null,
    'options' => [],
    'showdropdown' => false,
    'required' => false,
    'disabled' => false,
    'columnName' => 'name',
    'clickaction' => 'selectPelapor',
    'manualMode' => false,
    'manualModelName' => null,
    'enableManualAction' => 'enableManualMode',
    'addManualAction' => 'addManualData',
])

<fieldset class="relative fieldset md:col-span-1"
    x-data="{
        open: false,
        init() {
            // Pantau perubahan showdropdown dari Livewire secara realtime
            $watch('showlive', value => {
                if (value) this.open = true;
            })
        },
        {{-- Mengambil status showdropdown dari Livewire --}}
        get showlive() {
            return @js($showdropdown);
        }
    }">

    @if ($label)
        <x-form.label :label="$label" :required="$required" />
    @endif

    <div class="relative">
        {{-- Input Search --}}
        <input
            {{ $disabled ? 'disabled' : '' }}
            type="text"
            wire:model.live.debounce.300ms="{{ $modelsearch }}"
            placeholder="{{ __($placeholder) }}"
            x-on:focus="open = true"
            x-on:input="open = true"
            @click.away="open = false"
            {{ $attributes->merge([
                'class' => 'input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs ' .
                    ($disabled ? 'bg-base-200 opacity-70 ' : '') .
                    ($errors->has($modelid) || ($manualModelName && $errors->has($manualModelName))
                        ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500'
                        : ''),
            ]) }}
        />

        {{-- Dropdown --}}
        {{-- Menggunakan showdropdown dari Livewire sebagai gate utama --}}
        @if (!$disabled && $showdropdown)
            <ul
                x-show="open"
                {{-- transisi halus agar tidak kaku saat muncul/hilang --}}
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                class="absolute z-[9999] w-full mt-1 overflow-auto border rounded-md shadow-2xl bg-base-100 max-h-60 border-base-content/10"
                @click.stop {{-- Mencegah dropdown tertutup saat klik area manual --}}
            >
                {{-- Spinner saat mencari --}}
                <div wire:loading wire:target="{{ $modelsearch }}" class="flex flex-col items-center justify-center px-4 py-4 text-center">
                    <span class="loading loading-spinner loading-sm text-info"></span>
                </div>

                <div wire:loading.remove wire:target="{{ $modelsearch }}">
                    @if (count($options) > 0)
                        @foreach ($options as $opt)
                            <li
                                wire:click="{{ $clickaction }}({{ $opt->id }}, '{{ addslashes($opt->{$columnName}) }}')"
                                {{-- KEY unik sangat penting agar Livewire tidak bingung saat re-render --}}
                                wire:key="opt-{{ $modelsearch }}-{{ $opt->id }}"
                                x-on:click="open = false"
                                class="px-3 py-2 text-sm transition-colors border-b cursor-pointer hover:bg-base-200 border-base-content/5 last:border-none"
                            >
                                {{ $opt->{$columnName} }}
                            </li>
                        @endforeach
                    @else
                        {{-- Mode Manual Trigger --}}
                        @if (!$manualMode)
                            <li wire:click="{{ $enableManualAction }}"
                                class="flex items-center gap-2 px-3 py-3 text-sm italic cursor-pointer text-warning hover:bg-base-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                {{ __('Tidak ditemukan, klik untuk tambah manual') }}
                            </li>
                        @endif
                    @endif

                    {{-- Input Manual Field --}}
                    @if ($manualMode)
                        <li class="p-2 border-t bg-base-200/50" wire:key="manual-input-{{ $modelsearch }}">
                            <div class="flex items-center gap-1">
                                <input type="text" wire:model.live="{{ $manualModelName }}"
                                    placeholder="{{ __('Nama manual...') }}"
                                    class="w-full input input-bordered input-xs focus:ring-1 focus:ring-info bg-base-100"
                                    @keydown.enter.prevent="$wire.{{ $addManualAction }}(); open = false"
                                />
                                <button type="button" wire:click="{{ $addManualAction }}" x-on:click="open = false" class="btn btn-primary btn-xs">
                                    {{ __('Tambah') }}
                                </button>
                            </div>
                        </li>
                    @endif
                </div>
            </ul>
        @endif
    </div>

    {{-- Error handling --}}
    @php $errorKey = ($manualMode && $manualModelName) ? $manualModelName : $modelid; @endphp
    <x-label-error :messages="$errors->get($errorKey)" />
</fieldset>
