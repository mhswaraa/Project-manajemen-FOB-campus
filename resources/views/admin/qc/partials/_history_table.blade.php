{{-- Tabel untuk Riwayat Pemeriksaan --}}
<div>
    <h2 class="text-xl font-semibold text-gray-700 mb-4">Riwayat Pemeriksaan</h2>
    <div class="bg-white rounded-lg shadow-md overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proyek & Penjahit</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Qty Diterima</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Qty Ditolak</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan & Pemeriksa</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($approvedProgress as $report)
                    <tr>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900">{{ $report->assignment?->project?->name ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500">{{ $report->assignment?->tailor?->user?->name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-bold text-green-600">{{ $report->accepted_qty ?? '-' }}</td>
                        <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-bold text-red-600">{{ $report->rejected_qty ?? '-' }}</td>
                        <td class="px-4 py-4 whitespace-normal text-sm text-gray-800">
                            @if($report->qc_notes)
                                <p class="text-xs text-gray-600 italic break-words">"{{ $report->qc_notes }}"</p>
                            @else
                                <p class="text-xs text-gray-400 italic">- Tidak ada catatan -</p>
                            @endif
                            @if($report->qcAdmin)
                                <p class="text-xs text-gray-400 mt-1">
                                    Oleh: {{ $report->qcAdmin->name }} pada {{ $report->qc_checked_at?->format('d/m/y H:i')}}
                                </p>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-gray-500">Belum ada riwayat pemeriksaan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{-- Paginasi untuk riwayat --}}
    <div class="mt-4">
        {{ $approvedProgress->links() }}
    </div>
</div>
