<div class="mt-6 overflow-x-auto">
    <table class="table w-full table-xs border border-collapse border-base-300">
        {{-- SECTION 1: PENYEBAB LANGSUNG (Kondisi & Perilaku) --}}
        <thead>
            <tr class="text-center bg-orange-100">
                <th colspan="3" class="py-1 italic font-bold border text-base-content border-base-300">PENYEBAB LANGSUNG</th>
            </tr>
            <tr class="bg-gray-200 text-base-content uppercase text-[10px]">
                <th class="w-1/2 px-4 py-2 font-bold border border-base-300">Kondisi Tidak Aman</th>
                <th class="w-1/2 px-4 py-2 font-bold border border-base-300">Description</th>
                <th class="w-10 border border-base-300"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($unsafe_conditions as $index => $row)
            <tr wire:key="cond-{{ $index }}">
                <td class="p-1 align-top border border-base-300">
                    <x-form.select model="unsafe_conditions.{{ $index }}.item" :options="$this->unsafeConditionOptions" placeholder="Choose..." />
                </td>
                <td class="p-1 align-top border border-base-300">
                    <x-form.text_area wire:model="unsafe_conditions.{{ $index }}.description" rows="2" />
                </td>
                <td class="p-1 text-center border border-base-300">
                    <button type="button" wire:click="removeRow('unsafe_conditions', {{ $index }})" class="btn btn-ghost btn-xs text-error">✕</button>
                </td>
            </tr>
            @endforeach

            <tr class="bg-gray-200 text-base-content uppercase text-[10px]">
                <th class="px-4 py-2 font-bold border border-base-300">Perilaku Tidak Aman</th>
                <th class="px-4 py-2 font-bold border border-base-300 text-left">Description</th>
                <th class="border border-base-300"></th>
            </tr>
            @foreach($unsafe_acts as $index => $row)
            <tr wire:key="act-{{ $index }}">
                <td class="p-1 align-top border border-base-300">
                    <x-form.select model="unsafe_acts.{{ $index }}.item" :options="$this->unsafeActOptions" placeholder="Choose..." />
                </td>
                <td class="p-1 align-top border border-base-300">
                    <x-form.text_area wire:model="unsafe_acts.{{ $index }}.description" rows="2" />
                </td>
                <td class="p-1 text-center border border-base-300">
                    <button type="button" wire:click="removeRow('unsafe_acts', {{ $index }})" class="btn btn-ghost btn-xs text-error">✕</button>
                </td>
            </tr>
            @endforeach
        </tbody>

        {{-- SECTION 2: PENYEBAB DASAR --}}
        <thead>
            <tr class="text-center bg-orange-100">
                <th colspan="3" class="py-1 italic font-bold border text-base-content border-base-300">PENYEBAB DASAR</th>
            </tr>
        </thead>
        <tbody>
            {{-- 2.1 FAKTOR PRIBADI --}}
            <tr class="bg-gray-200 text-base-content uppercase text-[10px]">
                <th class="px-4 py-1 font-bold border border-base-300">2.1 Faktor Pribadi</th>
                <th class="px-4 py-1 font-bold border border-base-300 text-left">Description</th>
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
                    <button type="button" wire:click="removeRow('personal_factors', {{ $index }})" class="btn btn-ghost btn-xs text-error">✕</button>
                </td>
            </tr>
            @endforeach

            {{-- 2.2 FAKTOR PEKERJAAN --}}
            <tr class="bg-gray-200 text-base-content uppercase text-[10px]">
                <th class="px-4 py-1 font-bold border border-base-300">2.2 Faktor Pekerjaan</th>
                <th class="px-4 py-1 font-bold border border-base-300 text-left">Description</th>
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
                    <button type="button" wire:click="removeRow('job_factors', {{ $index }})" class="btn btn-ghost btn-xs text-error">✕</button>
                </td>
            </tr>
            @endforeach

            {{-- 2.3 KELEMAHAN SISTEM KONTROL --}}
            <tr class="bg-gray-200 text-base-content uppercase text-[10px]">
                <th class="px-4 py-1 font-bold border border-base-300">2.3 Kelemahan Sistem Kontrol</th>
                <th class="px-4 py-1 font-bold border border-base-300 text-left">Description</th>
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
                    <button type="button" wire:click="removeRow('control_system_factors', {{ $index }})" class="btn btn-ghost btn-xs text-error">✕</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Tombol Navigasi Tambah (Opsional: Bisa ditaruh di bawah tabel atau per seksi) --}}
    <div class="mt-4 flex flex-wrap gap-2">
        <button type="button" wire:click="addRow('personal_factors')" class="btn btn-xs btn-outline btn-primary">+ Faktor Pribadi</button>
        <button type="button" wire:click="addRow('job_factors')" class="btn btn-xs btn-outline btn-primary">+ Faktor Pekerjaan</button>
        <button type="button" wire:click="addRow('control_system_factors')" class="btn btn-xs btn-outline btn-primary">+ Sistem Kontrol</button>
    </div>
</div>