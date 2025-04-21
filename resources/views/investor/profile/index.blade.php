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
             class="flex items-center gap-3 p-2 rounded-lg
                    {{ $active ? 'bg-green-100 text-green-800' : 'text-gray-700 hover:bg-green-50' }}">
            <!-- ikon home -->
            Dashboard
          </a>
  
          {{-- Daftar Proyek --}}
          @php $active = request()->routeIs('investor.projects.*'); @endphp
          <a href="{{ route('investor.projects.index') }}"
             class="flex items-center gap-3 p-2 rounded-lg
                    {{ $active ? 'bg-green-100 text-green-800' : 'text-gray-700 hover:bg-green-50' }}">
            Daftar Proyek
          </a>
  
          {{-- Investasi Saya --}}
          @php $active = request()->routeIs('investor.investments.*'); @endphp
          <a href="{{ route('investor.investments.index') }}"
             class="flex items-center gap-3 p-2 rounded-lg
                    {{ $active ? 'bg-green-100 text-green-800' : 'text-gray-700 hover:bg-green-50' }}">
            Investasi Saya
          </a>
  
          {{-- Profil --}}
          @php $active = request()->routeIs('investor.profile*'); @endphp
          <a href="{{ route('investor.profile') }}"
             class="flex items-center gap-3 p-2 rounded-lg
                    {{ $active ? 'bg-green-100 text-green-800' : 'text-gray-700 hover:bg-green-50' }}">
            Profil
          </a>
  
          {{-- Logout --}}
          <form method="POST" action="{{ route('logout') }}" class="mt-4 pt-4 border-t">
            @csrf
            <button type="submit"
                    class="flex items-center gap-3 w-full text-left p-2 rounded-lg text-red-600 hover:bg-red-50">
              Logout
            </button>
          </form>
        </nav>
      </aside>
  
      {{-- Main Content --}}
      <main class="flex-1 overflow-y-auto p-6">
        <div class="max-w-lg mx-auto">
  
          {{-- Alert --}}
          @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
              {{ session('success') }}
            </div>
          @endif
  
          @if(!$investor)
            {{-- Form Create Data Diri --}}
            <h1 class="text-2xl font-semibold text-green-700 mb-4">Lengkapi Data Diri</h1>
            <form action="{{ route('investor.profile.update') }}" method="POST" class="space-y-4">
              @csrf
  
              <div>
                <x-input-label for="name" :value="__('Nama')" />
                <x-text-input id="name" name="name" type="text"
                              class="block w-full"
                              :value="old('name', Auth::user()->name)" required />
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
              </div>
  
              <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" name="email" type="email"
                              class="block w-full"
                              :value="old('email', Auth::user()->email)" required />
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
              </div>
  
              <div>
                <x-input-label for="phone" :value="__('No. HP')" />
                <x-text-input id="phone" name="phone" type="text"
                              class="block w-full"
                              :value="old('phone')" required />
                <x-input-error :messages="$errors->get('phone')" class="mt-1" />
              </div>
  
              <div class="flex justify-end">
                <x-primary-button>Save</x-primary-button>
              </div>
            </form>
  
          @else
            {{-- Tampilkan Data Diri --}}
            <h1 class="text-2xl font-semibold text-green-700 mb-4">Profil Anda</h1>
            <dl class="space-y-2">
              <div>
                <dt class="font-medium text-gray-700">Nama</dt>
                <dd class="text-gray-900">{{ $investor->name }}</dd>
              </div>
              <div>
                <dt class="font-medium text-gray-700">Email</dt>
                <dd class="text-gray-900">{{ $investor->email }}</dd>
              </div>
              <div>
                <dt class="font-medium text-gray-700">No. HP</dt>
                <dd class="text-gray-900">{{ $investor->phone }}</dd>
              </div>
              @if($investor->amount)
              <div>
                <dt class="font-medium text-gray-700">Investasi</dt>
                <dd class="text-gray-900">Rp {{ number_format($investor->amount,2,',','.') }}</dd>
              </div>
              @endif
              @if($investor->deadline)
              <div>
                <dt class="font-medium text-gray-700">Deadline</dt>
                <dd class="text-gray-900">{{ $investor->deadline }}</dd>
              </div>
              @endif
            </dl>
  
            {{-- Tombol Edit --}}
            <a href="{{ route('investor.profile') }}#edit"
               class="mt-6 inline-block px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
              Edit Profil
            </a>
          @endif
  
        </div>
      </main>
    </div>
  </x-app-layout>
  