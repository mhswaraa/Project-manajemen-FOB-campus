<x-app-layout>
  <div class="flex">
    @include('layouts.partials.admin-sidebar')
    <main class="flex-1 p-6">

      <h2 class="text-2xl mb-4">Investasi Pending</h2>
      @if(session('success'))
        <div class="p-3 mb-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
      @endif

      <table class="min-w-full bg-white shadow rounded-lg overflow-hidden">
        <thead class="bg-gray-50">
          <tr>
            <th>ID</th><th>Proyek</th><th>Investor</th><th>Jumlah</th><th>Deadline</th><th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($pending as $inv)
            <tr>
              <td>{{ $inv->id }}</td>
              <td>{{ $inv->project->name }}</td>
              <td>{{ $inv->investor->name }}</td>
              <td>Rp {{ number_format($inv->amount,0,',','.') }}</td>
              <td>{{ $inv->deadline }}</td>
              <td>
                <form action="{{ route('admin.investments.approve',$inv) }}"
                      method="POST" onsubmit="return confirm('Approve?');">
                  @csrf
                  <button class="px-2 py-1 bg-indigo-600 text-white rounded">Approve</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center py-4 text-gray-500">Tidak ada pending.</td></tr>
          @endforelse
        </tbody>
      </table>

    </main>
  </div>
</x-app-layout>
