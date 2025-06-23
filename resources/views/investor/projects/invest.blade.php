<x-app-layout>
  <div class="flex h-screen bg-gray-50">
    @include('investor.partials.sidebar')

    <main class="flex-1 overflow-y-auto p-6 lg:p-8">
      
      {{-- Tombol Kembali & Judul --}}
      <div class="mb-6">
        <a href="{{ route('investor.projects.index') }}" class="flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 mb-2">
          <x-heroicon-s-arrow-left class="h-4 w-4" />
          Kembali ke Marketplace
        </a>
      </div>

      {{-- Kita pindahkan form untuk membungkus semuanya --}}
      <form id="investment-form" action="{{ route('investor.projects.store', $project) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8"
             x-data="{ 
                qty: {{ old('qty', 1) }},
                pricePerPiece: {{ $project->price_per_piece }},
                profitPerPiece: {{ $project->profit }},
                maxQty: {{ $remainingQty }},
                agreementChecked: false,
                agreementModalOpen: false,
                totalInvestment() { return this.qty * this.pricePerPiece; },
                totalProfit() { return this.qty * this.profitPerPiece; }
             }">
          
          {{-- Kolom Kiri: Detail Proyek --}}
          <div class="lg:col-span-3">
            <div class="bg-white rounded-lg shadow-lg">
              @if($project->image)
                <img src="{{ asset('storage/' . $project->image) }}" alt="{{ $project->name }}" class="h-80 w-full object-cover rounded-t-lg">
              @else
                <div class="h-80 w-full bg-gray-200 flex items-center justify-center rounded-t-lg"><x-heroicon-o-photo class="w-20 h-20 text-gray-400"/></div>
              @endif
              <div class="p-6">
                <h1 class="text-3xl font-bold text-gray-900">{{ $project->name }}</h1>
                <p class="text-gray-500 mt-2">Deadline Proyek: <span class="font-medium text-red-600">{{ \Carbon\Carbon::parse($project->deadline)->isoFormat('dddd, D MMMM Y') }}</span></p>
                <p class="mt-4 text-gray-600 leading-relaxed">Ini adalah peluang investasi yang menjanjikan di industri fesyen. Dengan mendanai proyek ini, Anda berkontribusi pada penciptaan produk berkualitas tinggi sambil mendapatkan potensi keuntungan yang menarik.</p>
              </div>
            </div>
          </div>

          {{-- Kolom Kanan: Aksi Investasi --}}
          <div class="lg:col-span-2">
            <div class="bg-white p-6 rounded-lg shadow-lg sticky top-8">
              <h2 class="text-xl font-semibold text-gray-800">Formulir Investasi</h2>
              {{-- Detail Finansial & Kalkulator Interaktif --}}
              <div class="mt-4 grid grid-cols-2 gap-4 text-center">
                  <div class="bg-gray-50 p-3 rounded-lg"><p class="text-xs text-gray-500">Harga per Slot</p><p class="text-sm font-bold text-gray-800">Rp {{ number_format($project->price_per_piece, 0, ',', '.') }}</p></div>
                  <div class="bg-green-50 p-3 rounded-lg"><p class="text-xs text-green-700">Profit per Slot</p><p class="text-sm font-bold text-green-600">Rp {{ number_format($project->profit, 0, ',', '.') }}</p></div>
              </div>
              <div class="mt-6">
                <label for="qty" class="block text-sm font-medium text-gray-700">Jumlah Slot (Unit/pcs)</label>
                <div class="mt-1 flex items-center gap-4"><input type="number" name="qty" id="qty" x-model.number="qty" min="1" :max="maxQty" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></div>
                <input type="range" x-model.number="qty" min="1" :max="maxQty" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer mt-2">
                <p class="text-xs text-gray-500 mt-1">Slot tersedia: {{ $remainingQty }} pcs</p>
                <x-input-error :messages="$errors->get('qty')" class="mt-2" />
              </div>
              <div class="mt-6 pt-4 border-t-2 border-dashed">
                <div class="space-y-2 text-sm"><div class="flex justify-between"><dt class="text-gray-500">Total Investasi Anda</dt><dd class="font-semibold text-gray-900" x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(totalInvestment())"></dd></div><div class="flex justify-between"><dt class="text-gray-500">Estimasi Keuntungan</dt><dd class="font-semibold text-green-600" x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(totalProfit())"></dd></div></div>
              </div>
              <div class="mt-6 space-y-4">
                  <div>
                    <label for="receipt" class="block text-sm font-medium text-gray-700">Upload Bukti Pembayaran</label>
                    <input type="file" name="receipt" id="receipt" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <x-input-error :messages="$errors->get('receipt')" class="mt-2"/>
                  </div>
                  <div>
                      <label for="message" class="block text-sm font-medium text-gray-700">Pesan (Opsional)</label>
                      <textarea id="message" name="message" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Contoh: Pembayaran untuk 10 slot Kemeja Flanel.">{{ old('message') }}</textarea>
                  </div>
              </div>

              <div class="mt-6">
                {{-- Tombol ini sekarang membuka modal --}}
                <button type="button" @click="agreementModalOpen = true" class="w-full px-4 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-transform transform hover:scale-105">
                  Ajukan Investasi Sekarang
                </button>
              </div>
            </div>

            <!-- Modal untuk Perjanjian Investasi -->
            <div x-show="agreementModalOpen" @keydown.escape.window="agreementModalOpen = false" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
              <div class="flex items-center justify-center min-h-screen px-4">
                <div x-show="agreementModalOpen" @click.away="agreementModalOpen = false" x-transition class="fixed inset-0 transition-opacity" aria-hidden="true"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>
                <div x-show="agreementModalOpen" x-transition class="inline-block bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all my-8 max-w-2xl w-full">
                  <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900">Perjanjian Investasi</h3>
                    <p class="text-sm text-gray-500 mt-1">Harap tinjau dan setujui rincian investasi Anda sebelum melanjutkan.</p>
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg max-h-64 overflow-y-auto space-y-4">
                      <dl class="text-sm space-y-2">
                        <div><dt class="font-semibold text-gray-800">Nama Proyek</dt><dd class="text-gray-600">{{ $project->name }}</dd></div>
                        <div><dt class="font-semibold text-gray-800">Jumlah Investasi</dt><dd class="text-gray-600"><span x-text="qty"></span> slot/pcs senilai <span class="font-bold" x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(totalInvestment())"></span></dd></div>
                        <div><dt class="font-semibold text-gray-800">Estimasi Keuntungan</dt><dd class="text-gray-600"><span class="font-bold text-green-600" x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(totalProfit())"></span></dd></div>
                      </dl>
                      <div class="text-xs text-gray-500">
                          <h4 class="font-semibold text-gray-600">Syarat & Ketentuan</h4>
                          <p>Dengan melanjutkan, saya menyatakan bahwa saya telah memahami rincian proyek, potensi risiko, dan estimasi keuntungan. Saya setuju untuk menanamkan modal sejumlah yang tertera di atas dan memahami bahwa pengembalian investasi bergantung pada keberhasilan proyek.</p>
                      </div>
                    </div>
                    <div class="mt-6">
                        <label for="agreement" class="flex items-center">
                            <input type="checkbox" id="agreement" name="agreement" x-model="agreementChecked" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-600">Saya telah membaca dan menyetujui perjanjian investasi ini.</span>
                        </label>
                         <x-input-error :messages="$errors->get('agreement')" class="mt-2"/>
                    </div>
                  </div>
                  <div class="bg-gray-50 px-6 py-4 flex flex-col-reverse sm:flex-row sm:justify-end sm:gap-3">
                    <button type="button" @click="agreementModalOpen = false" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
                    <button type="submit" form="investment-form" :disabled="!agreementChecked" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:w-auto sm:text-sm disabled:bg-gray-400 disabled:cursor-not-allowed">Konfirmasi & Ajukan</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </main>
  </div>
</x-app-layout>
