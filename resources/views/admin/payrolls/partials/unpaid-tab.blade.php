{{-- Filter Periode --}}
<div class="mb-6">
  <form action="{{ route('admin.payrolls.index') }}" method="GET">
    <input type="hidden" name="tab" value="unpaid">
    <label for="month" class="block text-sm font-medium text-gray-700">Pilih Periode Bulan</label>
    <div class="mt-1 flex gap-2">
      <input type="month" name="month" id="month" value="{{ $currentMonth }}" class="block w-full max-w-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
      <button type="submit" class="inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">Filter</button>
    </div>
  </form>
</div>

{{-- Daftar Tagihan Upah --}}
<div class="overflow-x-auto">
  <table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50"><tr><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penjahit</th><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Upah</th><th scope="col" class="relative px-6 py-3"><span class="sr-only">Aksi</span></th></tr></thead>
    <tbody class="bg-white divide-y divide-gray-200">
      @forelse ($payrollData as $tailor)
      <tr>
        <td class="px-6 py-4 whitespace-nowrap"><div class="flex items-center"><div class="flex-shrink-0 h-10 w-10"><img class="h-10 w-10 rounded-full object-cover" src="https://ui-avatars.com/api/?name={{ urlencode($tailor->user->name) }}&background=E0F2F1&color=00796B" alt=""></div><div class="ml-4"><div class="text-sm font-medium text-gray-900">{{ $tailor->user->name }}</div><div class="text-sm text-gray-500">{{ $tailor->user->email }}</div></div></div></td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($currentMonth)->format('F Y') }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600">Rp {{ number_format($tailor->unpaid_wage, 0, ',', '.') }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
          <button type="button" @click="paymentModalOpen = true; tailorId = {{ $tailor->tailor_id }}; tailorName = '{{ addslashes($tailor->user->name) }}'; paymentAmount = {{ $tailor->unpaid_wage }}; paymentAmountFormatted = '{{ number_format($tailor->unpaid_wage, 0, ',', '.') }}'; progressIds = '{{ json_encode($tailor->progress_ids_to_pay) }}'; periodStart = '{{ \Carbon\Carbon::parse($currentMonth)->startOfMonth()->toDateString() }}'; periodEnd = '{{ \Carbon\Carbon::parse($currentMonth)->endOfMonth()->toDateString() }}';" class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 shadow-sm text-xs">Bayar & Catat</button>
        </td>
      </tr>
      @empty
      <tr><td colspan="4" class="px-6 py-12 text-center text-gray-500">Tidak ada tagihan upah untuk periode ini.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
