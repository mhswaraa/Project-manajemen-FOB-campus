<x-app-layout>
  <div class="flex h-screen bg-gray-50">
    @include('admin.partials.sidebar')

    <main class="flex-1 overflow-y-auto p-6 lg:p-8">
      
      {{-- Header & Tombol Aksi --}}
      <div class="flex flex-col sm:flex-row justify-between items-start mb-8">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">Manajemen Investor</h1>
          <p class="text-gray-500 mt-1">Kelola semua investor yang terdaftar di platform Anda.</p>
        </div>
      </div>

      {{-- Pesan Sukses --}}
      @if(session('success'))
        <div role="alert" class="mb-6 rounded-xl border border-gray-100 bg-white p-4 shadow-sm"><div class="flex items-start gap-4"><span class="text-green-600"><x-heroicon-s-check-circle class="h-6 w-6"/></span><div class="flex-1"><strong class="block font-medium text-gray-900">Sukses!</strong><p class="mt-1 text-sm text-gray-700">{{ session('success') }}</p></div></div></div>
      @endif

      {{-- Kartu Statistik & Pencarian --}}
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md"><p class="text-sm font-medium text-gray-500">Total Investor</p><p class="text-3xl font-bold text-green-600 mt-1">{{ $investorCount }}</p></div>
        <div class="bg-white p-6 rounded-lg shadow-md"><p class="text-sm font-medium text-gray-500">Total Dana Terinvestasi</p><p class="text-3xl font-bold text-indigo-600 mt-1">Rp {{ number_format($totalFunds, 0, ',', '.') }}</p></div>
        {{-- Form Pencarian --}}
        <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
            <form action="{{ route('admin.investors.index') }}" method="GET" class="w-full">
                <label for="search" class="text-sm font-medium text-gray-500">Cari Investor</label>
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><x-heroicon-o-magnifying-glass class="h-5 w-5 text-gray-400"/></div>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md bg-white placeholder-gray-500 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" placeholder="Nama atau email...">
                </div>
            </form>
        </div>
      </div>

      {{-- Tabel Investor --}}
      <div class="bg-white shadow-md rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Investor</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontak</th>
              {{-- PERBAIKAN: Menambahkan kolom MOU --}}
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status MOU</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aktivitas Investasi</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bergabung Pada</th>
              <th scope="col" class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($investors as $user)
            <tr>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-10 w-10">
                    <img class="h-10 w-10 rounded-full object-cover" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=E8EAF6&color=3F51B5" alt="">
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                    <div class="text-sm text-gray-500">ID: {{ $user->id }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ $user->email }}</div>
                <div class="text-sm text-gray-500">{{ $user->investor->phone ?? '-' }}</div>
              </td>
              {{-- PERBAIKAN: Menampilkan status MOU --}}
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                @if($user->investor && $user->investor->mou_path)
                    <a href="{{ Storage::url($user->investor->mou_path) }}" target="_blank" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 hover:bg-green-200">
                        <x-heroicon-s-check-circle class="h-4 w-4 mr-1"/>
                        Sudah Diunggah
                    </a>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        <x-heroicon-s-x-circle class="h-4 w-4 mr-1"/>
                        Belum Ada
                    </span>
                @endif
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">Rp {{ number_format($user->investments_sum_amount ?? 0, 0, ',', '.') }}</div>
                <div class="text-sm text-gray-500">{{ $user->investments_count }} Proyek</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ $user->created_at->format('d M Y') }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                {{-- <a href="{{ route('admin.investors.edit', $user->investor->investor_id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a> --}}
              </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">Tidak ada investor yang ditemukan.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
       <div class="mt-4">{{ $investors->links() }}</div>

    </main>
    
    {{-- Modal untuk Tambah Investor --}}
    <x-modal name="add-investor-modal" :show="$errors->isNotEmpty()" focusable>
      <form method="POST" action="{{ route('admin.investors.store') }}" class="p-6">
        @csrf
        <h2 class="text-lg font-medium text-gray-900">Tambah Investor Baru</h2>
        <p class="mt-1 text-sm text-gray-600">Buat akun pengguna dan profil untuk investor baru.</p>
        <div class="mt-6 space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <x-input-label for="name" :value="__('Nama Lengkap')" />
              <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required />
              <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>
             <div>
              <x-input-label for="phone" :value="__('No. Telepon')" />
              <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone')" required />
              <x-input-error :messages="$errors->get('phone')" class="mt-1" />
            </div>
          </div>
          <div>
            <x-input-label for="email" :value="__('Alamat Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <x-input-label for="password" :value="__('Password')" />
              <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
              <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>
            <div>
              <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
              <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
            </div>
          </div>
        </div>
        <div class="mt-6 flex justify-end">
          <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
          <x-primary-button class="ml-3">Simpan Investor</x-primary-button>
        </div>
      </form>
    </x-modal>
  </div>
</x-app-layout>
