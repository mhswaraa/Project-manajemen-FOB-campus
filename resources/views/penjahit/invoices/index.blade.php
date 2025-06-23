<x-app-layout>
    {{-- Kode ini mirip dengan halaman riwayat gaji admin, tetapi disesuaikan untuk penjahit --}}
    <div class="flex h-screen bg-gray-100">
        @include('penjahit.partials.sidebar')
        <main class="flex-1 overflow-y-auto p-6 lg:p-8" x-data="{ receiptModalOpen: false, receiptImageUrl: '' }">
            <div class="mb-8"><h1 class="text-3xl font-bold text-gray-800">Riwayat Invoice</h1><p class="text-gray-500 mt-1">Lacak status semua tagihan yang telah Anda terbitkan.</p></div>
            @if(session('success'))<div role="alert" class="mb-6 rounded-xl border border-gray-100 bg-white p-4 shadow-sm"><div class="flex items-start gap-4"><span class="text-green-600"><x-heroicon-s-check-circle class="h-6 w-6"/></span><div class="flex-1"><strong class="block font-medium text-gray-900">Sukses!</strong><p class="mt-1 text-sm text-gray-700">{{ session('success') }}</p></div></div></div>@endif
            <div class="bg-white shadow-md rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200"><thead class="bg-gray-50"><tr><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Invoice</th><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Terbit</th><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th><th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Bukti Bayar</th></tr></thead><tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($invoices as $invoice)
                    <tr><td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-600">{{ $invoice->invoice_number }}</td><td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $invoice->issue_date->format('d M Y') }}</td><td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-800">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</td><td class="px-6 py-4 whitespace-nowrap"><span @class(['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', 'bg-yellow-100 text-yellow-800' => $invoice->status == 'pending', 'bg-green-100 text-green-800' => $invoice->status == 'paid'])>{{ ucfirst($invoice->status) }}</span></td><td class="px-6 py-4 whitespace-nowrap text-center">@if ($invoice->receipt_path)<button @click="receiptModalOpen = true; receiptImageUrl = '{{ asset('storage/' . $invoice->receipt_path) }}'" class="px-3 py-1 text-xs font-medium text-indigo-700 bg-indigo-100 rounded-full hover:bg-indigo-200">Lihat</button>@else<span class="text-xs text-gray-400 italic">-</span>@endif</td></tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">Anda belum pernah menerbitkan invoice.</td></tr>
                    @endforelse
                </tbody></table>
                <div class="p-4 border-t">{{ $invoices->links() }}</div>
            </div>
            <!-- Modal untuk menampilkan bukti bayar -->
            <div x-show="receiptModalOpen" @keydown.escape.window="receiptModalOpen = false" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;"><div class="flex items-center justify-center min-h-screen px-4 text-center"><div x-show="receiptModalOpen" @click.away="receiptModalOpen = false" x-transition class="fixed inset-0 transition-opacity" aria-hidden="true"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div><div x-show="receiptModalOpen" x-transition class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all my-8 max-w-lg w-full"><div class="bg-white p-4"><div class="flex justify-between items-center mb-4"><h3 class="text-lg font-medium text-gray-900">Bukti Pembayaran</h3><button @click="receiptModalOpen = false" class="text-gray-400 hover:text-gray-500"><x-heroicon-s-x-mark class="h-6 w-6"/></button></div><img :src="receiptImageUrl" alt="Bukti Pembayaran" class="w-full h-auto rounded"></div></div></div></div>
        </main>
    </div>
</x-app-layout>
