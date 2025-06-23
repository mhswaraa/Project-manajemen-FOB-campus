{{-- resources/views/penjahit/projects/index.blade.php --}}
<x-app-layout>
  <div class="flex h-screen bg-gray-100 text-gray-800">
    @include('penjahit.partials.sidebar')

    <main class="flex-1 overflow-y-auto p-6">
      <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Daftar Proyek Tersedia</h1>
        <p class="text-gray-500 mt-1">Pilih proyek yang ingin Anda kerjakan dari daftar di bawah ini.</p>
      </div>

      <div class="mb-6 p-4 bg-white rounded-lg shadow-sm">
        <div class="flex flex-col md:flex-row gap-4 items-center">
          <form action="{{ route('penjahit.projects.index') }}" method="GET" class="flex-grow w-full md:w-auto">
            <label for="search" class="sr-only">Cari Proyek</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <x-heroicon-o-magnifying-glass class="h-5 w-5 text-gray-400"/>
              </div>
              <input type="text" name="search" id="search" value="{{ $search ?? '' }}"
                     class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 sm:text-sm"
                     placeholder="Cari nama proyek...">
            </div>
            {{-- Hidden inputs to preserve sorting while searching --}}
            @if($sortBy) <input type="hidden" name="sort_by" value="{{ $sortBy }}"> @endif
            @if($sortDirection) <input type="hidden" name="sort_direction" value="{{ $sortDirection }}"> @endif
          </form>

          <div class="flex items-center gap-2 text-sm">
            <span class="text-gray-500 font-medium">Urutkan:</span>
            @php
                // Helper untuk membuat link sorting
                function sort_link($label, $column, $currentSort, $currentDir) {
                    $newDir = ($currentSort == $column && $currentDir == 'asc') ? 'desc' : 'asc';
                    $icon = $currentSort == $column ? ($currentDir == 'asc' ? 'chevron-up' : 'chevron-down') : 'chevron-up-down';
                    $isActive = $currentSort == $column;
                    $class = $isActive ? 'bg-teal-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300';
                    $url = route('penjahit.projects.index', ['sort_by' => $column, 'sort_direction' => $newDir, 'search' => request('search')]);

                    return "<a href='{$url}' class='px-3 py-1 rounded-full flex items-center gap-1 transition {$class}'>
                                {$label} <x-heroicon-s-{$icon} class='h-4 w-4' />
                            </a>";
                }
            @endphp
            {!! sort_link('Deadline', 'deadline', $sortBy, $sortDirection) !!}
            {!! sort_link('Sisa Slot', 'remaining', $sortBy, $sortDirection) !!}
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($projects as $project)
          <div x-data="{}" class="bg-white rounded-xl shadow-lg overflow-hidden flex flex-col transition hover:scale-105 duration-300">
            @if($project->image)
              <img src="{{ asset('storage/' . $project->image) }}" alt="{{ $project->name }}" class="h-48 w-full object-cover">
            @else
              <div class="h-48 w-full bg-gray-200 flex items-center justify-center">
                <x-heroicon-o-photo class="w-16 h-16 text-gray-400"/>
              </div>
            @endif

            <div class="p-5 flex flex-col flex-grow">
              @php
                  $deadline = \Carbon\Carbon::parse($project->deadline);
                  $daysRemaining = now()->diffInDays($deadline, false);
                  $badgeColor = $daysRemaining < 7 ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800';
              @endphp
              <span class="px-2 py-1 text-xs font-semibold rounded-full self-start {{ $badgeColor }}">
                Deadline: {{ $deadline->format('d M Y') }}
              </span>
              
              <h3 class="mt-2 text-lg font-bold text-gray-900 leading-tight">{{ $project->name }}</h3>
              
              <p class="mt-1 text-sm font-semibold text-green-600">
                Upah: Rp {{ number_format($project->wage_per_piece ?? 10000, 0, ',', '.') }} / pcs
                <span class="text-xs text-gray-500 font-normal italic">(Contoh)</span>
              </p>

              <div class="mt-4 flex-grow"></div> <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                <div>
                  <p class="text-sm text-gray-500">Sisa Slot</p>
                  <p class="font-bold text-gray-800">{{ $project->remaining }} pcs</p>
                </div>
                <button @click.prevent="$dispatch('open-modal', 'project-detail-{{ $project->id }}')"
                   class="px-4 py-2 bg-gray-800 text-white text-sm font-semibold rounded-lg hover:bg-gray-700 shadow-md">
                  Lihat Detail
                </button>
              </div>
            </div>
          </div>
          
          <x-modal name="project-detail-{{ $project->id }}" :show="false" focusable>
              <div class="p-6">
                  @if($project->image)
                    <img src="{{ asset('storage/' . $project->image) }}" alt="{{ $project->name }}" class="h-64 w-full object-cover rounded-lg mb-4">
                  @endif
                  <h2 class="text-2xl font-bold text-gray-900">{{ $project->name }}</h2>
                  <div class="mt-4 space-y-2 text-gray-600">
                      <p><span class="font-semibold">Deadline:</span> {{ \Carbon\Carbon::parse($project->deadline)->format('d F Y') }}</p>
                      <p><span class="font-semibold">Upah per Pcs:</span> <span class="text-green-600 font-bold">Rp {{ number_format($project->wage_per_piece ?? 10000, 0, ',', '.') }}</span></p>
                      <p><span class="font-semibold">Sisa Slot Tersedia:</span> {{ $project->remaining }} pcs</p>
                  </div>
                  <div class="mt-6 flex justify-end gap-3">
                      <x-secondary-button x-on:click="$dispatch('close')">
                          Batal
                      </x-secondary-button>
                      <a href="{{ route('penjahit.projects.take', $project) }}" class="inline-flex items-center px-4 py-2 bg-teal-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-700">
                          Ambil Tugas Ini
                      </a>
                  </div>
              </div>
          </x-modal>
        @empty
          <div class="md:col-span-2 xl:col-span-3 bg-white text-center rounded-lg shadow p-8">
            <x-heroicon-o-circle-stack class="w-16 h-16 mx-auto text-gray-300" />
            <h3 class="mt-4 text-lg font-medium text-gray-700">Tidak Ada Proyek yang Sesuai</h3>
            <p class="mt-1 text-sm text-gray-500">Coba ubah kata kunci pencarian Anda atau kembali lagi nanti.</p>
          </div>
        @endforelse
      </div>
    </main>
  </div>
</x-app-layout>