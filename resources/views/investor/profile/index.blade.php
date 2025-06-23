<x-app-layout>
  <div class="flex h-screen bg-gray-50">
    @include('investor.partials.sidebar')

    <main class="flex-1 overflow-y-auto p-6 lg:p-8">
      <h1 class="text-3xl font-bold text-gray-800 mb-6">Profil Investor</h1>

      {{-- Pesan Peringatan & Sukses --}}
      @if(session('warning'))
        <div class="mb-6 p-4 bg-yellow-100 border-l-4 border-yellow-400 text-yellow-700">
          <p class="font-bold">Perhatian</p>
          <p>{{ session('warning') }}</p>
        </div>
      @endif
      @if(session('success'))
        <div role="alert" class="mb-6 rounded-xl border border-gray-100 bg-white p-4 shadow-sm"><div class="flex items-start gap-4"><span class="text-green-600"><x-heroicon-s-check-circle class="h-6 w-6"/></span><div class="flex-1"><strong class="block font-medium text-gray-900">Sukses!</strong><p class="mt-1 text-sm text-gray-700">{{ session('success') }}</p></div></div></div>
      @endif

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Kolom Kiri: Form & Keamanan --}}
        <div class="lg:col-span-2 space-y-8">
          <!-- Form Informasi Profil -->
          <div class="bg-white shadow-md rounded-lg p-6">
            <section>
              <header>
                <h2 class="text-lg font-medium text-gray-900">Informasi Profil</h2>
                <p class="mt-1 text-sm text-gray-600">Lengkapi atau perbarui informasi profil dan kontak Anda.</p>
              </header>
              <form action="{{ route('investor.profile.update') }}" method="POST" class="mt-6 space-y-6">
                @csrf
                <div>
                  <x-input-label for="name" :value="__('Nama Lengkap')" />
                  <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus />
                  <x-input-error :messages="$errors->get('name')" class="mt-2"/>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="email" :value="__('Alamat Email (Tidak dapat diubah)')" />
                        <x-text-input id="email" type="email" class="mt-1 block w-full bg-gray-100" :value="$user->email" disabled />
                    </div>
                    <div>
                        <x-input-label for="phone" :value="__('No. Telepon Aktif')" />
                        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $investor->phone ?? '')" required />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2"/>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                  <x-primary-button>Simpan Perubahan</x-primary-button>
                </div>
              </form>
            </section>
          </div>
          <!-- Keamanan Akun (Menggunakan partials dari Penjahit) -->
          <div class="bg-white shadow-md rounded-lg p-6">
            @include('penjahit.partials.update-password-form')
          </div>
        </div>

        {{-- Kolom Kanan: Ringkasan Portofolio --}}
        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-lg shadow-md sticky top-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Ringkasan Portofolio</h3>
                <div class="space-y-4">
                    <div class="p-4 rounded-lg bg-blue-50">
                        <p class="text-sm font-medium text-blue-600">Total Dana Diinvestasikan</p>
                        <p class="text-2xl font-bold text-blue-800 mt-1">Rp {{ number_format($totalInvested, 0, ',', '.') }}</p>
                    </div>
                    <div class="p-4 rounded-lg bg-green-50">
                        <p class="text-sm font-medium text-green-600">Estimasi Total Profit</p>
                        <p class="text-2xl font-bold text-green-800 mt-1">Rp {{ number_format($estimatedProfit, 0, ',', '.') }}</p>
                    </div>
                    <div class="p-4 rounded-lg bg-indigo-50">
                        <p class="text-sm font-medium text-indigo-600">Jumlah Proyek Diikuti</p>
                        <p class="text-2xl font-bold text-indigo-800 mt-1">{{ $projectsCount }} Proyek</p>
                    </div>
                </div>
                 <a href="{{ route('investor.investments.index') }}" class="mt-6 w-full flex items-center justify-center gap-2 text-center px-4 py-3 bg-gray-800 text-white text-sm font-semibold rounded-lg hover:bg-gray-700 shadow-md">
                    <x-heroicon-s-wallet class="h-5 w-5"/> Lihat Riwayat Investasi
                </a>
            </div>
        </div>
      </div>
    </main>
  </div>
</x-app-layout>
