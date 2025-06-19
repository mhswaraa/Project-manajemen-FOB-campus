{{-- resources/views/penjahit/projects/index.blade.php --}}
<x-app-layout>
  <div class="flex h-screen bg-gray-100 text-gray-800">
    @include('penjahit.partials.sidebar')

    <main class="flex-1 overflow-y-auto p-6">
      <h2 class="text-xl font-semibold mb-4">Daftar Proyek Tersedia</h2>

      <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Proyek</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Qty</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sudah Diambil</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sisa</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @forelse($projects as $i => $project)
              <tr>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $i + 1 }}</td>
                <td class="px-6 py-4 text-sm text-gray-800">{{ $project->name }}</td>
                {{-- Total Qty = invested_qty --}}
                <td class="px-6 py-4 text-sm text-gray-600">
                  {{ $project->invested }} pcs
                </td>
                {{-- Sudah Diambil = taken_qty --}}
                <td class="px-6 py-4 text-sm text-gray-600">
                  {{ $project->taken }} pcs
                </td>
                {{-- Sisa = remaining --}}
                <td class="px-6 py-4 text-sm text-gray-600">
                  {{ $project->remaining }} pcs
                </td>
                <td class="px-6 py-4 text-sm">
                  @if($project->remaining > 0)
                    <a href="{{ route('penjahit.projects.take', $project) }}"
                       class="inline-block px-4 py-1 bg-teal-600 text-white rounded hover:bg-teal-700 text-sm">
                      Ambil Tugas
                    </a>
                  @else
                    <span class="text-gray-400 text-sm">Habis</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                  Belum ada proyek yang tersedia.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </main>
  </div>
</x-app-layout>
