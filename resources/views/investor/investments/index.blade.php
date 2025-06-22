{{-- resources/views/investor/investments/index.blade.php --}}
<x-app-layout>
  <div class="flex h-screen bg-gray-50 text-gray-800">
    {{-- Sidebar --}}
    @include('investor.partials.sidebar')

    {{-- Main Content --}}
    <main class="flex-1 p-6 overflow-y-auto">
      {{-- Header --}}
      <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-green-700">Investasi Saya</h1>
        @if(session('success'))
          <div class="px-4 py-2 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
          </div>
        @endif
      </div>

      @if($investments->isEmpty())
        <p class="text-gray-500">
          Belum ada investasi. Ayo investasi di
          <a href="{{ route('investor.projects.index') }}"
             class="text-green-600 hover:underline">
            Daftar Proyek
          </a>.
        </p>
      @else
        <div class="bg-white shadow rounded-lg overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proyek</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga/pcs</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Investasi</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progress Produksi</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status Investasi</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              @foreach($investments as $i => $inv)
                @php
                  $project = $inv->project;
                  $price   = $project->price_per_piece;
                  $total   = $inv->amount;
                  // Hitung total progress produksi lewat relasi hasManyThrough progress()
                  $doneQty = $project->progress->sum('quantity_done');
                @endphp
                <tr>
                  <td class="px-6 py-4 text-sm text-gray-600">{{ $i + 1 }}</td>
                  <td class="px-6 py-4 text-sm text-gray-800">{{ $project->name }}</td>
                  <td class="px-6 py-4 text-sm text-gray-600">{{ $inv->qty }} pcs</td>
                  <td class="px-6 py-4 text-sm text-gray-600">
                    Rp {{ number_format($price, 0, ',', '.') }}
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-600">
                    Rp {{ number_format($total, 0, ',', '.') }}
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-600">
                    {{ $doneQty }} pcs
                  </td>
                  <td class="px-6 py-4 text-sm font-medium">
                    @if($inv->approved)
                      <span class="text-green-600">Disetujui</span>
                    @else
                      <span class="text-yellow-600">Menunggu</span>
                    @endif
                  </td>
                  <td class="px-6 py-4 text-center text-sm font-medium">
                    <a href="{{ route('investor.investments.show', $inv) }}"
                       class="text-blue-600 hover:underline">
                      Detail
                    </a>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </main>
  </div>
</x-app-layout>
