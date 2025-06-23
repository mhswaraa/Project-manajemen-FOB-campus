<x-app-layout>
  {{-- Sertakan Chart.js dari CDN --}}
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <div class="flex h-screen bg-gray-50">
    @include('investor.partials.sidebar')

    <main class="flex-1 overflow-y-auto p-6 lg:p-8">
      {{-- Header --}}
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Dashboard Investor</h1>
        <p class="text-gray-500 mt-1">Selamat datang, {{ Auth::user()->name }}. Pantau kinerja portofolio Anda di sini.</p>
      </div>

      {{-- Kartu Statistik --}}
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md">
          <p class="text-sm font-medium text-gray-500">Total Dana Diinvestasikan</p>
          <p class="text-2xl font-bold text-blue-600 mt-1">Rp {{ number_format($totalInvested, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
          <p class="text-sm font-medium text-gray-500">Estimasi Keuntungan</p>
          <p class="text-2xl font-bold text-green-600 mt-1">Rp {{ number_format($estimatedProfit, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
          <p class="text-sm font-medium text-gray-500">Proyek Aktif Diikuti</p>
          <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $activeProjectsCount }} Proyek</p>
        </div>
        {{-- Tombol Aksi --}}
        <a href="{{ route('investor.projects.index') }}" class="bg-teal-500 text-white p-6 rounded-lg shadow-md flex items-center justify-center text-center hover:bg-teal-600 transition">
          <div>
            <x-heroicon-o-currency-dollar class="w-8 h-8 mx-auto" />
            <p class="mt-2 text-sm font-semibold">Cari Peluang Baru</p>
          </div>
        </a>
      </div>

      {{-- Visualisasi & Aktivitas --}}
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Kolom Kiri: Alokasi Portofolio --}}
        <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-md">
           <h3 class="text-lg font-medium text-gray-900 mb-4">Alokasi Portofolio</h3>
           @if($chartData->isNotEmpty())
            <div class="h-80">
                <canvas id="portfolioChart"></canvas>
            </div>
           @else
            <div class="text-center py-12">
                <p class="text-gray-500">Anda belum memiliki investasi aktif untuk ditampilkan.</p>
            </div>
           @endif
        </div>
        
        {{-- Kolom Kanan: Aktivitas Terbaru --}}
        <div class="bg-white p-6 rounded-lg shadow-md">
           <h3 class="text-lg font-medium text-gray-900 mb-4">Aktivitas Terbaru</h3>
           <div class="space-y-4">
             @forelse ($recentActivities as $activity)
               <div class="flex items-start gap-4">
                  <div @class(['flex-shrink-0 h-10 w-10 rounded-lg flex items-center justify-center', 'bg-green-100' => $activity->approved, 'bg-yellow-100' => !$activity->approved])>
                      <x-heroicon-o-check-circle @class(['h-6 w-6', 'text-green-600' => $activity->approved, 'text-yellow-600' => !$activity->approved]) />
                  </div>
                  <div>
                      <p class="text-sm font-medium text-gray-800">
                          Investasi pada <span class="font-bold">{{ $activity->project->name }}</span>
                      </p>
                      <p @class(['text-xs font-semibold', 'text-green-600' => $activity->approved, 'text-yellow-600' => !$activity->approved])>
                          Status: {{ $activity->approved ? 'Disetujui' : 'Menunggu Persetujuan' }}
                      </p>
                      <p class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</p>
                  </div>
               </div>
             @empty
                <p class="text-sm text-gray-500 text-center py-8">Tidak ada aktivitas terbaru.</p>
             @endforelse
           </div>
        </div>
      </div>
    </main>
  </div>
  
  <script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('portfolioChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        label: 'Alokasi Dana',
                        data: {!! json_encode($chartData) !!},
                        backgroundColor: [
                            '#4f46e5', // Indigo
                            '#059669', // Green
                            '#2563eb', // Blue
                            '#d97706', // Amber
                            '#db2777', // Pink
                            '#6d28d9', // Violet
                        ],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed !== null) {
                                        label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed);
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }
    });
  </script>
</x-app-layout>
