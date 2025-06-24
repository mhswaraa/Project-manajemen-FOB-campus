<x-app-layout>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <div class="flex h-screen bg-gray-100">
    @include('ceo.partials.sidebar')

    <main class="flex-1 overflow-y-auto p-6 lg:p-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Analisis Kohort Investor</h1>
            <p class="text-gray-500 mt-1">Menganalisis retensi dan nilai seumur hidup investor berdasarkan bulan akuisisi.</p>
        </div>
        
        {{-- Kartu KPI Utama --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <p class="text-sm font-medium text-gray-500">Rata-rata Retensi Bulan Pertama</p>
                <p class="text-3xl font-bold text-teal-600 mt-1">{{ number_format($averageRetentionMonth1, 1) }}%</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <p class="text-sm font-medium text-gray-500">Rata-rata Investor LTV</p>
                <p class="text-3xl font-bold text-indigo-600 mt-1">Rp {{ number_format($overallLtv, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            {{-- Tabel Retensi --}}
            <div class="xl:col-span-2 bg-white shadow-md rounded-lg overflow-x-auto">
                <div class="p-4 border-b"><h3 class="font-semibold text-gray-700">Tabel Retensi Investor (%)</h3></div>
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kohort Akuisisi</th><th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Investor Baru</th>@for ($i = 0; $i < $maxMonths; $i++)<th class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Bulan ke-{{$i}}</th>@endfor</tr></thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($retentionTable as $data)
                            <tr>
                                <td class="px-4 py-4 whitespace-nowrap font-medium text-gray-900">{{ $data['cohort_date'] }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-gray-800 font-bold">{{ $data['cohort_size'] }}</td>
                                @for ($i = 0; $i < $maxMonths; $i++)
                                    <td class="p-2 whitespace-nowrap text-gray-700 text-center">
                                        @php
                                            $count = $data['retention_counts'][$i] ?? 0;
                                            $percentage = $data['cohort_size'] > 0 ? ($count / $data['cohort_size']) * 100 : 0;
                                            $opacity = ($percentage > 0) ? ($percentage / 100) * 0.8 + 0.2 : 0.1; // Opacity dinamis
                                            $bgColor = 'bg-teal-600';
                                            if ($i == 0) $bgColor = 'bg-blue-600';
                                        @endphp
                                        <div class="p-2 rounded-md text-white font-bold {{ $bgColor }}" style="opacity: {{ $opacity }};">
                                            {{ number_format($percentage, 0) }}%
                                            <span class="text-xs block opacity-75">({{ $count }})</span>
                                        </div>
                                    </td>
                                @endfor
                            </tr>
                        @empty
                            <tr><td colspan="{{ $maxMonths + 2 }}" class="text-center py-8 text-gray-500">Data tidak cukup untuk menampilkan analisis kohort.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Grafik LTV --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                <h3 class="font-semibold text-gray-700 mb-4">Investor LTV (per Kohort)</h3>
                <div class="h-96">
                    <canvas id="ltvChart"></canvas>
                </div>
            </div>
        </div>
    </main>
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('ltvChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($ltvData->keys()) !!},
                    datasets: [{
                        label: 'Rata-rata LTV (Rp)',
                        data: {!! json_encode($ltvData->values()) !!},
                        backgroundColor: 'rgba(79, 70, 229, 0.6)',
                        borderColor: 'rgba(79, 70, 229, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y', // Membuat bar chart menjadi horizontal
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { x: { beginAtZero: true } }
                }
            });
        }
    });
  </script>
</x-app-layout>
