<div class="overflow-x-auto border border-gray-200 rounded-lg">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Investor
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Proyek
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Jumlah Investasi
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Profit Dibayarkan
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Tanggal Bayar
                </th>
                <th scope="col" class="relative px-6 py-3">
                    <span class="sr-only">Aksi</span>
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            {{-- Loop melalui variabel $payoutHistory yang sekarang berisi koleksi Payout --}}
            @forelse ($payoutHistory as $payout)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{-- Karena kita sudah eager load, akses ini sekarang aman --}}
                        {{ $payout->investment->investor->user->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $payout->investment->project->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        Rp {{ number_format($payout->investment->amount, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-semibold">
                        {{-- PERBAIKAN: Menggunakan nama kolom 'amount' yang benar --}}
                        Rp {{ number_format($payout->amount, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{-- PERBAIKAN: Menggunakan 'paid_at' dan properti Carbon langsung --}}
                        {{ $payout->paid_at->isoFormat('D MMMM YYYY') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                        <a href="{{ route('admin.payouts.show', $payout->id) }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Detail
                        </a>
                        <button 
                            type="button"
                            @click="$dispatch('open-modal', { name: 'receipt-modal', receiptUrl: '{{ Storage::url($payout->receipt_path) }}' })"
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-cyan-700 bg-cyan-100 hover:bg-cyan-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500">
                            Lihat Bukti
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        Belum ada riwayat pembayaran yang tercatat.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    {{-- Menampilkan link paginasi --}}
    <div class="p-4 border-t">
        {{ $payoutHistory->links() }}
    </div>
</div>

@include('admin.payouts.partials.receipt-modal')