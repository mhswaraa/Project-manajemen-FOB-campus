{{-- resources/views/dashboard/admin.blade.php --}}
<x-app-layout>
    <div class="flex h-screen bg-gray-100 text-gray-800">
        {{-- Sidebar --}}
        {{-- resources/views/dashboard/admin.blade.php --}}
<aside class="w-64 bg-white border-r shadow-lg hidden md:flex flex-col">
  <div class="p-6 text-2xl font-bold text-indigo-600 tracking-tight border-b">
    🧵 PM FOB
  </div>
  <nav class="flex-1 px-4 py-6 space-y-2" x-data="{ openProyek: false }">
    {{-- Dashboard --}}
    @php $active = request()->routeIs('dashboard'); @endphp
    <a href="{{ route('dashboard') }}"
       class="flex items-center gap-3 py-2 px-3 rounded-lg transition {{ $active ? 'bg-indigo-200 text-indigo-800' : 'text-gray-700 hover:bg-indigo-100 hover:text-indigo-700' }}">
      <!-- icon home -->
      Dashboard
    </a>

    {{-- Manajemen Proyek --}}
    @php $isProyekSection = request()->routeIs('admin.projects.*') || request()->routeIs('admin.projects.invested'); @endphp
    <button @click="openProyek = !openProyek"
            class="flex items-center justify-between w-full gap-3 py-2 px-3 rounded-lg transition
                   {{ $isProyekSection ? 'bg-indigo-200 text-indigo-800' : 'text-gray-700 hover:bg-indigo-100 hover:text-indigo-700' }}">
      <span class="flex items-center gap-3">
        <!-- icon proyek -->
        Manajemen Proyek
      </span>
      <svg :class="{ 'transform rotate-90': openProyek }"
           class="w-4 h-4 transition-transform"
           xmlns="http://www.w3.org/2000/svg" fill="none"
           viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 5l7 7-7 7"/>
      </svg>
    </button>

    {{-- Submenu Proyek --}}
    <div x-show="openProyek"
         x-collapse
         class="space-y-1 pl-8">
      {{-- Link ke semua proyek --}}
      <a href="{{ route('admin.projects.index') }}"
         class="block py-2 px-3 rounded-lg transition
                {{ request()->routeIs('admin.projects.index') ? 'bg-indigo-100 text-indigo-800' : 'text-gray-600 hover:bg-indigo-50 hover:text-gray-800' }}">
        Daftar Proyek
      </a>
      {{-- Link ke proyek yang sudah diinvestasi --}}
      <a href="{{ route('admin.projects.invested') }}"
         class="block py-2 px-3 rounded-lg transition
                {{ request()->routeIs('admin.projects.invested') ? 'bg-indigo-100 text-indigo-800' : 'text-gray-600 hover:bg-indigo-50 hover:text-gray-800' }}">
        Proyek Ter‑Investasi
      </a>
    </div>

    {{-- Manajemen Penjahit --}}
    @php $active = request()->routeIs('admin.penjahits.*'); @endphp
    <a href="{{ route('admin.penjahits.index') }}"
       class="flex items-center gap-3 py-2 px-3 rounded-lg transition {{ $active ? 'bg-indigo-200 text-indigo-800' : 'text-gray-700 hover:bg-indigo-100 hover:text-indigo-700' }}">
      <!-- icon penjahit -->
      Manajemen Penjahit
    </a>

    {{-- Manajemen Investor --}}
    @php $active = request()->routeIs('admin.investors.*'); @endphp
    <a href="{{ route('admin.investors.index') }}"
       class="flex items-center gap-3 py-2 px-3 rounded-lg transition {{ $active ? 'bg-indigo-200 text-indigo-800' : 'text-gray-700 hover:bg-indigo-100 hover:text-indigo-700' }}">
      <!-- icon investor -->
      Manajemen Investor
    </a>

    {{-- Logout --}}
    <form method="POST" action="{{ route('logout') }}" class="pt-4">
      @csrf
      <button type="submit"
              class="flex items-center gap-3 w-full text-left py-2 px-3 rounded-lg text-red-600 hover:bg-red-100 transition">
        <!-- icon logout -->
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
                        ['label' => 'Admin', 'count' => $adminCount, 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="7" r="4"/><path d="M5.5 21a6.5 6.5 0 0113 0"/></svg>', 'color' => 'bg-indigo-100', 'text' => 'text-indigo-600'],
                        ['label' => 'Investor', 'count' => $investorCount, 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 8v8m4-4H8"/></svg>', 'color' => 'bg-green-100', 'text' => 'text-green-600'],
                        ['label' => 'Owner', 'count' => $ownerCount, 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4z"/></svg>', 'color' => 'bg-yellow-100', 'text' => 'text-yellow-600'],
                        ['label' => 'Penjahit', 'count' => $penjahitCount, 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 4l8 8m0 0l-8 8m8-8H4"/></svg>', 'color' => 'bg-teal-100', 'text' => 'text-teal-600'],
                        ['label' => 'Proyek', 'count' => $projectCount, 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-pink-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 7h18M3 12h18M3 17h18"/></svg>', 'color' => 'bg-pink-100', 'text' => 'text-pink-600'],
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

            {{-- Tabel 1: List Proyek --}}
            <div class="bg-white shadow rounded-lg mb-8 overflow-x-auto">
                <div class="px-6 py-4 border-b">
                    <h2 class="text-lg font-semibold text-gray-700">Daftar Proyek</h2>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ProyekID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Proyek</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Anggaran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deadline</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gambar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($projects as $proj)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $proj->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $proj->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">Rp
                                    {{ number_format($proj->budget, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $proj->deadline }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    @if($proj->image)
                                        <img src="{{ asset('storage/' . $proj->image) }}" alt="Gbr Proyek"
                                            class="h-10 w-10 object-cover rounded">
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ ucfirst($proj->status) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Tabel 2: List Investor --}}
            <div class="bg-white shadow rounded-lg mb-8 overflow-x-auto">
                <div class="px-6 py-4 border-b">
                    <h2 class="text-lg font-semibold text-gray-700">Daftar Investor</h2>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Investor
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. HP</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Investasi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deadline</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($investors as $i => $inv)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $i + 1 }}</td>
                                <td class="px-6 py-4 text-sm text-gray-800">{{ $inv->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $inv->email }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $inv->phone }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">Rp
                                    {{ number_format($inv->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $inv->deadline }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Tabel 3: List Penjahit --}}
            <div class="bg-white shadow rounded-lg overflow-x-auto">
                <div class="px-6 py-4 border-b">
                    <h2 class="text-lg font-semibold text-gray-700">Daftar Penjahit Borongan</h2>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alamat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. HP</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($penjahits as $j => $pen)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $j + 1 }}</td>
                                <td class="px-6 py-4 text-sm text-gray-800">{{ $pen->user->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $pen->address }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $pen->phone }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ ucfirst($pen->status) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</x-app-layout>