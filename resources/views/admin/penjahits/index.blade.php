<x-app-layout>
  <div class="flex h-screen bg-gray-50">
    @include('admin.partials.sidebar')

    <main class="flex-1 overflow-y-auto p-6 lg:p-8">
      
      {{-- Header & Tombol Aksi --}}
      <div class="flex flex-col sm:flex-row justify-between items-start mb-8">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">Manajemen Penjahit</h1>
          <p class="text-gray-500 mt-1">Kelola semua sumber daya penjahit Anda.</p>
        </div>
        <button x-data @click.prevent="$dispatch('open-modal', 'add-tailor-modal')"
                class="mt-4 sm:mt-0 flex-shrink-0 inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
          <x-heroicon-s-user-plus class="h-5 w-5"/>
          Tambah Penjahit Baru
        </button>
      </div>

      {{-- Pesan Sukses --}}
      @if(session('success'))
        <div role="alert" class="mb-6 rounded-xl border border-gray-100 bg-white p-4 shadow-sm"><div class="flex items-start gap-4"><span class="text-green-600"><x-heroicon-s-check-circle class="h-6 w-6"/></span><div class="flex-1"><strong class="block font-medium text-gray-900">Sukses!</strong><p class="mt-1 text-sm text-gray-700">{{ session('success') }}</p></div></div></div>
      @endif

      {{-- Kartu Statistik & Pencarian --}}
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md"><p class="text-sm font-medium text-gray-500">Total Penjahit</p><p class="text-3xl font-bold text-teal-600 mt-1">{{ $tailorCount }}</p></div>
        <div class="bg-white p-6 rounded-lg shadow-md"><p class="text-sm font-medium text-gray-500">Tersedia</p><p class="text-3xl font-bold text-green-600 mt-1">{{ $availableCount }}</p></div>
        <div class="bg-white p-6 rounded-lg shadow-md"><p class="text-sm font-medium text-gray-500">Sibuk</p><p class="text-3xl font-bold text-orange-600 mt-1">{{ $busyCount }}</p></div>
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
            <form action="{{ route('admin.penjahits.index') }}" method="GET" class="w-full">
                <label for="search" class="text-sm font-medium text-gray-500">Cari Penjahit</label>
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><x-heroicon-o-magnifying-glass class="h-5 w-5 text-gray-400"/></div>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md bg-white placeholder-gray-500 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" placeholder="Nama atau email...">
                </div>
            </form>
        </div>
      </div>

      {{-- Tabel Penjahit --}}
      <div class="bg-white shadow-md rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penjahit</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keahlian</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kinerja</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th scope="col" class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($penjahits as $tailor)
            <tr>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-10 w-10"><img class="h-10 w-10 rounded-full object-cover" src="https://ui-avatars.com/api/?name={{ urlencode($tailor->user->name) }}&background=E0F2F1&color=00796B" alt=""></div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">{{ $tailor->user->name }}</div>
                    <div class="text-sm text-gray-500">{{ $tailor->user->email }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="flex flex-wrap gap-1">
                    @forelse ($tailor->specializations as $spec)
                        <span class="px-2 py-1 text-xs font-medium text-gray-600 bg-gray-100 rounded-full">{{ $spec->name }}</span>
                    @empty
                        <span class="text-xs text-gray-400 italic">Belum diatur</span>
                    @endforelse
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ $tailor->progress_sum_quantity_done ?? 0 }} pcs selesai</div>
                <div class="text-sm text-gray-500">{{ $tailor->assignments_count ?? 0 }} tugas aktif</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                  <span @class(['px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                      'bg-green-100 text-green-800' => $tailor->status == 'available',
                      'bg-yellow-100 text-yellow-800' => $tailor->status == 'busy',
                      'bg-red-100 text-red-800' => $tailor->status == 'inactive',
                  ])>{{ ucfirst($tailor->status) }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <a href="{{ route('admin.penjahits.edit', $tailor) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
              </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">Tidak ada penjahit yang ditemukan.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="mt-4">{{ $penjahits->links() }}</div>

    </main>

    {{-- Modal Tambah Penjahit --}}
    <x-modal name="add-tailor-modal" :show="$errors->isNotEmpty()" focusable>
      <form method="POST" action="{{ route('admin.penjahits.store') }}" class="p-6">
        @csrf
        <h2 class="text-lg font-medium text-gray-900">Tambah Penjahit Baru</h2>
        <p class="mt-1 text-sm text-gray-600">Buat akun pengguna dan profil untuk penjahit baru.</p>
        <div class="mt-6 space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><x-input-label for="name" value="Nama Lengkap" /><x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required /><x-input-error :messages="$errors->get('name')" class="mt-1" /></div>
            <div><x-input-label for="email" value="Alamat Email" /><x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required /><x-input-error :messages="$errors->get('email')" class="mt-1" /></div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><x-input-label for="password" value="Password" /><x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required /><x-input-error :messages="$errors->get('password')" class="mt-1" /></div>
            <div><x-input-label for="password_confirmation" value="Konfirmasi Password" /><x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required /></div>
          </div>
          <div><x-input-label for="address" value="Alamat Lengkap" /><x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address')" required /><x-input-error :messages="$errors->get('address')" class="mt-1" /></div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
             <div><x-input-label for="phone" value="No. Telepon" /><x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone')" required /><x-input-error :messages="$errors->get('phone')" class="mt-1" /></div>
             <div><x-input-label for="status" value="Status Awal" /><select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required><option value="available" selected>Available</option><option value="busy">Busy</option><option value="inactive">Inactive</option></select></div>
          </div>
        </div>
        <div class="mt-6 flex justify-end">
          <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
          <x-primary-button class="ml-3">Simpan Penjahit</x-primary-button>
        </div>
      </form>
    </x-modal>
  </div>
</x-app-layout>
