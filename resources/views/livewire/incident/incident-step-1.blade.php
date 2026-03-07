 <div class="grid grid-cols-1 gap-2 mt-12 mb-8 md:grid-cols-2 lg:grid-cols-3">
     <x-form.tgl-waktu label="Tanggal & Waktu Kejadian" model="date_time" required />
     <x-form.search-template label="Lokasi" required modelsearch="searchLocation" modelid="location_id" :options="$locations" :showdropdown="$show_location" clickaction="selectLocation" namedb="name" />
     {{-- Lokasi spesifik muncul hanya jika lokasi utama sudah dipilih --}}
     @if ($location_id)
     <x-form.input-text label="Lokasi Spesifik" model="location_specific" placeholder="Masukkan detail lokasi spesifik..." required />
     @endif
     <x-form.department-contractor-selector
         model="deptCont"
         :departments="$departments"
         :contractors="$contractors"
         :showDropdown="$showDropdown"
         :showContractorDropdown="$showContractorDropdown" />
     <fieldset class="fieldset">
         <x-form.label label="PIC" required />
         <select wire:model.live="penanggungJawab"
             class="select select-xs select-bordered w-full focus-within:outline-none focus-within:border-info focus-within:ring-0 {{ $errors->has('penanggungJawab') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}">
             <option value="">{{__('-- Pilih --')}}</option>
             @foreach ($penanggungJawabOptions as $pj)
             <option value="{{ $pj['id'] }}">{{ $pj['name'] }}</option>
             @endforeach
         </select>
         <x-label-error :messages="$errors->get('penanggungJawab')" />
     </fieldset>

     <x-form.searchable-select-advanced label="Dilaporkan Oleh" placeholder="Cari Nama Pelapor..."
         modelsearch="searchPelapor" modelid="pelapor_id" {{-- ID asli di DB --}} :options="$pelapors"
         :showdropdown="$showPelaporDropdown" {{-- Logic Manual --}} :manualMode="$manualPelaporMode"
         manualModelName="manualPelaporName" enableManualAction="enableManualPelapor"
         addManualAction="addPelaporManual" clickaction="selectPelapor" />
 </div>