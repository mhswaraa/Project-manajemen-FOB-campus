<!-- Modal Pembayaran Profit -->
<div x-show="paymentModalOpen" @keydown.escape.window="paymentModalOpen = false" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4">
        <div x-show="paymentModalOpen" @click.away="paymentModalOpen = false" x-transition class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        
        <div x-show="paymentModalOpen" x-transition class="inline-block bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all my-8 max-w-lg w-full">
            {{-- Template ini hanya akan dirender jika 'selectedInvestment' memiliki data --}}
            <template x-if="selectedInvestment">
                {{-- PERUBAHAN: Form diperbaiki agar berfungsi --}}
                <form method="POST" action="{{ route('admin.payouts.process') }}" enctype="multipart/form-data">
                    @csrf
                    
                    {{-- Input tersembunyi untuk mengirim ID investasi yang akan dibayar --}}
                    <input type="hidden" name="investment_ids[]" :value="selectedInvestment.id">

                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900">Konfirmasi Pembayaran Profit</h3>
                        <div class="mt-4 space-y-2 text-sm">
                            <p><span class="text-gray-500">Penerima:</span> <strong class="text-gray-800" x-text="selectedInvestment.investor.user.name"></strong></p>
                            <p><span class="text-gray-500">Proyek:</span> <strong class="text-gray-800" x-text="selectedInvestment.project.name"></strong></p>
                            <p><span class="text-gray-500">Jumlah Profit:</span> <strong class="text-green-600" x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(selectedInvestment.profit)"></strong></p>
                        </div>
                        <div class="mt-4">
                            <label for="receipt" class="block text-sm font-medium text-gray-700">Upload Bukti Transfer</label>
                            <input type="file" name="receipt" id="receipt" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <x-input-error :messages="$errors->get('receipt')" class="mt-1" />
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:ml-3 sm:w-auto sm:text-sm">Konfirmasi Pembayaran</button>
                        <button type="button" @click="paymentModalOpen = false; selectedInvestment = null" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
                    </div>
                </form>
            </template>
        </div>
    </div>
</div>
