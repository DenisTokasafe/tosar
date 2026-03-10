<div class="grid grid-cols-1">
    <fieldset class="p-3 my-4 border shadow-md border-base-300 fieldset card bg-base-100">
        <legend class="text-sm font-semibold card-title ">{{ __('Personel Terlibat / Korban') }}</legend>
        <div class="flex items-center justify-between pb-2 border-b">
            <h3 class="text-sm font-bold uppercase">{{ __('Pihak Terlibat Langsung') }}</h3>
            <button type="button" wire:click="addDirectlyInvolvedRow" class="btn btn-primary btn-xs">
                + {{ __('Tambah Pihak') }}
            </button>
        </div>
        @foreach ($directly_involved as $index => $person)
        <div class="flex items-start gap-4 mb-4">
            <div class="w-1/2">
                <x-form.searchable-select-advanced
                    label="Nama Personel Terlibat/Korban"
                    placeholder="Cari Nama..."
                    modelsearch="directly_involved.{{ $index }}.employee_name"
                    modelid="directly_involved.{{ $index }}.employee_id"
                    :options="$involved_personnel_options"
                    :showdropdown="$person['show_employee_dropdown']"
                    enableManualAction="enableInvolvedPersonnelManual({{ $index }})"
                    clickaction="selectInvolvedPersonnel({{ $index }}, " />
            </div>
            <div class="w-1/4">
                @if ($person['employee_id'])
                <x-form.input-text label="NIK" model="directly_involved.{{ $index }}.employee_nik" disabled />
                @else
                <x-form.input-text label="NIK" model="directly_involved.{{ $index }}.employee_nik" />
                @endif
                <div class="w-1/4">
                    <x-form.input-text label="Jabatan" model="directly_involved.{{ $index }}.jabatan" required />
                </div>
                <div class="w-1/4">
                    <x-form.input-text label="Roster" model="directly_involved.{{ $index }}.roster" required />
                </div>
                <div class="w-1/4">
                    <x-form.input-text label="Sift" model="directly_involved.{{ $index }}.sift" required />
                </div>
                <div class="w-1/4">
                    <x-form.select label="Peran" model="directly_involved.{{ $index }}.keterlibatan">
                        <option value="" disabled selected>-- Pilih Peran --</option>
                        <option value="saksi">Saksi</option>
                        <option value="korban_cedera">Korban Cedera</option>
                        <option value="kontraktor">Kontraktor</option>
                        <option value="operator">Operator</option>
                        <option value="lainnya">Lainnya</option>
                    </x-form.select>
                </div>
                <div class="w-1/4">
                    @if ($person['employee_id'])
                    <x-form.input-text label="Departemen/Divisi" model="directly_involved.{{ $index }}.dept_cont" disabled />
                    @else
                    <x-form.input-text label="Departemen/Divisi" model="directly_involved.{{ $index }}.dept_cont" />
                    @endif
                </div>
                <div class="w-1/4">
                    <x-form.input-text label="Pengalaman Kerja (tahun)" model="directly_involved.{{ $index }}.pengalaman_kerja" required />
                </div>

                <div class="w-12">
                    @if(count($directly_involved) > 1)
                    <button type="button" wire:click="removeDirectlyInvolvedRow({{ $index }})" class="btn btn-ghost btn-xs text-error mt-2">✕</button>
                    @endif
                </div>
            </div>

            @endforeach
    </fieldset>
</div>