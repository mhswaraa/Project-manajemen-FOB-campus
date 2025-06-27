<div class="bg-white shadow-md rounded-lg overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Investor</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proyek</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah Profit</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Bayar</th>
                <th class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($payoutHistory as $payout)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $payout->investment->investor->user->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payout->investment->project->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">Rp {{ number_format($payout->amount, 0, ',', '.') }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payout->payment_date->isoFormat('D MMMM YYYY') }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    {{-- FIX: Mengubah tombol modal menjadi link ke halaman detail --}}
                    <a href="{{ route('admin.payouts.show', $payout) }}" class="text-indigo-600 hover:text-indigo-900">
                        Detail
                    </a>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">Belum ada riwayat pembayaran profit.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $payoutHistory->links() }}
</div>
