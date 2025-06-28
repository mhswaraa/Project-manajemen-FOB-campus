<x-app-layout>
  <div class="flex h-screen bg-gray-50">
    @include('admin.partials.sidebar')

    <main class="flex-1 overflow-y-auto p-6 lg:p-8">
      <div class="max-w-4xl mx-auto">
        {{-- Header Halaman --}}
        <div class="flex flex-col sm:flex-row justify-between items-start mb-8">
          <div>
            <h1 class="text-3xl font-bold text-gray-800">Tambah Pengguna Baru</h1>
            <p class="text-gray-500 mt-1">Buat akun baru dan tentukan perannya dalam sistem.</p>
          </div>
          <a href="{{ route('admin.users.index') }}" class="mt-4 sm:mt-0 flex-shrink-0 inline-flex items-center gap-2 px-4 py-2 border border-gray-300 bg-white text-gray-700 font-semibold rounded-lg hover:bg-gray-50 shadow-sm">
            <x-heroicon-s-arrow-left class="h-5 w-5" />
            Kembali ke Daftar
          </a>
        </div>

        {{-- Kartu Form Utama dengan Alpine.js untuk state management --}}
        <div class="bg-white p-8 rounded-xl shadow-md" x-data="{ role: '{{ old('role', '') }}' }">
          <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
            @csrf
            
            {{-- Bagian Detail Pengguna --}}
            <div>
              <h3 class="text-lg font-medium text-gray-900">Detail Pengguna</h3>
              <p class="mt-1 text-sm text-gray-500">Masukkan informasi dasar untuk pengguna baru.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-gray-200 pt-6">
              {{-- Nama Lengkap --}}
              <div>
                <x-input-label for="name" :value="__('Nama Lengkap')" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
              </div>

              {{-- Email --}}
              <div>
                <x-input-label for="email" :value="__('Alamat Email')" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
              </div>
            </div>
            
            {{-- Role --}}
            <div>
              <x-input-label for="role" :value="__('Peran (Role)')" />
              <select name="role" id="role" x-model="role" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                  <option value="" disabled>— Pilih Role —</option>
                  <option value="admin">Admin</option>
                  <option value="ceo">CEO</option>
                  <option value="investor">Investor</option>
                  <option value="penjahit">Penjahit</option>
              </select>
              <x-input-error :messages="$errors->get('role')" class="mt-2" />
            </div>

            {{-- Input Gdrive Link yang kondisional --}}
            <div x-show="role === 'investor' || role === 'penjahit'" x-transition>
                <x-input-label for="gdrive_link" :value="__('Link Folder Google Drive (Opsional)')" />
                <x-text-input id="gdrive_link" name="gdrive_link" type="url" class="mt-1 block w-full" :value="old('gdrive_link')" placeholder="https://drive.google.com/..." />
                <p class="mt-1 text-xs text-gray-500">Link ini akan digunakan untuk mengirim dokumen.</p>
                <x-input-error :messages="$errors->get('gdrive_link')" class="mt-2" />
            </div>

            {{-- Bagian Kredensial --}}
             <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900">Kredensial Login</h3>
                <p class="mt-1 text-sm text-gray-500">Atur password awal untuk pengguna. Password ini dapat diubah nanti.</p>
            </div>

            {{-- Password --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
              </div>
              <div>
                <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
                <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
              </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex items-center justify-end pt-6 border-t border-gray-200">
              <x-primary-button>{{ __('Simpan Pengguna') }}</x-primary-button>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
</x-app-layout>
