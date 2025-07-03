{{-- Tabel untuk Laporan yang Menunggu Pemeriksaan --}}
<div class="mb-10">
    <h2 class="text-xl font-semibold text-gray-700 mb-4">Menunggu Pemeriksaan</h2>
    <div class="bg-white rounded-lg shadow-md overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proyek & Penjahit</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl Lapor</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Qty Lapor</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($pendingProgress as $report)
                    <tr>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900">{{ $report->assignment?->project?->name ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500">{{ $report->assignment?->tailor?->user?->name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">{{ $report->date?->isoFormat('D MMM YY') }}</td>
                        <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-bold text-blue-600">{{ $report->quantity_done }}</td>
                        <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.qc.show', $report->id) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 text-xs">
                                Proses
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-gray-500">Tidak ada laporan yang perlu diperiksa saat ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
