<x-app-layout>
  <div class="flex h-screen bg-gray-100">
    @include('ceo.partials.sidebar')

    <main class="flex-1 overflow-y-auto p-6 lg:p-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Papan Peringkat & Efisiensi Produksi</h1>
            <p class="text-gray-500 mt-1">Menganalisis kinerja penjahit dan efektivitas siklus produksi.</p>
        </div>
        
        <div class="mb-8 p-4 bg-white rounded-lg shadow-sm">
            <form action="{{ route('ceo.reports.production-leaderboard') }}" method="GET" class="flex items-center gap-4">
                <label for="period" class="text-sm font-medium text-gray-700">Tampilkan data produktivitas untuk:</label>
                <select name="period" id="period" onchange="this.form.submit()" class="block w-full max-w-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="7" @selected($currentPeriod == 7)>7 Hari Terakhir</option>
                    <option value="30" @selected($currentPeriod == 30)>30 Hari Terakhir</option>
                    <option value="90" @selected($currentPeriod == 90)>90 Hari Terakhir</option>
                </select>
            </form>
        </div>

        {{-- KPI Utama & Penjahit Paling Cepat --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <p class="text-sm font-medium text-gray-500">Rata-rata Siklus Produksi</p>
                <p class="text-3xl font-bold text-indigo-600 mt-1">{{ $averageProductionCycle }} <span class="text-xl font-medium text-gray-600">hari</span></p>
                <p class="text-xs text-gray-400 mt-1">Dari investasi pertama hingga proyek 100% selesai.</p>
            </div>
            <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-md">
                 <h3 class="text-lg font-medium text-gray-900 mb-4">Penjahit Paling Cepat (Rata-rata per Tugas)</h3>
                 <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    @forelse($fastestTailors as $tailor)
                        <div class="text-center">
                            <img class="h-12 w-12 rounded-full object-cover mx-auto" src="https://ui-avatars.com/api/?name={{ urlencode($tailor->name) }}&background=E0F2F1&color=00796B" alt="">
                            <p class="text-sm font-semibold mt-2">{{ explode(' ', $tailor->name)[0] }}</p>
                            <p class="text-xs text-indigo-600 font-bold">{{ number_format($tailor->avg_completion_days, 1) }} hari</p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 col-span-full">Data belum cukup.</p>
                    @endforelse
                 </div>
            </div>
        </div>

        {{-- Papan Peringkat Produktivitas & Spesialis --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Top 5 Penjahit Paling Produktif</h3>
                <div class="space-y-4">
                  @forelse ($topProductiveTailors as $index => $tailor)
                    <div class="flex items-center gap-4 p-3 rounded-lg {{ $index == 0 ? 'bg-amber-50' : '' }}">
                        <div class="text-xl font-bold text-gray-400 w-8 text-center">{{ $index + 1 }}</div>
                        <img class="h-12 w-12 rounded-full object-cover" src="https://ui-avatars.com/api/?name={{ urlencode($tailor->user->name) }}&background=E0F2F1&color=00796B" alt="">
                        <div class="flex-grow"><p class="font-semibold text-gray-800">{{ $tailor->user->name }}</p><p class="text-sm text-gray-500">{{ $tailor->specializations->pluck('name')->join(', ') ?: 'Belum ada keahlian' }}</p></div>
                        <div class="text-right"><p class="text-lg font-bold text-teal-600">{{ $tailor->total_done ?? 0 }}</p><p class="text-xs text-gray-500">Pcs Selesai</p></div>
                    </div>
                  @empty
                     <p class="text-sm text-gray-500 text-center py-8">Tidak ada data kinerja penjahit pada periode ini.</p>
                  @endforelse
                </div>
            </div>
             <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Top Spesialis per Kategori Proyek</h3>
                <div class="space-y-4">
                  @forelse ($topSpecialists as $specialist)
                    <div class="flex items-center gap-4 p-3 rounded-lg">
                        <div class="flex-shrink-0 h-10 w-10 bg-gray-100 rounded-lg flex items-center justify-center"><x-heroicon-o-sparkles class="h-6 w-6 text-gray-500" /></div>
                        <div class="flex-grow">
                            <p class="font-semibold text-gray-800">{{ $specialist->project_category }}</p>
                            <p class="text-sm text-gray-500">Dikuasai oleh: <span class="font-medium text-indigo-600">{{ $specialist->name }}</span></p>
                        </div>
                        <div class="text-right"><p class="text-lg font-bold text-gray-700">{{ $specialist->project_count }}</p><p class="text-xs text-gray-500">Proyek</p></div>
                    </div>
                  @empty
                     <p class="text-sm text-gray-500 text-center py-8">Data spesialis belum tersedia.</p>
                  @endforelse
                </div>
            </div>
        </div>
    </main>
  </div>
</x-app-layout>
