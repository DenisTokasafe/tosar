<div class="mt-4 overflow-x-auto">
    <table class="table table-xs w-full border border-collapse border-base-300">
        <thead>
            <tr class="text-center bg-gray-200">
                <th class="w-1/6 px-4 py-2 font-bold border border-base-300 text-base-content">Factors</th>
                <th class="w-2/5 px-4 py-2 font-bold border border-base-300 text-base-content">Temuan</th>
                <th class="w-2/5 px-4 py-2 font-bold border border-base-300 text-base-content">Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            {{-- Menggunakan properti dari controller --}}
            @foreach($peepoFactors as $key => $label)
            <tr>
                <td class="px-4 font-bold align-middle bg-gray-100 border border-base-300 text-base-content">
                    {{ $label }}
                </td>
                <td class="p-2 border border-base-300">
                    <x-form.text_area
                        wire:model="peepo.{{ $key }}.temuan"
                        placeholder="Masukkan temuan untuk faktor {{ $label }}..."
                        rows="3" />
                </td>
                <td class="p-2 border border-base-300">
                    <x-form.text_area
                        wire:model="peepo.{{ $key }}.deskripsi"
                        placeholder="Contoh: Siapa yang terlibat, Apa yang terjadi, Dimana, Kapan, Mengapa, dan Bagaimana urutannya."
                        rows="3"
                        required />
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>