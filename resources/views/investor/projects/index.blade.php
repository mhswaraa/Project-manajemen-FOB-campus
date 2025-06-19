{{-- resources/views/investor/projects/index.blade.php --}}
<x-app-layout>
  <div class="flex h-screen bg-gray-50 text-gray-800">
    {{-- Sidebar --}}
    @include('investor.partials.sidebar')

    {{-- Main Content --}}
    <main class="flex-1 p-6 overflow-y-auto">
      {{-- Header + Filter --}}
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-green-700">Daftar Proyek Aktif</h1>
        <form method="GET" action="{{ route('investor.projects.index') }}" class="flex gap-2">
          <select name="category" class="rounded border-gray-300">
            <option value="">Semua Kategori</option>
            {{-- kategori dinamis jika ada --}}
            <option value="fashion" {{ request('category')=='fashion' ? 'selected':'' }}>Fashion</option>
            <option value="tech"    {{ request('category')=='tech'    ? 'selected':'' }}>Tech</option>
          </select>
          <button type="submit"
                  class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700">
            Filter
          </button>
        </form>
      </div>

      {{-- Flash Message --}}
      @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
          {{ session('success') }}
        </div>
      @endif

      {{-- Table / List --}}
      <div class="bg-white shadow rounded-lg overflow-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Nama Proyek
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Harga / pcs
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Total Qty
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Profit
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Deadline
              </th>
              <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                Aksi
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @forelse($projects as $project)
              <tr>
                <td class="px-6 py-4 text-sm text-gray-800">{{ $project->name }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">
                  Rp {{ number_format($project->price_per_piece, 0, ',', '.') }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                  {{ $project->quantity }} pcs
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                  Rp {{ number_format($project->profit, 0, ',', '.') }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $project->deadline }}</td>
                <td class="px-6 py-4 text-center">
                  <a href="{{ route('investor.projects.invest', $project) }}"
                     class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700">
                    Investasikan
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                  Belum ada proyek aktif.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Pagination --}}
      <div class="mt-4">
        {{ $projects->withQueryString()->links() }}
      </div>
    </main>
  </div>
</x-app-layout>
