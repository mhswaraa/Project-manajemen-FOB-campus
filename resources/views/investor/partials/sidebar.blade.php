{{-- resources/views/investor/partials/sidebar.blade.php --}}
<div class="flex h-screen w-64 flex-col justify-between border-e bg-white">
    <div class="px-4 py-6">
        {{-- Logo dan Nama Panel --}}
        <a href="{{ route('investor.dashboard') }}" class="flex items-center gap-2.5 px-2 mb-6">
            <span class="grid h-10 w-10 place-content-center rounded-lg bg-green-100 text-green-600">
                <x-heroicon-s-chart-pie class="h-6 w-6"/>
            </span>
            <span class="text-xl font-bold text-gray-800">Investor Panel</span>
        </a>

        {{-- Daftar Menu Utama --}}
        <ul class="space-y-1">
            <li>
                <a href="{{ route('investor.dashboard') }}"
                   @class([
                       'flex items-center gap-3 rounded-lg px-4 py-2 text-sm font-medium transition',
                       'bg-green-100 text-green-700' => request()->routeIs('investor.dashboard'),
                       'text-gray-500 hover:bg-gray-100 hover:text-gray-700' => !request()->routeIs('investor.dashboard')
                   ])>
                    <x-heroicon-o-home class="h-5 w-5" />
                    Dashboard
                </a>
            </li>

            <li>
                <a href="{{ route('investor.projects.index') }}"
                   @class([
                       'flex items-center gap-3 rounded-lg px-4 py-2 text-sm font-medium transition',
                       'bg-green-100 text-green-700' => request()->routeIs('investor.projects.*'),
                       'text-gray-500 hover:bg-gray-100 hover:text-gray-700' => !request()->routeIs('investor.projects.*')
                   ])>
                    <x-heroicon-o-currency-dollar class="h-5 w-5" />
                    Marketplace Proyek
                </a>
            </li>
            
            <li>
                <a href="{{ route('investor.investments.index') }}"
                   @class([
                       'flex items-center gap-3 rounded-lg px-4 py-2 text-sm font-medium transition',
                       'bg-green-100 text-green-700' => request()->routeIs('investor.investments.*'),
                       'text-gray-500 hover:bg-gray-100 hover:text-gray-700' => !request()->routeIs('investor.investments.*')
                   ])>
                    <x-heroicon-o-wallet class="h-5 w-5" />
                    Riwayat Investasi
                </a>
            </li>

            <li>
                <a href="{{ route('investor.payouts.index') }}"
                   @class([
                       'flex items-center gap-3 rounded-lg px-4 py-2 text-sm font-medium transition',
                       'bg-green-100 text-green-700' => request()->routeIs('investor.payouts.*'),
                       'text-gray-500 hover:bg-gray-100 hover:text-gray-700' => !request()->routeIs('investor.payouts.*')
                   ])>
                    <x-heroicon-o-gift-top class="h-5 w-5" />
                    Riwayat Profit
                </a>
            </li>

            <li>
                <a href="{{ route('investor.profile') }}"
                   @class([
                       'flex items-center gap-3 rounded-lg px-4 py-2 text-sm font-medium transition',
                       'bg-green-100 text-green-700' => request()->routeIs('investor.profile*'),
                       'text-gray-500 hover:bg-gray-100 hover:text-gray-700' => !request()->routeIs('investor.profile*')
                   ])>
                    <x-heroicon-o-user-circle class="h-5 w-5" />
                    Profil Saya
                </a>
            </li>
        </ul>
    </div>

    {{-- Bagian Bawah: Profil Pengguna & Logout --}}
    <div class="sticky inset-x-0 bottom-0 border-t border-gray-100">
        <div class="flex items-center gap-3 bg-white p-4">
            <img alt="Profil Investor"
                 src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=C8E6C9&color=2E7D32"
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
