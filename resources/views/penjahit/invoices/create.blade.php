<x-app-layout>
  <div class="flex h-screen bg-gray-100">
    @include('penjahit.partials.sidebar')

    <main class="flex-1 overflow-y-auto p-6 lg:p-8" 
          x-data="{ 
            selectedProgress: [], 
            totalAmount: 0,
            calculateTotal() {
                let total = 0;
                this.selectedProgress.forEach(id => {
                    const el = document.querySelector(`#progress-${id}`);
                    if (el) {
                        total += parseFloat(el.dataset.wage);
                    }
                });
                this.totalAmount = total;
            }
          }">
      
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Buat Invoice Baru</h1>
        <p class="text-gray-500 mt-1">Pilih pekerjaan yang sudah selesai untuk ditagihkan ke admin.</p>
      </div>

      <form action="{{ route('penjahit.invoices.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {{-- Kolom Kiri: Daftar Pekerjaan --}}
          <div class="lg:col-span-2 bg-white shadow-md rounded-lg">
            <div class="p-4 border-b"><h3 class="font-semibold">Pekerjaan Selesai & Belum Ditagih</h3></div>
            <div class="max-h-96 overflow-y-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 sticky top-0"><tr><th class="p-4 w-4"></th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detail Pekerjaan</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Upah</th></tr></thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  @forelse ($unbilledProgress as $progress)
                    <tr id="progress-{{ $progress->id }}" data-wage="{{ $progress->wage }}">
                      <td class="p-4"><input type="checkbox" name="progress_ids[]" value="{{ $progress->id }}" x-model="selectedProgress" @change="calculateTotal" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"></td>
                      <td class="px-6 py-4 whitespace-nowrap"><div class="text-sm font-medium text-gray-900">{{ $progress->assignment->project->name }}</div><div class="text-sm text-gray-500">Tgl Lapor: {{ $progress->date->format('d M Y') }}</div></td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $progress->quantity_done }} pcs</td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">Rp {{ number_format($progress->wage, 0, ',', '.') }}</td>
                    </tr>
                  @empty
                    <tr><td colspan="4" class="px-6 py-12 text-center text-gray-500">Tidak ada pekerjaan selesai yang bisa ditagih saat ini.</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
          {{-- Kolom Kanan: Ringkasan & Aksi --}}
          <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-lg shadow-md sticky top-8">
              <h3 class="text-lg font-medium text-gray-900">Ringkasan Invoice</h3>
              <div class="mt-4 pt-4 border-t space-y-2">
                <div class="flex justify-between text-sm"><dt class="text-gray-500">Item Dipilih</dt><dd class="font-medium text-gray-900" x-text="selectedProgress.length"></dd></div>
                <div class="flex justify-between text-lg"><dt class="font-medium text-gray-800">Total Tagihan</dt><dd class="font-bold text-green-600" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(totalAmount)"></dd></div>
              </div>
              <button type="submit" :disabled="selectedProgress.length === 0" class="mt-6 w-full text-center px-4 py-3 bg-indigo-600 text-white text-sm font-semibold rounded-lg shadow-md hover:bg-indigo-700 disabled:bg-gray-400 disabled:cursor-not-allowed">
                Terbitkan Invoice
              </button>
            </div>
          </div>
        </div>
      </form>
    </main>
  </div>
</x-app-layout>
