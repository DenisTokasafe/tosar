<section class="w-full">
    <x-toast />
    {{-- Breadcrumb di sebelah kanan --}}
    <div class="flex justify-start mb-2 " wire:ignore>
        {{ Breadcrumbs::render('incident-create') }}
    </div>
    <flux:heading level="1" class="mb-1 capitalize">Buat Laporan Insiden</flux:heading>
    <flux:subheading size="sm" class="mb-1 text-accent">Laporkan insiden dengan detail untuk penanganan yang tepat.</flux:subheading>

    <x-incident.layout>
        {{-- PROGRESS & STEPS VISUAL --}}
        <ul class="absolute inset-x-0 top-0 z-10 border-t border-l-0 border-r-0 rounded-t-sm shadow-md border-base-300 steps lg:steps-horizontal bg-base-100">
            <li class="step {{ $currentStep >= 1 ? 'step-primary' : '' }} text-[10px] uppercase font-bold">Informasi Dasar</li>
            <li class="step {{ $currentStep >= 2 ? 'step-primary' : '' }} text-[10px] uppercase font-bold">Detail & Risiko</li>
            <li class="step {{ $currentStep >= 3 ? 'step-primary' : '' }} text-[10px] uppercase font-bold">Dokumentasi</li>
        </ul>
        {{-- STEP 1: Info Dasar --}}
        @if($currentStep == 1)
        <div class="grid grid-cols-1 gap-4 mt-12 mb-8 space-y-4 md:grid-cols-2 lg:grid-cols-3">
            <x-form.tgl-waktu label="Tanggal & Waktu Kejadian" model="date_time" required />
            <x-form.search-template label="Lokasi" required modelsearch="searchLocation" modelid="location_id" :options="$locations" :showdropdown="$show_location" clickaction="selectLocation" namedb="name" />
            {{-- Lokasi spesifik muncul hanya jika lokasi utama sudah dipilih --}}
            @if ($location_id)
            <x-form.input-text label="Lokasi Spesifik" model="location_specific" placeholder="Masukkan detail lokasi spesifik..." required />
            @endif

            <x-form.select-categroy-bahaya :key-word="$keyWord" :ktas="$ktas" :ttas="$ttas" model_kta="kondisi_tidak_aman" model_tta="tindakan_tidak_aman" />
            <x-form.searchable-select-advanced label="Dilaporkan Oleh" placeholder="Cari Nama Pelapor..."
                modelsearch="searchPelapor" modelid="pelapor_id" {{-- ID asli di DB --}} :options="$pelapors"
                :showdropdown="$showPelaporDropdown" {{-- Logic Manual --}} :manualMode="$manualPelaporMode"
                manualModelName="manualPelaporName" enableManualAction="enableManualPelapor"
                addManualAction="addPelaporManual" clickaction="selectPelapor" />
        </div>
        @endif
        {{-- STEP 2: Detail Kejadian --}}
        @if($currentStep == 2)
        <div class="mt-12 mb-8">
            <x-form.select label="Tipe Insiden" model="event_type_id" :options="$eventTypes" option-label="event_type_name" required />
            <x-form.select label="Jenis Insiden" model="event_sub_type_id" :options="$eventSubTypes" option-label="event_sub_type_name" required />
            <div class="flex flex-col-reverse gap-2 mt-2 md:flex-row">
                {{-- Kolom Likelihood & Consequence --}}
                <div class="space-y-4 md:grow">
                    {{-- Consequence --}}
                    <fieldset class="fieldset ">
                        <x-form.label label="Consequence" required />
                        <select wire:model.live="consequence_id"
                            class="select select-xs md:select-xs select-bordered w-full md:max-w-md focus-within:outline-none focus-within:border-info focus-within:ring-0 {{ $errors->has('consequence_id') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}">
                            <option value="">{{__('-- Pilih --')}}</option>
                            @foreach ($consequencess as $cons)
                            <option value="{{ $cons->id }}">{{ __($cons->name) }}</option>
                            @endforeach
                        </select>
                        <x-label-error :messages="$errors->get('consequence_id')" />

                        @if ($consequence_id)
                        @php
                        $selectedConsequence = $consequencess->firstWhere('id', $consequence_id);
                        @endphp
                        @if ($selectedConsequence)
                        <div
                            class="h-20 p-2 mt-1 overflow-y-auto text-sm text-gray-600 border rounded bg-gray-50">
                            {{ __($selectedConsequence->description) ?? 'Tidak ada deskripsi' }}
                        </div>
                        @endif
                        @endif
                    </fieldset>
                    {{-- Likelihood --}}
                    <fieldset class="fieldset ">
                        <x-form.label label="Likelihood" required />
                        <select wire:model.live="likelihood_id"
                            class="select select-xs md:select-xs select-bordered w-full md:max-w-md focus-within:outline-none focus-within:border-info focus-within:ring-0 {{ $errors->has('likelihood_id') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}">
                            <option value="">{{__('-- Pilih --')}}</option>
                            @foreach ($likelihoodss as $like)
                            <option value="{{ $like->id }}">{{ __($like->name) }}</option>
                            @endforeach
                        </select>
                        <x-label-error :messages="$errors->get('likelihood_id')" />

                        @if ($likelihood_id)
                        @php
                        $selectedLikelihood = $likelihoodss->firstWhere('id', $likelihood_id);
                        @endphp
                        @if ($selectedLikelihood)
                        <div
                            class="h-20 p-2 mt-1 overflow-y-auto text-sm text-gray-600 border rounded bg-gray-50">
                            {{ __($selectedLikelihood->description) ?? 'Tidak ada deskripsi' }}
                        </div>
                        @endif
                        @endif
                    </fieldset>


                </div>
                {{-- Kolom Risk Matrix --}}
                <div class="flex-none overflow-x-auto ">

                    <table class="table table-xs w-60">
                        <thead>
                            <tr class="text-center text-[9px]">
                                <td class=" border-1">{{ __('Level') }}</td>
                                <td class="text-white rotate_text border-1 bg-emerald-500">{{ __('Rendah') }}</td>
                                <td class="text-white bg-yellow-500 rotate_text border-1">{{ __('Sedang') }}</td>
                                <td class="text-white bg-orange-500 rotate_text border-1">{{ __('Tinggi') }}</td>
                                <td class="text-white rotate_text border-1 bg-rose-500">{{ __('Ekstrem') }}</td>
                                <td class="text-black bg-gray-100 rotate_text border-1">{{ __('Ditutup') }}</td>
                            </tr>
                            <tr class="text-center text-[9px]">
                                <th class="border-1">Likelihood ↓ / Consequence →</th>
                                @foreach ($consequences as $c)
                                <th class="rotate_text border-1">{{ __($c->name) }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($likelihoods as $l)
                            <tr class="w-32 text-xs text-center">

                                <td class="w-1 font-bold border-1">{{ __($l->name) }}</td>
                                @foreach ($consequences as $c)
                                @php
                                $cell =
                                App\Models\RiskMatrixCell::where('likelihood_id', $l->id)
                                ->where('risk_consequence_id', $c->id)
                                ->first() ?? null;
                                $score = $l->level * $c->level;
                                $severity = $cell?->severity ?? '';
                                $color = match ($severity) {
                                'Rendah' => 'bg-emerald-500',
                                'Sedang' => 'bg-yellow-500',
                                'Tinggi' => 'bg-orange-500',
                                'Ekstrem' => 'bg-rose-500',
                                default => 'bg-gray-100',
                                };
                                @endphp
                                <td
                                    class="border cursor-pointer   @if ($likelihood_id == $l->id && $consequence_id == $c->id) border-2 bg-primary border-primary-content @endif">
                                    <span wire:click="edit({{ $l->id }}, {{ $c->id }})"
                                        class="btn btn-square btn-xs   {{ $color }}">{{ Str::upper(substr(__($severity), 0, 1)) }}</span>
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($RiskAssessment != null)
            <table class="table mt-4 table-xs table-zebra">

                <tr>
                    <th class="w-40 text-xs border-base-300">Potential Risk Rating</th>
                    <td class="pl-2 text-xs border-base-300">
                        {{ $RiskAssessment->name }}
                    </td>
                </tr>
                <tr>
                    <th class="w-40 text-xs border-base-300">Notify</th>
                    <td class="pl-2 text-xs border-base-300">
                        {{ $RiskAssessment->reporting_obligation }}
                    </td>
                </tr>
                <tr>
                    <th class="w-40 text-xs border-base-300">Deadline</th>
                    <td class="pl-2 text-xs border-base-300">{{ $RiskAssessment->notes }}</td>
                </tr>
                <tr>
                    <th class="w-40 text-xs border-base-300">Coordinator</th>
                    <td class="pl-2 text-xs border-base-300">
                        {{ $RiskAssessment->coordinator }}
                    </td>
                </tr>
            </table>
            @endif
            <flux:separator variant="subtle" class="my-4" />
            <x-form.text_area label="Kronologi Kejadian" model="description" placeholder="Jelaskan Kronologi Kejadian" required />
            <fieldset class="p-3 my-4 border shadow-md border-base-300 fieldset card bg-base-100">
                <legend class="text-sm font-semibold card-title ">{{ __('Bagian Tubuh yang Terluka') }}</legend>
                <div @class([ 'grid grid-cols-1 gap-2' , 'md:grid-cols-2'=> $selectedBodyPartCategory,
                    'md:grid-cols-1' => !$selectedBodyPartCategory,
                    ])>
                    <x-form.select
                        label="Kategori Bagian Tubuh"
                        model="selectedBodyPartCategory"
                        :options="$this->existingCategory"
                        option-value="category"
                        option-label="category"
                        placeholder="-- {{__('Pilih Kategori Bagian Tubuh')}} --"
                        required />

                    @if ($selectedBodyPartCategory)
                    <x-form.select
                        label="Detail Bagian Tubuh"
                        model="selectedBodyPart"
                        :options="$detailsBodyPart"
                        option-label="display_name"
                        placeholder="-- {{__('Pilih Detail Bagian Tubuh')}} --"
                        required />
                    @endif
                </div>
            </fieldset>
        </div>
        @endif
        {{-- STEP 3: Tindakan --}}
        @if($currentStep == 3)
        <div class="grid grid-cols-1 gap-4 mt-12 mb-8 space-y-4 md:grid-cols-2 lg:grid-cols-3">
            <fieldset class=" fieldset">
                <x-form.upload label="Lampirkan Foto Dokumentasi Deskripsi" model="documentation_description"
                    :file="$documentation_description" />
                <div wire:loading.remove wire:target="documentation_description">
                    @if ($documentation_description)
                    @if (in_array($documentation_description->getClientOriginalExtension(), ['jpg', 'jpeg', 'png']))
                    <img src="{{ $documentation_description->temporaryUrl() }}"
                        class="mt-2 {{ $documentation_description ? 'w-40' : '' }} h-auto rounded border" />
                    @elseif (in_array($documentation_description->getClientOriginalExtension(), ['pdf', 'doc', 'docx']))
                    <div class="flex items-center gap-2 mt-2">
                        @if ($documentation_description->getClientOriginalExtension() == 'pdf')
                        <x-icon.pdf class="w-8 h-8" />
                        <span
                            class="text-sm text-red-600">{{ $documentation_description->getClientOriginalName() }}</span>
                        @elseif (in_array($documentation_description->getClientOriginalExtension(), ['doc', 'docx']))
                        <x-icon.word class="w-8 h-8" />
                        <span
                            class="text-sm text-blue-600">{{ $documentation_description->getClientOriginalName() }}</span>
                        @else
                        {{-- Ikon generik untuk file lain --}}
                        <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zM6 20V4h7v4h4v12H6z" />
                        </svg>
                        <span class="text-sm text-gray-600">File:
                            {{ $documentation_description->getClientOriginalName() }}</span>
                        @endif
                        @else
                        <p class="mt-2 text-sm text-gray-600">File:
                            {{ $documentation_description->getClientOriginalName() }}
                        </p>
                        @endif
                        @endif
                    </div>
                    <x-label-error :messages="$errors->get('documentation_description')" />
            </fieldset>
        </div>
        @endif
        {{-- Navigasi Step --}}
        <div class="absolute inset-x-0 bottom-0 z-50 flex justify-end gap-2 p-2 shadow-md md:mt-4 bg-base-100">
            @if($currentStep > 1)
            <button type="button" class="btn btn-xs btn-outline" wire:click="$set('currentStep', {{ $currentStep - 1 }})">Sebelumnya</button>
            @endif
            @if($currentStep < $totalSteps)
                <button type="button" class="btn btn-xs btn-primary" wire:click="$set('currentStep', {{ $currentStep + 1 }})">Selanjutnya</button>
                @else
                <button type="button" class="btn btn-xs btn-success" wire:click="submit">Submit</button>
                @endif
        </div>
    </x-incident.layout>
</section>
