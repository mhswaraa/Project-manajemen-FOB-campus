<x-app-layout>
  <div class="flex h-screen bg-gray-50">
    @include('admin.partials.sidebar')
    <main class="flex-1 overflow-y-auto p-6 lg:p-8">
      
      @php
          // Mengambil nomor telepon dari profil penjahit
          $phoneNumber = $invoice->tailor->phone ?? null;
          if ($phoneNumber && substr($phoneNumber, 0, 1) === '0') {
              $phoneNumber = '62' . substr($phoneNumber, 1);
          }
          
          // LANGKAH FINAL: Mengambil link Gdrive langsung dari profil penjahit
          $gdriveLink = $invoice->tailor->gdrive_link ?? null;

          // Membuat pesan WhatsApp lengkap
          $documentTitle = $invoice->status == 'paid' ? 'Bukti Pembayaran' : 'Invoice';
          $adminName = auth()->user()->name;
          $companyName = "Mariee Konveksi";
          
          $message = "Halo " . $invoice->tailor->user->name . ",\n\n";
          $message .= "Saya " . $adminName . " dari " . $companyName . ".\n\n";
          $message .= "Berikut kami kirimkan " . $documentTitle . " dengan nomor #" . $invoice->invoice_number . ".\n\n";
          $message .= "Silakan unduh dokumen melalui tautan berikut:\n";
          $message .= $gdriveLink . "\n\n"; // Langsung menggunakan link dari profil
          $message .= "Terima kasih.";

          // URL-encode pesan untuk link WhatsApp
          $whatsappUrl = "https://wa.me/{$phoneNumber}?text=" . urlencode($message);
      @endphp

      {{-- Header Halaman --}}
      <div class="flex flex-col sm:flex-row justify-between items-start mb-8">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">Detail Invoice #{{ $invoice->invoice_number }}</h1>
          <p class="text-gray-500 mt-1">Tagihan untuk: <span class="font-semibold text-indigo-600">{{ $invoice->tailor->user->name }}</span></p>
        </div>
         <a href="{{ route('admin.invoices.index', ['tab' => $invoice->status]) }}" class="mt-4 sm:mt-0 flex-shrink-0 inline-flex items-center gap-2 px-4 py-2 border border-gray-300 bg-white text-gray-700 font-semibold rounded-lg hover:bg-gray-50 shadow-sm">
            <x-heroicon-s-arrow-left class="h-5 w-5"/>
            Kembali ke Daftar
        </a>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Kolom Kiri: Detail Invoice --}}
        <div class="lg:col-span-2 space-y-6">
          <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex justify-between items-start"><div><h2 class="text-2xl font-bold text-gray-800">Invoice #{{ $invoice->invoice_number }}</h2><p class="text-gray-500">Diterbitkan oleh: <span class="font-medium">{{ $invoice->tailor->user->name }}</span></p><p class="text-sm text-gray-500">Tanggal: {{ $invoice->issue_date->format('d F Y') }}</p></div><span @class(['px-3 py-1 text-sm font-semibold rounded-full', 'bg-yellow-100 text-yellow-800' => $invoice->status == 'pending', 'bg-green-100 text-green-800' => $invoice->status == 'paid'])>{{ ucfirst($invoice->status) }}</span></div>
            <div class="mt-6 pt-4 border-t"><h3 class="text-lg font-medium text-gray-900 mb-4">Rincian Pekerjaan</h3><div class="space-y-2 max-h-72 overflow-y-auto pr-2">
                {{-- ==================================================================== --}}
                {{-- AWAL PERUBAHAN: Menampilkan jumlah yang diterima (accepted_qty) --}}
                {{-- ==================================================================== --}}
                @foreach($invoice->progressItems as $item)
                <div class="flex justify-between text-sm">
                  <p class="text-gray-700">{{ $item->assignment->project->name }} ({{ $item->accepted_qty }} pcs)</p>
                  <p class="text-gray-900">Rp {{ number_format($item->accepted_qty * $item->assignment->project->wage_per_piece, 0,',','.') }}</p>
                </div>
                @endforeach
                {{-- ==================================================================== --}}
                {{-- AKHIR PERUBAHAN --}}
                {{-- ==================================================================== --}}
            </div></div>
            <div class="flex justify-end mt-4 pt-4 border-t"><span class="text-lg font-bold text-gray-800">Total: Rp {{ number_format($invoice->total_amount, 0,',','.') }}</span></div>
          </div>
        </div>

        {{-- Kolom Kanan: Panel Aksi --}}
        <div class="lg:col-span-1 space-y-6">
          
          {{-- Kartu Aksi Pembayaran / Detail --}}
          @if($invoice->status == 'pending')
            <div class="bg-white p-6 rounded-lg shadow-md">
              <h3 class="text-lg font-medium text-gray-900">Proses Pembayaran</h3>
              <form action="{{ route('admin.invoices.pay', $invoice) }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                @csrf
                <div><label for="receipt" class="block text-sm font-medium text-gray-700">Upload Bukti Transfer</label><input type="file" name="receipt" id="receipt" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"><x-input-error :messages="$errors->get('receipt')" class="mt-1" /></div>
                <button type="submit" class="w-full text-center px-4 py-3 bg-indigo-600 text-white text-sm font-semibold rounded-lg shadow-md hover:bg-indigo-700">Konfirmasi & Tandai Lunas</button>
              </form>
            </div>
          @else
            <div class="bg-white p-6 rounded-lg shadow-md">
              <h3 class="text-lg font-medium text-gray-900">Detail Pembayaran</h3>
              <div class="mt-4 space-y-3 text-sm">
                  <div class="flex justify-between"><dt class="text-gray-500">Tgl Bayar</dt><dd class="font-semibold text-gray-900">{{ $invoice->payment_date->format('d F Y') }}</dd></div>
                  
                  {{-- AWAL PERBAIKAN: Menambahkan pengecekan untuk menghindari error jika processor tidak ada --}}
                  @if($invoice->processor)
                    <div class="flex justify-between"><dt class="text-gray-500">Diproses oleh</dt><dd class="font-semibold text-gray-900">{{ $invoice->processor->name }}</dd></div>
                  @endif
                  {{-- AKHIR PERUBAHAN --}}

              </div>
              @if($invoice->receipt_path)
              <a href="{{ asset('storage/' . $invoice->receipt_path) }}" target="_blank" class="mt-6 w-full flex items-center justify-center gap-2 text-center px-4 py-3 bg-gray-100 text-gray-800 text-sm font-semibold rounded-lg hover:bg-gray-200">
                  <x-heroicon-s-photo class="h-5 w-5"/> Lihat Bukti Transfer
              </a>
              @endif
            </div>
          @endif
          
          {{-- Panel Aksi Dokumen yang Otomatis --}}
          <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-medium text-gray-900">Aksi Dokumen</h3>
            <div class="mt-4 space-y-3">
                <a href="{{ route('admin.invoices.download', $invoice) }}"
                   class="w-full flex-shrink-0 inline-flex items-center justify-center gap-2 px-4 py-2 bg-gray-600 text-white text-sm font-semibold rounded-lg hover:bg-gray-700 shadow-sm">
                  <x-heroicon-s-arrow-down-tray class="h-5 w-5"/>
                  Download PDF untuk di-upload
                </a>
                
                <a href="{{ ($phoneNumber && $gdriveLink) ? $whatsappUrl : '#' }}" 
                   target="_blank"
                   @if(!$phoneNumber || !$gdriveLink)
                       onclick="alert('Pastikan nomor telepon dan link Gdrive penjahit sudah terisi di Manajemen User.'); return false;"
                   @endif
                   @class([
                       'w-full flex-shrink-0 inline-flex items-center justify-center gap-2 px-4 py-2 bg-teal-500 text-white font-semibold rounded-lg hover:bg-teal-600 shadow-sm transition',
                       'opacity-50 cursor-not-allowed' => !$phoneNumber || !$gdriveLink
                   ])>
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.8 0-67.6-9.5-97.2-26.7l-7-4.1-72.5 19.1L83 358.4l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.8-16.2-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.9 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>
                    Kirim via WhatsApp
                </a>
                 @if(!$gdriveLink)
                    <p class="text-xs text-center text-red-600 mt-2">Link Gdrive belum diatur untuk penjahit ini.</p>
                @endif
            </div>
          </div>

        </div>
      </div>
    </main>
  </div>
</x-app-layout>
```
