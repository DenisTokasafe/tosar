<div class="mt-4 overflow-x-auto">
    <table class="table w-full border border-collapse table-xs border-base-300">
        <thead>
            {{-- Sub-Header Utama Penyebab Langsung --}}
            <tr class="text-center bg-orange-100">
                <th colspan="3" class="py-1 italic font-bold border text-base-content border-base-300">
                    PENYEBAB LANGSUNG
                </th>
            </tr>

            {{-- SEKSI 1: KONDISI TIDAK AMAN --}}
            <tr class="bg-gray-200 text-base-content">
                <th class="w-1/2 px-4 py-2 font-bold border border-base-300 uppercase text-[10px]">Kondisi Tidak Aman</th>
                <th class="w-1/2 px-4 py-2 font-bold border border-base-300 uppercase text-[10px]">Description</th>
                <th class="w-10 border border-base-300"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($unsafe_conditions as $index => $row)
            <tr wire:key="unsafe-condition-{{ $index }}">
                <td class="p-1 align-top border border-base-300">
                    <x-form.select
                        model="unsafe_conditions.{{ $index }}.item"
                        :options="$this->unsafeConditionOptions"
                        placeholder="Choose an item." />
                </td>
                <td class="p-1 align-top border border-base-300">
                    <x-form.text_area
                        wire:model="unsafe_conditions.{{ $index }}.description"
                        placeholder="Tambahkan rincian deskripsi..."
                        rows="2" />
                </td>
                <td class="p-1 text-center align-middle border border-base-300">
                    @if(count($unsafe_conditions) > 1)
                    <button type="button" wire:click="removeRow('unsafe_conditions', {{ $index }})"
                        class="btn btn-ghost btn-xs text-error">✕</button>
                    @endif
                </td>
            </tr>
            @endforeach
            <tr class="bg-base-200/50">
                <td colspan="3" class="p-2 border border-base-300">
                    <button type="button" wire:click="addRow('unsafe_conditions')" class="font-bold btn btn-xs btn-ghost text-primary">
                        + TAMBAH KONDISI TIDAK AMAN
                    </button>
                </td>
            </tr>

            {{-- SEKSI 2: PERILAKU TIDAK AMAN --}}
            <thead>
                <tr class="bg-gray-200 text-base-content">
                    <th class="w-1/2 px-4 py-2 font-bold border border-base-300 uppercase text-[10px]">Perilaku Tidak Aman</th>
                    <th class="w-1/2 px-4 py-2 font-bold border border-base-300 uppercase text-[10px]">Description</th>
                    <th class="w-10 border border-base-300"></th>
                </tr>
            </thead>
            @foreach($unsafe_acts as $index => $row)
            <tr wire:key="unsafe-act-{{ $index }}">
                <td class="p-1 align-top border border-base-300">
                    <x-form.select
                        model="unsafe_acts.{{ $index }}.item"
                        :options="$this->unsafeActOptions"
                        placeholder="Choose an item." />
                </td>
                <td class="p-1 align-top border border-base-300">
                    <x-form.text_area
                        wire:model="unsafe_acts.{{ $index }}.description"
                        placeholder="Tambahkan rincian deskripsi..."
                        rows="2" />
                </td>
                <td class="p-1 text-center align-middle border border-base-300">
                    @if(count($unsafe_acts) > 1)
                    <button type="button" wire:click="removeRow('unsafe_acts', {{ $index }})"
                        class="btn btn-ghost btn-xs text-error">✕</button>
                    @endif
                </td>
            </tr>
            @endforeach
            <tr class="bg-base-200/50">
                <td colspan="3" class="p-2 border border-base-300">
                    <button type="button" wire:click="addRow('unsafe_acts')" class="font-bold btn btn-xs btn-ghost text-primary">
                        + TAMBAH PERILAKU TIDAK AMAN
                    </button>
                </td>
            </tr>
            <tr class="text-center bg-orange-100">
                <th colspan="3" class="py-1 italic font-bold border text-base-content border-base-300">PENYEBAB DASAR</th>
            </tr>
            <tr class="bg-gray-200 text-base-content uppercase text-[10px]">
                <th class="px-4 py-1 font-bold border border-base-300">2.1 Faktor Pribadi</th>
                <th class="px-4 py-1 font-bold text-left border border-base-300">Description</th>
                <th class="border border-base-300"></th>
            </tr>
            @foreach($personal_factors as $index => $row)
            <tr wire:key="personal-{{ $index }}">
                <td class="p-1 align-top border border-base-300">
                    <x-form.select model="personal_factors.{{ $index }}.item" :options="$this->personalFactorOptions" placeholder="Choose..." />
                </td>
                <td class="p-1 align-top border border-base-300">
                    <x-form.text_area wire:model="personal_factors.{{ $index }}.description" rows="2" />
                </td>
                <td class="p-1 text-center border border-base-300">
                    @if(count($personal_factors) > 1)
                    <button type="button" wire:click="removeRow('personal_factors', {{ $index }})" class="btn btn-ghost btn-xs text-error">✕</button>
                    @endif
                </td>
            </tr>
            @endforeach
            <tr class="bg-base-200/50">
                <td colspan="3" class="p-2 border border-base-300">
                    <button type="button" wire:click="addRow('personal_factors')" class="btn btn-xs btn-outline btn-primary">+ Faktor Pribadi</button>
                </td>
            </tr>
            {{-- 2.2 FAKTOR PEKERJAAN --}}
            <tr class="bg-gray-200 text-base-content uppercase text-[10px]">
                <th class="px-4 py-1 font-bold border border-base-300">2.2 Faktor Pekerjaan</th>
                <th class="px-4 py-1 font-bold text-left border border-base-300">Description</th>
                <th class="border border-base-300"></th>
            </tr>
            @foreach($job_factors as $index => $row)
            <tr wire:key="job-{{ $index }}">
                <td class="p-1 align-top border border-base-300">
                    <x-form.select model="job_factors.{{ $index }}.item" :options="$this->jobFactorOptions" placeholder="Choose..." />
                </td>
                <td class="p-1 align-top border border-base-300">
                    <x-form.text_area wire:model="job_factors.{{ $index }}.description" rows="2" />
                </td>
                <td class="p-1 text-center border border-base-300">
                    @if(count($job_factors) > 1)
                    <button type="button" wire:click="removeRow('job_factors', {{ $index }})" class="btn btn-ghost btn-xs text-error">✕</button>
                    @endif
                </td>
            </tr>
            @endforeach
            <tr class="bg-base-200/50">
                <td colspan="3" class="p-2 border border-base-300">
                    <button type="button" wire:click="addRow('job_factors')" class="btn btn-xs btn-outline btn-primary">+ Faktor Pekerjaan</button>
                </td>
            </tr>

            {{-- 2.3 KELEMAHAN SISTEM KONTROL --}}
            <tr class="bg-gray-200 text-base-content uppercase text-[10px]">
                <th class="px-4 py-1 font-bold border border-base-300">2.3 Kelemahan Sistem Kontrol</th>
                <th class="px-4 py-1 font-bold text-left border border-base-300">Description</th>
                <th class="border border-base-300"></th>
            </tr>
            @foreach($control_system_factors as $index => $row)
            <tr wire:key="control-{{ $index }}">
                <td class="p-1 align-top border border-base-300">
                    <x-form.select model="control_system_factors.{{ $index }}.item" :options="$this->controlSystemOptions" placeholder="Choose..." />
                </td>
                <td class="p-1 align-top border border-base-300">
                    <x-form.text_area wire:model="control_system_factors.{{ $index }}.description" rows="2" />
                </td>
                <td class="p-1 text-center border border-base-300">
                    @if(count($control_system_factors) > 1)
                    <button type="button" wire:click="removeRow('control_system_factors', {{ $index }})" class="btn btn-ghost btn-xs text-error">✕</button>
                    @endif
                </td>
            </tr>
            @endforeach
            <tr class="bg-base-200/50">
                <td colspan="3" class="p-2 border border-base-300">
                    <button type="button" wire:click="addRow('control_system_factors')" class="btn btn-xs btn-outline btn-primary">+ Sistem Kontrol</button>
                </td>
            </tr>
        </tbody>

    </table>

</div>