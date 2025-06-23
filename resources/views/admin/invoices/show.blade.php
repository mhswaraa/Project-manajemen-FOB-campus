<x-app-layout>
  <div class="flex h-screen bg-gray-50">
    @include('admin.partials.sidebar')
    <main class="flex-1 overflow-y-auto p-6 lg:p-8">
      <div class="mb-6"><a href="{{ route('admin.invoices.index', ['tab' => $invoice->status]) }}" class="flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 mb-2"><x-heroicon-s-arrow-left class="h-4 w-4" />Kembali ke Daftar Invoice</a></div>
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
          <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex justify-between items-start"><div><h2 class="text-2xl font-bold text-gray-800">Invoice #{{ $invoice->invoice_number }}</h2><p class="text-gray-500">Diterbitkan oleh: <span class="font-medium">{{ $invoice->tailor->user->name }}</span></p><p class="text-sm text-gray-500">Tanggal: {{ $invoice->issue_date->format('d F Y') }}</p></div><span @class(['px-3 py-1 text-sm font-semibold rounded-full', 'bg-yellow-100 text-yellow-800' => $invoice->status == 'pending', 'bg-green-100 text-green-800' => $invoice->status == 'paid'])>{{ ucfirst($invoice->status) }}</span></div>
            <div class="mt-6 pt-4 border-t"><h3 class="text-lg font-medium text-gray-900 mb-4">Rincian Pekerjaan</h3><div class="space-y-2 max-h-72 overflow-y-auto pr-2">
                @foreach($invoice->progressItems as $item)
                <div class="flex justify-between text-sm"><p class="text-gray-700">{{ $item->assignment->project->name }} ({{ $item->quantity_done }} pcs)</p><p class="text-gray-900">Rp {{ number_format($item->quantity_done * $item->assignment->project->wage_per_piece, 0,',','.') }}</p></div>
                @endforeach
            </div></div>
            <div class="flex justify-end mt-4 pt-4 border-t"><span class="text-lg font-bold text-gray-800">Total: Rp {{ number_format($invoice->total_amount, 0,',','.') }}</span></div>
          </div>
        </div>
        <div class="lg:col-span-1">
          @if($invoice->status == 'pending')
          <div class="bg-white p-6 rounded-lg shadow-md sticky top-8">
            <h3 class="text-lg font-medium text-gray-900">Proses Pembayaran</h3>
            <form action="{{ route('admin.invoices.pay', $invoice) }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
              @csrf
              <div><label for="receipt" class="block text-sm font-medium text-gray-700">Upload Bukti Transfer</label><input type="file" name="receipt" id="receipt" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"><x-input-error :messages="$errors->get('receipt')" class="mt-1" /></div>
              <button type="submit" class="w-full text-center px-4 py-3 bg-indigo-600 text-white text-sm font-semibold rounded-lg shadow-md hover:bg-indigo-700">Konfirmasi & Tandai Lunas</button>
            </form>
          </div>
          @else
          <div class="bg-white p-6 rounded-lg shadow-md sticky top-8">
            <h3 class="text-lg font-medium text-gray-900">Detail Pembayaran</h3>
            <div class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">Tgl Bayar</dt><dd class="font-semibold text-gray-900">{{ $invoice->payment_date->format('d F Y') }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Diproses oleh</dt><dd class="font-semibold text-gray-900">{{ $invoice->processor->name }}</dd></div>
            </div>
            @if($invoice->receipt_path)
            <a href="{{ asset('storage/' . $invoice->receipt_path) }}" target="_blank" class="mt-6 w-full flex items-center justify-center gap-2 text-center px-4 py-3 bg-gray-100 text-gray-800 text-sm font-semibold rounded-lg hover:bg-gray-200">
                <x-heroicon-s-photo class="h-5 w-5"/> Lihat Bukti Transfer
            </a>
            @endif
          </div>
          @endif
        </div>
      </div>
    </main>
  </div>
</x-app-layout>
