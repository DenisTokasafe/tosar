<section class="w-full">
    <x-toast />
    <x-tabs-wpi.layout heading="Buat Laporan Inspeksi Kebakaran" subheading="Inspeksi Kebakaran - Site Tokatindung">
        <div class="p-6 bg-white rounded-lg shadow">
            <div class="mb-4">
                <fieldset class="w-full fieldset md:max-w-80">
                    <x-form.label label="Pilih Jenis Alat" required />
                    <select wire:model.live="type"
                        class="select select-xs select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden {{ $errors->has('type') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}">
                        <option value="">-- Pilih --</option>
                        @foreach (array_keys($fields) as $key)
                            <option value="{{ $key }}">{{ $key }}</option>
                        @endforeach
                    </select>
                    <x-label-error :messages="$errors->get('type')" />
                </fieldset>
            </div>

            <div class="grid grid-cols-1 gap-2 mb-4 md:grid-cols-3">
                {{-- <x-form.input-floating label="Area" model="area" required /> --}}
                <x-form.search-floating label="Area" required modelsearch="searchLocation" modelid="location_id"
                    placeholder="Area..." :options="$locations" :showdropdown="$show_location" clickaction="selectLocation"
                    namedb="name" />
                <x-form.input-floating label="Lokasi Spesifik" model="location" required />
                <x-form.datepicker label="Tanggal / Date" model="inspection_date" />
            </div>

            <div class="p-4 mb-4 border rounded-lg bg-gray-50">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3 ">
                    @if (isset($fields[$type]['inputs']))
                        @foreach ($fields[$type]['inputs'] as $inputField)
                            <fieldset class="fieldset">

                                <label class="floating-label">
                                    <input type="text" wire:key="condition-{{ $inputField }}"
                                        wire:model.live="conditions.{{ $inputField }}"
                                        placeholder="{{ $inputField }}"
                                        class=" input-xs input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden {{ $errors->has('conditions.' . $inputField) ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}" />
                                    <span>{{ $inputField }} <span class="font-bold text-red-500">*</span></span>
                                </label>
                                <x-label-error :messages="$errors->get('conditions.' . $inputField)" />
                            </fieldset>
                        @endforeach
                    @endif
                </div>
                <h3 class="mb-3 font-bold">Kondisi Checklist ({{ $type }}):</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3 lg:grid-cols-4">

                    @foreach ($fields[$type]['checks'] as $field)
                        <fieldset class="p-2 border rounded-md fieldset">
                            <label class="label">
                                {{ $field }}
                                <input type="checkbox" wire:key="condition-{{ $field }}" checked
                                    wire:model="conditions.{{ $field }}"
                                    class="checkbox checkbox-xs border-rose-600 bg-rose-500 checked:border-emerald-500 checked:bg-emerald-400 checked:text-emerald-800" />
                            </label>
                        </fieldset>
                    @endforeach
                </div>
            </div>
            <x-form.textarea label="Deskripsi Temuan" required model="remarks" placeholder="Remarks/Catatan..." />
            <fieldset class=" fieldset">
                <x-form.upload label="Lampirkan foto atau dokumentasi" model="dokumentasi" :file="$dokumentasi" />
                <div wire:loading.remove wire:target="dokumentasi">
                    @if ($dokumentasi)
                        @if (in_array($dokumentasi->getClientOriginalExtension(), ['jpg', 'jpeg', 'png']))
                            <img src="{{ $dokumentasi->temporaryUrl() }}"
                                class="mt-2 {{ $dokumentasi ? 'w-40' : '' }} h-auto rounded border" />
                        @elseif (in_array($dokumentasi->getClientOriginalExtension(), ['pdf', 'doc', 'docx']))
                            <div class="flex items-center gap-2 mt-2">
                                @if ($dokumentasi->getClientOriginalExtension() == 'pdf')
                                    <x-icon.pdf class="w-8 h-8" />
                                    <span
                                        class="text-sm text-red-600">{{ $dokumentasi->getClientOriginalName() }}</span>
                                @elseif (in_array($dokumentasi->getClientOriginalExtension(), ['doc', 'docx']))
                                    <x-icon.word class="w-8 h-8" />
                                    <span
                                        class="text-sm text-blue-600">{{ $dokumentasi->getClientOriginalName() }}</span>
                                @else
                                    {{-- Ikon generik untuk file lain --}}
                                    <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zM6 20V4h7v4h4v12H6z" />
                                    </svg>
                                    <span class="text-sm text-gray-600">File:
                                        {{ $dokumentasi->getClientOriginalName() }}</span>
                                @endif
                            </div>
                        @else
                            <p class="mt-2 text-sm text-gray-600">File:
                                {{ $dokumentasi->getClientOriginalName() }}
                            </p>
                        @endif
                    @endif
                </div>
                <x-label-error :messages="$errors->get('dokumentasi')" />
            </fieldset>
            <fieldset class="fieldset">
                <x-form.searchable-select-advanced label="Dilaporkan Oleh" placeholder="Cari Nama Pelapor..."
                    modelsearch="searchResponsibility" modelid="action_responsible_id" {{-- ID asli di DB --}}
                    :options="$pelapors" :showdropdown="$showPelaporDropdown" {{-- Logic Manual --}} :manualMode="$manualPelaporMode"
                    manualModelName="manualPelaporName" enableManualAction="enableManualPelapor"
                    addManualAction="addPelaporManual" clickaction="selectPelapor" />
                <div class="flex flex-wrap gap-2 mb-2">
                    @foreach ($inspected_users as $index => $user)
                        <div
                            class="flex items-center gap-1 px-2 py-1 text-xs font-medium border rounded bg-info/10 text-info border-info/20">
                            <span>{{ $user['name'] }}</span>
                            <button type="button" wire:click="removeInspectedUser({{ $index }})"
                                class="hover:text-error">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>
            </fieldset>
            <button wire:click="save" class="btn btn-soft btn-success btn-xs hover:btn-success/80">
                Simpan Laporan
            </button>
        </div>
    </x-tabs-wpi.layout>
