{{-- resources/views/investor/partials/sidebar.blade.php --}}
<aside class="w-64 bg-white border-r shadow flex flex-col">
    <div class="p-6 text-2xl font-bold text-green-600 border-b">
      💹 Investor Panel
    </div>
    <nav class="flex-1 p-4 space-y-2">
      {{-- Dashboard --}}
      @php $active = request()->routeIs('investor.dashboard'); @endphp
      <a href="{{ route('investor.dashboard') }}"
         class="block py-2 px-3 rounded-lg transition
           {{ $active 
              ? 'bg-green-100 text-green-800' 
              : 'text-gray-700 hover:bg-green-50' }}">
        Dashboard
      </a>
  
      {{-- Daftar Proyek --}}
      @php $active = request()->routeIs('investor.projects.*'); @endphp
      <a href="{{ route('investor.projects.index') }}"
         class="block py-2 px-3 rounded-lg transition
           {{ $active 
              ? 'bg-green-100 text-green-800' 
              : 'text-gray-700 hover:bg-green-50' }}">
        Daftar Proyek
      </a>
  
      {{-- Investasi Saya --}}
      @php $active = request()->routeIs('investor.investments.*'); @endphp
      <a href="{{ route('investor.investments.index') }}"
         class="block py-2 px-3 rounded-lg transition
           {{ $active 
              ? 'bg-green-100 text-green-800' 
              : 'text-gray-700 hover:bg-green-50' }}">
        Investasi Saya
      </a>
  
      {{-- Profil --}}
      @php $active = request()->routeIs('investor.profile*'); @endphp
      <a href="{{ route('investor.profile') }}"
         class="block py-2 px-3 rounded-lg transition
           {{ $active 
              ? 'bg-green-100 text-green-800' 
              : 'text-gray-700 hover:bg-green-50' }}">
        Profil
      </a>
  
      {{-- Logout --}}
      <form method="POST" action="{{ route('logout') }}" class="mt-4 pt-4 border-t">
        @csrf
        <button type="submit"
                class="w-full text-left py-2 px-3 rounded-lg text-red-600 hover:bg-red-50">
          Logout
        </button>
      </form>
    </nav>
  </aside>
  