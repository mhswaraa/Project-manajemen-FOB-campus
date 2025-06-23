<x-app-layout>
  <div class="flex h-screen bg-gray-50">
    @include('admin.partials.sidebar')
    <main class="flex-1 overflow-y-auto p-6 lg:p-8">
      <div class="mb-8"><h1 class="text-3xl font-bold text-gray-800">Manajemen Invoice</h1><p class="text-gray-500 mt-1">Kelola semua tagihan yang masuk dari para penjahit.</p></div>
      @if(session('success'))<div role="alert" class="mb-6 rounded-xl border border-gray-100 bg-white p-4 shadow-sm"><div class="flex items-start gap-4"><span class="text-green-600"><x-heroicon-s-check-circle class="h-6 w-6"/></span><div class="flex-1"><strong class="block font-medium text-gray-900">Sukses!</strong><p class="mt-1 text-sm text-gray-700">{{ session('success') }}</p></div></div></div>@endif

      <div class="bg-white shadow-md rounded-lg">
        <div class="border-b border-gray-200"><nav class="-mb-px flex gap-6 px-6">
          <a href="{{ route('admin.invoices.index', ['tab' => 'pending']) }}" @class(['shrink-0 border-b-2 px-1 pb-4 text-sm font-medium', 'border-indigo-500 text-indigo-600' => $tab == 'pending', 'border-transparent text-gray-500 hover:text-gray-700' => $tab != 'pending'])>Perlu Diproses</a>
          <a href="{{ route('admin.invoices.index', ['tab' => 'paid']) }}" @class(['shrink-0 border-b-2 px-1 pb-4 text-sm font-medium', 'border-indigo-500 text-indigo-600' => $tab == 'paid', 'border-transparent text-gray-500 hover:text-gray-700' => $tab != 'paid'])>Sudah Dibayar</a>
        </nav></div>
        <div class="overflow-x-auto"><table class="min-w-full divide-y divide-gray-200"><thead class="bg-gray-50"><tr><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Invoice</th><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penjahit</th><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Terbit</th><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Tagihan</th><th scope="col" class="relative px-6 py-3"><span class="sr-only">Aksi</span></th></tr></thead><tbody class="bg-white divide-y divide-gray-200">
            @forelse ($invoices as $invoice)
            <tr><td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-600">{{ $invoice->invoice_number }}</td><td class="px-6 py-4 whitespace-nowrap"><div class="flex items-center"><div class="flex-shrink-0 h-10 w-10"><img class="h-10 w-10 rounded-full object-cover" src="https://ui-avatars.com/api/?name={{ urlencode($invoice->tailor->user->name) }}&background=E0F2F1&color=00796B" alt=""></div><div class="ml-4"><div class="text-sm font-medium text-gray-900">{{ $invoice->tailor->user->name }}</div><div class="text-sm text-gray-500">{{ $invoice->tailor->user->email }}</div></div></div></td><td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $invoice->issue_date->format('d M Y') }}</td><td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-800">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</td><td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"><a href="{{ route('admin.invoices.show', $invoice) }}" class="text-indigo-600 hover:text-indigo-900">Lihat & Proses</a></td></tr>
            @empty
            <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">Tidak ada invoice di tab ini.</td></tr>
            @endforelse
        </tbody></table></div>
        <div class="p-4 border-t">{{ $invoices->links() }}</div>
      </div>
    </main>
  </div>
</x-app-layout>
