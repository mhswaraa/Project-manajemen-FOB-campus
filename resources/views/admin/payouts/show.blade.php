<x-app-layout>
    <div class="flex h-screen bg-gray-50">
        @include('admin.partials.sidebar')
        <main class="flex-1 overflow-y-auto p-6 lg:p-8">
            
            @php
                // Variabel utama dari data payout yang diterima
                $investment = $payout->investment;
                $investor = $investment->investor;
                $project = $investment->project;
                
                // Kalkulasi finansial dengan variabel yang sudah benar
                $investorProfit = $payout->amount; // <-- PERBAIKAN: Menggunakan kolom 'amount'
                $investorInitialInvestment = $investment->amount;
                $totalReturn = $investorInitialInvestment + $investorProfit;

                // Logika untuk pesan WhatsApp
                $phoneNumber = $investor->phone ?? null;
                if ($phoneNumber && substr($phoneNumber, 0, 1) === '0') {
                    $phoneNumber = '62' . substr($phoneNumber, 1);
                }
                
                $gdriveLink = $investor->gdrive_link ?? null;
                $documentTitle = 'Bukti Pembayaran Profit';
                $adminName = auth()->user()->name;
                $companyName = "Mariee Konveksi";

                $message = "Halo " . $investor->user->name . ",\n\n";
                $message .= "Saya " . $adminName . " dari " . $companyName . ".\n\n";
                $message .= "Berikut kami kirimkan " . $documentTitle . " untuk investasi Anda pada proyek " . $project->name . ".\n\n";
                $message .= "Silakan unduh dokumen melalui tautan berikut:\n";
                $message .= $gdriveLink . "\n\n";
                $message .= "Terima kasih atas investasi dan kepercayaan Anda.";

                $whatsappUrl = "https://wa.me/{$phoneNumber}?text=" . urlencode($message);
            @endphp

            {{-- Header Halaman --}}
            <div class="flex flex-col sm:flex-row justify-between items-start mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Detail Pembayaran #{{ $payout->id }}</h1>
                    <p class="text-gray-500 mt-1">Pembayaran profit untuk: <span class="font-semibold text-indigo-600">{{ $investor->user->name }}</span></p>
                </div>
                 <a href="{{ route('admin.payouts.index', ['tab' => 'history']) }}" class="mt-4 sm:mt-0 flex-shrink-0 inline-flex items-center gap-2 px-4 py-2 border border-gray-300 bg-white text-gray-700 font-semibold rounded-lg hover:bg-gray-50 shadow-sm">
                    <x-heroicon-s-arrow-left class="h-5 w-5"/>
                    Kembali ke Riwayat
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Kolom Kiri: Detail Pembayaran --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex justify-between items-start">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-800">Rincian Pembayaran</h2>
                                <p class="text-gray-500">Proyek: <span class="font-medium">{{ $project->name }}</span></p>
                            </div>
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">Lunas</span>
                        </div>
                        
                        <div class="mt-6 pt-4 border-t">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Rincian Perhitungan</h3>
                            <dl class="space-y-3 text-sm">
                                <div class="bg-indigo-50 p-3 rounded-lg">
                                    <div class="font-semibold text-gray-700 mb-2">Bagian Investor</div>
                                    <div class="flex justify-between"><dt class="text-gray-500">Investasi Awal</dt><dd class="font-medium text-gray-800">Rp {{ number_format($investorInitialInvestment, 0, ',', '.') }}</dd></div>
                                    {{-- PERBAIKAN: Menampilkan profit yang dibayarkan dari $payout->amount --}}
                                    <div class="flex justify-between"><dt class="text-gray-500">Profit ({{ $investment->equity_percentage }}%)</dt><dd class="font-medium text-green-600">Rp {{ number_format($investorProfit, 0, ',', '.') }}</dd></div>
                                </div>
                                <div class="flex justify-between items-center pt-3 border-t">
                                    <dt class="text-base font-semibold text-gray-700">Total Dana Kembali</dt>
                                    <dd class="font-bold text-xl text-indigo-600">Rp {{ number_format($totalReturn, 0, ',', '.') }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="mt-6 pt-4 border-t">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Detail Transaksi</h3>
                             <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                {{-- PERBAIKAN: Menggunakan kolom 'paid_at' dan properti Carbon --}}
                                <div><dt class="text-gray-500">Tanggal Bayar</dt><dd class="font-semibold text-gray-900">{{ $payout->paid_at->isoFormat('D MMMM YYYY, HH:mm') }}</dd></div>
                                <div><dt class="text-gray-500">ID Pembayaran</dt><dd class="font-mono text-gray-900">#{{ $payout->id }}</dd></div>
                                <div><dt class="text-gray-500">ID Investasi</dt><dd class="font-mono text-gray-900">#{{ $payout->investment_id }}</dd></div>
                                
                                {{-- DIHAPUS: Bagian "Diproses oleh" dan "Catatan" karena sudah tidak relevan --}}
                             </dl>
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan: Panel Aksi Dokumen --}}
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-lg font-medium text-gray-900">Aksi Dokumen</h3>
                        <div class="mt-4 space-y-3">
                            <a href="{{ Storage::url($payout->receipt_path) }}" target="_blank" class="w-full flex items-center justify-center gap-2 text-center px-4 py-3 bg-gray-100 text-gray-800 text-sm font-semibold rounded-lg hover:bg-gray-200">
                                <x-heroicon-s-photo class="h-5 w-5"/> Lihat Bukti Transfer
                            </a>
                            <a href="{{ route('admin.payouts.pdf', $payout) }}" target="_blank" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-gray-600 text-white text-sm font-semibold rounded-lg hover:bg-gray-700">
                                <x-heroicon-s-arrow-down-tray class="h-5 w-5"/> Download PDF
                            </a>
                            <a href="{{ ($phoneNumber && $gdriveLink) ? $whatsappUrl : '#' }}" 
                               target="_blank"
                               @if(!$phoneNumber || !$gdriveLink)
                                   onclick="alert('Pastikan nomor telepon dan link Gdrive investor sudah terisi di Manajemen User.'); return false;"
                               @endif
                               @class([
                                   'w-full flex items-center justify-center gap-2 px-4 py-2 bg-teal-500 text-white text-sm font-semibold rounded-lg hover:bg-teal-600 transition',
                                   'opacity-50 cursor-not-allowed' => !$phoneNumber || !$gdriveLink
                               ])>
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.8 0-67.6-9.5-97.2-26.7l-7-4.1-72.5 19.1L83 358.4l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.8-16.2-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.9 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>
                                Kirim via WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</x-app-layout>
