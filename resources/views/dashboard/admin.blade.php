<x-app-layout>
    <div class="flex h-screen bg-gray-100 text-gray-800">
        {{-- Sidebar --}}
        <aside class="w-64 bg-white border-r shadow-lg hidden md:flex flex-col">
            <div class="p-6 text-2xl font-bold text-indigo-600 tracking-tight border-b">
                🧵 PM FOB
            </div>
            <nav class="flex-1 px-4 py-6 space-y-2">
                <a href="#"
                    class="flex items-center gap-3 py-2 px-3 rounded-lg text-gray-700 hover:bg-indigo-100 hover:text-indigo-700 transition">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6"></path>
                    </svg>
                    Dashboard
                </a>
                <a href="#"
                    class="flex items-center gap-3 py-2 px-3 rounded-lg text-gray-700 hover:bg-indigo-100 hover:text-indigo-700 transition">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path d="M17 20h5v-2a4 4 0 00-3-3.87"></path>
                        <path d="M9 20H4v-2a4 4 0 013-3.87"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <circle cx="17" cy="7" r="4"></circle>
                    </svg>
                    Manajemen User
                </a>
                <a href="#"
                    class="flex items-center gap-3 py-2 px-3 rounded-lg text-gray-700 hover:bg-indigo-100 hover:text-indigo-700 transition">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path d="M3 7h18M3 12h18M3 17h18"></path>
                    </svg>
                    Manajemen Proyek
                </a>
                <a href="#"
                    class="flex items-center gap-3 py-2 px-3 rounded-lg text-gray-700 hover:bg-indigo-100 hover:text-indigo-700 transition">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path d="M9 17v-6h6v6m-6 0h6"></path>
                        <path d="M4 4h16v16H4V4z"></path>
                    </svg>
                    Laporan
                </a>
                <form method="POST" action="{{ route('logout') }}" class="pt-4">
                    @csrf
                    <button type="submit"
                        class="flex items-center gap-3 w-full text-left py-2 px-3 rounded-lg text-red-600 hover:bg-red-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M17 16l4-4m0 0l-4-4m4 4H7"></path>
                            <path d="M3 12a9 9 0 0118 0"></path>
                        </svg>
                        Logout
                    </button>
                </form>
            </nav>
        </aside>

        {{-- Main Content --}}
        <main class="flex-1 overflow-y-auto p-6">
            <div class="mb-6">
                <h1 class="text-3xl font-semibold text-indigo-700">Selamat Datang, {{ Auth::user()->name }}! 👋</h1>
                <p class="text-sm text-gray-500">Anda login sebagai <strong>{{ Auth::user()->role }}</strong></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-lg shadow hover:shadow-md transition">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">📈 Statistik Proyek</h3>
                    <p class="text-gray-600 text-sm">Jumlah proyek aktif: <span class="font-bold">5</span></p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow hover:shadow-md transition">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">👥 Manajemen User</h3>
                    <p class="text-gray-600 text-sm">Total user terdaftar: <span class="font-bold">10</span></p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow hover:shadow-md transition">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">📄 Laporan Terkini</h3>
                    <p class="text-gray-600 text-sm">Lihat data perkembangan proyek terkini.</p>
                </div>
            </div>
        </main>
    </div>
</x-app-layout>