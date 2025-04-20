<x-app-layout>
    <div class="flex h-screen bg-gray-100 text-gray-800">
        {{-- Sidebar --}}
        <aside class="w-64 bg-white border-r shadow-lg hidden md:flex flex-col">
            <div class="p-6 text-2xl font-bold text-indigo-600 tracking-tight border-b">
                🧮 Investor Panel
            </div>
            <nav class="flex-1 px-4 py-6 space-y-2">
                {{-- Menu Dashboard --}}
                @php $active = request()->routeIs('investor.dashboard'); @endphp
                <a href="{{ route('investor.dashboard') }}"
                   class="flex items-center gap-3 py-2 px-3 rounded-lg transition
                      {{ $active 
                         ? 'bg-indigo-200 text-indigo-800' 
                         : 'text-gray-700 hover:bg-indigo-100 hover:text-indigo-700' }}">
                    <!-- ikon Home -->
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-5 h-5 text-indigo-500"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6"/>
                    </svg>
                    Dashboard
                </a>

                {{-- Menu Create --}}
                @php $active = request()->routeIs('investors.create'); @endphp
                <a href="{{ route('investors.create') }}"
                   class="flex items-center gap-3 py-2 px-3 rounded-lg transition
                      {{ $active 
                         ? 'bg-indigo-200 text-indigo-800' 
                         : 'text-gray-700 hover:bg-indigo-100 hover:text-indigo-700' }}">
                    <!-- ikon Plus -->
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-5 h-5 text-indigo-500"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 4v16m8-8H4"/>
                    </svg>
                    Buat Data
                </a>

                 {{-- Logout --}}
                 <form method="POST" action="{{ route('logout') }}" class="pt-4">
                    @csrf
                    <button type="submit"
                            class="flex items-center gap-3 w-full text-left py-2 px-3 rounded-lg text-red-600 hover:bg-red-100 transition">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-5 h-5"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7
                                     m6 4v1a2 2 0 002 2h3a2 2 0 002-2V7
                                     a2 2 0 00-2-2h-3a2 2 0 00-2 2v1"/>
                        </svg>
                        Logout
                    </button>
                </form>
            </nav>
        </aside>

        {{-- Main Content --}}
        <main class="flex-1 overflow-y-auto p-6">
            {{-- Header --}}
            <div class="mb-8">
                <h1 class="text-3xl font-semibold text-indigo-700">Investor Dashboard</h1>
                <p class="text-gray-500">Halo, {{ Auth::user()->name }}!</p>
            </div>

            {{-- Tabel Data Investor (jika ada) --}}
            @if(isset($investors) && $investors->count())
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Investasi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deadline</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($investors as $i => $inv)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $i+1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $inv->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $inv->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ number_format($inv->amount, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $inv->deadline }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500">Belum ada data investasi. Silakan klik “Buat Data” untuk menambahkan.</p>
            @endif
        </main>
    </div>
</x-app-layout>
