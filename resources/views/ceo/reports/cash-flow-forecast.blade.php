<x-app-layout>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <div class="flex h-screen bg-gray-100">
    @include('ceo.partials.sidebar')

    <main class="flex-1 overflow-y-auto p-6 lg:p-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Peramalan Arus Kas</h1>
            <p class="text-gray-500 mt-1">Memproyeksikan arus kas masuk dan keluar untuk perencanaan strategis.</p>
        </div>

        @php
            // Hitung total untuk KPI Cards (3 bulan ke depan)
            $totalProjectedIn = $forecastData->take(3)->sum('funds_in');
            $totalProjectedOut = $forecastData->take(3)->sum(fn($data) => $data['wage_out'] + $data['profit_out']);
            $netProjectedFlow = $totalProjectedIn - $totalProjectedOut;
        @endphp

        {{-- Kartu KPI Peramalan --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow-md"><p class="text-sm font-medium text-gray-500">Proyeksi Dana Masuk (3 Bln)</p><p class="text-3xl font-bold text-blue-600 mt-1">Rp {{ number_format($totalProjectedIn, 0,',','.') }}</p></div>
            <div class="bg-white p-6 rounded-lg shadow-md"><p class="text-sm font-medium text-gray-500">Proyeksi Dana Keluar (3 Bln)</p><p class="text-3xl font-bold text-red-600 mt-1">Rp {{ number_format($totalProjectedOut, 0,',','.') }}</p></div>
            <div class="bg-white p-6 rounded-lg shadow-md"><p class="text-sm font-medium text-gray-500">Proyeksi Arus Kas Bersih (3 Bln)</p><p class="text-3xl font-bold mt-1 {{ $netProjectedFlow >= 0 ? 'text-gray-800' : 'text-red-600' }}">Rp {{ number_format($netProjectedFlow, 0,',','.') }}</p></div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-5 gap-8">
            {{-- Grafik Peramalan --}}
            <div class="xl:col-span-3 bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Grafik Proyeksi Arus Kas (6 Bulan ke Depan)</h3>
                @if($forecastData->isNotEmpty())
                    <div class="h-96">
                        <canvas id="cashFlowChart"></canvas>
                    </div>
                @else
                    <div class="text-center py-16 text-gray-500">Tidak ada data untuk peramalan.</div>
                @endif
            </div>

            {{-- Tabel Rincian --}}
            <div class="xl:col-span-2 bg-white shadow-md rounded-lg">
                <div class="p-4 border-b"><h3 class="font-semibold text-gray-700">Rincian Proyeksi per Bulan</h3></div>
                <div class="max-h-[28rem] overflow-y-auto">
                    @forelse ($forecastData as $data)
                        @php
                            $cashOut = $data['wage_out'] + $data['profit_out'];
                            $netFlow = $data['funds_in'] - $cashOut;
                        @endphp
                        <div class="p-4 border-b">
                            <h4 class="font-bold text-gray-800">{{ $data['month_name'] }}</h4>
                            <dl class="mt-2 space-y-1 text-sm">
                                <div class="flex justify-between"><dt class="text-gray-500">Dana Masuk</dt><dd class="font-medium text-blue-600">+ Rp {{ number_format($data['funds_in'], 0,',','.') }}</dd></div>
                                <div class="flex justify-between"><dt class="text-gray-500">Biaya Upah</dt><dd class="font-medium text-orange-600">- Rp {{ number_format($data['wage_out'], 0,',','.') }}</dd></div>
                                <div class="flex justify-between"><dt class="text-gray-500">Pembayaran Profit</dt><dd class="font-medium text-red-600">- Rp {{ number_format($data['profit_out'], 0,',','.') }}</dd></div>
                                <div class="flex justify-between pt-2 border-t font-bold"><dt class="text-gray-800">Arus Kas Bersih</dt><dd class="{{ $netFlow >= 0 ? 'text-gray-900' : 'text-red-600' }}">Rp {{ number_format($netFlow, 0,',','.') }}</dd></div>
                            </dl>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500">Tidak ada data untuk ditampilkan.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </main>
  </div>
  
  <script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('cashFlowChart');
        @if($forecastData->isNotEmpty())
        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($forecastData->pluck('month_name')) !!},
                    datasets: [
                        {
                            label: 'Dana Masuk (Rp)',
                            data: {!! json_encode($forecastData->pluck('funds_in')) !!},
                            backgroundColor: 'rgba(59, 130, 246, 0.6)', // Blue
                            borderColor: 'rgba(59, 130, 246, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Dana Keluar (Rp)',
                            data: {!! json_encode($forecastData->map(fn($d) => $d['wage_out'] + $d['profit_out'])) !!},
                            backgroundColor: 'rgba(239, 68, 68, 0.6)', // Red
                            borderColor: 'rgba(239, 68, 68, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'top' } },
                    scales: { 
                        x: { stacked: false },
                        y: { stacked: false, beginAtZero: true, ticks: { callback: value => 'Rp ' + new Intl.NumberFormat('id-ID').format(value) } } 
                    }
                }
            });
        }
        @endif
    });
  </script>
</x-app-layout>
