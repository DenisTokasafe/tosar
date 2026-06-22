<div class="p-6 bg-white rounded-lg shadow">
    <h2 class="text-xl font-bold mb-4">Daftar Hasil MCU (Pending & Reviewed)</h2>

    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-gray-100 uppercase tracking-wider">
                <tr>
                    <th class="px-4 py-3">Nama Karyawan</th>
                    <th class="px-4 py-3">Jadwal MCU</th>
                    <th class="px-4 py-3">Status Kebugaran</th>
                    <th class="px-4 py-3">Status Dokumen</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($mcuResults as $result)
                <tr>
                    <td class="px-4 py-3 font-medium text-gray-900">
                        {{ $result->participant->employee->name ?? 'Tidak diketahui' }}
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        {{ $result->participant->schedule->schedule_date->format('d M Y') ?? '-' }}
                    </td>
                    <td class="px-4 py-3">
                        @if($result->status)
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs uppercase">
                            {{ str_replace('_', ' ', $result->status) }}
                        </span>
                        @else
                        <span class="text-gray-400 italic">Belum direview</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($result->workflow_status === 'reviewed')
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs uppercase">
                            Selesai Direview
                        </span>
                        @else
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs uppercase">
                            Menunggu Dokter
                        </span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <button wire:click="openReviewModal({{ $result->id }})" class="text-indigo-600 hover:text-indigo-900 font-semibold">
                            Lihat Detail
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                        Tidak ada data MCU dengan status tersebut.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $mcuResults->links() }}
    </div>
</div>