<x-app-layout>
  <div class="flex h-screen bg-gray-50">
    @include('admin.partials.sidebar')

    <main class="flex-1 overflow-y-auto p-6 lg:p-8" 
          x-data="{ 
            paymentModalOpen: false,
            receiptModalOpen: false, 
            receiptImageUrl: '',
            selectedInvestment: null 
          }">
      
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Pembayaran Profit Investor</h1>
        <p class="text-gray-500 mt-1">Kelola pembayaran keuntungan untuk investasi pada proyek yang telah selesai.</p>
      </div>

      @if(session('success'))<div role="alert" class="mb-6 rounded-xl border bg-white p-4 shadow-sm"><div class="flex items-start gap-4"><span class="text-green-600"><x-heroicon-s-check-circle class="h-6 w-6"/></span><div class="flex-1"><strong class="block font-medium text-gray-900">Sukses!</strong><p class="mt-1 text-sm text-gray-700">{{ session('success') }}</p></div></div></div>@endif
      @if(session('error'))<div role="alert" class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 shadow-sm"><div class="flex items-start gap-4"><span class="text-red-600"><x-heroicon-s-x-circle class="h-6 w-6"/></span><div class="flex-1"><strong class="block font-medium text-red-900">Gagal!</strong><p class="mt-1 text-sm text-red-700">{{ session('error') }}</p></div></div></div>@endif

      {{-- Konten Utama dengan Tab --}}
      <div class="bg-white shadow-md rounded-lg">
        {{-- Navigasi Tab --}}
        <div class="border-b border-gray-200">
          <nav class="-mb-px flex gap-6 px-6">
            <a href="{{ route('admin.payouts.index', ['tab' => 'unpaid']) }}"
               @class(['shrink-0 border-b-2 px-1 pb-4 text-sm font-medium', 'border-indigo-500 text-indigo-600' => $tab == 'unpaid', 'border-transparent text-gray-500 hover:text-gray-700' => $tab != 'unpaid'])>
              Siap Dibayar
            </a>
            <a href="{{ route('admin.payouts.index', ['tab' => 'history']) }}"
               @class(['shrink-0 border-b-2 px-1 pb-4 text-sm font-medium', 'border-indigo-500 text-indigo-600' => $tab == 'history', 'border-transparent text-gray-500 hover:text-gray-700' => $tab != 'history'])>
              Riwayat Pembayaran
            </a>
          </nav>
        </div>
        
        <div class="overflow-x-auto">
            @if ($tab == 'unpaid')
                {{-- Tabel untuk Pembayaran yang Siap Diproses --}}
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50"><tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Investor</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proyek Selesai</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profit Dibayarkan</th><th class="relative px-6 py-3"><span class="sr-only">Aksi</span></th></tr></thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($payoutsReady as $investment)
                      @php $profit = $investment->qty * $investment->project->profit; @endphp
                      <tr>
                        <td class="px-6 py-4 whitespace-nowrap"><div class="flex items-center"><div class="flex-shrink-0 h-10 w-10"><img class="h-10 w-10 rounded-full object-cover" src="https://ui-avatars.com/api/?name={{ urlencode($investment->investor->user->name) }}&background=E8EAF6&color=3F51B5" alt=""></div><div class="ml-4"><div class="text-sm font-medium text-gray-900">{{ $investment->investor->user->name }}</div><div class="text-sm text-gray-500">{{ $investment->investor->user->email }}</div></div></div></td>
                        <td class="px-6 py-4 whitespace-nowrap"><div class="text-sm font-medium text-gray-900">{{ $investment->project->name }}</div><div class="text-sm text-gray-500">Investasi {{ $investment->qty }} slot</div></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600">Rp {{ number_format($profit, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"><button type="button" @click="paymentModalOpen = true; selectedInvestment = {{ json_encode($investment->load('investor.user', 'project')) }};" class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 shadow-sm text-xs">Bayar & Catat</button></td>
                      </tr>
                    @empty
                      <tr><td colspan="4" class="px-6 py-12 text-center text-gray-500">Tidak ada pembayaran profit yang siap diproses saat ini.</td></tr>
                    @endforelse
                  </tbody>
                </table>
            @else
                {{-- Tabel untuk Riwayat Pembayaran --}}
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50"><tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Investor</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detail Pembayaran</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diproses Oleh</th><th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Bukti</th></tr></thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($payoutHistory as $payout)
                    <tr>
                      <td class="px-6 py-4 whitespace-nowrap"><div class="flex items-center"><div class="flex-shrink-0 h-10 w-10"><img class="h-10 w-10 rounded-full object-cover" src="https://ui-avatars.com/api/?name={{ urlencode($payout->investment->investor->user->name) }}&background=E8EAF6&color=3F51B5" alt=""></div><div class="ml-4"><div class="text-sm font-medium text-gray-900">{{ $payout->investment->investor->user->name }}</div></div></div></td>
                      <td class="px-6 py-4 whitespace-nowrap"><div class="text-sm font-bold text-gray-900">Rp {{ number_format($payout->profit_amount, 0, ',', '.') }}</div><div class="text-sm text-gray-500">Proyek: {{ $payout->investment->project->name }}</div><div class="text-sm text-gray-500">Tgl Bayar: {{ $payout->payment_date->format('d M Y') }}</div></td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payout->processor->name }}</td>
                      <td class="px-6 py-4 whitespace-nowrap text-center">@if ($payout->receipt_path)<button @click="receiptModalOpen = true; receiptImageUrl = '{{ asset('storage/' . $payout->receipt_path) }}'" class="px-3 py-1 text-xs font-medium text-indigo-700 bg-indigo-100 rounded-full hover:bg-indigo-200">Lihat</button>@else<span class="text-xs text-gray-400 italic">-</span>@endif</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-6 py-12 text-center text-gray-500">Belum ada riwayat pembayaran yang tercatat.</td></tr>
                    @endforelse
                  </tbody>
                </table>
                <div class="p-4 border-t">{{ $payoutHistory->links() }}</div>
            @endif
        </div>
      </div>

      <!-- Modal Pembayaran Profit (sama seperti sebelumnya) -->
      @include('admin.payouts.partials.payment-modal')
      
      <!-- Modal Lihat Bukti (diletakkan di sini untuk digunakan kedua tab) -->
      @include('admin.payouts.partials.receipt-modal')
    </main>
  </div>
</x-app-layout>
