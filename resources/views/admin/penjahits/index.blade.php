{{-- resources/views/admin/penjahits/index.blade.php --}}
<x-app-layout>
    <div class="flex h-screen bg-gray-100 text-gray-800">
      {{-- Sidebar --}}
       @include('admin.partials.sidebar') {{-- sesuaikan include --}}
      <main class="flex-1 overflow-y-auto p-6">
        {{-- Header + Alert --}}
        <div class="mb-6 flex items-center justify-between">
          <h1 class="text-2xl font-semibold text-teal-700">Manajemen Penjahit</h1>
          @if(session('success'))
            <div class="px-4 py-2 bg-green-100 text-green-800 rounded">
              {{ session('success') }}
            </div>
          @endif
        </div>
  
        {{-- Card Ringkasan --}}
        <div class="mb-8 grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="bg-white p-6 rounded-lg shadow flex items-center">
            <div class="p-3 bg-teal-100 rounded-full">
              <svg xmlns="http://www.w3.org/2000/svg"
                   class="w-6 h-6 text-teal-600" fill="none"
                   viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 4v16m8-8H4"/>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm text-gray-500">Total Penjahit</p>
              <p class="text-2xl font-semibold text-gray-800">{{ $tailorCount }}</p>
            </div>
          </div>
        </div>
  
        {{-- Tombol Tambah --}}
        <div class="mb-4">
          <a href="{{ route('admin.penjahits.create') }}"
             class="inline-block px-4 py-2 bg-teal-600 text-white rounded hover:bg-teal-700">
            + Tambah Penjahit
          </a>
        </div>
  
        {{-- Tabel Penjahit --}}
        <div class="bg-white shadow rounded-lg overflow-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alamat</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. HP</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              @foreach($tailors as $i => $p)
                <tr>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $i+1 }}</td>
                  <td class="px-6 py-4 text-sm text-gray-800">{{ $p->address }}</td>
                  <td class="px-6 py-4 text-sm text-gray-600">{{ $p->email }}</td>
                  <td class="px-6 py-4 text-sm text-gray-600">{{ $p->phone }}</td>
                  <td class="px-6 py-4 text-sm text-gray-600">{{ ucfirst($p->status) }}</td>
                  <td class="px-6 py-4 text-center text-sm font-medium space-x-2">
                    <a href="{{ route('admin.penjahits.edit',$p) }}"
                       class="text-teal-600 hover:text-teal-900">Edit</a>
                    <form action="{{ route('admin.penjahits.destroy',$p) }}"
                          method="POST" class="inline-block"
                          onsubmit="return confirm('Hapus penjahit ini?');">
                      @csrf @method('DELETE')
                      <button type="submit"
                              class="text-red-600 hover:text-red-900">Hapus</button>
                    </form>
                  </td>
                </tr>
              @endforeach
              @if($tailors->isEmpty())
                <tr>
                  <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                    Belum ada penjahit terdaftar.
                  </td>
                </tr>
              @endif
            </tbody>
          </table>
        </div>
      </main>
    </div>
  </x-app-layout>
  