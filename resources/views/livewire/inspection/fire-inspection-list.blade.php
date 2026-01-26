<section class="w-full">
    <x-toast />

    <x-tabs-wpi.layout heading="Daftar Laporan Fire Protection" subheading="Site Tokatindung">

        <div class="overflow-x-auto">
            <table class="table w-full border table-xs">
                <thead>
                    <tr class="bg-gray-100">
                        <th>No</th>
                        <th>Jenis Alat</th>
                        <th>Lokasi & Area</th>
                        <th>Data Teknis & Kondisi</th>
                        <th>Pemeriksa</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($inspections as $index => $item)
                        <tr wire:key="row-{{ $item->id }}">
                            <td>{{ $inspections->firstItem() + $index }}</td>
                            <td>
                                <span class="badge badge-soft badge-info">{{ $item->type }}</span>
                            </td>
                            <td>
                                <div class="font-bold">{{ $item->location }}</div>
                                <div class="text-[10px] opacity-60">{{ $item->area }}</div>
                            </td>
                            <td>
                                <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-[10px]">
                                    @foreach ($item->conditions as $key => $value)
                                        <div class="flex justify-between py-1 border-b border-dotted">
                                            <span class="font-medium uppercase text-[10px]">{{ $key }}:</span>

                                            {{-- Hapus tanda petik karena di JSON datanya boolean murni --}}
                                            @if ($value === true)
                                                <span class="text-success text-[10px] font-bold">✔ Good</span>
                                            @elseif($value === false)
                                                <span class="font-bold text-error text-[10px]">✘ No Good</span>
                                            @else
                                                {{-- Ini untuk data seperti "01" atau "6.8 Kg" --}}
                                                <span class="text-blue-600 text-[10px]">{{ $value }}</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                {{-- Menampilkan pemeriksa yang digabung dengan '|' --}}
                                @php $pemeriksa = explode('|', $item->inspected_by); @endphp
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($pemeriksa as $nama)
                                        <span class="badge badge-ghost badge-xs">{{ $nama }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($item->inspection_date)->format('d/m/Y') }}</td>
                            <td>
                                <div class="flex gap-2">
                                    @if ($item->documentation_path)
                                        <a href="{{ Storage::url($item->documentation_path) }}" target="_blank"
                                            class="btn btn-ghost btn-xs text-info">Doc</a>
                                    @endif
                                    <button wire:click="edit({{ $item->id }})"
                                        class="btn btn-ghost btn-xs">Edit</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">
                {{ $inspections->links() }}
            </div>
        </div>
    </x-tabs-wpi.layout>
</section>
