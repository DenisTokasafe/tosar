<section class="w-full">
    <x-toast />
    <x-tabs-wpi.layout heading="List Laporan WPI KPLH" subheading="TT-MGT-FRS-024A">
<div class="flex flex-col items-center justify-between gap-4 mb-6 md:flex-row">
        <div>
            <h1 class="text-xl font-bold tracking-wider text-gray-800 uppercase">Daftar Laporan WPI</h1>
            <p class="text-xs italic text-gray-500">Work Permit Inspection - KPLH Site Tokatindung</p>
        </div>

        <div class="flex items-center gap-3">
            <input type="text" wire:model.live.debounce.300ms="search"
                placeholder="Cari Departemen..."
                class="w-64 input input-bordered input-sm focus:ring-info" />

            <a href="/wpi/create" class="text-xs uppercase btn btn-primary btn-sm">
                + Laporan Baru
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="table w-full border border-collapse border-gray-200 table-xs">
            <thead class="bg-gray-800 text-white italic uppercase text-[10px]">
                <tr>
                    <th class="p-3 border border-gray-700">Tanggal</th>
                    <th class="border border-gray-700">Lokasi</th>
                    <th class="border border-gray-700">Departemen / Kontraktor</th>
                    <th class="text-center border border-gray-700">Petugas</th>
                    <th class="text-center border border-gray-700">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @forelse ($reports as $report)
                    <tr class="transition-colors border-b hover:bg-gray-50">
                        <td class="p-3 font-medium">
                            {{ date('d-m-Y', strtotime($report->report_date)) }}
                            <span class="block text-[10px] text-gray-400">{{ $report->report_time }}</span>
                        </td>
                        <td>{{ $report->location }}</td>
                        <td class="font-bold uppercase text-info">{{ $report->department }}</td>
                        <td class="text-center">
                            <span class="font-normal badge badge-ghost badge-sm">{{ count($report->inspectors) }} Orang</span>
                        </td>
                        <td class="flex justify-center gap-2 p-3">
                            {{-- Action Edit: Mengarah ke form dengan ID --}}
                            <a href="/wpi/edit/{{ $report->id }}" class="text-blue-500 btn btn-square btn-ghost btn-xs hover:bg-blue-50" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </a>

                            {{-- Action Delete dengan Konfirmasi --}}
                            <button wire:click="deleteReport({{ $report->id }})"
                                wire:confirm="PERINGATAN: Menghapus laporan ini akan menghapus semua data temuan dan file lampiran selamanya. Lanjutkan?"
                                class="text-red-500 btn btn-square btn-ghost btn-xs hover:bg-red-50" title="Hapus">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-10 italic text-center text-gray-400">Data laporan tidak ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $reports->links() }}
    </div>
    </x-tabs-wpi.layout>
</section>
