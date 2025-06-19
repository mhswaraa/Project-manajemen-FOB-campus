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