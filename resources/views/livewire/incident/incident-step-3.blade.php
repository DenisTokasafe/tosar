<table class="table w-full border border-collapse table-xs table-compact border-base-300 mt-4">
    <thead class="text-xs text-white uppercase bg-black">
        <tr>
            <th class="w-1/4 border border-base-300">Peran</th>
            <th class="w-1/4 border border-base-300">Nama</th>
            <th class="w-1/4 border border-base-300">Dept/Perusahaan</th>
            <th class="w-1/4 text-center border border-base-300">Jabatan</th>
        </tr>
    </thead>
    <tbody>
        {{-- SEKSI PEMIMPIN INVESTIGASI --}}
        @foreach($pemimpin as $index => $item)
        {{-- Tambahkan wire:key di tag TR --}}
        <tr wire:key="row-pemimpin-{{ $index }}">
            @if($loop->first)
            <td rowspan="{{ count($pemimpin ) }}" class="font-bold border border-base-300 bg-base-100">
                Pemimpin Investigasi: (Personil KPLH)
            </td>
            @endif
            <td class="p-1 border border-base-300">
                {{-- Contoh untuk Pemimpin --}}
                <x-form.searchable-select2 wire:key="select-pemimpin-{{ $index }}"
                    placeholder="Cari Pemimpin..." modelsearch="searchQuery.{{ $index }}.pemimpin"
                    modelid="pemimpin.{{ $index }}.user_id" :options="$options"
                    {{-- Perhatikan perubahan di bawah ini: tambahkan [$index] --}}
                    :showdropdown="($showDropdownPartisipan[$index] ?? false) && $activeType === 'pemimpin' && $activeIndex === $index"
                    clickaction="selectUser(VALUE_ID, {{ $index }}, 'pemimpin')"
                    x-on:focusin="$wire.set('activeType', 'pemimpin'); $wire.set('activeIndex', {{ $index }})" />
            </td>
            <td class="p-1 border border-base-300">
                <x-form.input-text model="pemimpin.{{ $index }}.dept"
                    wire:key="dept-field-{{ $index }}-{{ $pemimpin[$index]['dept'] ?? 'new' }}"
                    :disabled="$item['jabatan'] ? true : false" />

            </td>
            <td class="flex items-center gap-1 border border-base-300">
                <x-form.input-text model="pemimpin.{{ $index }}.jabatan"
                    wire:key="jabatan-pemimpin-{{ $index }}" />

                <button type="button" wire:click="addRow('pemimpin')"
                    class="btn btn-xs btn-ghost text-success">+</button>
                @if(count($pemimpin) > 1)
                <button type="button" wire:click="removeRow('pemimpin', {{ $index }})"
                    class="btn btn-xs btn-ghost text-error">×</button>
                @endif
            </td>
        </tr>
        @endforeach

        {{-- SEKSI FACILITATOR --}}
        @foreach($facilitator as $index => $item)
        <tr wire:key="row-facilitator-{{ $index }}">
            @if($loop->first)
            <td rowspan="{{ count($facilitator) }}" class="font-bold border border-base-300 bg-base-100">
                Facilitator: (Personil KPLH)
            </td>
            @endif
            <td class="p-1 border border-base-300">
                <x-form.searchable-select2 wire:key="select-facilitator-{{ $index }}"
                    placeholder="Cari Facilitator..." modelsearch="searchQuery.{{ $index }}.facilitator"
                    modelid="facilitator.{{ $index }}.user_id" :options="$options"
                    {{-- Cek spesifik untuk type facilitator dan index-nya --}}
                    :showdropdown="($showDropdownPartisipan[$index] ?? false) && $activeType === 'facilitator' && $activeIndex === $index"
                    {{-- Mengirimkan type 'facilitator' ke fungsi selectUser --}}
                    clickaction="selectUser(VALUE_ID, {{ $index }}, 'facilitator')"
                    {{-- Set state saat input fokus --}}
                    x-on:focusin="$wire.set('activeType', 'facilitator'); $wire.set('activeIndex', {{ $index }})" />
            </td>
            <td class="p-1 border border-base-300">
                <x-form.input-text model="facilitator.{{ $index }}.dept"
                    wire:key="dept-facilitator-{{ $index }}-{{ $facilitator[$index]['dept'] ?? 'new' }}"
                    :disabled="!empty($item['user_id'])" />
            </td>
            <td class="flex items-center gap-1 border border-base-300">
                <x-form.input-text model="facilitator.{{ $index }}.jabatan"
                    wire:key="jabatan-facilitator-{{ $index }}" />

                <button type="button" wire:click="addRow('facilitator')"
                    class="btn btn-xs btn-ghost text-success">+</button>

                @if(count($facilitator) > 1)
                <button type="button" wire:click="removeRow('facilitator', {{ $index }})"
                    class="btn btn-xs btn-ghost text-error">×</button>
                @endif
            </td>
        </tr>
        @endforeach
        {{-- SEKSI TIM ANGGOTA --}}
        @foreach($anggota as $index => $item)
        <tr wire:key="row-anggota-{{ $index }}">
            @if($loop->first)
            <td rowspan="{{ count($anggota) }}" class="font-bold border border-base-300 bg-base-100"> Tim Anggota</td>
            @endif
            <td class="p-1 border border-base-300">
                <x-form.searchable-select2
                    wire:key="select-anggota-{{ $index }}"
                    placeholder="Cari Anggota..."
                    modelsearch="searchQuery.{{ $index }}.anggota"
                    modelid="anggota.{{ $index }}.user_id"
                    :options="$options"
                    {{-- Logika dropdown: pastikan index dan type anggota cocok --}}
                    :showdropdown="($showDropdownPartisipan[$index] ?? false) && $activeType === 'anggota' && $activeIndex === $index"

                    {{-- Mengirim 'anggota' ke fungsi selectUser --}}
                    clickaction="selectUser(VALUE_ID, {{ $index }}, 'anggota')"

                    {{-- Set state saat fokus --}}
                    x-on:focusin="$wire.set('activeType', 'anggota'); $wire.set('activeIndex', {{ $index }})" />
            </td>
            <td class="p-1 border border-base-300">
                <x-form.input-text
                    model="anggota.{{ $index }}.dept"
                    wire:key="dept-anggota-{{ $index }}-{{ $anggota[$index]['dept'] ?? 'new' }}" />
            </td>
            <td class="flex items-center gap-1 p-1 border border-base-300">
                <x-form.input-text
                    model="anggota.{{ $index }}.jabatan"
                    wire:key="jabatan-anggota-{{ $index }}" />
                <button type="button" wire:click="addRow('anggota')"
                    class="btn btn-xs btn-ghost text-success">+</button>

                @if(count($anggota) > 1)
                <button type="button" wire:click="removeRow('anggota', {{ $index }})"
                    class="btn btn-xs btn-ghost text-error">×</button>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>