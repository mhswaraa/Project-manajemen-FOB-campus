
{{-- resources/views/ceo/partials/sidebar.blade.php --}}
<div class="flex h-screen w-64 flex-col justify-between border-e bg-white">
    <div class="px-4 py-6">
        {{-- Logo dan Nama Panel --}}
         <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-2 mb-6">
            <span class="grid h-10 w-10 place-content-center rounded-lg bg-gray-700 text-white">
                <x-heroicon-s-user class="h-6 w-6"/>
            </span>
            <span class="text-xl font-bold text-gray-800">CEO Dashboard</span>
        </a>

        {{-- Daftar Menu Utama --}}
        <ul class="space-y-1">
            {{-- Menu Dropdown untuk Proyek --}}
           <li>
                <a href="{{ route('dashboard') }}" 
                   @class([
                       'flex items-center gap-3 rounded-lg px-4 py-2 text-sm font-medium transition',
                       'bg-gray-200 text-gray-800' => request()->routeIs('dashboard'),
                       'text-gray-500 hover:bg-gray-100 hover:text-gray-700' => !request()->routeIs('dashboard')
                   ])>
                    <x-heroicon-o-presentation-chart-line class="h-5 w-5" /> Ringkasan Eksekutif
                </a>
            </li>
            <li>
                <a href="{{ route('ceo.reports.investor-cohort') }}" 
                   @class([
                       'flex items-center gap-3 rounded-lg px-4 py-2 text-sm font-medium transition',
                       'bg-gray-200 text-gray-800' => request()->routeIs('ceo.reports.investor-cohort'),
                       'text-gray-500 hover:bg-gray-100 hover:text-gray-700' => !request()->routeIs('ceo.reports.investor-cohort')
                   ])>
                    <x-heroicon-o-users class="h-5 w-5" /> Analisis Kohort
                </a>
            </li>
             <li>
                <a href="{{ route('ceo.reports.production-leaderboard') }}" 
                   @class([
                       'flex items-center gap-3 rounded-lg px-4 py-2 text-sm font-medium transition',
                       'bg-gray-200 text-gray-800' => request()->routeIs('ceo.reports.production-leaderboard'),
                       'text-gray-500 hover:bg-gray-100 hover:text-gray-700' => !request()->routeIs('ceo.reports.production-leaderboard')
                   ])>
                    <x-heroicon-o-trophy class="h-5 w-5" /> Papan Peringkat Produksi
                </a>
            </li>
            {{-- MENU BARU DI SINI --}}
            <li>
                <a href="{{ route('ceo.reports.cash-flow-forecast') }}" 
                   @class([
                       'flex items-center gap-3 rounded-lg px-4 py-2 text-sm font-medium transition',
                       'bg-gray-200 text-gray-800' => request()->routeIs('ceo.reports.cash-flow-forecast'),
                       'text-gray-500 hover:bg-gray-100 hover:text-gray-700' => !request()->routeIs('ceo.reports.cash-flow-forecast')
                   ])>
                    <x-heroicon-o-presentation-chart-bar class="h-5 w-5" /> Peramalan & Proyeksi
                </a>
            </li>
        </ul>
    </div>

    {{-- Bagian Bawah: Profil Pengguna & Logout --}}
    <div class="sticky inset-x-0 bottom-0 border-t border-gray-100">
        <div class="flex items-center gap-3 bg-white p-4">
            <img alt="Profil Admin"
                 src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=E8EAF6&color=3F51B5"
                 class="h-10 w-10 rounded-full object-cover" />
            <div>
                <p class="text-sm font-semibold text-gray-700">{{ Auth::user()->name }}</p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs text-red-600 hover:underline focus:outline-none">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
