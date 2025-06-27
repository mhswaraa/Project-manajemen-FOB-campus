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
        
        <div class="p-4">
            @if ($tab == 'unpaid')
                {{-- PERBAIKAN: Menggunakan struktur tabel baru --}}
                @include('admin.payouts.partials.unpaid-tab', ['payoutsReady' => $payoutsReady])
            @else
                {{-- Tabel untuk Riwayat Pembayaran --}}
                @include('admin.payouts.partials.history-tab', ['payoutHistory' => $payoutHistory])
            @endif
        </div>
      </div>

      <!-- Modal Pembayaran dan Bukti akan di-include dari dalam partials -->
    </main>
  </div>
</x-app-layout>
