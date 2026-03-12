<div class="grid grid-cols-1 mt-4">

    <div class="grid justify-items-stretch mb-4">
        <div class="justify-self-end-safe"> <button type="button" wire:click="addDirectlyInvolvedRow"
                class="btn btn-primary btn-xs ">
                + {{ __('Tambah Personel') }}
            </button></div>
    </div>
    <div class="overflow-x-auto">
        <table class="table table-xs w-full border border-base-300">
            <thead>
                <tr class="bg-gray-200 text-center text-base-content">
                    <th class="border border-base-300  text-sm font-bold w-1/6">Nama</th>
                    <th class="border border-base-300  text-sm font-bold">ID</th>
                    <th class="border border-base-300  text-sm font-bold ">Dept. / Perusahaan</th>
                    <th class="border border-base-300  text-sm font-bold">Jabatan</th>
                    <th class="border border-base-300  text-sm font-bold">Roster</th>
                    <th class="border border-base-300  text-sm font-bold">Shift</th>
                    <th class="border border-base-300  text-sm font-bold">Keterlibatan</th>
                    <th class="border border-base-300  text-sm font-bold">Pengalaman (Tahun)</th>
                    <th class="w-10 border border-base-300"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($directly_involved as $index => $person)
                <tr wire:key="involved-{{ $index }}">
                    {{-- Kolom Nama --}}
                    <td class="p-1 border border-base-300 align-top">
                        <x-form.searchable-select2 placeholder="Cari Nama..."
                            wire:key="employee_name-field-{{ $index }}-{{ $directly_involved[$index]['employee_name'] ?? 'new' }}"
                            modelsearch="searchKorban.{{ $index }}"
                            modelid="directly_involved.{{ $index }}.employee_name"
                            :options="$involved_personnel_options"
                            :showdropdown="$show_employee_dropdown[$index] ?? false"
                            clickaction="selectInvolvedPersonnel(VALUE_ID, VALUE_NAME, {{ $index }})" />
                    </td>

                    {{-- Kolom ID# / Perusahaan (Gabungan NIK & Dept) --}}
                    <td class="p-1 border border-base-300 align-top">

                        <x-form.input-text model="directly_involved.{{ $index }}.employee_nik"
                            placeholder="NIK/ID#"
                            wire:key="nik-field-{{ $index }}-{{ $directly_involved[$index]['employee_id'] ?? 'new' }}"
                            :disabled="$person['employee_id'] ? true : false" />

                    </td>
                    <td>
                        <x-form.input-text model="directly_involved.{{ $index }}.dept_cont"
                            placeholder="Dept/Perusahaan"
                            wire:key="dept_cont-field-{{ $index }}-{{ $directly_involved[$index]['dept_cont'] ?? 'new' }}"
                            :disabled="$person['employee_id'] ? true : false" />
                    </td>

                    {{-- Kolom Jabatan --}}
                    <td class="p-1 border border-base-300 align-top">
                        <x-form.input-text model="directly_involved.{{ $index }}.jabatan" required />
                    </td>

                    {{-- Kolom Roster --}}
                    <td class="p-1 border border-base-300 align-top">
                        <x-form.input-text model="directly_involved.{{ $index }}.roster" required />
                    </td>

                    {{-- Kolom Shift --}}
                    <td class="p-1 border border-base-300 align-top">
                        <x-form.input-text model="directly_involved.{{ $index }}.sift" required />
                    </td>

                    {{-- Kolom Keterlibatan --}}
                    <td class="p-1 border border-base-300 align-top">
                        <x-form.select model="directly_involved.{{ $index }}.keterlibatan"
                            :options="$this->keterlibatanOptions" />
                    </td>

                    {{-- Kolom Pengalaman (Tahun) --}}
                    <td class="p-1 border border-base-300 align-top text-center">
                        <x-form.input-text model="directly_involved.{{ $index }}.pengalaman_kerja" required />
                    </td>

                    {{-- Kolom Aksi --}}
                    <td class="p-1 border border-base-300 text-center align-middle">
                        @if(count($directly_involved) > 1)
                        <button type="button" wire:click="removeDirectlyInvolvedRow({{ $index }})"
                            class="btn btn-ghost btn-xs text-error">✕</button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>