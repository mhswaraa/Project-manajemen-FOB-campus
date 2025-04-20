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
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 py-2 px-3 rounded-lg transition {{ $active ? 'bg-indigo-200 text-indigo-800' : 'text-gray-700 hover:bg-indigo-100 hover:text-indigo-700' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6"/>
                    </svg>
                    Dashboard
                </a>

                {{-- Manajemen Proyek --}}
                @php $active = request()->routeIs('admin.projects.*'); @endphp
                <a href="{{ route('admin.projects.index') }}" class="flex items-center gap-3 py-2 px-3 rounded-lg transition {{ $active ? 'bg-indigo-200 text-indigo-800' : 'text-gray-700 hover:bg-indigo-100 hover:text-indigo-700' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18M3 17h18"/>
                    </svg>
                    Manajemen Proyek
                </a>

                {{-- Manajemen Penjahit --}}
                @php $active = request()->routeIs('admin.penjahits.*'); @endphp
                <a href="{{ route('admin.penjahits.index') }}" class="flex items-center gap-3 py-2 px-3 rounded-lg transition {{ $active ? 'bg-indigo-200 text-indigo-800' : 'text-gray-700 hover:bg-indigo-100 hover:text-indigo-700' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2l4 7-4 7-4-7 4-7z"/>
                    </svg>
                    Manajemen Penjahit
                </a>

                {{-- Manajemen Investor --}}
                @php $active = request()->routeIs('admin.investors.*'); @endphp
                <a href="{{ route('admin.investors.index') }}" class="flex items-center gap-3 py-2 px-3 rounded-lg transition {{ $active ? 'bg-indigo-200 text-indigo-800' : 'text-gray-700 hover:bg-indigo-100 hover:text-indigo-700' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm0 10v2m0-14V2"/>
                    </svg>
                    Manajemen Investor
                </a>

                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}" class="pt-4">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 w-full text-left py-2 px-3 rounded-lg text-red-600 hover:bg-red-100 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 002 2h3a2 2 0 002-2V7a2 2 0 00-2-2h-3a2 2 0 00-2 2v1"/>
                        </svg>
                        Logout
                    </button>
                </form>
            </nav>
        </aside>

        <main class="flex-1 overflow-y-auto p-6">
            {{-- Header + Alert --}}
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-indigo-700">Manajemen Investor</h1>
                @if(session('success'))
                    <div class="px-4 py-2 bg-green-100 text-green-800 rounded">
                        {{ session('success') }}
                    </div>
                @endif
            </div>

            {{-- Card Ringkasan --}}
            <div class="mb-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-lg shadow flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M12 8v8m4-4H8"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Total Investor</p>
                        <p class="text-2xl font-semibold text-gray-800">{{ $investorCount }}</p>
                    </div>
                </div>
                {{-- (Boleh tambahkan card lain jika perlu) --}}
            </div>

            {{-- Tombol Tambah --}}
            <div class="mb-4">
                <a href="{{ route('admin.investors.create') }}"
                   class="inline-block px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    + Tambah Investor
                </a>
            </div>

            {{-- Tabel Investor --}}
            <div class="bg-white shadow rounded-lg overflow-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. HP</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Investasi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deadline</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($investors as $i => $inv)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $i+1 }}</td>
                            <td class="px-6 py-4 text-sm text-gray-800">{{ $inv->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $inv->email }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $inv->phone }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">Rp {{ number_format($inv->amount,0,',','.') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $inv->deadline }}</td>
                            <td class="px-6 py-4 text-center text-sm font-medium space-x-2">
                                <a href="{{ route('admin.investors.edit',$inv) }}"
                                   class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                <form action="{{ route('admin.investors.destroy',$inv) }}"
                                      method="POST" class="inline-block"
                                      onsubmit="return confirm('Hapus investor ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                        @if($investors->isEmpty())
                            <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                Belum ada investor terdaftar.
                            </td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</x-app-layout>
