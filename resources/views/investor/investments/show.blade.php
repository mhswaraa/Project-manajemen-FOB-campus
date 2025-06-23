<x-app-layout>
  <div class="flex h-screen bg-gray-50">
    @include('investor.partials.sidebar')

    <main class="flex-1 overflow-y-auto p-6 lg:p-8" x-data="{ receiptModalOpen: false, receiptImageUrl: '' }">
      
      {{-- Header --}}
      <div class="mb-6">
        <a href="{{ route('investor.investments.index') }}" class="flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 mb-2">
          <x-heroicon-s-arrow-left class="h-4 w-4" />
          Kembali ke Riwayat Investasi
        </a>
        <h1 class="text-3xl font-bold text-gray-800">Detail Investasi</h1>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Kolom Kiri: Detail Investasi & Finansial --}}
        <div class="lg:col-span-2 space-y-8">
          {{-- Kartu Kuitansi Investasi --}}
          <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex justify-between items-start">
              <div>
                <h2 class="text-xl font-bold text-gray-800">Proyek: {{ $project->name }}</h2>
                <p class="text-sm text-gray-500">ID Investasi: #{{ $investment->id }}</p>
              </div>
              <span @class(['px-3 py-1 text-sm font-semibold rounded-full', 'bg-yellow-100 text-yellow-800' => !$investment->approved, 'bg-green-100 text-green-800' => $investment->approved ])>
                {{ $investment->approved ? 'Disetujui' : 'Pending' }}
              </span>
            </div>
            <div class="mt-6 pt-4 border-t">
              <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                <div><dt class="text-gray-500">Tanggal Pengajuan</dt><dd class="mt-1 font-medium text-gray-900">{{ $investment->created_at->format('d F Y') }}</dd></div>
                <div><dt class="text-gray-500">Jumlah Slot</dt><dd class="mt-1 font-medium text-gray-900">{{ $investment->qty }} pcs</dd></div>
                <div><dt class="text-gray-500">Total Investasi</dt><dd class="mt-1 font-bold text-indigo-600">Rp {{ number_format($investment->amount, 0, ',', '.') }}</dd></div>
                <div><dt class="text-gray-500">Estimasi Profit Anda</dt><dd class="mt-1 font-bold text-green-600">Rp {{ number_format($investment->qty * $project->profit, 0, ',', '.') }}</dd></div>
              </dl>
            </div>
            @if($investment->receipt)
            <div class="mt-6 pt-4 border-t">
              <button @click="receiptModalOpen = true; receiptImageUrl = '{{ asset('storage/' . $investment->receipt) }}'" class="w-full flex items-center justify-center gap-2 text-center px-4 py-2 bg-gray-100 text-gray-800 text-sm font-semibold rounded-lg hover:bg-gray-200">
                <x-heroicon-s-photo class="h-5 w-5"/> Lihat Bukti Pembayaran
              </button>
            </div>
            @endif
          </div>

          {{-- Linimasa Status (Timeline) --}}
          <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Linimasa Investasi</h3>
            <ol class="relative border-l border-gray-200">                  
                <li class="mb-10 ml-6">            
                    <span class="absolute flex items-center justify-center w-6 h-6 bg-green-100 rounded-full -left-3 ring-8 ring-white"><x-heroicon-s-check-circle class="w-4 h-4 text-green-600"/></span>
                    <h3 class="flex items-center mb-1 text-base font-semibold text-gray-900">Investasi Diajukan</h3>
                    <time class="block mb-2 text-sm font-normal leading-none text-gray-400">{{ $investment->created_at->format('d M Y, H:i') }}</time>
                </li>
                <li class="mb-10 ml-6">
                    @if($investment->approved)
                      <span class="absolute flex items-center justify-center w-6 h-6 bg-green-100 rounded-full -left-3 ring-8 ring-white"><x-heroicon-s-check-circle class="w-4 h-4 text-green-600"/></span>
                      <h3 class="mb-1 text-base font-semibold text-gray-900">Disetujui oleh Admin</h3>
                      <time class="block mb-2 text-sm font-normal leading-none text-gray-400">Status proyek kini aktif.</time>
                    @else
                      <span class="absolute flex items-center justify-center w-6 h-6 bg-gray-100 rounded-full -left-3 ring-8 ring-white"><x-heroicon-s-clock class="w-4 h-4 text-gray-500"/></span>
                      <h3 class="mb-1 text-base font-semibold text-gray-500">Menunggu Persetujuan</h3>
                    @endif
                </li>
                <li class="ml-6">
                    @if($productionPercentage >= 100)
                      <span class="absolute flex items-center justify-center w-6 h-6 bg-green-100 rounded-full -left-3 ring-8 ring-white"><x-heroicon-s-check-circle class="w-4 h-4 text-green-600"/></span>
                      <h3 class="mb-1 text-base font-semibold text-gray-900">Proyek Selesai</h3>
                    @else
                      <span class="absolute flex items-center justify-center w-6 h-6 bg-gray-100 rounded-full -left-3 ring-8 ring-white"><x-heroicon-s-cog-6-tooth class="w-4 h-4 text-gray-500"/></span>
                      <h3 class="mb-1 text-base font-semibold text-gray-500">Produksi Berlangsung</h3>
                    @endif
                </li>
            </ol>
          </div>
        </div>
        
        {{-- Kolom Kanan: Progres Proyek Keseluruhan --}}
        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-lg shadow-md sticky top-8">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Progres Proyek Keseluruhan</h3>
              <div class="space-y-6">
                {{-- Progres Pendanaan Proyek --}}
                <div>
                  <div class="flex justify-between text-sm"><span class="font-medium text-gray-600">Pendanaan</span><span class="font-semibold text-blue-600">{{ $fundingPercentage }}%</span></div>
                  <div class="w-full bg-gray-200 rounded-full h-2 mt-1"><div class="bg-blue-600 h-2 rounded-full" style="width: {{ $fundingPercentage }}%"></div></div>
                </div>
                {{-- Progres Produksi Proyek --}}
                <div>
                  <div class="flex justify-between text-sm"><span class="font-medium text-gray-600">Produksi</span><span class="font-semibold text-teal-600">{{ $productionPercentage }}%</span></div>
                  <div class="w-full bg-gray-200 rounded-full h-2 mt-1"><div class="bg-teal-500 h-2 rounded-full" style="width: {{ $productionPercentage }}%"></div></div>
                </div>
              </div>
            </div>
        </div>
      </div>

      <!-- Modal untuk menampilkan bukti bayar -->
      <div x-show="receiptModalOpen" @keydown.escape.window="receiptModalOpen = false" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;"><div class="flex items-center justify-center min-h-screen px-4 text-center"><div x-show="receiptModalOpen" @click.away="receiptModalOpen = false" x-transition class="fixed inset-0 transition-opacity" aria-hidden="true"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div><div x-show="receiptModalOpen" x-transition class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all my-8 max-w-lg w-full"><div class="bg-white p-4"><div class="flex justify-between items-center mb-4"><h3 class="text-lg font-medium text-gray-900">Bukti Pembayaran</h3><button @click="receiptModalOpen = false" class="text-gray-400 hover:text-gray-500"><x-heroicon-s-x-mark class="h-6 w-6"/></button></div><img :src="receiptImageUrl" alt="Bukti Pembayaran" class="w-full h-auto rounded"></div></div></div></div>
    </main>
  </div>
</x-app-layout>
