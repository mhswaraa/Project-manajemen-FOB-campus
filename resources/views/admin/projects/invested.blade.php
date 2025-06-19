{{-- resources/views/admin/projects/invested.blade.php --}}
<x-app-layout>
  <div class="flex h-screen bg-gray-100 text-gray-800">
    @include('admin.partials.sidebar')

    <main class="flex-1 overflow-y-auto p-6">
      {{-- Header --}}
      <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-indigo-700">Daftar Investasi</h1>
        @if(session('success'))
          <div class="px-4 py-2 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
          </div>
        @endif
      </div>

      {{-- Tabel Investasi --}}
      <div class="bg-white shadow rounded-lg overflow-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Investor</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proyek</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah Investasi</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Investasi</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Approved</th>
              <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @forelse($investments as $inv)
              <tr>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $inv->id }}</td>
                <td class="px-6 py-4 text-sm text-gray-800">
                  {{ $inv->investor->name }} (ID#{{ $inv->investor_id }})
                </td>
                <td class="px-6 py-4 text-sm text-gray-800">
                  {{ $inv->project->name }} (ID#{{ $inv->project_id }})
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                  {{ $inv->qty ?? '–' }} pcs
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                  Rp {{ number_format($inv->amount, 0, ',', '.') }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                  {{ $inv->created_at->format('Y-m-d') }}
                </td>
                <td class="px-6 py-4 text-sm font-medium">
                  @if($inv->approved)
                    <span class="text-green-600">✓</span>
                  @else
                    <span class="text-red-600">✘</span>
                  @endif
                </td>
                <td class="px-6 py-4 text-center text-sm font-medium">
                  @unless($inv->approved)
                    <form action="{{ route('admin.projects.invested.approve', $inv) }}"
                          method="POST"
                          onsubmit="return confirm('Approve investasi #{{ $inv->id }}?')"
                          class="inline">
                      @csrf
                      <button type="submit"
                              class="px-2 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                        Approve
                      </button>
                    </form>
                  @endunless
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                  Belum ada investasi.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </main>
  </div>
</x-app-layout>
