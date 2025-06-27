<!-- Modal Lihat Bukti -->
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
            <div class="bg-white p-6">
                <div class="flex justify-between items-center pb-3 border-b">
                    <h3 class="text-lg font-medium text-gray-900" x-text="'Detail Pembayaran #' + payout.id"></h3>
                    <button @click="receiptModalOpen = false" class="text-gray-400 hover:text-gray-500">
                        <x-heroicon-s-x-mark class="h-6 w-6"/>
                    </button>
                </div>
                <div class="mt-4">
                    <img :src="receiptImageUrl" alt="Bukti Pembayaran" class="w-full h-auto rounded border">
                </div>
                {{-- FIX: Menambahkan tombol Download PDF di bawah gambar --}}
                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <a x-bind:href="receiptImageUrl" target="_blank" class="w-full flex items-center justify-center gap-2 text-center px-4 py-3 bg-gray-100 text-gray-800 text-sm font-semibold rounded-lg hover:bg-gray-200">
                        <x-heroicon-s-photo class="h-5 w-5"/> Lihat Gambar
                     </a>
                     {{-- Tombol Download PDF Baru --}}
                     <a x-bind:href="`/admin/payouts/${payout.id}/download`" class="w-full flex items-center justify-center gap-2 text-center px-4 py-3 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700">
                        <x-heroicon-s-arrow-down-tray class="h-5 w-5"/> Download PDF
                     </a>
                </div>
            </div>
        </div>
    </div>
</div>
