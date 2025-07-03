<x-app-layout>
  <div class="flex h-screen bg-gray-50">
    @include('investor.partials.sidebar')

    <main class="flex-1 overflow-y-auto p-6 lg:p-8">
      
      {{-- Header --}}
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Riwayat Investasi Saya</h1>
        <p class="text-gray-500 mt-1">Lacak semua investasi Anda, mulai dari pengajuan hingga progres produksi.</p>
      </div>

      {{-- Pesan Sukses --}}
      @if(session('success'))
        <div role="alert" class="mb-6 rounded-xl border border-gray-100 bg-white p-4 shadow-sm"><div class="flex items-start gap-4"><span class="text-green-600"><x-heroicon-s-check-circle class="h-6 w-6"/></span><div class="flex-1"><strong class="block font-medium text-gray-900">Sukses!</strong><p class="mt-1 text-sm text-gray-700">{{ session('success') }}</p></div></div></div>
      @endif

      {{-- Navigasi Tab --}}
      <div class="mb-6 border-b border-gray-200">
        <nav class="-mb-px flex gap-6">
          <a href="{{ route('investor.investments.index', ['tab' => 'all']) }}" @class(['shrink-0 border-b-2 px-1 pb-4 text-sm font-medium', 'border-indigo-500 text-indigo-600' => $tab == 'all', 'border-transparent text-gray-500 hover:text-gray-700' => $tab != 'all'])>Semua</a>
          <a href="{{ route('investor.investments.index', ['tab' => 'pending']) }}" @class(['shrink-0 border-b-2 px-1 pb-4 text-sm font-medium', 'border-indigo-500 text-indigo-600' => $tab == 'pending', 'border-transparent text-gray-500 hover:text-gray-700' => $tab != 'pending'])>Menunggu Persetujuan</a>
          <a href="{{ route('investor.investments.index', ['tab' => 'active']) }}" @class(['shrink-0 border-b-2 px-1 pb-4 text-sm font-medium', 'border-indigo-500 text-indigo-600' => $tab == 'active', 'border-transparent text-gray-500 hover:text-gray-700' => $tab != 'active'])>Aktif & Selesai</a>
        </nav>
      </div>

      {{-- Layout Kartu Investasi --}}
      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
        @forelse ($investments as $investment)
          <div class="bg-white rounded-xl shadow-lg overflow-hidden flex flex-col">
            {{-- Header Kartu dengan Gambar Proyek --}}
            <div class="relative">
              @if($investment->project->image)
                <img src="{{ asset('storage/' . $investment->project->image) }}" alt="{{ $investment->project->name }}" class="h-48 w-full object-cover">
              @else
                <div class="h-48 w-full bg-gray-200 flex items-center justify-center"><x-heroicon-o-photo class="w-16 h-16 text-gray-400"/></div>
              @endif
              {{-- Badge Status --}}
              <span @class(['absolute top-3 right-3 px-2 py-1 text-xs font-semibold rounded-full text-white shadow', 'bg-yellow-500' => !$investment->approved, 'bg-green-500' => $investment->approved ])>
                {{ $investment->approved ? 'Disetujui' : 'Pending' }}
              </span>
            </div>
            
            <div class="p-5 flex flex-col flex-grow">
              <h3 class="text-lg font-bold text-gray-900 leading-tight">{{ $investment->project->name }}</h3>
              <p class="text-sm text-gray-500 mt-1">Diajukan pada: {{ $investment->created_at->format('d M Y') }}</p>
              
              {{-- Detail Investasi --}}
              <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-2 gap-4 text-sm">
                  <div><p class="text-xs text-gray-500">Dana Anda</p><p class="font-bold text-gray-800">Rp {{ number_format($investment->amount, 0, ',', '.') }}</p></div>
                  <div><p class="text-xs text-gray-500">Jumlah Slot</p><p class="font-bold text-gray-800">{{ $investment->qty }} pcs</p></div>
              </div>

              {{-- ==================================================================== --}}
              {{-- AWAL PERUBAHAN: Tampilan progres produksi disesuaikan --}}
              {{-- ==================================================================== --}}
              @if($investment->approved)
                <div class="mt-4">
                  <div class="flex justify-between items-center mb-1 text-sm">
                    <span class="font-medium text-gray-600">Progres Produksi</span>
                    <span class="font-semibold text-teal-600">{{ $investment->production_progress }}%</span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-teal-500 h-2 rounded-full" style="width: {{ $investment->production_progress }}%"></div>
                  </div>
                  <p class="text-xs text-gray-500 text-right mt-1">
                    {{ $investment->production_completed_qty }} / {{ $investment->production_target_qty }} pcs selesai
                  </p>
                </div>
              @endif
              {{-- ==================================================================== --}}
              {{-- AKHIR PERUBAHAN --}}
              {{-- ==================================================================== --}}

              <div class="mt-5 pt-4 border-t border-gray-100 flex-grow flex items-end">
                  {{-- Tombol Aksi --}}
                  @if(!$investment->approved)
                    <div class="flex items-center gap-2 w-full">
                      <form action="{{ route('investor.investments.destroy', $investment) }}" method="POST" class="w-full" onsubmit="return confirm('Anda yakin ingin membatalkan pengajuan investasi ini?')">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="w-full text-center px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700">Batalkan</button>
                      </form>
                    </div>
                  @else
                    <a href="{{ route('investor.investments.show', $investment) }}" class="w-full text-center px-4 py-2 bg-gray-800 text-white text-sm font-semibold rounded-lg hover:bg-gray-700">Lihat Detail</a>
                  @endif
              </div>
            </div>
          </div>
        @empty
          <div class="md:col-span-2 xl:col-span-3 bg-white text-center rounded-lg shadow p-10">
            <x-heroicon-o-wallet class="w-16 h-16 mx-auto text-gray-300" />
            <h3 class="mt-4 text-lg font-medium text-gray-900">Tidak Ada Investasi</h3>
            <p class="mt-1 text-sm text-gray-500">Anda belum memiliki investasi pada kategori ini.</p>
          </div>
        @endforelse
      </div>

       <div class="mt-8">{{ $investments->links() }}</div>

    </main>
  </div>
</x-app-layout>
