{{-- resources/views/penjahit/partials/sidebar.blade.php --}}
<div class="flex h-screen w-64 flex-col justify-between border-e bg-white">
    <div class="px-4 py-6">
        {{-- Logo dan Nama Panel --}}
        <a href="{{ route('penjahit.dashboard') }}" class="flex items-center gap-2 px-2 mb-6">
            <span class="grid h-10 w-10 place-content-center rounded-lg bg-teal-100 text-teal-600">
                {{-- Menggunakan SVG inline untuk ikon gunting yang lebih unik --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 9.75l-4.5-4.5m0 0l-4.5 4.5m4.5-4.5v12.75m4.5-4.5l-4.5 4.5m0 0l-4.5-4.5m4.5 4.5v-12.75" transform="rotate(45 12 12)" />
                    <circle cx="7.5" cy="7.5" r="2.5" />
                    <circle cx="16.5" cy="7.5" r="2.5" />
                </svg>
            </span>
            <span class="text-xl font-bold text-gray-800">Penjahit Panel</span>
        </a>

        {{-- Daftar Menu Utama --}}
        <ul class="space-y-1">
            <li>
                <a href="{{ route('penjahit.dashboard') }}"
                   @class([
                       'flex items-center gap-3 rounded-lg px-4 py-2 text-sm font-medium transition',
                       'bg-teal-100 text-teal-700' => request()->routeIs('penjahit.dashboard'),
                       'text-gray-500 hover:bg-gray-100 hover:text-gray-700' => !request()->routeIs('penjahit.dashboard')
                   ])>
                    <x-heroicon-o-home class="h-5 w-5" />
                    Dashboard
                </a>
            </li>

            <li>
                <a href="{{ route('penjahit.projects.index') }}"
                   @class([
                       'flex items-center gap-3 rounded-lg px-4 py-2 text-sm font-medium transition',
                       'bg-teal-100 text-teal-700' => request()->routeIs('penjahit.projects.*'),
                       'text-gray-500 hover:bg-gray-100 hover:text-gray-700' => !request()->routeIs('penjahit.projects.*')
                   ])>
                    <x-heroicon-o-magnifying-glass class="h-5 w-5" />
                    Cari Proyek
                </a>
            </li>
            
            <li>
                <a href="{{ route('penjahit.tasks.index') }}"
                   @class([
                       'flex items-center gap-3 rounded-lg px-4 py-2 text-sm font-medium transition',
                       'bg-teal-100 text-teal-700' => request()->routeIs('penjahit.tasks.*'),
                       'text-gray-500 hover:bg-gray-100 hover:text-gray-700' => !request()->routeIs('penjahit.tasks.*')
                   ])>
                    <x-heroicon-o-briefcase class="h-5 w-5" />
                    Tugas Saya
                </a>
            </li>

            <li>
                <a href="{{ route('penjahit.profile.index') }}"
                   @class([
                       'flex items-center gap-3 rounded-lg px-4 py-2 text-sm font-medium transition',
                       'bg-teal-100 text-teal-700' => request()->routeIs('penjahit.profile.*'),
                       'text-gray-500 hover:bg-gray-100 hover:text-gray-700' => !request()->routeIs('penjahit.profile.*')
                   ])>
                    <x-heroicon-o-user-circle class="h-5 w-5" />
                    Profil & Portofolio
                </a>
            </li>

            {{-- MENU BARU: Manajemen Invoice --}}
            <li x-data="{ open: {{ request()->routeIs('penjahit.invoices.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                   @class([
                       'w-full flex items-center justify-between gap-3 rounded-lg px-4 py-2 text-sm font-medium transition',
                       'bg-teal-100 text-teal-700' => request()->routeIs('penjahit.invoices.*'),
                       'text-gray-500 hover:bg-gray-100 hover:text-gray-700' => !request()->routeIs('penjahit.invoices.*')
                   ])>
                    <span class="flex items-center gap-3">
                        <x-heroicon-o-document-text class="h-5 w-5" />
                        Manajemen Invoice
                    </span>
                    <x-heroicon-s-chevron-down class="h-4 w-4 shrink-0 transition-transform" ::class="{'rotate-180': open}" />
                </button>
                <ul x-show="open" x-collapse class="mt-1 space-y-1 pl-6">
                    <li>
                        <a href="{{ route('penjahit.invoices.create') }}" 
                           @class(['block rounded-lg px-4 py-2 text-sm font-medium transition', 'text-teal-700 font-semibold' => request()->routeIs('penjahit.invoices.create'),'text-gray-500 hover:bg-gray-100 hover:text-gray-700' => !request()->routeIs('penjahit.invoices.create')])>
                           Buat Invoice Baru
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('penjahit.invoices.index') }}" 
                           @class(['block rounded-lg px-4 py-2 text-sm font-medium transition', 'text-teal-700 font-semibold' => request()->routeIs('penjahit.invoices.index'),'text-gray-500 hover:bg-gray-100 hover:text-gray-700' => !request()->routeIs('penjahit.invoices.index')])>
                           Riwayat Invoice
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>

    {{-- Bagian Bawah: Profil Pengguna & Logout --}}
    <div class="sticky inset-x-0 bottom-0 border-t border-gray-100">
        <div class="flex items-center gap-2 bg-white p-4">
            <img alt="Profil"
                 src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=E0F2F1&color=00796B"
                 class="h-10 w-10 rounded-full object-cover" />

            <div class="flex-1">
                <p class="text-sm font-semibold text-gray-700">{{ Auth::user()->name }}</p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs text-red-600 hover:underline">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
