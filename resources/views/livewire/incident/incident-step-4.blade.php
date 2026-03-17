<div class="mt-4 overflow-x-auto">
    <table class="table w-full border border-collapse table-xs border-base-300">
        <thead>
            <tr class="text-center ">
                <th class="w-1/6 px-4 font-bold border border-base-300 text-base-content">Factors</th>
                <th class="w-2/5 px-4 font-bold border border-base-300 text-base-content">Temuan</th>
                <th class="w-2/5 px-4 font-bold border border-base-300 text-base-content">Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            {{-- Menggunakan properti dari controller --}}
            @foreach($peepoFactors as $key => $label)
            <tr>
                <td class="px-4 font-bold align-middle border border-base-300 text-base-content">
                    <div class="flex flex-col gap-1">
                        {{ $label }}
                        @if(!empty($peepo[$key]['temuan']) && !empty($peepo[$key]['deskripsi']))
                        <span class="badge badge-success badge-xs">Lengkap</span>
                        @else
                        <span class="badge badge-ghost badge-xs text-gray-400">Belum Lengkap</span>
                        @endif
                    </div>
                </td>
                <td class="p-2 border border-base-300">
                    <x-form.text_area
                        model="peepo.{{ $key }}.temuan"
                        placeholder="Masukkan temuan untuk faktor {{ $label }}..."
                        rows="2" />
                </td>
                <td class="p-2 border border-base-300">
                    <x-form.text_area
                        model="peepo.{{ $key }}.deskripsi"
                        placeholder="Contoh: Siapa yang terlibat, Apa yang terjadi, Dimana, Kapan, Mengapa, dan Bagaimana urutannya."
                        rows="2"
                        required />
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>