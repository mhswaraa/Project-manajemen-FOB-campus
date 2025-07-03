<x-app-layout>
    <div class="flex h-screen bg-gray-50">
        @include('admin.partials.sidebar')
        <main class="flex-1 overflow-y-auto p-6 lg:p-8">
            
            {{-- Header Halaman --}}
            <div class="flex flex-col sm:flex-row justify-between items-start mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Proses Quality Control</h1>
                    <p class="text-gray-500 mt-1">Periksa laporan progres dari penjahit.</p>
                </div>
                <a href="{{ route('admin.qc.index') }}" class="mt-4 sm:mt-0 inline-flex items-center gap-2 px-4 py-2 border border-gray-300 bg-white text-gray-700 font-semibold rounded-lg hover:bg-gray-50 shadow-sm">
                    <x-heroicon-s-arrow-left class="h-5 w-5"/>
                    Kembali ke Daftar QC
                </a>
            </div>

            {{-- Pesan Error Validasi --}}
            @if(session('error'))
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                    <p class="font-bold">Terjadi Kesalahan</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            @if($progress)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Kolom Kiri: Form QC --}}
                <div class="lg:col-span-2">
                    <form action="{{ route('admin.qc.process', $progress) }}" method="POST" class="bg-white p-6 rounded-lg shadow-md space-y-6" x-data="{
                        quantityDone: {{ $progress->quantity_done ?? 0 }},
                        acceptedQty: {{ old('accepted_qty', $progress->quantity_done ?? 0) }},
                        rejectedQty: {{ old('rejected_qty', 0) }},
                        updateRejected() {
                            const accepted = parseInt(this.acceptedQty) || 0;
                            if (accepted >= 0 && accepted <= this.quantityDone) {
                                this.rejectedQty = this.quantityDone - accepted;
                            } else if (accepted > this.quantityDone) {
                                this.acceptedQty = this.quantityDone;
                                this.rejectedQty = 0;
                            }
                        },
                        updateAccepted() {
                            const rejected = parseInt(this.rejectedQty) || 0;
                            if (rejected >= 0 && rejected <= this.quantityDone) {
                                this.acceptedQty = this.quantityDone - rejected;
                            } else if (rejected > this.quantityDone) {
                                this.rejectedQty = this.quantityDone;
                                this.acceptedQty = 0;
                            }
                        }
                    }" x-init="updateRejected()">

                        @csrf

                        <h2 class="text-xl font-bold text-gray-800 border-b pb-3">Form Pemeriksaan</h2>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jumlah Dilaporkan</label>
                            <p class="mt-1 text-2xl font-bold text-indigo-600">{{ $progress->quantity_done ?? 0 }} pcs</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="accepted_qty" class="block text-sm font-medium text-gray-700">Jumlah Diterima</label>
                                <x-text-input id="accepted_qty" name="accepted_qty" type="number" class="mt-1 block w-full" x-model="acceptedQty" @input="updateRejected()" required />
                                <x-input-error :messages="$errors->get('accepted_qty')" class="mt-1" />
                            </div>
                            <div>
                                <label for="rejected_qty" class="block text-sm font-medium text-gray-700">Jumlah Ditolak (Revisi)</label>
                                <x-text-input id="rejected_qty" name="rejected_qty" type="number" class="mt-1 block w-full" x-model="rejectedQty" @input="updateAccepted()" required />
                                <x-input-error :messages="$errors->get('rejected_qty')" class="mt-1" />
                            </div>
                        </div>

                        <div>
                            <label for="qc_notes" class="block text-sm font-medium text-gray-700">Catatan QC (Opsional)</label>
                            <textarea id="qc_notes" name="qc_notes" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('qc_notes') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">Berikan catatan jika ada pekerjaan yang ditolak/perlu direvisi.</p>
                            <x-input-error :messages="$errors->get('qc_notes')" class="mt-1" />
                        </div>

                        <div class="flex justify-end pt-4 border-t">
                            <x-primary-button>Simpan Hasil QC</x-primary-button>
                        </div>
                    </form>
                </div>

                {{-- Kolom Kanan: Info Laporan --}}
                <div class="lg:col-span-1">
                    <div class="bg-white p-6 rounded-lg shadow-md space-y-4">
                        <h3 class="text-lg font-medium text-gray-900">Informasi Laporan</h3>
                        <dl class="text-sm space-y-2">
                            <div class="flex justify-between"><dt class="text-gray-500">Proyek:</dt><dd class="font-semibold text-gray-800 text-right">{{ $progress->assignment?->project?->name ?? 'N/A' }}</dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500">Penjahit:</dt><dd class="font-semibold text-gray-800 text-right">{{ $progress->assignment?->tailor?->user?->name ?? 'N/A' }}</dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500">Tanggal Lapor:</dt><dd class="font-semibold text-gray-800 text-right">{{ $progress->date?->isoFormat('D MMMM YYYY') ?? 'N/A' }}</dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500">ID Laporan:</dt><dd class="font-mono text-gray-800">#{{ $progress->id ?? 'N/A' }}</dd></div>
                        </dl>
                        @if($progress->notes)
                        <div class="pt-4 border-t">
                            <dt class="text-gray-500 font-medium">Catatan Penjahit:</dt>
                            <dd class="mt-1 text-gray-700 italic">"{{ $progress->notes }}"</dd>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @else
            <div class="bg-white p-6 rounded-lg shadow-md">
                <p class="text-center text-gray-500">Laporan progres tidak ditemukan atau sudah tidak valid.</p>
            </div>
            @endif
        </main>
    </div>
</x-app-layout>
