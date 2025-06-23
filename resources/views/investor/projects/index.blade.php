<x-app-layout>
  <div class="flex h-screen bg-gray-50">
    @include('investor.partials.sidebar')

    <main class="flex-1 overflow-y-auto p-6 lg:p-8">
      {{-- Header --}}
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Marketplace Proyek</h1>
        <p class="text-gray-500 mt-1">Temukan dan danai proyek-proyek fesyen menjanjikan berikutnya.</p>
      </div>

      {{-- Fitur Filter & Pencarian --}}
      <div class="mb-6 p-4 bg-white rounded-lg shadow-sm">
        <form action="{{ route('investor.projects.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center">
          {{-- Pencarian --}}
          <div class="flex-grow w-full md:w-auto">
            <label for="search" class="sr-only">Cari Proyek</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <x-heroicon-o-magnifying-glass class="h-5 w-5 text-gray-400"/>
              </div>
              <input type="text" name="search" id="search" value="{{ $search ?? '' }}" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md placeholder-gray-500 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Cari nama proyek...">
            </div>
          </div>
          {{-- Pengurutan --}}
          <div class="flex-shrink-0 w-full md:w-auto">
            <label for="sort_by" class="sr-only">Urutkan</label>
            <select name="sort_by" id="sort_by" onchange="this.form.submit()" class="block w-full border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
              <option value="latest" @selected($sortBy == 'latest')>Terbaru</option>
              <option value="deadline" @selected($sortBy == 'deadline')>Segera Deadline</option>
              <option value="popular" @selected($sortBy == 'popular')>Paling Populer</option>
            </select>
          </div>
        </form>
      </div>

      {{-- Layout Kartu Proyek --}}
      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
        @forelse($projects as $project)
          @php
            $funded = $project->funded_qty ?? 0;
            $fundingPercentage = $project->quantity > 0 ? ($funded / $project->quantity) * 100 : 0;
            $targetDana = $project->quantity * $project->price_per_piece;
          @endphp
          <div class="bg-white rounded-xl shadow-lg overflow-hidden flex flex-col transition hover:shadow-2xl duration-300">
            <a href="{{ route('investor.projects.invest', $project) }}" class="block">
              @if($project->image)
                <img src="{{ asset('storage/' . $project->image) }}" alt="{{ $project->name }}" class="h-56 w-full object-cover">
              @else
                <div class="h-56 w-full bg-gray-200 flex items-center justify-center"><x-heroicon-o-photo class="w-16 h-16 text-gray-400"/></div>
              @endif
            </a>
            <div class="p-5 flex flex-col flex-grow">
              <h3 class="text-lg font-bold text-gray-900 leading-tight">{{ $project->name }}</h3>
              <p class="text-sm text-gray-500 mt-1 flex items-center gap-1.5"><x-heroicon-s-calendar class="w-4 h-4 text-gray-400"/> Deadline: {{ \Carbon\Carbon::parse($project->deadline)->format('d M Y') }}</p>
              
              {{-- Progress Bar Pendanaan --}}
              <div class="mt-4">
                <div class="flex justify-between items-center mb-1 text-sm"><span class="font-medium text-gray-600">Pendanaan</span><span class="font-semibold text-indigo-600">{{ number_format($fundingPercentage, 0) }}%</span></div>
                <div class="w-full bg-gray-200 rounded-full h-2"><div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $fundingPercentage }}%"></div></div>
                <div class="text-right text-xs text-gray-500 mt-1">Terkumpul Rp {{ number_format($funded * $project->price_per_piece, 0, ',', '.') }}</div>
              </div>

              {{-- Detail Finansial --}}
              <div class="mt-4 pt-4 border-t border-gray-100 flex-grow grid grid-cols-2 gap-4 text-center">
                  <div><p class="text-xs text-gray-500">Harga per Slot</p><p class="text-sm font-bold text-gray-800">Rp {{ number_format($project->price_per_piece, 0, ',', '.') }}</p></div>
                  <div><p class="text-xs text-gray-500">Profit per Slot</p><p class="text-sm font-bold text-green-600">Rp {{ number_format($project->profit, 0, ',', '.') }}</p></div>
              </div>
              
              <div class="mt-5">
                <a href="{{ route('investor.projects.invest', $project) }}" class="w-full block text-center px-4 py-3 bg-gray-800 text-white text-sm font-semibold rounded-lg hover:bg-gray-700 shadow-md">Lihat & Investasi</a>
              </div>
            </div>
          </div>
        @empty
          <div class="md:col-span-2 xl:col-span-3 bg-white text-center rounded-lg shadow p-10"><x-heroicon-o-circle-stack class="w-16 h-16 mx-auto text-gray-300" /><h3 class="mt-4 text-lg font-medium text-gray-900">Tidak Ada Peluang Investasi</h3><p class="mt-1 text-sm text-gray-500">Saat ini belum ada proyek baru yang tersedia. Silakan kembali lagi nanti.</p></div>
        @endforelse
      </div>
    </main>
  </div>
</x-app-layout>
