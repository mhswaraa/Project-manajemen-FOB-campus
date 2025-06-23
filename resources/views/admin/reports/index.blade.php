<x-app-layout>
  <div class="flex h-screen bg-gray-50">
    @include('admin.partials.sidebar')

    <main class="flex-1 overflow-y-auto p-6 lg:p-8">
      
      {{-- Header --}}
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Laporan & Analitik</h1>
        <p class="text-gray-500 mt-1">Lihat ringkasan kinerja keuangan dan produksi bisnis Anda.</p>
      </div>

      {{-- Filter Tanggal --}}
      <div class="mb-8 p-4 bg-white rounded-lg shadow-sm">
        <form action="{{ route('admin.reports.index') }}" method="GET">
          <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 items-end gap-4">
            <div>
              <label for="start_date" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
              <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
              <label for="end_date" class="block text-sm font-medium text-gray-700">Tanggal Akhir</label>
              <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="flex gap-2">
              <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Terapkan Filter
              </button>
              <a href="{{ route('admin.reports.index') }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                Reset
              </a>
            </div>
          </div>
        </form>
      </div>
      
      {{-- Laporan Keuangan --}}
      <h2 class="text-xl font-semibold text-gray-700 mb-4">Laporan Keuangan <span class="text-base font-normal text-gray-500">({{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }})</span></h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <p class="text-sm font-medium text-gray-500">Total Dana Masuk</p>
            <p class="text-3xl font-bold text-blue-600 mt-1">Rp {{ number_format($totalFunds, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <p class="text-sm font-medium text-gray-500">Total Upah Dibayarkan</p>
            <p class="text-3xl font-bold text-orange-600 mt-1">Rp {{ number_format($totalWagesPaid, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <p class="text-sm font-medium text-gray-500">Total Realisasi Profit</p>
            <p class="text-3xl font-bold text-green-600 mt-1">Rp {{ number_format($totalProfit, 0, ',', '.') }}</p>
        </div>
      </div>
      
      {{-- Laporan Kinerja --}}
      <h2 class="text-xl font-semibold text-gray-700 mb-4">Laporan Kinerja</h2>
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Kinerja Proyek --}}
        <div class="bg-white p-6 rounded-lg shadow-md">
           <h3 class="text-lg font-medium text-gray-900 mb-4">5 Proyek Paling Profitabel</h3>
           <div class="space-y-4">
             @forelse ($topProjects as $project)
               <div class="flex items-center">
                  <div class="flex-shrink-0 h-10 w-10 bg-gray-100 rounded-lg flex items-center justify-center"><x-heroicon-o-briefcase class="h-6 w-6 text-gray-500" /></div>
                  <div class="ml-4 flex-grow">
                      <p class="text-sm font-medium text-gray-900">{{ $project->name }}</p>
                      <p class="text-sm text-gray-500">{{ $project->realized_qty ?? 0 }} pcs didanai</p>
                  </div>
                  <div class="text-sm font-bold text-green-600">Rp {{ number_format($project->realized_profit, 0, ',', '.') }}</div>
               </div>
             @empty
                <p class="text-sm text-gray-500 text-center py-4">Tidak ada data proyek pada rentang tanggal ini.</p>
             @endforelse
           </div>
        </div>
        
        {{-- Kinerja Penjahit --}}
        <div class="bg-white p-6 rounded-lg shadow-md">
           <h3 class="text-lg font-medium text-gray-900 mb-4">5 Penjahit Paling Produktif</h3>
            <div class="space-y-4">
             @forelse ($topTailors as $tailor)
               <div class="flex items-center">
                  <img class="h-10 w-10 rounded-full object-cover" src="https://ui-avatars.com/api/?name={{ urlencode($tailor->user->name) }}&background=E0F2F1&color=00796B" alt="">
                  <div class="ml-4 flex-grow">
                      <p class="text-sm font-medium text-gray-900">{{ $tailor->user->name }}</p>
                      <p class="text-sm text-gray-500">{{ $tailor->specializations->pluck('name')->join(', ') ?: 'Belum ada keahlian' }}</p>
                  </div>
                  <div class="text-sm font-bold text-indigo-600">{{ $tailor->total_done ?? 0 }} pcs</div>
               </div>
             @empty
                <p class="text-sm text-gray-500 text-center py-4">Tidak ada data progres penjahit pada rentang tanggal ini.</p>
             @endforelse
           </div>
        </div>
      </div>
    </main>
  </div>
</x-app-layout>
