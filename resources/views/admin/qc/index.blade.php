<x-app-layout>
    <div class="flex h-screen bg-gray-100 text-gray-800">
        @include('admin.partials.sidebar')

        <main class="flex-1 overflow-y-auto p-6 lg:p-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Quality Control (QC)</h1>
                <p class="text-gray-500 mt-1">Periksa laporan progres dari penjahit dan lihat riwayat pemeriksaan.</p>
            </div>

            {{-- PERUBAHAN: Menambahkan Navigasi Tab --}}
            <div class="mb-6 border-b border-gray-200">
                <nav class="-mb-px flex space-x-6" aria-label="Tabs">
                    {{-- Tab untuk Menunggu Pemeriksaan --}}
                    <a href="{{ route('admin.qc.index', ['tab' => 'pending']) }}"
                       @class([
                           'shrink-0 border-b-2 px-1 pb-4 text-sm font-medium',
                           'border-indigo-500 text-indigo-600' => $tab === 'pending',
                           'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' => $tab !== 'pending',
                       ])>
                        Menunggu Pemeriksaan
                    </a>
                    {{-- Tab untuk Riwayat Pemeriksaan --}}
                    <a href="{{ route('admin.qc.index', ['tab' => 'history']) }}"
                       @class([
                           'shrink-0 border-b-2 px-1 pb-4 text-sm font-medium',
                           'border-indigo-500 text-indigo-600' => $tab === 'history',
                           'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' => $tab !== 'history',
                       ])>
                        Riwayat Pemeriksaan
                    </a>
                </nav>
            </div>

            {{-- Menampilkan notifikasi sukses atau error --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-lg shadow-sm" role="alert">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-100 text-red-800 rounded-lg shadow-sm" role="alert">{{ session('error') }}</div>
            @endif

            {{-- PERUBAHAN: Memuat partial secara kondisional berdasarkan tab yang aktif --}}
            @if ($tab === 'pending')
                @include('admin.qc.partials._pending_table')
            @else
                @include('admin.qc.partials._history_table')
            @endif
            
        </main>
    </div>
</x-app-layout>
