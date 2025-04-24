<x-app-layout>
    <div class="flex h-screen bg-gray-50 text-gray-800">
      {{-- Sidebar --}}
      @include('investor.partials.sidebar')
  
      {{-- Main Content --}}
      <main class="flex-1 p-6 overflow-y-auto">
        {{-- Header --}}
        <div class="mb-6 flex items-center justify-between">
          <h1 class="text-2xl font-semibold text-green-700">Investasi Saya</h1>
          @if(session('success'))
            <div class="text-green-600">{{ session('success') }}</div>
          @endif
        </div>
  
        @if($investments->isEmpty())
          <p class="text-gray-500">Belum ada investasi. Ayo investasi di <a href="{{ route('investor.projects.index') }}" class="text-green-600 hover:underline">Daftar Proyek</a>.</p>
        @else
          <div class="bg-white shadow rounded-lg overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proyek</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah (Rp)</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deadline Investasi</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progress Produksi</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status Investasi</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                @foreach($investments as $i => $inv)
                  @php
                    // Ambil progress terakhir (jika ada)
                    $lastProgress = $inv->project->productionProgress->sortByDesc('created_at')->first();
                    $progressPct  = $lastProgress
                                   ? ($lastProgress->completed_units / $lastProgress->total_units) * 100
                                   : 0;
                  @endphp
                  <tr>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $i+1 }}</td>
                    <td class="px-6 py-4 text-sm text-gray-800">
                      {{ $inv->project->name }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                      Rp {{ number_format($inv->amount,0,',','.') }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $inv->deadline }}</td>
                    <td class="px-6 py-4">
                      @if($lastProgress)
                        <div class="text-sm text-gray-800">
                          {{ round($progressPct) }}%
                        </div>
                        <div class="w-full bg-gray-200 h-2 rounded mt-1">
                          <div class="bg-green-600 h-2 rounded"
                               style="width: {{ round($progressPct) }}%"></div>
                        </div>
                      @else
                        <span class="text-sm text-gray-500">Belum ada update</span>
                      @endif
                    </td>
                    <td class="px-6 py-4 text-sm font-medium">
                      @if(now()->gt(\Carbon\Carbon::parse($inv->deadline)))
                        <span class="text-red-600">Selesai</span>
                      @else
                        <span class="text-green-600">Aktif</span>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </main>
    </div>
  </x-app-layout>
  