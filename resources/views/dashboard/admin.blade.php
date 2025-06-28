<x-app-layout>
  <div class="flex h-screen bg-gray-50">
    @include('admin.partials.sidebar')

    <main class="flex-1 overflow-y-auto p-6 lg:p-8">
      
      {{-- Header --}}
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Admin Dashboard</h1>
        <p class="text-gray-500 mt-1">Selamat datang, {{ Auth::user()->name }}. Kelola semua aspek dari dasbor ini.</p>
      </div>

      {{-- Baris 1: Kartu Statistik Entitas --}}
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
          <div><p class="text-sm font-medium text-gray-500">Total Proyek</p><p class="text-3xl font-bold text-indigo-600">{{ $projectCount }}</p></div>
          <div class="p-3 bg-indigo-100 rounded-full"><x-heroicon-o-briefcase class="w-6 h-6 text-indigo-600" /></div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
          <div><p class="text-sm font-medium text-gray-500">Total Investor</p><p class="text-3xl font-bold text-green-600">{{ $investorCount }}</p></div>
          <div class="p-3 bg-green-100 rounded-full"><x-heroicon-o-user-group class="w-6 h-6 text-green-600" /></div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
          <div><p class="text-sm font-medium text-gray-500">Total Penjahit</p><p class="text-3xl font-bold text-teal-600">{{ $penjahitCount }}</p></div>
          <div class="p-3 bg-teal-100 rounded-full"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.25 9.75l-4.5-4.5m0 0l-4.5 4.5m4.5-4.5v12.75m4.5-4.5l-4.5 4.5m0 0l-4.5-4.5m4.5 4.5v-12.75" transform="rotate(45 12 12)" /><circle cx="7.5" cy="7.5" r="2.5" /><circle cx="16.5" cy="7.5" r="2.5" /></svg></div>
        </div>
        <a href="{{ route('admin.users.create') }}" class="bg-gray-800 text-white p-6 rounded-lg shadow-md flex items-center justify-center text-center hover:bg-gray-700 transition">
          <div><x-heroicon-o-user-plus class="w-8 h-8 mx-auto" /><p class="mt-2 text-sm font-semibold">Tambah Pengguna Baru</p></div>
        </a>
      </div>

      {{-- Baris 2: Kartu Statistik Keuangan & Operasional --}}
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <p class="text-sm font-medium text-gray-500">Dana Terkumpul (Disetujui)</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">Rp {{ number_format($totalApprovedFund, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <p class="text-sm font-medium text-gray-500">Potensi Profit Kotor</p>
            <p class="text-2xl font-bold text-green-600 mt-1">Rp {{ number_format($potentialGrossProfit, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <p class="text-sm font-medium text-gray-500">Estimasi Biaya Upah</p>
            <p class="text-2xl font-bold text-orange-600 mt-1">Rp {{ number_format($estimatedWageCost, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <p class="text-sm font-medium text-gray-500">Tingkat Penyelesaian Produksi</p>
            <p class="text-2xl font-bold text-purple-600 mt-1">{{ $completionRate }}%</p>
        </div>
      </div>

      {{-- Sesi Aksi Utama & Laporan --}}
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Kolom Kiri: Investasi Menunggu Persetujuan --}}
        <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-md">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">Investasi Perlu Persetujuan</h2>
            <a href="{{ route('admin.projects.invested') }}" class="text-sm text-indigo-600 hover:underline">Lihat Semua</a>
          </div>
          <div class="space-y-4">
            @forelse ($pendingInvestments as $investment)
              <div class="p-4 rounded-lg border border-gray-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                  <p class="font-semibold text-gray-900">{{ $investment->project->name }}</p>
                  <p class="text-sm text-gray-500">Oleh: <span class="font-medium text-gray-700">{{ $investment->investor->user->name }}</span> | <span class="font-bold text-green-600">Rp {{ number_format($investment->amount, 0, ',', '.') }}</span> ({{ $investment->qty }} pcs)</p>
                </div>
                <form action="{{ route('admin.projects.invested.approve', $investment) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menyetujui investasi ini?')">@csrf<button type="submit" class="flex-shrink-0 px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 shadow-sm">Approve</button></form>
              </div>
            @empty
              <div class="text-center py-8 px-4 border-2 border-dashed rounded-lg"><x-heroicon-o-check-circle class="w-12 h-12 text-gray-300 mx-auto" /><p class="mt-2 text-gray-700 font-semibold">Luar Biasa!</p><p class="text-sm text-gray-500">Tidak ada investasi yang menunggu persetujuan.</p></div>
            @endforelse
          </div>
        </div>

        {{-- Kolom Kanan: Proyek Berisiko --}}
        <div class="bg-white p-6 rounded-lg shadow-md">
          <h2 class="text-xl font-bold text-gray-800 mb-4">Proyek Perlu Perhatian</h2>
          <div class="space-y-4">
            @forelse ($atRiskProjects as $project)
            @php
                $assigned = $project->assigned_work ?? 0;
                $completed = $project->completed_work ?? 0;
                $riskPercentage = $assigned > 0 ? round(($completed / $assigned) * 100) : 0;
            @endphp
            <div>
              <a href="{{ route('admin.projects.edit', $project) }}" class="font-semibold text-gray-900 hover:text-indigo-600">{{ $project->name }}</a>
              <p class="text-xs text-gray-500">Deadline: {{ \Carbon\Carbon::parse($project->deadline)->diffForHumans() }}</p>
              <div class="flex justify-between items-center mb-1 text-sm mt-2">
                <span class="font-medium text-red-600">Progress: {{ $riskPercentage }}%</span>
              </div>
              <div class="w-full bg-red-100 rounded-full h-2.5"><div class="bg-red-500 h-2.5 rounded-full" style="width: {{ $riskPercentage }}%"></div></div>
            </div>
            @empty
              <div class="text-center py-6"><x-heroicon-o-shield-check class="w-10 h-10 text-gray-300 mx-auto" /><p class="mt-2 text-sm text-gray-500">Tidak ada proyek yang berisiko saat ini. Kerja bagus!</p></div>
            @endforelse
          </div>
        </div>

      </div>
    </main>
  </div>
</x-app-layout>
