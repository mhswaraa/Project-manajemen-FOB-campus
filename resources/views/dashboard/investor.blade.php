<x-app-layout>
    <div class="flex h-screen bg-gray-50 text-gray-800">
      {{-- Sidebar --}}
      <aside class="w-64 bg-white border-r shadow flex flex-col">
        <div class="p-6 text-2xl font-bold text-green-600 border-b">
          💹 Investor
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
        </nav>
      </aside>
  
      {{-- Main Content --}}
      <main class="flex-1 p-6 overflow-y-auto">
        {{-- Header --}}
        <div class="mb-6">
          <h1 class="text-3xl font-semibold text-green-700">Dashboard</h1>
          <p class="text-gray-600">Halo, {{ Auth::user()->name }}!</p>
        </div>
  
        {{-- Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
          <div class="bg-white p-6 rounded-lg shadow flex items-center">
            <div class="p-3 bg-green-100 rounded-full">
              <svg xmlns="http://www.w3.org/2000/svg"
                   class="w-6 h-6 text-green-600" fill="none"
                   viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M12 8v8m4-4H8"/>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm text-gray-500">Total Investasi</p>
              <p class="text-2xl font-semibold">{{ number_format($totalInvested,2,',','.') }}</p>
            </div>
          </div>
  
          <div class="bg-white p-6 rounded-lg shadow flex items-center">
            <div class="p-3 bg-green-100 rounded-full">
              <svg xmlns="http://www.w3.org/2000/svg"
                   class="w-6 h-6 text-green-600" fill="none"
                   viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M9 17v-6l12-2"/>
                <circle cx="5" cy="19" r="2"/>
                <circle cx="17" cy="17" r="2"/>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm text-gray-500">Proyek Diikuti</p>
              <p class="text-2xl font-semibold">{{ $projectsCount }}</p>
            </div>
          </div>
  
          {{-- Opsional: rata‑rata progress --}}
          {{-- <div>…</div> --}}
        </div>
  
        {{-- Recent Investments --}}
        <div class="bg-white shadow rounded-lg p-6">
          <h2 class="text-lg font-semibold text-gray-700 mb-4">Investasi Terbaru</h2>
          @if($recentProjects->isEmpty())
            <p class="text-gray-500">Belum ada investasi.</p>
          @else
            <ul class="divide-y divide-gray-200">
              @foreach($recentProjects as $inv)
                <li class="py-3 flex justify-between items-center">
                  <div>
                    <p class="font-medium text-gray-800">{{ $inv->project->name }}</p>
                    <p class="text-sm text-gray-500">Rp {{ number_format($inv->amount,2,',','.') }} • {{ $inv->deadline }}</p>
                  </div>
                  <a href="{{ route('investor.projects.index') }}"
                     class="text-green-600 hover:underline text-sm">Detail</a>
                </li>
              @endforeach
            </ul>
          @endif
        </div>
      </main>
    </div>
  </x-app-layout>
  