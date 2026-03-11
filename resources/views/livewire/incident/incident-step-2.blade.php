<div class="grid grid-cols-1">
    <fieldset class="p-3 my-4 border shadow-md border-base-300 fieldset card bg-base-100">
        <legend class="text-sm font-semibold card-title ">
            {{ __('Personel Terlibat / Korban') }}</legend>
        <div class="grid justify-items-stretch">

            <button type="button" wire:click="addDirectlyInvolvedRow"
                class="btn btn-primary btn-xs justify-self-end-safe">
                + {{ __('Tambah Personel') }}
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="table w-full border table-compact">
                <thead>
                    <tr class="bg-base-200">
                        <th class="w-1/4">Nama Personel/Korban</th>
                        <th class="w-32">NIK</th>
                        <th>Dept/Divisi</th>
                        <th>Jabatan</th>
                        <th class="w-24">Roster</th>
                        <th class="w-24">Shift</th>
                        <th>Peran</th>
                        <th class="w-24">Exp (Thn)</th>
                        <th class="w-10"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($directly_involved as $index => $person)
                        <tr wire:key="involved-{{ $index }}">
                            <td class="align-top">
                                <x-form.searchable-select2 placeholder="Cari Nama..."
                                    wire:key="employee_name-field-{{ $index }}-{{ $directly_involved[$index]['employee_name'] ?? 'new' }}"
                                    modelsearch="searchKorban.{{ $index }}"
                                    modelid="directly_involved.{{ $index }}.employee_name"
                                    :options="$involved_personnel_options"
                                    :showdropdown="$show_employee_dropdown[$index] ?? false"
                                    {{-- Tambahkan index sebagai argumen ketiga --}}
                                    clickaction="selectInvolvedPersonnel(VALUE_ID, VALUE_NAME, {{ $index }})" />


                            </td>
                            <td class="align-top">
                                <x-form.input-text model="directly_involved.{{ $index }}.employee_nik"
                                    wire:key="nik-field-{{ $index }}-{{ $directly_involved[$index]['employee_id'] ?? 'new' }}"
                                    :disabled="$person['employee_id'] ? true : false" />
                            </td>
                            <td class="align-top">
                                <x-form.input-text model="directly_involved.{{ $index }}.dept_cont"
                                    wire:key="dept_cont-field-{{ $index }}-{{ $directly_involved[$index]['dept_cont'] ?? 'new' }}"
                                    :disabled="$person['employee_id'] ? true : false" />
                            </td>
                            <td class="align-top">
                                <x-form.input-text model="directly_involved.{{ $index }}.jabatan" required />
                            </td>
                            <td class="align-top">
                                <x-form.input-text model="directly_involved.{{ $index }}.roster" required />
                            </td>
                            <td class="align-top">
                                <x-form.input-text model="directly_involved.{{ $index }}.sift" required />
                            </td>
                            <td class="align-top">
                                <x-form.select model="directly_involved.{{ $index }}.keterlibatan"
                                    :options="$this->keterlibatanOptions" />
                            </td>
                            <td class="align-top">
                                <x-form.input-text model="directly_involved.{{ $index }}.pengalaman_kerja" required />
                            </td>

                            <td class="text-center align-top">
                                @if(count($directly_involved) > 1)
                                    <button type="button" wire:click="removeDirectlyInvolvedRow({{ $index }})"
                                        class="btn btn-ghost btn-xs text-error">
                                        ✕
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </fieldset>
</div>
