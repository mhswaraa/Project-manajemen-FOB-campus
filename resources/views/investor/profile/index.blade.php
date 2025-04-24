<x-app-layout>
  <div class="flex h-screen bg-gray-50 text-gray-800">

    {{-- Sidebar --}}
    <aside class="w-64 bg-white border-r shadow flex flex-col">
      <div class="p-6 text-2xl font-bold text-green-600 border-b">
        💹 Investor
      </div>
      <nav class="flex-1 p-4 space-y-3">
        {{-- Dashboard --}}
        <a href="{{ route('investor.dashboard') }}"
           class="flex items-center gap-3 p-2 rounded-lg transition
              {{ request()->routeIs('investor.dashboard') 
                 ? 'bg-green-100 text-green-800'
                 : 'text-gray-700 hover:bg-green-50' }}">
          {{-- home icon --}}
          <svg xmlns="http://www.w3.org/2000/svg"
               class="w-5 h-5 text-green-500"
               fill="none" viewBox="0 0 24 24" stroke="currentColor"
               stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6" />
          </svg>
          <span>Dashboard</span>
        </a>

        {{-- Daftar Proyek --}}
        <a href="{{ route('investor.projects.index') }}"
           class="flex items-center gap-3 p-2 rounded-lg transition
              {{ request()->routeIs('investor.projects.*')
                 ? 'bg-green-100 text-green-800'
                 : 'text-gray-700 hover:bg-green-50' }}">
          {{-- folder icon --}}
          <svg xmlns="http://www.w3.org/2000/svg"
               class="w-5 h-5 text-green-500"
               fill="none" viewBox="0 0 24 24" stroke="currentColor"
               stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M3 7h18M3 12h18M3 17h18" />
          </svg>
          <span>Daftar Proyek</span>
        </a>

        {{-- Investasi Saya --}}
        <a href="{{ route('investor.investments.index') }}"
           class="flex items-center gap-3 p-2 rounded-lg transition
              {{ request()->routeIs('investor.investments.*')
                 ? 'bg-green-100 text-green-800'
                 : 'text-gray-700 hover:bg-green-50' }}">
          {{-- cash icon --}}
          <svg xmlns="http://www.w3.org/2000/svg"
               class="w-5 h-5 text-green-500"
               fill="none" viewBox="0 0 24 24" stroke="currentColor"
               stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm0 10v2m0-14V2" />
          </svg>
          <span>Investasi Saya</span>
        </a>

        {{-- Profil --}}
        <a href="{{ route('investor.profile') }}"
           class="flex items-center gap-3 p-2 rounded-lg transition
              {{ request()->routeIs('investor.profile*')
                 ? 'bg-green-100 text-green-800'
                 : 'text-gray-700 hover:bg-green-50' }}">
          {{-- user icon --}}
          <svg xmlns="http://www.w3.org/2000/svg"
               class="w-5 h-5 text-green-500"
               fill="none" viewBox="0 0 24 24" stroke="currentColor"
               stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M5.121 17.804A11.955 11.955 0 0112 15c2.485 0 4.78.758 6.879 2.046M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          <span>Profil</span>
        </a>

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}" class="mt-4 pt-4 border-t">
          @csrf
          <button type="submit"
                  class="flex items-center gap-3 w-full text-left p-2 rounded-lg text-red-600 hover:bg-red-50">
            {{-- logout icon --}}
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="w-5 h-5"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor"
                 stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M17 16l4-4m0 0l-4-4m4 4H7
                       m6 4v1a2 2 0 002 2h3a2 2 0 002-2V7
                       a2 2 0 00-2-2h-3a2 2 0 00-2 2v1" />
            </svg>
            <span>Logout</span>
          </button>
        </form>
      </nav>
    </aside>

    {{-- Main Content --}}
    <main class="flex-1 overflow-y-auto p-6">
      <div class="max-w-3xl mx-auto">
        {{-- Alert --}}
        @if(session('success'))
          <div class="mb-6 px-4 py-3 bg-green-100 text-green-800 rounded-lg shadow-sm">
            {{ session('success') }}
          </div>
        @endif

        @if(!$investor)
          {{-- Card Create Form --}}
          <div class="bg-white shadow-lg rounded-lg p-6">
            <h2 class="text-2xl font-bold text-green-700 mb-4">Lengkapi Data Diri</h2>
            <form action="{{ route('investor.profile.update') }}" method="POST" class="space-y-5">
              @csrf
              <div>
                <x-input-label for="name" :value="__('Nama')" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                              :value="old('name', Auth::user()->name)" required />
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
              </div>
              <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                              :value="old('email', Auth::user()->email)" required />
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
              </div>
              <div>
                <x-input-label for="phone" :value="__('No. HP')" />
                <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full"
                              :value="old('phone')" required />
                <x-input-error :messages="$errors->get('phone')" class="mt-1" />
              </div>
              <div>
                <x-input-label for="amount" :value="__('Jumlah Investasi')" />
                <x-text-input id="amount" name="amount" type="number" step="0.01"
                              class="block w-full"
                              value="{{ old('amount', $investor->amount ?? 0) }}" required />
                <x-input-error :messages="$errors->get('amount')" class="mt-1" />
              </div>
              <div>
                <x-input-label for="registered_at" :value="__('Tanggal Daftar')" />
                <x-text-input id="registered_at"
                              name="registered_at"
                              type="date"
                              class="block w-full"
                              :value="old('registered_at', now()->format('Y-m-d'))" 
                              required />
                <x-input-error :messages="$errors->get('registered_at')" class="mt-1" />
              </div>
              <div class="flex justify-end">
                <x-primary-button>Save Profile</x-primary-button>
              </div>
            </form>
          </div>

        @else
          {{-- Card Detail Profil --}}
          <div class="bg-white shadow-lg rounded-lg p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Info Dasar --}}
            <div>
              <h2 class="text-2xl font-bold text-green-700 mb-4">Profil Anda</h2>
              <dl class="space-y-4">
                <div>
                  <dt class="text-sm font-medium text-gray-600">Nama</dt>
                  <dd class="mt-1 text-gray-800">{{ $investor->name }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-600">Email</dt>
                  <dd class="mt-1 text-gray-800">{{ $investor->email }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-600">No. HP</dt>
                  <dd class="mt-1 text-gray-800">{{ $investor->phone }}</dd>
                </div>
              </dl>
            </div>
           {{-- Statistik Singkat --}}
<div class="border-l md:pl-6">
  <h3 class="text-lg font-semibold text-gray-700 mb-3">Statistik Investasi</h3>
  <dl class="space-y-4">
    @if($investor->amount)
      <div class="flex justify-between">
        <dt class="text-sm font-medium text-gray-600">Total Investasi</dt>
        <dd class="font-bold">{{ number_format($investor->amount, 2, ',', '.') }}</dd>
      </div>
    @endif

    {{-- Ganti deadline ➔ registered_at --}}
    @if($investor->registered_at)
      <div class="flex justify-between">
        <dt class="text-sm font-medium text-gray-600">Tanggal Daftar</dt>
        <dd>{{ \Carbon\Carbon::parse($investor->registered_at)->format('d M Y') }}</dd>
      </div>
    @endif
  </dl>
</div>
            {{-- Edit Button --}}
            <div class="md:col-span-2 text-right">
              <a href="{{ route('investor.profile') }}#edit"
                 class="inline-block px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                Edit Profil
              </a>
            </div>
          </div>
        @endif

      </div>
    </main>
  </div>
</x-app-layout>
