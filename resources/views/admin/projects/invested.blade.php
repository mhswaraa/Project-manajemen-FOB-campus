<x-app-layout>
  <div class="flex h-screen bg-gray-50">
    @include('admin.partials.sidebar')

    <main class="flex-1 overflow-y-auto p-6 lg:p-8" x-data="{ receiptModalOpen: false, receiptImageUrl: '' }">
      
      {{-- Header --}}
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Manajemen Investasi</h1>
        <p class="text-gray-500 mt-1">Verifikasi dan kelola semua transaksi investasi yang masuk.</p>
      </div>

      {{-- Pesan Sukses --}}
      @if(session('success'))
        <div role="alert" class="mb-6 rounded-xl border border-gray-100 bg-white p-4 shadow-sm"><div class="flex items-start gap-4"><span class="text-green-600"><x-heroicon-s-check-circle class="h-6 w-6"/></span><div class="flex-1"><strong class="block font-medium text-gray-900">Sukses!</strong><p class="mt-1 text-sm text-gray-700">{{ session('success') }}</p></div></div></div>
      @endif

      {{-- Kartu Ringkasan --}}
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md">
          <p class="text-sm font-medium text-gray-500">Menunggu Persetujuan</p>
          <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $pendingCount }} Transaksi</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
          <p class="text-sm font-medium text-gray-500">Investasi Disetujui</p>
          <p class="text-3xl font-bold text-green-600 mt-1">{{ $approvedCount }} Transaksi</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
          <p class="text-sm font-medium text-gray-500">Total Dana Terkumpul</p>
          <p class="text-3xl font-bold text-indigo-600 mt-1">Rp {{ number_format($totalInvestedAmount, 0, ',', '.') }}</p>
        </div>
      </div>

      {{-- Konten Utama dengan Tab --}}
      <div class="bg-white shadow-md rounded-lg">
        {{-- Navigasi Tab --}}
        <div class="border-b border-gray-200">
          <nav class="-mb-px flex gap-6 px-6">
            <a href="{{ route('admin.projects.invested', ['status' => 'pending']) }}"
               @class(['shrink-0 border-b-2 px-1 pb-4 text-sm font-medium',
                      'border-indigo-500 text-indigo-600' => $status == 'pending',
                      'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' => $status != 'pending'
               ])>
              Menunggu Persetujuan
            </a>
            <a href="{{ route('admin.projects.invested', ['status' => 'approved']) }}"
               @class(['shrink-0 border-b-2 px-1 pb-4 text-sm font-medium',
                      'border-indigo-500 text-indigo-600' => $status == 'approved',
                      'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' => $status != 'approved'
               ])>
              Telah Disetujui
            </a>
          </nav>
        </div>
        
        {{-- Tabel Data --}}
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Investor</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detail Investasi</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Bukti Bayar</th>
                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              @forelse ($investments as $investment)
              <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                      <img class="h-10 w-10 rounded-full object-cover" src="https://ui-avatars.com/api/?name={{ urlencode($investment->investor->user->name) }}&background=E8EAF6&color=3F51B5" alt="">
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">{{ $investment->investor->user->name }}</div>
                      <div class="text-sm text-gray-500">{{ $investment->investor->user->email }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">{{ $investment->project->name }}</div>
                  <div class="text-sm text-gray-500">
                    <span class="font-semibold">{{ $investment->qty }} pcs</span>
                    <span class="mx-1">·</span>
                    <span>Rp {{ number_format($investment->amount, 0, ',', '.') }}</span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $investment->created_at->format('d M Y, H:i') }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  @if ($investment->receipt)
                    <button @click="receiptModalOpen = true; receiptImageUrl = '{{ asset('storage/' . $investment->receipt) }}'"
                            class="px-3 py-1 text-xs font-medium text-indigo-700 bg-indigo-100 rounded-full hover:bg-indigo-200">
                      Lihat Bukti
                    </button>
                  @else
                    <span class="px-3 py-1 text-xs font-medium text-gray-600 bg-gray-100 rounded-full">-</span>
                  @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                  @if (!$investment->approved)
                  <form action="{{ route('admin.projects.invested.approve', $investment) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menyetujui investasi ini?')">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 shadow-sm text-xs">Approve</button>
                  </form>
                  @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium text-green-700 bg-green-100">
                      <x-heroicon-s-check-circle class="h-4 w-4"/>
                      Disetujui
                    </span>
                  @endif
                </td>
              </tr>
              @empty
              <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">Tidak ada data untuk ditampilkan di tab ini.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        {{-- Paginasi --}}
        <div class="p-4 border-t border-gray-200">
            {{ $investments->links() }}
        </div>
      </div>

      <!-- Modal untuk menampilkan bukti bayar -->
      <div x-show="receiptModalOpen" @keydown.escape.window="receiptModalOpen = false" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
          <div class="flex items-center justify-center min-h-screen px-4 text-center">
              <div x-show="receiptModalOpen" @click.away="receiptModalOpen = false"
                  x-transition:enter="ease-out duration-300"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  x-transition:leave="ease-in duration-200"
                  x-transition:leave-start="opacity-100"
                  x-transition:leave-end="opacity-0"
                  class="fixed inset-0 transition-opacity" aria-hidden="true">
                  <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
              </div>

              <div x-show="receiptModalOpen"
                  x-transition:enter="ease-out duration-300"
                  x-transition:enter-start="opacity-0 scale-95"
                  x-transition:enter-end="opacity-100 scale-100"
                  x-transition:leave="ease-in duration-200"
                  x-transition:leave-start="opacity-100 scale-100"
                  x-transition:leave-end="opacity-0 scale-95"
                  class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all my-8 max-w-lg w-full">
                  <div class="bg-white p-4">
                      <div class="flex justify-between items-center mb-4">
                          <h3 class="text-lg font-medium text-gray-900">Bukti Pembayaran</h3>
                          <button @click="receiptModalOpen = false" class="text-gray-400 hover:text-gray-500">
                              <x-heroicon-s-x-mark class="h-6 w-6"/>
                          </button>
                      </div>
                      <img :src="receiptImageUrl" alt="Bukti Pembayaran" class="w-full h-auto rounded">
                  </div>
              </div>
          </div>
      </div>

    </main>
  </div>
</x-app-layout>
