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
