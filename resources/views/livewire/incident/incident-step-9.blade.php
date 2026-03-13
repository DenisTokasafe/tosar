<div class="w-full">
    <fieldset class="w-full mt-4 border fieldset bg-base-200 border-base-300 rounded-box">
        <legend class="fieldset-legend">Penerimaan & Komentar Project Manager: (Jika kontraktor)</legend>
        <div x-data="ckeditorHelper('penerimaan_komentar_contractor')" wire:ignore>
            <div x-ref="editorElement" data-placeholder="Masukkan Penerimaan & Komentar Project Manager: (Jika kontraktor) ..."></div>
        </div>
        <x-label-error :messages="$errors->get('penerimaan_komentar_contractor')" />

        <div class="grid grid-cols-1 gap-4 mt-4 md:grid-cols-3">
            <x-form.searchable-select2 wire:key="select-penerimaan-komentar-contractor"
                placeholder="Cari Nama..." modelsearch="searchNamePenerimaan.kontraktor"
                modelid="penerimaan_komentar_contractor_id"
                :options="$pelapors"
                :showdropdown="$showPenerimaanKomentarContractorDropdown"
                clickaction="selectPenerimaanKomentarContractor"
                x-on:focusin="$wire.set('activeTypePenerimaan', 'penerimaan_komentar_contractor'); $wire.set('activeIndexPenerimaan', 0)" />
        </div>


    </fieldset>
    <fieldset class="w-full mt-2 border fieldset bg-base-200 border-base-300 rounded-box">
        <legend class="fieldset-legend">Penerimaan & Komentar Project Manager</legend>
        <div x-data="ckeditorHelper('penerimaan_komentar_internal')" wire:ignore>
            <div x-ref="editorElement" data-placeholder="Masukkan Penerimaan & Komentar Project Manager..."></div>
        </div>
        <x-label-error :messages="$errors->get('penerimaan_komentar_internal')" />

        <div class="grid grid-cols-1 gap-4 mt-4 md:grid-cols-3">
            <x-form.searchable-select2 wire:key="select-penerimaan-komentar-internal"
                placeholder="Cari Nama..." modelsearch="searchNamePenerimaan.internal"
                modelid="penerimaan_komentar_internal_id"
                :options="$pelapors"
                :showdropdown="$showPenerimaanKomentarInternalDropdown"
                clickaction="selectPenerimaanKomentarInternal"
                x-on:focusin="$wire.set('activeTypePenerimaan', 'penerimaan_komentar_internal'); $wire.set('activeIndexPenerimaan', 0)" />
        </div>


    </fieldset>

    <fieldset class="w-full mt-2 border fieldset bg-base-200 border-base-300 rounded-box">
        <legend class="fieldset-legend">Penerimaan & Komentar OHS Dept Head</legend>
        <div x-data="ckeditorHelper('penerimaan_komentar_ohs')" wire:ignore>
            <div x-ref="editorElement" data-placeholder="Masukkan Penerimaan & Komentar OHS Dept Head..."></div>
        </div>
        <x-label-error :messages="$errors->get('penerimaan_komentar_ohs')" />

        <div class="grid grid-cols-1 gap-4 mt-4 md:grid-cols-3">
            <x-form.searchable-select2 wire:key="select-penerimaan-komentar-ohs"
                placeholder="Cari Nama..." modelsearch="searchNamePenerimaan.ohs"
                modelid="penerimaan_komentar_ohs_id"
                :options="$pelapors"
                :showdropdown="$showPenerimaanKomentarOhsDropdown"
                clickaction="selectPenerimaanKomentarOhs"
                x-on:focusin="$wire.set('activeTypePenerimaan', 'penerimaan_komentar_ohs'); $wire.set('activeIndexPenerimaan', 0)" />
        </div>


    </fieldset>
    <fieldset class="w-full mt-2 border fieldset bg-base-200 border-base-300 rounded-box">
        <legend class="fieldset-legend">Penerimaan & Komentar KTT: (Hanya untuk insiden dengan aktual Level 3,4,5)</legend>
        <div x-data="ckeditorHelper('penerimaan_komentar_ktt')" wire:ignore>
            <div x-ref="editorElement" data-placeholder="Masukkan Penerimaan & Komentar KTT: (Hanya untuk insiden dengan aktual Level 3,4,5)..."></div>
        </div>
        <x-label-error :messages="$errors->get('penerimaan_komentar_ktt')" />

        <div class="grid grid-cols-1 gap-4 mt-4 md:grid-cols-3">
            <x-form.searchable-select2 wire:key="select-penerimaan-komentar-ktt"
                placeholder="Cari Nama..." modelsearch="searchNamePenerimaan.ktt"
                modelid="penerimaan_komentar_ktt_id"
                :options="$pelapors"
                :showdropdown="$showPenerimaanKomentarKttDropdown"
                clickaction="selectPenerimaanKomentarKtt"
                x-on:focusin="$wire.set('activeTypePenerimaan', 'penerimaan_komentar_ktt'); $wire.set('activeIndexPenerimaan', 0)" />
        </div>
    </fieldset>
</div>