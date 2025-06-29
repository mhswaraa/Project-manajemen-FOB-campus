{{-- resources/views/admin/partials/sidebar.blade.php --}}
<div class="flex h-screen w-64 flex-col justify-between border-e bg-white">
    <div class="px-4 py-6">
        {{-- Logo dan Nama Panel --}}
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-2 mb-6">
            <span class="grid h-10 w-10 place-content-center rounded-lg bg-indigo-100 text-indigo-600">
                <x-heroicon-s-shield-check class="h-6 w-6"/>
            </span>
            <span class="text-xl font-bold text-gray-800">Admin Panel</span>
        </a>

        <ul class="space-y-1">
                {{-- 1. Dashboard --}}
                <li>
                    <a href="{{ route('dashboard') }}"
                       @class([
                           'flex items-center gap-3 rounded-lg px-4 py-2 text-sm font-medium transition',
                           'bg-indigo-100 text-indigo-700' => request()->routeIs('dashboard'),
                           'text-gray-500 hover:bg-gray-100 hover:text-gray-700' => !request()->routeIs('dashboard')
                       ])>
                        <x-heroicon-o-home class="h-5 w-5" />
                        Dashboard
                    </a>
                </li>
                
                {{-- 2. Manajemen User --}}
                <li>
                    <a href="{{ route('admin.users.index') }}"
                       @class([
                           'flex items-center gap-3 rounded-lg px-4 py-2 text-sm font-medium transition',
                           'bg-indigo-100 text-indigo-700' => request()->routeIs('admin.users.*'),
                           'text-gray-500 hover:bg-gray-100 hover:text-gray-700' => !request()->routeIs('admin.users.*')
                       ])>
                        <x-heroicon-o-users class="h-5 w-5" />
                        Manajemen User
                    </a>
                </li>

                {{-- 3. Menu Dropdown untuk Proyek --}}
                <li x-data="{ open: {{ request()->routeIs('admin.projects.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            @class([
                                'w-full flex items-center justify-between gap-3 rounded-lg px-4 py-2 text-sm font-medium transition',
                                'bg-indigo-100 text-indigo-700' => request()->routeIs('admin.projects.*'),
                                'text-gray-500 hover:bg-gray-100 hover:text-gray-700' => !request()->routeIs('admin.projects.*')
                            ])>
                        <span class="flex items-center gap-3">
                            <x-heroicon-o-briefcase class="h-5 w-5" />
                            Manajemen Proyek
                        </span>
                        {{-- FIX: Mengganti :class dengan x-bind:class --}}
                        <x-heroicon-s-chevron-down class="h-4 w-4 shrink-0 transition-transform" x-bind:class="{'rotate-180': open}" />
                    </button>
                    <ul x-show="open" x-collapse class="mt-1 space-y-1 pl-6">
                        <li><a href="{{ route('admin.projects.index') }}" @class(['block rounded-lg px-4 py-2 text-sm font-medium transition', 'text-indigo-700 font-semibold' => request()->routeIs('admin.projects.index'),'text-gray-500 hover:bg-gray-100 hover:text-gray-700' => !request()->routeIs('admin.projects.index')])>Daftar Proyek</a></li>
                        <li><a href="{{ route('admin.projects.invested') }}" @class(['block rounded-lg px-4 py-2 text-sm font-medium transition', 'text-indigo-700 font-semibold' => request()->routeIs('admin.projects.invested'),'text-gray-500 hover:bg-gray-100 hover:text-gray-700' => !request()->routeIs('admin.projects.invested')])>Daftar Investasi</a></li>
                    </ul>
                </li>
                
                {{-- 4. Manajemen Investor --}}
                <li>
                    <a href="{{ route('admin.investors.index') }}"
                       @class(['flex items-center gap-3 rounded-lg px-4 py-2 text-sm font-medium transition', 'bg-indigo-100 text-indigo-700' => request()->routeIs('admin.investors.*'), 'text-gray-500 hover:bg-gray-100 hover:text-gray-700' => !request()->routeIs('admin.investors.*')])>
                        <x-heroicon-o-user-group class="h-5 w-5" />
                        Manajemen Investor
                    </a>
                </li>
                
                {{-- 5. Menu Dropdown untuk Penjahit --}}
                <li x-data="{ open: {{ request()->routeIs('admin.penjahits.*') || request()->routeIs('admin.specializations.*') ? 'true' : 'false' }} }">
                     <button @click="open = !open"
                            @class(['w-full flex items-center justify-between gap-3 rounded-lg px-4 py-2 text-sm font-medium transition', 'bg-indigo-100 text-indigo-700' => request()->routeIs('admin.penjahits.*') || request()->routeIs('admin.specializations.*'), 'text-gray-500 hover:bg-gray-100 hover:text-gray-700' => !request()->routeIs('admin.penjahits.*') && !request()->routeIs('admin.specializations.*')])>
                        <span class="flex items-center gap-3">
                            <x-heroicon-o-scissors class="h-5 w-5" />
                            Manajemen Penjahit
                        </span>
                        {{-- FIX: Mengganti :class dengan x-bind:class --}}
                        <x-heroicon-s-chevron-down class="h-4 w-4 shrink-0 transition-transform" x-bind:class="{'rotate-180': open}" />
                    </button>
                     <ul x-show="open" x-collapse class="mt-1 space-y-1 pl-6">
                        <li><a href="{{ route('admin.penjahits.index') }}" @class(['block rounded-lg px-4 py-2 text-sm font-medium transition', 'text-indigo-700 font-semibold' => request()->routeIs('admin.penjahits.*'), 'text-gray-500 hover:bg-gray-100 hover:text-gray-700' => !request()->routeIs('admin.penjahits.*')])>Daftar Penjahit</a></li>
                        <li><a href="{{ route('admin.specializations.index') }}" @class(['block rounded-lg px-4 py-2 text-sm font-medium transition', 'text-indigo-700 font-semibold' => request()->routeIs('admin.specializations.*'), 'text-gray-500 hover:bg-gray-100 hover:text-gray-700' => !request()->routeIs('admin.specializations.*')])>Kelola Spesialisasi</a></li>
                        <li><a href="{{ route('admin.qc.index') }}" @class(['block rounded-lg px-4 py-2 text-sm font-medium transition', 'text-indigo-700 font-semibold' => request()->routeIs('admin.specializations.*'), 'text-gray-500 hover:bg-gray-100 hover:text-gray-700' => !request()->routeIs('admin.specializations.*')])>QC</a></li>
                    </ul>
                </li>

                 {{-- 6. Menu Dropdown untuk Keuangan --}}
                <li x-data="{ open: {{ request()->routeIs('admin.invoices.*') || request()->routeIs('admin.payouts.*') ? 'true' : 'false' }} }">
                     <button @click="open = !open"
                            @class(['w-full flex items-center justify-between gap-3 rounded-lg px-4 py-2 text-sm font-medium transition', 'bg-indigo-100 text-indigo-700' => request()->routeIs('admin.invoices.*') || request()->routeIs('admin.payouts.*'), 'text-gray-500 hover:bg-gray-100 hover:text-gray-700' => !request()->routeIs('admin.invoices.*') && !request()->routeIs('admin.payouts.*')])>
                        <span class="flex items-center gap-3">
                            <x-heroicon-o-currency-dollar class="h-5 w-5" />
                            Keuangan
                        </span>
                         {{-- FIX: Mengganti :class dengan x-bind:class --}}
                        <x-heroicon-s-chevron-down class="h-4 w-4 shrink-0 transition-transform" x-bind:class="{'rotate-180': open}" />
                    </button>
                     <ul x-show="open" x-collapse class="mt-1 space-y-1 pl-6">
                        <li><a href="{{ route('admin.invoices.index') }}" @class(['block rounded-lg px-4 py-2 text-sm font-medium transition', 'text-indigo-700 font-semibold' => request()->routeIs('admin.invoices.*'), 'text-gray-500 hover:bg-gray-100 hover:text-gray-700' => !request()->routeIs('admin.invoices.*')])>Invoice Penjahit</a></li>
                        <li><a href="{{ route('admin.payouts.index') }}" @class(['block rounded-lg px-4 py-2 text-sm font-medium transition', 'text-indigo-700 font-semibold' => request()->routeIs('admin.payouts.*'), 'text-gray-500 hover:bg-gray-100 hover:text-gray-700' => !request()->routeIs('admin.payouts.*')])>Payout Investor</a></li>
                    </ul>
                </li>
                
                {{-- 7. Laporan --}}
                <li>
                    <a href="{{ route('admin.reports.index') }}"
                       @class(['flex items-center gap-3 rounded-lg px-4 py-2 text-sm font-medium transition', 'bg-indigo-100 text-indigo-700' => request()->routeIs('admin.reports.*'), 'text-gray-500 hover:bg-gray-100 hover:text-gray-700' => !request()->routeIs('admin.reports.*')])>
                        <x-heroicon-o-chart-bar class="h-5 w-5" />
                        Laporan
                    </a>
                </li>
            </ul>
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
