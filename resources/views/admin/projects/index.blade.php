<x-app-layout>
  <div class="flex h-screen bg-gray-50">
    @include('admin.partials.sidebar')

    <main class="flex-1 overflow-y-auto p-6 lg:p-8">
      
      {{-- Header & Tombol Aksi --}}
      <div class="flex flex-col sm:flex-row justify-between items-start mb-8">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">Manajemen Proyek</h1>
          <p class="text-gray-500 mt-1">Buat, pantau, dan kelola semua proyek dari sini.</p>
        </div>
        <button x-data @click.prevent="$dispatch('open-modal', 'add-project-modal')"
                class="mt-4 sm:mt-0 flex-shrink-0 inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
          <x-heroicon-s-plus class="h-5 w-5"/>
          Tambah Proyek Baru
        </button>
      </div>

      {{-- Pesan Sukses --}}
      @if(session('success'))
        <div role="alert" class="mb-6 rounded-xl border border-gray-100 bg-white p-4 shadow-sm"><div class="flex items-start gap-4"><span class="text-green-600"><x-heroicon-s-check-circle class="h-6 w-6"/></span><div class="flex-1"><strong class="block font-medium text-gray-900">Sukses!</strong><p class="mt-1 text-sm text-gray-700">{{ session('success') }}</p></div></div></div>
      @endif

      {{-- Kartu Statistik --}}
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md"><p class="text-sm font-medium text-gray-500">Proyek Aktif</p><p class="text-3xl font-bold text-indigo-600 mt-1">{{ $activeProjectsCount }}</p></div>
        <div class="bg-white p-6 rounded-lg shadow-md"><p class="text-sm font-medium text-gray-500">Proyek Butuh Dana</p><p class="text-3xl font-bold text-yellow-600 mt-1">{{ $fundingNeededCount }}</p></div>
        <div class="bg-white p-6 rounded-lg shadow-md"><p class="text-sm font-medium text-gray-500">Proyek Selesai</p><p class="text-3xl font-bold text-green-600 mt-1">{{ $completedProjectsCount }}</p></div>
      </div>

      {{-- Tabel Proyek yang Ditingkatkan --}}
      <div class="bg-white shadow-md rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proyek</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress Pendanaan</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress Produksi</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th scope="col" class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($projects as $project)
            @php
                $investedQty = $project->invested_qty ?? 0;
                $fundingPercentage = $project->quantity > 0 ? round(($investedQty / $project->quantity) * 100) : 0;
                
                $completedQty = $project->completed_qty ?? 0;
                $productionPercentage = $investedQty > 0 ? round(($completedQty / $investedQty) * 100) : 0;
            @endphp
            <tr>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-10 w-10">
                    @if($project->image)
                        <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $project->image) }}" alt="">
                    @else
                        <span class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center"><x-heroicon-o-photo class="h-6 w-6 text-gray-400"/></span>
                    @endif
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">{{ $project->name }}</div>
                    <div class="text-sm text-gray-500">Target: {{ $project->quantity }} pcs</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ $investedQty }} / {{ $project->quantity }} pcs</div>
                <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1"><div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $fundingPercentage }}%"></div></div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ $completedQty }} / {{ $investedQty }} pcs</div>
                <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1"><div class="bg-green-600 h-1.5 rounded-full" style="width: {{ $productionPercentage }}%"></div></div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                  <span @class(['px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                      'bg-green-100 text-green-800' => $project->status == 'active',
                      'bg-red-100 text-red-800' => $project->status != 'active',
                  ])>
                      {{ ucfirst($project->status) }}
                  </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <a href="{{ route('admin.projects.edit', $project) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                <form action="{{ route('admin.projects.destroy', $project) }}" method="POST" class="inline-block ml-2" onsubmit="return confirm('Yakin ingin menghapus proyek ini? Ini tidak bisa dibatalkan.');">
                  @csrf @method('DELETE')
                  <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                </form>
              </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">Belum ada proyek yang dibuat. Silakan tambahkan proyek baru.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- ... (kode sebelum modal) ... --}}
      {{-- Modal untuk Tambah Proyek --}}
      <x-modal name="add-project-modal" :show="$errors->isNotEmpty()" focusable>
        <form method="POST" action="{{ route('admin.projects.store') }}" enctype="multipart/form-data" class="p-6">
          @csrf
          <h2 class="text-lg font-medium text-gray-900">Tambah Proyek Baru</h2>
          <p class="mt-1 text-sm text-gray-600">Isi detail proyek untuk membuka pendanaan bagi investor.</p>
          <div class="mt-6 space-y-4">
            <input type="hidden" name="status" value="active">
            {{-- ... (input nama, qty, deadline) ... --}}
            <div><x-input-label for="name" :value="__('Nama Proyek')" /><x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required /><x-input-error :messages="$errors->get('name')" class="mt-1" /></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><x-input-label for="quantity" :value="__('Total Kuantitas (pcs)')" /><x-text-input id="quantity" name="quantity" type="number" class="mt-1 block w-full" :value="old('quantity')" required /><x-input-error :messages="$errors->get('quantity')" class="mt-1" /></div>
                <div><x-input-label for="deadline" :value="__('Deadline')" /><x-text-input id="deadline" name="deadline" type="date" class="mt-1 block w-full" :value="old('deadline')" required /><x-input-error :messages="$errors->get('deadline')" class="mt-1" /></div>
            </div>
             <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div><x-input-label for="price_per_piece" :value="__('Modal/pcs')" /><x-text-input id="price_per_piece" name="price_per_piece" type="number" step="50" class="mt-1 block w-full" :value="old('price_per_piece')" required /><x-input-error :messages="$errors->get('price_per_piece')" class="mt-1" /></div>
                <div><x-input-label for="profit" :value="__('Profit Investor/pcs')" /><x-text-input id="profit" name="profit" type="number" step="50" class="mt-1 block w-full" :value="old('profit')" required /><x-input-error :messages="$errors->get('profit')" class="mt-1" /></div>
                {{-- INPUT BARU --}}
                <div><x-input-label for="convection_profit" :value="__('Profit Konveksi/pcs')" /><x-text-input id="convection_profit" name="convection_profit" type="number" step="50" class="mt-1 block w-full" :value="old('convection_profit')" required /><x-input-error :messages="$errors->get('convection_profit')" class="mt-1" /></div>
                <div><x-input-label for="wage_per_piece" :value="__('Upah Penjahit/pcs')" /><x-text-input id="wage_per_piece" name="wage_per_piece" type="number" step="50" class="mt-1 block w-full" :value="old('wage_per_piece')" required /><x-input-error :messages="$errors->get('wage_per_piece')" class="mt-1" /></div>
            </div>
            <div><x-input-label for="image" :value="__('Gambar Proyek (Opsional)')" /><input type="file" name="image" id="image" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"><x-input-error :messages="$errors->get('image')" class="mt-1" /></div>
          </div>
          <div class="mt-6 flex justify-end"><x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button><x-primary-button class="ml-3">Simpan Proyek</x-primary-button></div>
        </form>
      </x-modal>

    </main>
  </div>
</x-app-layout>