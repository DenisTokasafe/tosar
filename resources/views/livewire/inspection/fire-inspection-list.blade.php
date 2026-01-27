<section class="w-full">
    <x-toast />
    <!-- Open the modal using ID.showModal() method -->
    <button class="btn btn-square btn-xs btn-soft btn-accent" onclick="my_modal_2.showModal()">
        <svg
            xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
    </button>
    <dialog id="my_modal_2" class="modal">
        <div class="modal-box">
            <h3 class="text-lg font-bold">Hello!</h3>
            <p class="py-4">Press ESC key or click outside to close</p>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
    <x-tabs-wpi.layout heading="Daftar Laporan Fire Protection" subheading="Site Tokatindung">

        <div class="overflow-x-auto">
            <table class="table table-xs table-zebra">
                <thead>
                    <tr class="text-center bg-gray-100">
                        <th>No</th>
                        <th>Jenis Alat</th>
                        <th>Area & Lokasi spesifik</th>
                        <th>Data Teknis & Kondisi</th>
                        <th>Pemeriksa</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($inspections as $index => $item)
                        <tr wire:key="row-{{ $item->id }}" class=" odd:bg-white even:bg-gray-100">
                            <td class="text-center">{{ $inspections->firstItem() + $index }}</td>
                            <td class="text-center">
                                <span class="w-32 font-semibold badge badge-soft badge-info"><span
                                        class="text-xs">{{ $item->type }}</span></span>
                            </td>
                            <td class="text-center">
                                <div class="text-[10px] opacity-60">{{ $item->area }}</div>
                                <div class="font-bold">{{ $item->location }}</div>
                            </td>
                            <td>
                                <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-[10px]">
                                    @foreach ($item->conditions as $key => $value)
                                        <div class="flex justify-between py-1 border-b border-dotted">
                                            <span class="font-medium uppercase text-[10px]">{{ $key }}:</span>

                                            {{-- Hapus tanda petik karena di JSON datanya boolean murni --}}
                                            @if ($value === 'yes' || $value === true)
                                                <span class="text-success text-[10px] font-bold">✔</span>
                                            @elseif($value === false)
                                                <span class="font-bold text-error text-[10px]">✘</span>
                                            @else
                                                {{-- Ini untuk data seperti "01" atau "6.8 Kg" --}}
                                                <span
                                                    class="text-blue-600 font-semibold text-[10px]">{{ $value }}</span>
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
                            <td class="text-center">{{ \Carbon\Carbon::parse($item->inspection_date)->format('d/m/Y') }}
                            </td>
                            <td class="text-center">
                                <div class="flex gap-2">
                                    @if ($item->documentation_path)
                                        <a href="{{ Storage::url($item->documentation_path) }}" target="_blank"
                                            class="btn btn-ghost btn-xs text-info">Doc</a>
                                    @endif
                                    <button wire:click="edit({{ $item->id }})"
                                        class="btn btn-ghost btn-xs">Edit</button>

                                    <button wire:click="exportPDF({{ $item->id }})" wire:loading.attr="disabled"
                                        class="flex items-center gap-2 text-white btn btn-error btn-sm">

                                        {{-- Icon PDF --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>

                                        <span wire:loading.remove wire:target="exportPDF">Export to PDF</span>
                                        <span wire:loading wire:target="exportPDF">Generating PDF...</span>
                                    </button>
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
