<x-app-layout>
  <div class="flex h-screen bg-gray-50">
    @include('admin.partials.sidebar')

    {{-- PASTIKAN x-data DI SINI LENGKAP --}}
    <main class="flex-1 overflow-y-auto p-6 lg:p-8" 
          x-data="{ 
            paymentModalOpen: false, 
            receiptModalOpen: false, 
            receiptImageUrl: '',
            tailorId: null, 
            tailorName: '', 
            paymentAmount: 0, 
            paymentAmountFormatted: '',
            progressIds: '',
            periodStart: '',
            periodEnd: ''
          }">
      
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Penggajian Penjahit</h1>
        <p class="text-gray-500 mt-1">Hitung tagihan upah dan lihat riwayat pembayaran.</p>
      </div>

      @if(session('success'))
        <div role="alert" class="mb-6 rounded-xl border border-gray-100 bg-white p-4 shadow-sm"><div class="flex items-start gap-4"><span class="text-green-600"><x-heroicon-s-check-circle class="h-6 w-6"/></span><div class="flex-1"><strong class="block font-medium text-gray-900">Sukses!</strong><p class="mt-1 text-sm text-gray-700">{{ session('success') }}</p></div></div></div>
      @endif

      {{-- Konten Utama dengan Tab --}}
      <div class="bg-white shadow-md rounded-lg">
        {{-- Navigasi Tab --}}
        <div class="border-b border-gray-200">
          <nav class="-mb-px flex gap-6 px-6">
            <a href="{{ route('admin.payrolls.index', ['tab' => 'unpaid']) }}"
               @class(['shrink-0 border-b-2 px-1 pb-4 text-sm font-medium', 'border-indigo-500 text-indigo-600' => $tab == 'unpaid', 'border-transparent text-gray-500 hover:text-gray-700' => $tab != 'unpaid'])>
              Tagihan Bulan Ini
            </a>
            <a href="{{ route('admin.payrolls.index', ['tab' => 'history']) }}"
               @class(['shrink-0 border-b-2 px-1 pb-4 text-sm font-medium', 'border-indigo-500 text-indigo-600' => $tab == 'history', 'border-transparent text-gray-500 hover:text-gray-700' => $tab != 'history'])>
              Riwayat Pembayaran
            </a>
          </nav>
        </div>
        
        {{-- Konten untuk setiap tab --}}
        <div class="p-6">
          @if ($tab == 'unpaid')
            @include('admin.payrolls.partials.unpaid-tab', ['payrollData' => $payrollData, 'currentMonth' => $currentMonth])
          @else
            @include('admin.payrolls.partials.history-tab', ['payrollHistory' => $payrollHistory])
          @endif
        </div>
      </div>

      <!-- Modal untuk Konfirmasi Pembayaran & Upload Bukti -->
      @include('admin.payrolls.partials.payment-modal')
      
      <!-- Modal untuk menampilkan bukti bayar -->
      @include('admin.payrolls.partials.receipt-modal')
    </main>
  </div>
</x-app-layout>
