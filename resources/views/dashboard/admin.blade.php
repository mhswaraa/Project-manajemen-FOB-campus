<x-app-layout>
    <div class="flex h-screen bg-gray-100">
        {{-- Sidebar --}}
        <aside class="w-64 bg-white shadow-md hidden md:block">
            <div class="p-4 text-xl font-bold text-indigo-600 border-b">
                Project Manager
            </div>
            <nav class="p-4 space-y-2">
                <a href="#" class="block py-2 px-4 rounded hover:bg-indigo-100 text-gray-700">Dashboard</a>
                <a href="#" class="block py-2 px-4 rounded hover:bg-indigo-100 text-gray-700">Manajemen User</a>
                <a href="#" class="block py-2 px-4 rounded hover:bg-indigo-100 text-gray-700">Manajemen Proyek</a>
                <a href="#" class="block py-2 px-4 rounded hover:bg-indigo-100 text-gray-700">Laporan</a>
                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   class="block py-2 px-4 rounded hover:bg-red-100 text-red-600">Logout</a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </nav>
        </aside>

        {{-- Main Content --}}
        <main class="flex-1 p-6">
            <div class="text-2xl font-semibold mb-4">Selamat Datang, {{ Auth::user()->name }}!</div>
            <div class="text-sm text-gray-500 mb-6">Anda login sebagai <strong>{{ Auth::user()->role }}</strong></div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="font-bold text-lg mb-2">Statistik Proyek</h3>
                    <p class="text-gray-600">Jumlah proyek aktif: 5</p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="font-bold text-lg mb-2">Manajemen User</h3>
                    <p class="text-gray-600">Total user terdaftar: 10</p>
                </div>
            </div>
        </main>
    </div>
</x-app-layout>
