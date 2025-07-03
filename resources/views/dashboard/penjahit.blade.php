{{-- resources/views/penjahit/dashboard.blade.php --}}
<x-app-layout>
  <div class="flex h-screen bg-gray-100 text-gray-800">
    {{-- Sidebar --}}
    @include('penjahit.partials.sidebar')

    {{-- Main content --}}
    <main class="flex-1 overflow-y-auto p-6">
      {{-- 1. Header Sambutan --}}
      <div class="mb-8">
        <h1 class="text-3xl font-semibold text-teal-700">Dashboard Penjahit</h1>
        <p class="text-gray-500 mt-1">Selamat datang kembali, {{ Auth::user()->name }}!</p>
      </div>

      {{-- ==================================================================== --}}
      {{-- AWAL PERUBAHAN: Logika kalkulasi statistik disesuaikan --}}
      {{-- ==================================================================== --}}
      @php
        // Menghitung statistik untuk kartu dari data $assignments
        $totalAssigned = $assignments->sum('assigned_qty');
        
        // Total Selesai sekarang dihitung dari item yang DITERIMA QC
        $totalDone = $assignments->reduce(function ($carry, $item) {
            return $carry + $item->progress->where('status', 'approved')->sum('accepted_qty');
        }, 0);
        
        $activeTasksCount = $assignments->where('status', '!=', 'completed')->count();
        
        // Sisa pekerjaan dihitung dari Total Tugas dikurangi yang sudah DITERIMA QC
        $remainingWork = $totalAssigned - $totalDone;
      @endphp
      {{-- ==================================================================== --}}
      {{-- AKHIR PERUBAHAN --}}
      {{-- ==================================================================== --}}

      {{-- 2. Kartu Statistik --}}
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-500">Tugas Aktif</p>
            <p class="text-3xl font-bold text-teal-600">{{ $activeTasksCount }}</p>
          </div>
          <div class="p-3 bg-teal-100 rounded-full">
            <x-heroicon-o-briefcase class="w-6 h-6 text-teal-600" />
          </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-500">Total Ditugaskan</p>
            <p class="text-3xl font-bold text-gray-700">{{ $totalAssigned }} <span class="text-lg">pcs</span></p>
          </div>
          <div class="p-3 bg-gray-100 rounded-full">
            <x-heroicon-o-clipboard-document-list class="w-6 h-6 text-gray-600" />
          </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-500">Total Diterima QC</p>
            <p class="text-3xl font-bold text-green-600">{{ $totalDone }} <span class="text-lg">pcs</span></p>
          </div>
          <div class="p-3 bg-green-100 rounded-full">
            <x-heroicon-o-check-circle class="w-6 h-6 text-green-600" />
          </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-500">Sisa Pekerjaan</p>
            <p class="text-3xl font-bold text-orange-600">{{ $remainingWork }} <span class="text-lg">pcs</span></p>
          </div>
          <div class="p-3 bg-orange-100 rounded-full">
            <x-heroicon-o-clock class="w-6 h-6 text-orange-600" />
          </div>
        </div>
      </div>
      
      {{-- 3. Daftar Tugas yang Lebih Baik --}}
      <h2 class="text-xl font-semibold text-gray-700 mb-4">Rincian Tugas Anda</h2>
      <div class="space-y-4">
        @forelse($assignments as $task)
          {{-- PERUBAHAN: Logika progres per kartu disesuaikan --}}
          @php
            $assigned = $task->assigned_qty;
            $accepted = $task->progress->where('status', 'approved')->sum('accepted_qty');
            $pending = $task->progress->where('status', 'pending_qc')->sum('quantity_done');
            $percentage = $assigned > 0 ? round(($accepted / $assigned) * 100) : 0;
          @endphp
          <div class="bg-white rounded-lg shadow p-5 transition hover:shadow-lg">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
              <div class="flex-1 mb-4 sm:mb-0">
                <h3 class="text-lg font-bold text-gray-800">{{ $task->project->name }}</h3>
                <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full
                  {{ $task->status == 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                  Status: {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                </span>
              </div>
              <a href="{{ route('penjahit.tasks.show', $task) }}"
                 class="px-4 py-2 bg-teal-600 text-white text-sm font-medium rounded-lg hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                Lihat Detail & Update
              </a>
            </div>
            <div class="mt-4">
              <div class="flex justify-between items-center mb-1 text-sm">
                <span class="font-medium text-gray-600">Progress Diterima</span>
                <span class="font-semibold text-teal-600">{{ $percentage }}%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div class="bg-teal-500 h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
              </div>
              <div class="flex justify-between text-sm text-gray-500 mt-1">
                <span>{{ $accepted }} / {{ $assigned }} pcs</span>
                @if($pending > 0)
                  <span class="text-yellow-600 italic">{{ $pending }} pcs menunggu QC</span>
                @endif
              </div>
            </div>
          </div>
        @empty
          {{-- 4. Pesan untuk Keadaan Kosong --}}
          <div class="bg-white text-center rounded-lg shadow p-8">
            <x-heroicon-o-document-check class="w-16 h-16 mx-auto text-gray-300" />
            <h3 class="mt-4 text-lg font-medium text-gray-700">Tidak Ada Tugas</h3>
            <p class="mt-1 text-sm text-gray-500">Saat ini tidak ada tugas yang aktif untuk Anda.</p>
            <a href="{{ route('penjahit.projects.index') }}" class="mt-4 inline-block px-4 py-2 bg-teal-600 text-white text-sm font-medium rounded-lg hover:bg-teal-700">
              Lihat Proyek Tersedia
            </a>
          </div>
        @endforelse
      </div>
    </main>
  </div>
</x-app-layout>
