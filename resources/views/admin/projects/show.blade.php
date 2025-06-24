<x-app-layout>
    <div class="flex h-screen bg-gray-100">
        @include('admin.partials.sidebar')

        <main class="flex-1 overflow-y-auto p-6 lg:p-8">

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row justify-between items-start mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Detail Proyek</h1>
                    <p class="text-gray-500 mt-1">Laporan lengkap untuk: <span class="font-semibold text-indigo-600">{{ $project->name }}</span></p>
                </div>
                <div class="flex items-center space-x-2 mt-4 sm:mt-0">
                    <a href="{{ route('admin.projects.edit', $project) }}" class="flex-shrink-0 inline-flex items-center gap-2 px-4 py-2 border border-transparent bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <x-heroicon-s-pencil-square class="h-5 w-5"/>
                        Edit
                    </a>
                    <a href="{{ route('admin.projects.index') }}" class="flex-shrink-0 inline-flex items-center gap-2 px-4 py-2 border border-gray-300 bg-white text-gray-700 font-semibold rounded-lg hover:bg-gray-50 shadow-sm">
                        <x-heroicon-s-arrow-left class="h-5 w-5"/>
                        Kembali
                    </a>
                </div>
            </div>

            {{-- Detail Utama Proyek --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Kolom Kiri: Gambar, Progress, Penjahit --}}
                <div class="lg:col-span-2 space-y-8">
                    {{-- Gambar & Progress Bar --}}
                     <div class="bg-white p-6 rounded-xl shadow">
                        @if($project->image)
                            <img src="{{ asset('storage/' . $project->image) }}" alt="{{ $project->name }}" class="h-80 w-full object-cover rounded-lg mb-6">
                        @endif
                        <div class="space-y-5">
                            <div>
                                <div class="flex justify-between text-sm font-medium mb-1">
                                    <span class="text-gray-600">Pendanaan</span>
                                    <span class="text-blue-600">{{ $fundingPercentage }}% ({{ $investedQty }}/{{$project->quantity}} pcs)</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5"><div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $fundingPercentage }}%"></div></div>
                            </div>
                            <div>
                                <div class="flex justify-between text-sm font-medium mb-1">
                                    <span class="text-gray-600">Produksi</span>
                                    <span class="text-green-600">{{ $productionPercentage }}% ({{$completedQty}}/{{$investedQty}} pcs)</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5"><div class="bg-green-600 h-2.5 rounded-full" style="width: {{ $productionPercentage }}%"></div></div>
                            </div>
                        </div>
                    </div>

                    {{-- Tabel Penjahit --}}
                    <div class="bg-white rounded-xl shadow overflow-hidden">
                        <div class="p-6">
                             <h3 class="text-lg font-semibold text-gray-900">Detail Tim Produksi</h3>
                             <p class="text-sm text-gray-500">Daftar penjahit yang mengerjakan proyek ini.</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Penjahit</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tugas</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progress</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Upah</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($project->assignments as $assignment)
                                    @php
                                        $done = $assignment->progress->sum('quantity_done');
                                        $assigned = $assignment->assigned_qty;
                                        $tailorProgress = $assigned > 0 ? round(($done / $assigned) * 100) : 0;
                                        $tailorWage = $done * $project->wage_per_piece;
                                    @endphp
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">{{ $assignment->tailor->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $assigned }} pcs</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-full bg-gray-200 rounded-full h-2 mr-2"><div class="bg-teal-500 h-2 rounded-full" style="width: {{ $tailorProgress }}%"></div></div>
                                                <span class="text-sm text-gray-600">{{ $tailorProgress }}%</span>
                                            </div>
                                            <span class="text-xs text-gray-400">{{ $done }}/{{$assigned}} pcs selesai</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-800">Rp {{ number_format($tailorWage, 0, ',', '.') }}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">Belum ada penjahit yang ditugaskan.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan: Rincian Keuangan & Daftar Investor --}}
                <div class="lg:col-span-1 space-y-8">
                    {{-- Rincian Keuangan --}}
                    <div class="bg-white p-6 rounded-xl shadow">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Estimasi Keuangan (Target Penuh)</h3>
                        <dl class="space-y-3 text-sm">
                             <div class="flex justify-between items-center"><dt class="text-gray-500">Total Modal dari Investor</dt><dd class="font-semibold text-gray-800">Rp {{ number_format($project->quantity * $project->price_per_piece, 0,',','.') }}</dd></div>
                            <div class="pt-3 mt-3 border-t">
                                <h4 class="text-sm font-semibold text-gray-700 mb-2">Alokasi Dana:</h4>
                                <div class="flex justify-between items-center"><dt class="text-gray-500 ml-2">› Biaya Bahan</dt><dd class="text-gray-800">- Rp {{ number_format($project->quantity * $project->material_cost, 0,',','.') }}</dd></div>
                                <div class="flex justify-between items-center"><dt class="text-gray-500 ml-2">› Upah Jahit</dt><dd class="text-gray-800">- Rp {{ number_format($project->quantity * $project->wage_per_piece, 0,',','.') }}</dd></div>
                                <div class="flex justify-between items-center"><dt class="text-gray-500 ml-2">› Profit Investor</dt><dd class="text-gray-800">- Rp {{ number_format($project->quantity * $project->profit, 0,',','.') }}</dd></div>
                            </div>
                            <div class="pt-3 mt-3 border-t">
                                <div class="flex justify-between items-center"><dt class="font-bold text-gray-800">Profit Bersih Konveksi</dt><dd class="font-bold text-green-600">Rp {{ number_format($project->quantity * $project->convection_profit, 0,',','.') }}</dd></div>
                            </div>
                        </dl>
                    </div>

                    {{-- Daftar Investor --}}
                    <div class="bg-white rounded-xl shadow overflow-hidden">
                         <div class="p-6">
                             <h3 class="text-lg font-semibold text-gray-900">Daftar Investor</h3>
                             <p class="text-sm text-gray-500">Total <span class="font-bold">{{ $project->investments->count() }}</span> investor berpartisipasi.</p>
                        </div>
                         <div class="max-h-80 overflow-y-auto">
                            <ul class="divide-y divide-gray-200">
                                @forelse ($project->investments as $investment)
                                <li class="p-4 flex justify-between items-center hover:bg-gray-50">
                                    <div class="flex items-center space-x-3">
                                        <img class="h-10 w-10 rounded-full object-cover" src="https://ui-avatars.com/api/?name={{ urlencode($investment->investor->user->name) }}&background=E8EAF6&color=3F51B5" alt="">
                                        <div>
                                            <p class="font-medium text-sm text-gray-800">{{ $investment->investor->user->name }}</p>
                                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($investment->created_at)->isoFormat('D MMM YY') }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold text-gray-800">Rp {{ number_format($investment->amount, 0, ',', '.') }}</p>
                                        <p class="text-xs text-gray-500">{{ $investment->qty }} pcs</p>
                                    </div>
                                </li>
                                @empty
                                <li class="p-8 text-center text-sm text-gray-500">Belum ada investor untuk proyek ini.</li>
                                @endforelse
                            </ul>
                         </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</x-app-layout>
