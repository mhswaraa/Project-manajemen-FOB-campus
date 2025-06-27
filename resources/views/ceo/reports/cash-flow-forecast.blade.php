<x-app-layout>
    {{-- Penambahan Chart.js dari CDN --}}
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endpush

    <div class="flex h-screen bg-gray-100">
        @include('ceo.partials.sidebar')

        <main class="flex-1 overflow-y-auto p-6 lg:p-8">
            <div class="flex flex-col sm:flex-row justify-between items-start mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Perkiraan Arus Kas</h1>
                    <p class="text-gray-500 mt-1">Analisis pemasukan, pengeluaran, dan profitabilitas bulanan.</p>
                </div>
            </div>

            {{-- Kartu Metrik Utama --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                 <div class="bg-white p-5 rounded-xl shadow">
                    <div class="flex items-center space-x-4">
                        <div class="bg-green-100 text-green-600 p-3 rounded-full"><x-heroicon-o-arrow-trending-up class="w-6 h-6"/></div>
                        <div>
                            <p class="text-sm text-gray-500">Total Pemasukan</p>
                            <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-xl shadow">
                    <div class="flex items-center space-x-4">
                        <div class="bg-red-100 text-red-600 p-3 rounded-full"><x-heroicon-o-arrow-trending-down class="w-6 h-6"/></div>
                        <div>
                            <p class="text-sm text-gray-500">Total Pengeluaran</p>
                            <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
                 <div class="bg-white p-5 rounded-xl shadow">
                    <div class="flex items-center space-x-4">
                        <div class="bg-blue-100 text-blue-600 p-3 rounded-full"><x-heroicon-o-scale class="w-6 h-6"/></div>
                        <div>
                            <p class="text-sm text-gray-500">Arus Kas Bersih</p>
                            <p class="text-2xl font-bold {{ $totalNetCashFlow >= 0 ? 'text-gray-800' : 'text-red-600' }}">Rp {{ number_format($totalNetCashFlow, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-xl shadow">
                    <div class="flex items-center space-x-4">
                        <div class="bg-yellow-100 text-yellow-600 p-3 rounded-full"><x-heroicon-o-chart-pie class="w-6 h-6"/></div>
                        <div>
                            <p class="text-sm text-gray-500">Total Profit Konveksi</p>
                            <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalConvectionProfit, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Grafik Arus Kas --}}
            <div class="bg-white rounded-xl shadow p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Grafik Arus Kas Bulanan</h3>
                <div class="h-96">
                    <canvas id="cashFlowChart"></canvas>
                </div>
            </div>

            {{-- Tabel Rincian Arus Kas --}}
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="p-6">
                     <h3 class="text-lg font-semibold text-gray-900">Rincian Arus Kas Bulanan</h3>
                     <p class="text-sm text-gray-500">Data dikelompokkan berdasarkan bulan transaksi.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bulan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase text-green-600">Pemasukan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase text-red-600">Pengeluaran</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase text-blue-600">Arus Kas Bersih</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase text-yellow-800">Profit Konveksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($forecastData as $data)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">{{ $data['month'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-700 font-semibold">+ Rp {{ number_format($data['income'], 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="text-red-700 font-semibold">- Rp {{ number_format($data['expenses'], 0, ',', '.') }}</span>
                                    <div class="text-xs text-gray-400">
                                        Bahan: {{number_format($data['material_cost'], 0, ',', '.')}} | Upah: {{number_format($data['wage_cost'], 0, ',', '.')}} | Investor: {{number_format($data['investor_payout'], 0, ',', '.')}}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold {{ $data['net_cash_flow'] >= 0 ? 'text-blue-800' : 'text-red-800' }}">
                                    Rp {{ number_format($data['net_cash_flow'], 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-yellow-900">
                                    Rp {{ number_format($data['convection_profit'], 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">Tidak ada data transaksi untuk ditampilkan.</td></tr>
                            @endforelse
                        </tbody>
                         <tfoot class="bg-gray-50">
                            <tr class="font-bold text-gray-800">
                                <td class="px-6 py-4 text-sm">Total</td>
                                <td class="px-6 py-4 text-sm text-green-700">+ Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-sm text-red-700">- Rp {{ number_format($totalExpenses, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-sm {{ $totalNetCashFlow >= 0 ? 'text-blue-800' : 'text-red-800' }}">Rp {{ number_format($totalNetCashFlow, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-sm text-yellow-900">Rp {{ number_format($totalConvectionProfit, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </main>
    </div>

    {{-- Skrip untuk inisialisasi Chart.js --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('cashFlowChart').getContext('2d');
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($chartLabels),
                    datasets: [
                        {
                            type: 'line',
                            label: 'Arus Kas Bersih',
                            data: @json($chartNetFlowData),
                            borderColor: '#3B82F6', // blue-500
                            backgroundColor: 'transparent',
                            tension: 0.3,
                            borderWidth: 3,
                            pointBackgroundColor: '#3B82F6',
                            yAxisID: 'y',
                        },
                        {
                            type: 'line',
                            label: 'Profit Konveksi',
                            data: @json($chartProfitData),
                            borderColor: '#F59E0B', // amber-500
                            backgroundColor: 'transparent',
                            tension: 0.3,
                            borderWidth: 2,
                            borderDash: [5, 5],
                            pointBackgroundColor: '#F59E0B',
                            yAxisID: 'y',
                        },
                        {
                            type: 'bar',
                            label: 'Pemasukan',
                            data: @json($chartIncomeData),
                            backgroundColor: '#10B981', // green-500
                            borderRadius: 4,
                            yAxisID: 'y',
                        },
                        {
                            type: 'bar',
                            label: 'Pengeluaran',
                            data: @json($chartExpensesData),
                            backgroundColor: '#EF4444', // red-500
                            borderRadius: 4,
                            yAxisID: 'y',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value, index, values) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
