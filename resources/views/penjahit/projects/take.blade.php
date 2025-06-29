{{-- resources/views/penjahit/projects/take.blade.php --}}
<x-app-layout>
  <div class="flex h-screen bg-gray-100 text-gray-800">
    @include('penjahit.partials.sidebar')

    <main class="flex-1 overflow-y-auto p-6">
      
      {{-- Tombol Kembali & Judul --}}
      <div class="mb-6">
        <a href="{{ route('penjahit.projects.index') }}" class="flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 mb-2">
          <x-heroicon-s-arrow-left class="h-4 w-4" />
          Kembali ke Daftar Proyek
        </a>
        <h1 class="text-3xl font-bold text-gray-800">Konfirmasi Pengambilan Tugas</h1>
      </div>

      @if(session('error'))
        <div class="mb-6 p-4 bg-red-100 text-red-800 rounded-lg shadow-sm border border-red-200" role="alert">
            <p class="font-semibold">Terjadi Kesalahan</p>
            <p>{{ session('error') }}</p>
        </div>
      @endif

      {{-- Layout Utama 2 Kolom --}}
      <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
        
        {{-- Kolom Kiri: Detail Proyek (Konteks) --}}
        <div class="lg:col-span-3">
          <div class="bg-white rounded-lg shadow-lg p-6">
            @if($project->image)
              <img src="{{ asset('storage/' . $project->image) }}" alt="{{ $project->name }}" class="h-64 w-full object-cover rounded-lg mb-4">
            @endif
            <h2 class="text-2xl font-bold text-gray-900">{{ $project->name }}</h2>
            <p class="text-gray-500">Project ID: #{{ $project->id }}</p>
            
            <div class="mt-4 pt-4 border-t border-gray-200 space-y-3">
              <div class="flex items-center gap-3">
                <x-heroicon-o-calendar-days class="h-6 w-6 text-red-500" />
                <div>
                  <p class="text-sm text-gray-500">Deadline</p>
                  <p class="font-semibold">{{ \Carbon\Carbon::parse($project->deadline)->isoFormat('dddd, D MMMM Y') }}</p>
                </div>
              </div>
              <div class="flex items-center gap-3">
                <x-heroicon-o-currency-dollar class="h-6 w-6 text-green-500" />
                <div>
                  <p class="text-sm text-gray-500">Upah per Pcs</p>
                  <p class="font-semibold">Rp {{ number_format($project->wage_per_piece ?? 10000, 0, ',', '.') }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- Kolom Kanan: Form Aksi --}}
        <div class="lg:col-span-2">
          <form action="{{ route('penjahit.projects.store', $project) }}" method="POST" class="bg-white rounded-lg shadow-lg p-6 space-y-6">
            @csrf
            
            <div>
              <h3 class="text-xl font-semibold text-teal-700">Tentukan Jumlah</h3>
              <p class="text-sm text-gray-500 mt-1">Slot tersedia untuk diambil saat ini adalah <span class="font-bold text-black">{{ $remaining }}</span> pcs.</p>
            </div>

            {{-- Cek jika masih ada slot --}}
            @if ($remaining > 0)
              <div>
                <label for="qty" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Qty yang Diambil</label>
                <input id="qty" name="qty" type="number" min="1" max="{{ $remaining }}" value="{{ old('qty', 1) }}" required
                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500 text-lg">
                <x-input-error :messages="$errors->get('qty')" class="mt-2" />
                <p class="text-xs text-gray-500 mt-1">Masukkan jumlah antara 1 dan {{ $remaining }}.</p>
              </div>

              {{-- Peringatan Komitmen --}}
              <div class="p-4 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700">
                <p class="font-bold">Perhatian</p>
                <p class="text-sm">Pastikan Anda sanggup menyelesaikan pekerjaan sesuai jumlah yang Anda ambil sebelum tanggal deadline.</p>
              </div>

              <div class="flex flex-col gap-3">
                <button type="submit"
                        class="w-full px-4 py-3 bg-teal-600 text-white font-semibold rounded-lg hover:bg-teal-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-transform transform hover:scale-105">
                  Ya, Saya Ambil Tugas Ini
                </button>
                <a href="{{ route('penjahit.projects.index') }}"
                   class="w-full text-center px-4 py-2 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-100">
                  Batal
                </a>
              </div>
            @else
              {{-- Tampilan jika slot habis --}}
              <div class="text-center p-4 bg-gray-50 rounded-lg">
                <x-heroicon-o-x-circle class="w-12 h-12 text-gray-400 mx-auto" />
                <p class="mt-2 font-semibold text-gray-700">Slot Habis</p>
                <p class="text-sm text-gray-500">Maaf, semua kuota untuk proyek ini sudah diambil.</p>
              </div>
            @endif

          </form>
        </div>

      </div>
    </main>
  </div>
</x-app-layout>