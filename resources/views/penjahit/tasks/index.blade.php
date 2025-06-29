{{-- resources/views/penjahit/tasks/index.blade.php --}}
<x-app-layout>
  <div class="flex h-screen bg-gray-100 text-gray-800">
    @include('penjahit.partials.sidebar')

    <main class="flex-1 overflow-y-auto p-6">
      <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Tugas Saya</h1>
        <p class="text-gray-500 mt-1">Berikut adalah semua proyek yang sedang dan telah Anda kerjakan.</p>
      </div>

      {{-- Menampilkan pesan sukses --}}
      @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-lg shadow-sm">
            {{ session('success') }}
        </div>
      @endif
      @if(session('error'))
          <div class="mb-6 p-4 bg-red-100 text-red-800 rounded-lg shadow-sm">
              {{ session('error') }}
          </div>
      @endif

      {{-- Layout Kartu --}}
      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse ($assignments as $task)
          {{-- ==================================================================== --}}
          {{-- AWAL PERUBAHAN: Logika kalkulasi disesuaikan dengan status QC --}}
          {{-- ==================================================================== --}}
          @php
            $project = $task->project;
            $assigned = $task->assigned_qty;

            // Hitung progres HANYA dari yang sudah diterima QC
            $acceptedQty = $task->progress->where('status', 'approved')->sum('accepted_qty');
            
            // Hitung juga yang masih menunggu QC untuk ditampilkan sebagai catatan
            $pendingQty = $task->progress->where('status', 'pending_qc')->sum('quantity_done');
            
            $percentage = $assigned > 0 ? round(($acceptedQty / $assigned) * 100) : 0;
            
            // Logika untuk tombol hapus: bisa dihapus jika belum ada laporan sama sekali
            $hasAnyProgress = $task->progress->isNotEmpty();
          @endphp
          {{-- ==================================================================== --}}
          {{-- AKHIR PERUBAHAN --}}
          {{-- ==================================================================== --}}

          <div class="bg-white rounded-xl shadow-lg overflow-hidden flex flex-col">
            <div class="relative">
              @if($project->image)
                <img src="{{ asset('storage/' . $project->image) }}" alt="{{ $project->name }}" class="h-48 w-full object-cover">
              @else
                <div class="h-48 w-full bg-gray-200 flex items-center justify-center">
                  <x-heroicon-o-photo class="w-16 h-16 text-gray-400"/>
                </div>
              @endif
              <span @class([
                  'absolute top-3 right-3 px-2 py-1 text-xs font-semibold rounded-full text-white',
                  'bg-yellow-500' => $task->status === 'in_progress',
                  'bg-green-500' => $task->status === 'completed',
                  'bg-gray-500' => $task->status === 'pending',
              ])>
                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
              </span>
            </div>
            
            <div class="p-5 flex flex-col flex-grow">
              <h3 class="text-lg font-bold text-gray-900 leading-tight">{{ $project->name }}</h3>
              <p class="text-sm text-gray-500 mt-1 flex items-center gap-1.5">
                <x-heroicon-s-calendar class="w-4 h-4 text-gray-400"/>
                Deadline: {{ \Carbon\Carbon::parse($project->deadline)->format('d M Y') }}
              </p>

              {{-- PERUBAHAN: Tampilan progres diperbarui --}}
              <div class="mt-4">
                <div class="flex justify-between items-center mb-1 text-sm">
                  <span class="font-medium text-gray-600">Progress Diterima</span>
                  <span class="font-semibold text-teal-600">{{ $acceptedQty }} / {{ $assigned }} pcs</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                  <div class="bg-teal-500 h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                </div>
                 @if($pendingQty > 0)
                    <p class="text-xs text-yellow-600 italic mt-1 text-right">{{ $pendingQty }} pcs menunggu QC.</p>
                @endif
              </div>

              <div class="mt-5 pt-4 border-t border-gray-100 flex-grow flex items-end">
                <div class="w-full flex items-center gap-2">
                  <a href="{{ route('penjahit.tasks.show', $task) }}" class="flex-1 text-center px-4 py-2 bg-gray-800 text-white text-sm font-semibold rounded-lg hover:bg-gray-700 shadow-md">
                    Update & Lihat Detail
                  </a>

                  {{-- PERUBAHAN: Logika tombol hapus disesuaikan --}}
                  @if(!$hasAnyProgress)
                    <form action="{{ route('penjahit.tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Anda yakin ingin membatalkan tugas ini?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="p-2 bg-red-600 text-white rounded-lg hover:bg-red-700" title="Batalkan Tugas">
                        <x-heroicon-o-trash class="w-5 h-5" />
                      </button>
                    </form>
                  @endif
                </div>
              </div>
            </div>
          </div>
        @empty
          {{-- Tampilan jika tidak ada tugas sama sekali --}}
          <div class="md:col-span-2 xl:col-span-3 bg-white text-center rounded-lg shadow-lg p-10">
            <x-heroicon-o-document-magnifying-glass class="w-16 h-16 mx-auto text-gray-300" />
            <h3 class="mt-4 text-lg font-medium text-gray-900">Anda Belum Memiliki Tugas</h3>
            <p class="mt-1 text-sm text-gray-500">Silakan ambil tugas dari halaman daftar proyek yang tersedia.</p>
            <a href="{{ route('penjahit.projects.index') }}" class="mt-6 inline-block px-5 py-2.5 bg-teal-600 text-white text-sm font-semibold rounded-lg hover:bg-teal-700 shadow-sm">
              Cari Proyek Sekarang
            </a>
          </div>
        @endforelse
      </div>
    </main>
  </div>
</x-app-layout>
