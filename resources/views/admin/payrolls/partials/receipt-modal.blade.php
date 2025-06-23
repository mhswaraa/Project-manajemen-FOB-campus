<div class="overflow-x-auto">
  <table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
      <tr>
        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penjahit</th>
        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detail Pembayaran</th>
        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diproses Oleh</th>
        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Bukti</th>
      </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
      @forelse ($payrollHistory as $payroll)
      <tr>
        <td class="px-6 py-4 whitespace-nowrap">
          <div class="flex items-center">
            <div class="flex-shrink-0 h-10 w-10"><img class="h-10 w-10 rounded-full object-cover" src="https://ui-avatars.com/api/?name={{ urlencode($payroll->tailor->user->name) }}&background=E0F2F1&color=00796B" alt=""></div>
            <div class="ml-4">
              <div class="text-sm font-medium text-gray-900">{{ $payroll->tailor->user->name }}</div>
              <div class="text-sm text-gray-500">{{ $payroll->tailor->user->email }}</div>
            </div>
          </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
          <div class="text-sm font-bold text-gray-900">Rp {{ number_format($payroll->amount, 0, ',', '.') }}</div>
          <div class="text-sm text-gray-500">Periode: {{ $payroll->period_start->format('M Y') }}</div>
          <div class="text-sm text-gray-500">Tgl Bayar: {{ $payroll->payment_date->format('d M Y') }}</div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payroll->processor->name }}</td>
        <td class="px-6 py-4 whitespace-nowrap text-center">
          @if ($payroll->receipt_path)
            <button @click="receiptModalOpen = true; receiptImageUrl = '{{ asset('storage/' . $payroll->receipt_path) }}'" class="px-3 py-1 text-xs font-medium text-indigo-700 bg-indigo-100 rounded-full hover:bg-indigo-200">Lihat</button>
          @else
            <span class="text-xs text-gray-400 italic">-</span>
          @endif
        </td>
      </tr>
      @empty
      <tr><td colspan="4" class="px-6 py-12 text-center text-gray-500">Belum ada riwayat pembayaran yang tercatat.</td></tr>
      @endforelse
    </tbody>
  </table>
  <div class="p-4 border-t">{{ $payrollHistory->links() }}</div>
</div>
