{{-- resources/views/admin/qc/index.blade.php --}}
<x-app-layout>
  {{-- Alpine.js untuk state management modal --}}
  <div x-data="{ showModal: false, selectedProgress: null }" @keydown.escape.window="showModal = false">
    <div class="flex h-screen bg-gray-100 text-gray-800">
        @include('admin.partials.sidebar') {{-- Sesuaikan dengan path sidebar admin Anda --}}

        <main class="flex-1 overflow-y-auto p-6">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Quality Control (QC)</h1>
                <p class="text-gray-500 mt-1">Daftar laporan progres dari penjahit yang menunggu pemeriksaan.</p>
            </div>

            {{-- Menampilkan notifikasi sukses atau error --}}
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg shadow-sm">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg shadow-sm">{{ session('error') }}</div>
            @endif

            {{-- Tabel Daftar Laporan --}}
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        {{-- AWAL PERUBAHAN HEADER TABEL --}}
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proyek & Penjahit</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl Lapor</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Qty Lapor</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Qty Diterima</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Qty Ditolak</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status & Catatan QC</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                        {{-- AKHIR PERUBAHAN HEADER TABEL --}}
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($progressReports as $report)
                            <tr>
                                {{-- AWAL PERUBAHAN ISI TABEL --}}
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ $report->assignment->project->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $report->assignment->tailor->user->name }}</div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">{{ \Carbon\Carbon::parse($report->date)->isoFormat('D MMM YYYY') }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-bold text-blue-600">{{ $report->quantity_done }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-bold text-green-600">{{ $report->accepted_qty ?? '-' }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-bold text-red-600">{{ $report->rejected_qty ?? '-' }}</td>
                                <td class="px-4 py-4 whitespace-normal text-sm text-gray-800">
                                    <span @class([
                                        'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                        'bg-yellow-100 text-yellow-800' => $report->status === 'pending_qc',
                                        'bg-green-100 text-green-800' => $report->status === 'approved',
                                    ])>
                                        {{ str_replace('_', ' ', ucfirst($report->status)) }}
                                    </span>
                                    @if($report->qc_notes)
                                        <p class="text-xs text-gray-500 italic mt-1 break-words">"{{ $report->qc_notes }}"</p>
                                    @endif
                                     @if($report->qcAdmin)
                                        <p class="text-xs text-gray-400 mt-1">
                                            Oleh: {{ $report->qcAdmin->name }} pada {{ \Carbon\Carbon::parse($report->qc_checked_at)->format('d/m/y H:i')}}
                                        </p>
                                    @endif
                                </td>
                                {{-- AKHIR PERUBAHAN ISI TABEL --}}
                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if($report->status === 'pending_qc')
                                        <button type="button" @click="showModal = true; selectedProgress = {{ json_encode($report) }}"
                                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 text-xs">
                                            Proses
                                        </button>
                                    @else
                                        <button type="button" @click="showModal = true; selectedProgress = {{ json_encode($report) }}"
                                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 text-xs">
                                            Detail
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            {{-- PERBAIKAN: Colspan disesuaikan menjadi 7 --}}
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-gray-500">Tidak ada laporan yang perlu diperiksa saat ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $progressReports->links() }}
            </div>
        </main>
    </div>

    {{-- Modal untuk Proses QC --}}
    <div x-show="showModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full flex items-center justify-center" x-cloak>
        <div @click.away="showModal = false" class="relative mx-auto p-6 border w-full max-w-lg shadow-lg rounded-md bg-white">
            <div class="text-left">
                <h3 class="text-xl leading-6 font-bold text-gray-900" x-text="selectedProgress?.status === 'pending_qc' ? 'Proses Laporan QC' : 'Detail Hasil QC'"></h3>
                <div class="mt-2 text-sm text-gray-600">
                    <p>Proyek: <span x-text="selectedProgress?.assignment.project.name" class="font-semibold"></span></p>
                    <p>Penjahit: <span x-text="selectedProgress?.assignment.tailor.user.name" class="font-semibold"></span></p>
                    <p>Jumlah Dilaporkan: <span x-text="selectedProgress?.quantity_done + ' pcs'" class="font-bold text-blue-700"></span></p>
                </div>

                {{-- PERUBAHAN: Form sekarang hanya tampil jika status 'pending_qc' --}}
                <form x-show="selectedProgress?.status === 'pending_qc'" :action="`/admin/qc/process/${selectedProgress?.id}`" method="POST" class="space-y-4 mt-4">
                    @csrf
                    <div>
                        <label for="accepted_qty" class="block text-sm font-medium text-gray-700">Jumlah Diterima</label>
                        <input type="number" name="accepted_qty" id="accepted_qty" min="0" :max="selectedProgress?.quantity_done" required x-model="selectedProgress.accepted_qty"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label for="rejected_qty" class="block text-sm font-medium text-gray-700">Jumlah Ditolak (Reject)</label>
                        <input type="number" name="rejected_qty" id="rejected_qty" min="0" :max="selectedProgress?.quantity_done" required x-model="selectedProgress.rejected_qty"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label for="qc_notes" class="block text-sm font-medium text-gray-700">Catatan QC (jika ada)</label>
                        <textarea name="qc_notes" id="qc_notes" rows="3"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Contoh: Jahitan kurang rapi pada bagian lengan." x-model="selectedProgress.qc_notes"></textarea>
                    </div>
                    
                    <div class="pt-4 flex justify-end gap-3">
                        <button type="button" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300" @click="showModal = false">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Simpan Hasil QC
                        </button>
                    </div>
                </form>

                {{-- PERUBAHAN: Tampilan detail jika status sudah 'approved' --}}
                 <div x-show="selectedProgress?.status === 'approved'" class="mt-4 pt-4 border-t text-sm">
                    <p><strong>Hasil Pemeriksaan:</strong></p>
                    <p>Diterima: <span x-text="selectedProgress?.accepted_qty" class="font-semibold text-green-700"></span> pcs</p>
                    <p>Ditolak: <span x-text="selectedProgress?.rejected_qty" class="font-semibold text-red-700"></span> pcs</p>
                    <p>Catatan: <span x-text="selectedProgress?.qc_notes || '-'" class="italic"></span></p>
                     <p x-show="selectedProgress?.qc_admin_id">Diperiksa oleh: <span x-text="selectedProgress?.qc_admin.name"></span> pada <span x-text="new Date(selectedProgress?.qc_checked_at).toLocaleString('id-ID')"></span></p>
                    <div class="pt-4 flex justify-end">
                         <button type="button" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300" @click="showModal = false">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
</x-app-layout>
