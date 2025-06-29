{{-- resources/views/penjahit/tasks/show.blade.php --}}
<x-app-layout>
  <div x-data="{ showModal: false }" @keydown.escape.window="showModal = false">
    <div class="flex h-screen bg-gray-100 text-gray-800">
      @include('penjahit.partials.sidebar')

      <main class="flex-1 overflow-y-auto p-6">
        
        {{-- Tombol Kembali & Judul --}}
        <div class="mb-4">
          <a href="{{ route('penjahit.tasks.index') }}" class="flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
            <x-heroicon-s-arrow-left class="h-4 w-4" />
            Kembali ke Daftar Tugas
          </a>
        </div>

        @php
          // Kalkulasi data...
          $project = $assignment->project;
          $assignedQty = $assignment->assigned_qty;
          $doneQty = $assignment->progress->sum('quantity_done');
          $remainingQty = $assignedQty - $doneQty;
          $percentage = $assignedQty > 0 ? round(($doneQty / $assignedQty) * 100) : 0;
          $deadline = \Carbon\Carbon::parse($project->deadline);
        @endphp

        {{-- Informasi Tugas & Progress Bar --}}
        <div class="mb-6 bg-white p-6 rounded-lg shadow-md">
            {{-- Konten tidak berubah... --}}
            <div class="flex flex-col md:flex-row justify-between items-start">
              <div>
                <h1 class="text-3xl font-bold text-gray-800">{{ $project->name }}</h1>
                <p class="text-gray-500">Assignment ID: #{{ $assignment->id }}</p>
              </div>
              <div class="mt-4 md:mt-0 md:text-right">
                <p class="text-sm text-gray-500">Deadline Proyek</p>
                <p class="text-lg font-semibold text-red-600">{{ $deadline->format('d F Y') }}</p>
              </div>
            </div>
            <div class="mt-6">
              <div class="flex justify-between items-center mb-1">
                <span class="text-lg font-semibold text-teal-600">Progress: {{ $percentage }}%</span>
                <span class="text-sm font-medium text-gray-600">{{ $doneQty }} / {{ $assignedQty }} pcs</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-4">
                <div class="bg-gradient-to-r from-teal-400 to-teal-600 h-4 rounded-full" style="width: {{ $percentage }}%"></div>
              </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <div class="lg:col-span-2">
            {{-- Riwayat Progres --}}
            <div class="bg-white p-6 rounded-lg shadow-md h-full">
              <h3 class="text-xl font-semibold mb-4 text-gray-700">Riwayat Laporan Harian</h3>
              {{-- Notifikasi Sukses/Error --}}
              @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-lg text-sm">{{ session('success') }}</div>
              @endif
              @if(session('error'))
                <div class="mb-4 p-3 bg-red-100 text-red-800 rounded-lg text-sm">{{ session('error') }}</div>
              @endif
              <div class="overflow-y-auto max-h-96">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50 sticky top-0">
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                      <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($assignment->progress->sortByDesc('date') as $prog)
                      <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ \Carbon\Carbon::parse($prog->date)->format('d M Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 font-bold">{{ $prog->quantity_done }} pcs</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 italic">{{ $prog->notes ?: '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                          <div class="flex items-center justify-end gap-2">
                            {{-- AWAL PERUBAHAN TOMBOL EDIT --}}
                            <button type="button" @click="showModal = true"
                                    class="edit-progress-btn text-indigo-600 hover:text-indigo-900"
                                    data-action="{{ route('penjahit.tasks.progress.update', $prog->id) }}"
                                    data-quantity="{{ $prog->quantity_done }}"
                                    data-notes="{{ $prog->notes }}"
                                    data-date="{{ \Carbon\Carbon::parse($prog->date)->format('d F Y') }}">
                                Edit
                            </button>
                            {{-- AKHIR PERUBAHAN TOMBOL EDIT --}}
                            <form action="{{ route('penjahit.tasks.progress.destroy', $prog->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus laporan ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                            </form>
                          </div>
                        </td>
                      </tr>
                    @empty
                      <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">Belum ada progres yang dilaporkan.</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          {{-- Form Lapor Progres --}}
          <div class="bg-white p-6 rounded-lg shadow-md">
              {{-- Konten tidak berubah... --}}
              <h3 class="text-xl font-semibold mb-4 text-gray-700">Lapor Progress Hari Ini</h3>
              <p class="text-sm text-gray-500 mb-4">Sisa pekerjaan Anda: <span class="font-bold text-orange-600">{{ $remainingQty }} pcs</span>.</p>
              @if($remainingQty > 0 || $assignment->progress->isNotEmpty())
                <form action="{{ route('penjahit.tasks.progress.store', $assignment) }}" method="POST" class="space-y-4">
                  @csrf
                  <div>
                    <label for="date" class="block text-sm font-medium text-gray-700">Tanggal Laporan</label>
                    <input id="date" name="date" type="date" value="{{ old('date', date('Y-m-d')) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500">
                    <x-input-error :messages="$errors->get('date')" class="mt-2" />
                  </div>
                  <div>
                    <label for="quantity_done" class="block text-sm font-medium text-gray-700">Output Selesai (pcs)</label>
                    <input id="quantity_done" name="quantity_done" type="number" min="1" max="{{ $remainingQty > 0 ? $remainingQty : '' }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500" placeholder="Jumlah hari ini" value="{{ old('quantity_done') }}">
                    <x-input-error :messages="$errors->get('quantity_done')" class="mt-2" />
                  </div>
                  <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Catatan (opsional)</label>
                    <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500" placeholder="Contoh: Ada sedikit kendala pada mesin jahit.">{{ old('notes') }}</textarea>
                    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                  </div>
                  <div class="flex justify-end">
                    <button type="submit" class="w-full px-4 py-2 bg-teal-600 text-white font-semibold rounded-lg hover:bg-teal-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">Simpan Laporan</button>
                  </div>
                </form>
              @else
                <div class="text-center p-4 bg-green-50 rounded-lg">
                  <x-heroicon-s-check-circle class="w-12 h-12 text-green-500 mx-auto" />
                  <p class="mt-2 font-semibold text-green-800">Tugas Selesai!</p>
                  <p class="text-sm text-green-700">Semua item untuk tugas ini telah selesai dikerjakan.</p>
                </div>
              @endif
          </div>
        </div>
      </main>
    </div>

    <div x-show="showModal" class="fixed inset-0 bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full flex items-center justify-center" x-cloak>
      <div @click.away="showModal = false" class="relative mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
          <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2" id="modalTitle">Edit Laporan Progres</h3>
          <p class="text-sm text-gray-500 mb-4">Untuk tanggal: <span id="modalDate" class="font-semibold"></span></p>

          <form id="editProgressForm" action="" method="POST" class="space-y-4 text-left">
            @csrf
            @method('PUT')
            
            <div>
                <label for="modal_quantity_done" class="block text-sm font-medium text-gray-700">Jumlah Selesai (pcs)</label>
                <input type="number" name="quantity_done" id="modal_quantity_done" min="1" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500">
            </div>
            
            <div>
                <label for="modal_notes" class="block text-sm font-medium text-gray-700">Catatan (opsional)</label>
                <textarea name="notes" id="modal_notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500"></textarea>
            </div>
            
            <div class="items-center gap-2 pt-4">
                <button type="submit" class="w-full px-4 py-2 bg-teal-600 text-white font-semibold rounded-lg hover:bg-teal-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                    Simpan Perubahan
                </button>
                <button type="button" class="w-full mt-2 px-4 py-2 bg-gray-200 text-gray-800 font-semibold rounded-lg hover:bg-gray-300" @click="showModal = false">
                    Batal
                </button>
            </div>
          </form>
        </div>
      </div>
    </div>
    </div>

  {{-- AWAL DARI BLOK SCRIPT --}}
  @push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Dapatkan semua tombol edit
      const editButtons = document.querySelectorAll('.edit-progress-btn');
      
      // Dapatkan elemen form dan input di dalam modal
      const modalForm = document.getElementById('editProgressForm');
      const modalQuantityInput = document.getElementById('modal_quantity_done');
      const modalNotesInput = document.getElementById('modal_notes');
      const modalDateSpan = document.getElementById('modalDate');

      // Tambahkan event listener untuk setiap tombol
      editButtons.forEach(button => {
        button.addEventListener('click', function (event) {
          // Ambil data dari atribut data-* tombol yang diklik
          const actionUrl = this.dataset.action;
          const quantity = this.dataset.quantity;
          const notes = this.dataset.notes;
          const date = this.dataset.date;

          // Isi form di dalam modal dengan data yang sesuai
          modalForm.action = actionUrl;
          modalQuantityInput.value = quantity;
          modalNotesInput.value = notes;
          modalDateSpan.textContent = date;
        });
      });
    });
  </script>
  @endpush
  {{-- AKHIR DARI BLOK SCRIPT --}}
</x-app-layout>
