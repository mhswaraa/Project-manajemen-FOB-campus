{{-- resources/views/investor/investments/show.blade.php --}}
<x-app-layout>
  <div class="flex h-screen bg-gray-50 text-gray-800">
    {{-- Sidebar --}}
    @include('investor.partials.sidebar')

    {{-- Main Content --}}
    <main class="flex-1 p-6 overflow-y-auto">
      {{-- Tombol Kembali --}}
      <div class="mb-4">
        <a href="{{ route('investor.investments.index') }}"
           class="text-sm text-green-600 hover:underline">
          ← Kembali ke Investasi Saya
        </a>
      </div>

      {{-- Judul --}}
      <h1 class="text-2xl font-semibold text-green-700 mb-6">
        Detail Investasi #{{ $investment->id }}
      </h1>

      @php
        // Data proyek & finansial
        $proj      = $investment->project;
        $price     = $proj->price_per_piece;
        $qty       = $investment->qty;
        $modal     = $investment->amount;          // modal total
        $hppTotal  = $price * $qty;                // harga pokok total
        $profitPer = $proj->profit;                // profit per pcs
        $totProfit = $profitPer * $qty;            // total profit

        // Progress produksi (semua record untuk proyek ini)
        $allProg   = $proj->productionProgress;
        $doneQty   = $allProg->sum('quantity_done');
        $pctDone   = $qty ? round($doneQty / $qty * 100) : 0;
      @endphp

      {{-- Ringkasan Finansial --}}
      <div class="bg-white p-6 rounded-lg shadow mb-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-4">
          <div>
            <span class="font-medium">Modal (Total Investasi):</span><br>
            Rp {{ number_format($modal,0,',','.') }}
          </div>
          <div>
            <span class="font-medium">HPP (Harga Pokok):</span><br>
            Rp {{ number_format($hppTotal,0,',','.') }}
          </div>
          <div>
            <span class="font-medium">Total Profit:</span><br>
            Rp {{ number_format($totProfit,0,',','.') }}
          </div>
        </div>
        <div class="space-y-4">
          <div>
            <span class="font-medium">Profit per pcs:</span><br>
            Rp {{ number_format($profitPer,0,',','.') }}
          </div>
          <div>
            <span class="font-medium">Qty Investasi:</span><br>
            {{ $qty }} pcs
          </div>
          <div>
            <span class="font-medium">Harga/pcs:</span><br>
            Rp {{ number_format($price,0,',','.') }}
          </div>
        </div>
      </div>

      {{-- Progress Produksi --}}
<div class="bg-white p-6 rounded-lg shadow mb-6">
  <h2 class="text-lg font-semibold mb-4">Progress Produksi</h2>

  @if($doneQty > 0)
    <div class="text-sm text-gray-700 mb-2">
      {{ $doneQty }}/{{ $investment->qty }} pcs • {{ $pctDone }}%
    </div>
    <div class="w-full bg-gray-200 h-3 rounded mb-4">
      <div class="bg-green-600 h-3 rounded" style="width: {{ $pctDone }}%"></div>
    </div>

    <h3 class="font-medium mb-2">Riwayat Harian</h3>
    <ul class="divide-y divide-gray-200">
      @foreach($investment->project->progress->sortBy('date') as $prog)
        <li class="py-2 flex justify-between items-center">
          <div>
            <p class="text-sm font-medium">
              {{ \Carbon\Carbon::parse($prog->date)->format('d M Y') }}
            </p>
            <p class="text-gray-600 text-sm">{{ $prog->quantity_done }} pcs</p>
          </div>
          @if($prog->notes)
            <p class="text-xs text-gray-500 italic">{{ $prog->notes }}</p>
          @endif
        </li>
      @endforeach
    </ul>
  @else
    <p class="text-gray-500">Belum ada update progress produksi.</p>
  @endif
</div>

      {{-- Aksi Edit/Batalkan --}}
      @unless($investment->approved)
        <div class="flex space-x-2">
          <a href="{{ route('investor.investments.edit', $investment) }}"
             class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Edit
          </a>
          <form action="{{ route('investor.investments.destroy', $investment) }}"
                method="POST"
                onsubmit="return confirm('Batalkan investasi ini?');">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
              Batalkan
            </button>
          </form>
        </div>
      @endunless
    </main>
  </div>
</x-app-layout>
