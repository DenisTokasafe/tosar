<div class="mt-6">
    {{-- TAMPILAN MOBILE (Android/iPhone) --}}
    <div class="space-y-6 md:hidden">
        {{-- Loop untuk setiap kategori: Pemimpin, Facilitator, Anggota --}}
        @foreach(['pemimpin' => 'Pemimpin Investigasi (KPLH)', 'facilitator' => 'Facilitator (KPLH)', 'anggota' => 'Tim Anggota'] as $type => $label)
        <div class="space-y-3">
            <div class="badge badge-outline badge-sm font-bold">{{ $label }}</div>

            @foreach($$type as $index => $item)
            <div class="relative p-4 border shadow-sm rounded-xl bg-base-100 border-base-300" wire:key="mob-{{ $type }}-{{ $index }}">
                <div class="grid grid-cols-1 gap-3">
                    {{-- Search Nama --}}
                    <x-form.searchable-select2
                        label="Nama {{ $index + 1 }}"
                        wire:key="sel-mob-{{ $type }}-{{ $index }}"
                        placeholder="Cari Nama..."
                        modelsearch="searchQuery.{{ $index }}.{{ $type }}"
                        modelid="{{ $type }}.{{ $index }}.user_id"
                        :options="$options"
                        :showdropdown="($showDropdownPartisipan[$index] ?? false) && $activeType === $type && $activeIndex === $index"
                        clickaction="selectUser(VALUE_ID, {{ $index }}, '{{ $type }}')"
                        x-on:focusin="$wire.set('activeType', '{{ $type }}'); $wire.set('activeIndex', {{ $index }})" />

                    <div class="grid grid-cols-2 gap-2">
                        <x-form.input-text label="Dept" model="{{ $type }}.{{ $index }}.dept" :disabled="!empty($item['user_id'])" />
                        <x-form.input-text label="Jabatan" model="{{ $type }}.{{ $index }}.jabatan" />
                    </div>
                </div>

                {{-- Action Buttons Mobile --}}
                <div class="flex justify-end gap-2 mt-3">
                    @if(count($$type) > 1)
                    <button type="button" wire:click="removeRow('{{ $type }}', {{ $index }})" class="btn btn-error btn-xs btn-outline">Hapus</button>
                    @endif
                    <button type="button" wire:click="addRow('{{ $type }}')" class="btn btn-success btn-xs btn-outline">+ Tambah</button>
                </div>
            </div>
            @endforeach
        </div>
        @if(!$loop->last)
        <hr class="border-base-300"> @endif
        @endforeach
    </div>

    {{-- TAMPILAN TABLET & DESKTOP --}}
    <div class="hidden md:block overflow-x-auto border rounded-xl border-base-300 bg-base-100">
        <table class="table w-full border-collapse table-sm">
            <thead>
                <tr class="bg-base-300 text-base-content text-xs uppercase">
                    {{-- Pembagian lebar yang lebih seimbang --}}
                    <th class="w-[20%] border-r border-base-300 text-center">Peran</th>
                    <th class="w-[30%] border-r border-base-300">Nama</th>
                    <th class="w-[20%] border-r border-base-300">Dept/Perusahaan</th>
                    <th class="w-[20%] border-r border-base-300">Jabatan</th>
                    <th class="w-[10%] text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-xs">
                @foreach(['pemimpin' => 'Pemimpin Investigasi (KPLH)', 'facilitator' => 'Facilitator (KPLH)', 'anggota' => 'Tim Anggota'] as $type => $label)
                @foreach($$type as $index => $item)
                <tr wire:key="row-dt-{{ $type }}-{{ $index }}" class="hover:bg-base-200/50">

                    {{-- Logika Rowspan untuk Peran --}}
                    @if($loop->first)
                    <td rowspan="{{ count($$type) }}" class="font-bold border-r border-b border-base-300 bg-base-200/30 align-middle text-center px-2 py-4 leading-tight">
                        {{ $label }}
                    </td>
                    @endif

                    {{-- Kolom Nama dengan Searchable Select --}}
                    <td class="p-1 border-r border-b border-base-300">
                        <x-form.searchable-select2
                            wire:key="sel-dt-{{ $type }}-{{ $index }}"
                            placeholder="Cari Nama..."
                            modelsearch="searchQuery.{{ $index }}.{{ $type }}"
                            modelid="{{ $type }}.{{ $index }}.user_id"
                            :options="$options"
                            :showdropdown="($showDropdownPartisipan[$index] ?? false) && $activeType === $type && $activeIndex === $index"
                            clickaction="selectUser(VALUE_ID, {{ $index }}, '{{ $type }}')"
                            x-on:focusin="$wire.set('activeType', '{{ $type }}'); $wire.set('activeIndex', {{ $index }})" />
                    </td>

                    {{-- Kolom Dept --}}
                    <td class="p-1 border-r border-b border-base-300">
                        <x-form.input-text model="{{ $type }}.{{ $index }}.dept" :disabled="!empty($item['user_id'])" class="input-xs" />
                    </td>

                    {{-- Kolom Jabatan --}}
                    <td class="p-1 border-r border-b border-base-300">
                        <x-form.input-text model="{{ $type }}.{{ $index }}.jabatan" class="input-xs" />
                    </td>

                    {{-- Kolom Aksi --}}
                    <td class="p-1 border-b border-base-300 align-middle">
                        <div class="flex justify-center gap-1">

                            <x-button.btn-tooltip color="primary" icon="add" wireClick="addRow('{{ $type }}')" tooltip="Tambah Data" />

                            @if(count($$type) > 1)
                            <x-button.btn-tooltip color="error" icon="delete" wireClick="removeRow('{{ $type }}', {{ $index }})" tooltip="Hapus Data" />

                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>