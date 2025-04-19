<x-app-layout>
    <div class="flex h-screen bg-gray-100 text-gray-800">
        {{-- Sidebar --}}
        <aside class="w-64 bg-white border-r shadow-lg hidden md:flex flex-col">
            <div class="p-6 text-2xl font-bold text-indigo-600 tracking-tight border-b">
                🧵 PM FOB
            </div>
            <nav class="flex-1 px-4 py-6 space-y-2">
        
                {{-- Dashboard --}}
                @php $active = request()->routeIs('dashboard'); @endphp
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3 py-2 px-3 rounded-lg transition
                      {{ $active 
                         ? 'bg-indigo-200 text-indigo-800' 
                         : 'text-gray-700 hover:bg-indigo-100 hover:text-indigo-700' }}">
                    <!-- ikon -->
                    Dashboard
                </a>
        
                {{-- Manajemen Proyek --}}
                @php $active = request()->routeIs('projects.*'); @endphp
                <a href="#"
                   class="flex items-center gap-3 py-2 px-3 rounded-lg transition
                      {{ $active 
                         ? 'bg-indigo-200 text-indigo-800' 
                         : 'text-gray-700 hover:bg-indigo-100 hover:text-indigo-700' }}">
                    <!-- ikon -->
                    Manajemen Proyek
                </a>
        
                {{-- Manajemen Penjahit --}}
                @php $active = request()->routeIs('penjahits.*'); @endphp
                <a href="#"
                   class="flex items-center gap-3 py-2 px-3 rounded-lg transition
                      {{ $active 
                         ? 'bg-indigo-200 text-indigo-800' 
                         : 'text-gray-700 hover:bg-indigo-100 hover:text-indigo-700' }}">
                    <!-- ikon -->
                    Manajemen Penjahit
                </a>
        
                {{-- Manajemen Investor --}}
                @php $active = request()->routeIs('investors.*'); @endphp
                <a href="#"
                   class="flex items-center gap-3 py-2 px-3 rounded-lg transition
                      {{ $active 
                         ? 'bg-indigo-200 text-indigo-800' 
                         : 'text-gray-700 hover:bg-indigo-100 hover:text-indigo-700' }}">
                    <!-- ikon -->
                    Manajemen Investor
                </a>
        
                {{-- Laporan --}}
                @php $active = request()->routeIs('reports.*'); @endphp
                <a href="#"
                   class="flex items-center gap-3 py-2 px-3 rounded-lg transition
                      {{ $active 
                         ? 'bg-indigo-200 text-indigo-800' 
                         : 'text-gray-700 hover:bg-indigo-100 hover:text-indigo-700' }}">
                    <!-- ikon -->
                    Laporan
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
                <h1 class="text-3xl font-semibold text-indigo-700">Admin Dashboard</h1>
                <p class="text-gray-500">Halo, {{ Auth::user()->name }}!</p>
            </div>

            {{-- Cards Summary --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6 mb-8">
                @php
                    $cards = [
                        ['label'=>'Admin','count'=>$adminCount,'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="7" r="4"/><path d="M5.5 21a6.5 6.5 0 0113 0"/></svg>','color'=>'bg-indigo-100','text'=>'text-indigo-600'],
                        ['label'=>'Investor','count'=>$investorCount,'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 8v8m4-4H8"/></svg>','color'=>'bg-green-100','text'=>'text-green-600'],
                        ['label'=>'Owner','count'=>$ownerCount,'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4z"/></svg>','color'=>'bg-yellow-100','text'=>'text-yellow-600'],
                        ['label'=>'Penjahit','count'=>$penjahitCount,'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 4l8 8m0 0l-8 8m8-8H4"/></svg>','color'=>'bg-teal-100','text'=>'text-teal-600'],
                        ['label'=>'Proyek','count'=>$projectCount,'icon'=>'<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-pink-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 7h18M3 12h18M3 17h18"/></svg>','color'=>'bg-pink-100','text'=>'text-pink-600'],
                    ];
                @endphp

                @foreach ($cards as $card)
                    <div class="flex items-center p-4 bg-white rounded-lg shadow hover:shadow-md transition">
                        <div class="p-3 rounded-full {!! $card['color'] !!}">
                            {!! $card['icon'] !!}
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">{{ $card['label'] }}</p>
                            <p class="text-2xl font-semibold {!! $card['text'] !!}">{{ $card['count'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Tabel Investor --}}
            <div class="bg-white shadow rounded-lg mb-8 overflow-hidden">
                <div class="px-6 py-4 border-b">
                    <h2 class="text-lg font-semibold text-gray-700">Daftar Investor</h2>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($investors as $i => $inv)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $i+1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $inv->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $inv->email }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Tabel Penjahit Borongan --}}
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b">
                    <h2 class="text-lg font-semibold text-gray-700">Daftar Penjahit Borongan</h2>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($penjahits as $j => $pen)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $j+1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $pen->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $pen->email }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</x-app-layout>
