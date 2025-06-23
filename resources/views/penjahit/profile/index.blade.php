{{-- resources/views/penjahit/profile/index.blade.php --}}
<x-app-layout>
  <div class="flex h-screen bg-gray-100 text-gray-800">
    @include('penjahit.partials.sidebar')

    <main class="flex-1 overflow-y-auto p-6">
      <h1 class="text-3xl font-bold text-gray-800 mb-6">Pengaturan Profil & Portofolio</h1>

      {{-- Pesan Peringatan & Sukses --}}
      @if(session('warning'))
        <div class="mb-6 p-4 bg-yellow-100 border-l-4 border-yellow-400 text-yellow-700">
          <p class="font-bold">Perhatian</p>
          <p>{{ session('warning') }}</p>
        </div>
      @endif
      @if(session('success'))
        <div role="alert" class="mb-6 rounded-xl border border-gray-100 bg-white p-4">
            <div class="flex items-start gap-4">
                <span class="text-green-600"><x-heroicon-s-check-circle class="h-6 w-6"/></span>
                <div class="flex-1">
                    <strong class="block font-medium text-gray-900"> Sukses! </strong>
                    <p class="mt-1 text-sm text-gray-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
      @endif

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Kolom Kiri: Form Utama & Keamanan --}}
        <div class="lg:col-span-2 space-y-8">
          <!-- Kartu 1: Informasi Profil & Spesialisasi -->
          <div class="bg-white shadow-md rounded-lg p-6">
            <section>
              <header>
                <h2 class="text-lg font-medium text-gray-900">Informasi & Keahlian</h2>
                <p class="mt-1 text-sm text-gray-600">Perbarui data profil, kontak, dan daftar keahlian yang Anda miliki.</p>
              </header>
              <form action="{{ route('penjahit.profile.update') }}" method="POST" class="mt-6 space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="name" :value="__('Nama Lengkap')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2"/>
                    </div>
                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2"/>
                    </div>
                    <div>
                        <x-input-label for="phone" :value="__('No. HP')" />
                        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $tailor->phone ?? '')" required />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2"/>
                    </div>
                     <div>
                        <x-input-label for="status" :value="__('Status Ketersediaan')" />
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500" required>
                            <option value="available" @selected(old('status', $tailor->status ?? '') == 'available')>Available</option>
                            <option value="busy" @selected(old('status', $tailor->status ?? '') == 'busy')>Busy</option>
                            <option value="inactive" @selected(old('status', $tailor->status ?? '') == 'inactive')>Inactive</option>
                        </select>
                    </div>
                </div>
                <div>
                    <x-input-label for="address" :value="__('Alamat')" />
                    <textarea id="address" name="address" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-teal-500 focus:ring-teal-500" required>{{ old('address', $tailor->address ?? '') }}</textarea>
                </div>
                <div>
                    <x-input-label for="specializations" :value="__('Spesialisasi / Keahlian (Pilih beberapa)')" />
                    <select name="specializations[]" id="specializations" multiple class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                        @foreach ($specializations as $spec)
                            <option value="{{ $spec->id }}" @selected($tailor && $tailor->specializations->contains($spec))>
                                {{ $spec->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Tahan Ctrl (atau Cmd di Mac) untuk memilih lebih dari satu.</p>
                </div>
                <div class="flex items-center gap-4">
                  <x-primary-button>Simpan Profil & Keahlian</x-primary-button>
                </div>
              </form>
            </section>
          </div>

          <!-- Kartu 2: Ubah Password -->
          <div class="bg-white shadow-md rounded-lg p-6">
            @include('penjahit.partials.update-password-form')
          </div>
        </div>

        {{-- Kolom Kanan: Portofolio --}}
        <div class="lg:col-span-1 space-y-8">
            <div class="bg-white shadow-md rounded-lg p-6">
                <header>
                    <h2 class="text-lg font-medium text-gray-900">Portofolio Anda</h2>
                    <p class="mt-1 text-sm text-gray-600">Unggah foto-foto hasil kerja terbaik Anda untuk dilihat oleh Admin.</p>
                </header>
                <form action="{{ route('penjahit.profile.portfolio.add') }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="portfolio_image" :value="__('Pilih Gambar (max: 2MB)')"/>
                        <input type="file" name="portfolio_image" id="portfolio_image" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100" required>
                        <x-input-error :messages="$errors->get('portfolio_image')" class="mt-2"/>
                    </div>
                     <div>
                        <x-input-label for="portfolio_caption" :value="__('Keterangan (opsional)')"/>
                        <x-text-input type="text" name="portfolio_caption" id="portfolio_caption" class="mt-1 block w-full" placeholder="Cth: Kemeja Batik Tulis"/>
                    </div>
                    <x-primary-button class="w-full justify-center">
                        <x-heroicon-s-arrow-up-tray class="w-5 h-5 mr-2"/>
                        Unggah Portofolio
                    </x-primary-button>
                </form>
            </div>
            
            {{-- Galeri Portofolio --}}
            @if($tailor && $tailor->portfolios->isNotEmpty())
            <div class="bg-white shadow-md rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Galeri</h3>
                <div class="grid grid-cols-2 gap-4">
                    @foreach ($tailor->portfolios as $item)
                    <div class="relative group">
                        <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->caption ?? 'Portfolio' }}" class="rounded-md object-cover h-32 w-full">
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-60 transition-all duration-300 flex items-center justify-center rounded-md">
                           <form action="{{ route('penjahit.profile.portfolio.delete', $item) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus gambar ini?')">
                               @csrf
                               @method('DELETE')
                               <button type="submit" class="p-2 bg-red-600 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity">
                                   <x-heroicon-s-trash class="w-5 h-5"/>
                               </button>
                           </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
      </div>
    </main>
  </div>
</x-app-layout>