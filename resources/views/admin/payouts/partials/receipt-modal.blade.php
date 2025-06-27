<div
    x-data="{ open: false, receiptUrl: '' }"
    x-show="open"
    @open-modal.window="if ($event.detail.name === 'receipt-modal') { open = true; receiptUrl = $event.detail.receiptUrl; }"
    @keydown.escape.window="open = false"
    style="display: none;"
    class="fixed inset-0 bg-gray-900 bg-opacity-60 z-50 flex items-center justify-center"
    x-cloak
>
    <div 
        @click.outside="open = false"
        class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4"
    >
        <div class="p-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-medium">Bukti Pembayaran</h3>
            <button @click="open = false" class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="p-6">
            <template x-if="receiptUrl">
                <img :src="receiptUrl" alt="Bukti Pembayaran" class="w-full h-auto rounded-md object-contain max-h-[70vh]">
            </template>
            <template x-if="!receiptUrl">
                <p class="text-center text-gray-500">Gagal memuat gambar bukti pembayaran.</p>
            </template>
        </div>
    </div>
</div>
