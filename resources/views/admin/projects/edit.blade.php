<x-app-layout>
  <div class="flex h-screen bg-gray-50">
    @include('admin.partials.sidebar')

    <main class="flex-1 overflow-y-auto p-6 lg:p-8">
      
      {{-- Header --}}
      <div class="flex flex-col sm:flex-row justify-between items-start mb-8">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">Edit Proyek</h1>
          <p class="text-gray-500 mt-1">Detail dan pengaturan untuk: <span class="font-semibold text-indigo-600">{{ $project->name }}</span></p>
        </div>
        <a href="{{ route('admin.projects.index') }}" class="mt-4 sm:mt-0 flex-shrink-0 inline-flex items-center gap-2 px-4 py-2 border border-gray-300 bg-white text-gray-700 font-semibold rounded-lg hover:bg-gray-50 shadow-sm">
          <x-heroicon-s-arrow-left class="h-5 w-5"/>
          Kembali ke Daftar
        </a>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Kolom Kiri: Form Edit --}}
        <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-md space-y-6">
          <form method="POST" action="{{ route('admin.projects.update', $project) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            
            {{-- Bagian Detail Utama --}}
            <section>
              <h3 class="text-lg font-medium text-gray-900">Detail Utama</h3>
              <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <x-input-label for="name" :value="__('Nama Proyek')" />
                  <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $project->name)" required />
                  <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>
                <div>
                  <x-input-label for="quantity" :value="__('Total Kuantitas Target (pcs)')" />
                  <x-text-input id="quantity" name="quantity" type="number" class="mt-1 block w-full" :value="old('quantity', $project->quantity)" required />
                  <x-input-error :messages="$errors->get('quantity')" class="mt-1" />
                </div>
              </div>
            </section>

            {{-- Bagian Finansial --}}
            <section>
              <h3 class="text-lg font-medium text-gray-900 mt-6">Finansial</h3>
              <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                 <div>
                  <x-input-label for="price_per_piece" :value="__('Modal/pcs')" />
                  <x-text-input id="price_per_piece" name="price_per_piece" type="number" step="50" class="mt-1 block w-full" :value="old('price_per_piece', $project->price_per_piece)" required />
                </div>
                <div>
                  <x-input-label for="profit" :value="__('Profit Investor/pcs')" />
                  <x-text-input id="profit" name="profit" type="number" step="50" class="mt-1 block w-full" :value="old('profit', $project->profit)" required />
                </div>
                {{-- INPUT BARU --}}
                <div>
                  <x-input-label for="convection_profit" :value="__('Profit Konveksi/pcs')" />
                  <x-text-input id="convection_profit" name="convection_profit" type="number" step="50" class="mt-1 block w-full" :value="old('convection_profit', $project->convection_profit)" required />
                  <x-input-error :messages="$errors->get('convection_profit')" class="mt-1" />
                </div>
                <div>
                  <x-input-label for="wage_per_piece" :value="__('Upah Penjahit/pcs')" />
                  <x-text-input id="wage_per_piece" name="wage_per_piece" type="number" step="50" class="mt-1 block w-full" :value="old('wage_per_piece', $project->wage_per_piece)" required />
                </div>
              </div>
            </section>

            {{-- Bagian Lainnya --}}
            <section>
              <h3 class="text-lg font-medium text-gray-900 mt-6">Pengaturan Lainnya</h3>
              <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-6">
                  <div>
                    <x-input-label for="deadline" :value="__('Deadline')" />
                    <x-text-input id="deadline" name="deadline" type="date" class="mt-1 block w-full" :value="old('deadline', \Carbon\Carbon::parse($project->deadline)->format('Y-m-d'))" required />
                  </div>
                  <div>
                    <x-input-label for="status" :value="__('Status Proyek')" />
                    <select id="status" name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                      <option value="active" @selected(old('status', $project->status) == 'active')>Aktif</option>
                      <option value="inactive" @selected(old('status', $project->status) == 'inactive')>Tidak Aktif</option>
                    </select>
                  </div>
                  <div class="md:col-span-3">
                      <x-input-label for="image" :value="__('Ganti Gambar Proyek (Opsional)')" />
                      <input type="file" name="image" id="image" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                  </div>
              </div>
            </section>
            
            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
              <x-secondary-button type="button" onclick="window.location.href='{{ route('admin.projects.index') }}'">Batal</x-secondary-button>
              <x-primary-button>Simpan Perubahan</x-primary-button>
            </div>
          </form>
        </div>

        {{-- Kolom Kanan: Info & Stakeholder --}}
        <div class="lg:col-span-1 space-y-6">
          <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Status & Progres</h3>
            <div class="space-y-4">
              <div>
                <div class="flex justify-between text-sm"><span class="font-medium text-gray-600">Pendanaan</span><span class="font-semibold text-blue-600">{{ $fundingPercentage }}%</span></div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-1"><div class="bg-blue-600 h-2 rounded-full" style="width: {{ $fundingPercentage }}%"></div></div>
                <div class="text-xs text-gray-500 text-right mt-1">{{ $investedQty }} / {{ $project->quantity }} pcs didanai</div>
              </div>
              <div>
                <div class="flex justify-between text-sm"><span class="font-medium text-gray-600">Produksi</span><span class="font-semibold text-green-600">{{ $productionPercentage }}%</span></div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-1"><div class="bg-green-600 h-2 rounded-full" style="width: {{ $productionPercentage }}%"></div></div>
                <div class="text-xs text-gray-500 text-right mt-1">{{ $completedQty }} / {{ $investedQty }} pcs selesai</div>
              </div>
            </div>
          </div>
          
          <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Ringkasan Keuangan</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">Modal Terkumpul</dt><dd class="font-semibold text-gray-900">Rp {{ number_format($totalFunds, 0,',','.') }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Potensi Profit</dt><dd class="font-semibold text-gray-900">Rp {{ number_format($potentialProfit, 0,',','.') }}</dd></div>
                <div class="flex justify-between pt-2 border-t"><dt class="text-gray-500">Total Biaya Upah</dt><dd class="font-semibold text-gray-900">Rp {{ number_format($totalWageCost, 0,',','.') }}</dd></div>
            </dl>
          </div>

          <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Daftar Investor</h3>
            <ul class="space-y-3 max-h-40 overflow-y-auto">
              @forelse ($project->investments as $investment)
                <li class="flex items-center gap-3 text-sm">
                    <img class="h-8 w-8 rounded-full object-cover" src="https://ui-avatars.com/api/?name={{ urlencode($investment->investor->user->name) }}&background=E8EAF6&color=3F51B5" alt="">
                    <div><p class="font-medium text-gray-800">{{ $investment->investor->user->name }}</p><p class="text-xs text-gray-500">Investasi: {{ $investment->qty }} pcs</p></div>
                </li>
              @empty
                <li class="text-sm text-gray-500">Belum ada investor untuk proyek ini.</li>
              @endforelse
            </ul>
          </div>
        </div>
      </div>
    </main>
  </div>
</x-app-layout>
