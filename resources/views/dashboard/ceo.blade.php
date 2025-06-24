<x-app-layout>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>

  <div class="flex h-screen bg-gray-100">
    @include('ceo.partials.sidebar')

    <main class="flex-1 overflow-y-auto p-6 lg:p-8">
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Ringkasan Eksekutif</h1>
        <p class="text-gray-500 mt-1">Gambaran umum kinerja bisnis hingga hari ini, {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}.</p>
      </div>

      {{-- 1. Financial Snapshot --}}
      <h2 class="text-xl font-semibold text-gray-700 mb-4">Ringkasan Finansial</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <p class="text-sm font-medium text-gray-500">Total Dana Terkumpul</p>
            <p class="text-3xl font-bold text-blue-600 mt-1">Rp {{ number_format($totalFundsRaised, 0, ',', '.') }}</p>
        </div>
         <div class="bg-white p-6 rounded-lg shadow-md">
            <p class="text-sm font-medium text-gray-500">Total Profit Dibayarkan</p>
            <p class="text-3xl font-bold text-green-600 mt-1">Rp {{ number_format($totalProfitPaidOut, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <p class="text-sm font-medium text-gray-500">Perkiraan Arus Kas Bersih</p>
            <p class="text-3xl font-bold mt-1 {{ $netCashFlow >= 0 ? 'text-gray-800' : 'text-red-600' }}">Rp {{ number_format($netCashFlow, 0, ',', '.') }}</p>
        </div>
      </div>

      {{-- 2. Grafik Tren & Analisis --}}
       <h2 class="text-xl font-semibold text-gray-700 mb-4">Analisis Bisnis</h2>
      <div class="grid grid-cols-1 xl:grid-cols-5 gap-8 mb-8">
        <div class="xl:col-span-3 bg-white p-6 rounded-lg shadow-md">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Tren Pendanaan vs. Pembayaran Profit (6 Bulan Terakhir)</h3>
          <div class="h-80"><canvas id="financialTrendChart"></canvas></div>
        </div>

        <div class="xl:col-span-2 space-y-8">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Ringkasan Portofolio</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-500">Proyek Aktif</dt><dd class="font-semibold text-gray-900">{{ $activeProjects }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Proyek Selesai</dt><dd class="font-semibold text-gray-900">{{ $completedProjects }}</dd></div>
                    <div class="flex justify-between pt-2 border-t"><dt class="text-gray-500">Total Investor</dt><dd class="font-semibold text-gray-900">{{ $totalInvestors }}</dd></div>
                </dl>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Peluang & Proyek Hampir Penuh</h3>
                <div class="space-y-3">
                    @forelse ($nearlyFundedProjects as $project)
                        <div>
                           <p class="text-sm font-medium text-gray-800">{{ $project->name }}</p>
                           <p class="text-xs text-gray-500">Pendanaan: {{ number_format(($project->funded_qty / $project->quantity) * 100, 0) }}% tercapai</p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">Tidak ada proyek yang mendekati target pendanaan.</p>
                    @endforelse
                </div>
            </div>
        </div>
      </div>
    </main>
  </div>
  
  <script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('financialTrendChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($monthlyData['labels']) !!},
                    datasets: [
                        { label: 'Dana Masuk', data: {!! json_encode($monthlyData['funds']) !!}, backgroundColor: 'rgba(59, 130, 246, 0.5)', borderColor: 'rgba(59, 130, 246, 1)', borderWidth: 1 },
                        { label: 'Profit Dibayar', data: {!! json_encode($monthlyData['payouts']) !!}, backgroundColor: 'rgba(16, 185, 129, 0.5)', borderColor: 'rgba(16, 185, 129, 1)', borderWidth: 1 }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
            });
        }
    });
  </script>
</x-app-layout>
