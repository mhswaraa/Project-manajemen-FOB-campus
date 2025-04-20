<x-app-layout>
    <div class="flex h-screen bg-gray-100 text-gray-800">
      {{-- Sidebar --}}
      <aside class="w-64 bg-white border-r shadow-lg hidden md:flex flex-col">
        <div class="p-6 text-2xl font-bold text-indigo-600 tracking-tight border-b">
            🧵 PM FOB
        </div>
        <nav class="flex-1 px-4 py-6 space-y-2">
            {{-- Dashboard --}}
            @php $active = request()->routeIs('dashboard'); @endphp
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 py-2 px-3 rounded-lg transition {{ $active ? 'bg-indigo-200 text-indigo-800' : 'text-gray-700 hover:bg-indigo-100 hover:text-indigo-700' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6"/>
                </svg>
                Dashboard
            </a>

            {{-- Manajemen Proyek --}}
            @php $active = request()->routeIs('admin.projects.*'); @endphp
            <a href="{{ route('admin.projects.index') }}" class="flex items-center gap-3 py-2 px-3 rounded-lg transition {{ $active ? 'bg-indigo-200 text-indigo-800' : 'text-gray-700 hover:bg-indigo-100 hover:text-indigo-700' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18M3 17h18"/>
                </svg>
                Manajemen Proyek
            </a>

            {{-- Manajemen Penjahit --}}
            @php $active = request()->routeIs('admin.penjahits.*'); @endphp
            <a href="{{ route('admin.penjahits.index') }}" class="flex items-center gap-3 py-2 px-3 rounded-lg transition {{ $active ? 'bg-indigo-200 text-indigo-800' : 'text-gray-700 hover:bg-indigo-100 hover:text-indigo-700' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 2l4 7-4 7-4-7 4-7z"/>
                </svg>
                Manajemen Penjahit
            </a>

            {{-- Manajemen Investor --}}
            @php $active = request()->routeIs('admin.investors.*'); @endphp
            <a href="{{ route('admin.investors.index') }}" class="flex items-center gap-3 py-2 px-3 rounded-lg transition {{ $active ? 'bg-indigo-200 text-indigo-800' : 'text-gray-700 hover:bg-indigo-100 hover:text-indigo-700' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm0 10v2m0-14V2"/>
                </svg>
                Manajemen Investor
            </a>

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}" class="pt-4">
                @csrf
                <button type="submit" class="flex items-center gap-3 w-full text-left py-2 px-3 rounded-lg text-red-600 hover:bg-red-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 002 2h3a2 2 0 002-2V7a2 2 0 00-2-2h-3a2 2 0 00-2 2v1"/>
                    </svg>
                    Logout
                </button>
            </form>
        </nav>
    </aside>
  
      <main class="flex-1 overflow-y-auto p-6">
        <div class="mb-6 flex items-center justify-between">
          <h1 class="text-2xl font-semibold text-teal-700">Edit Penjahit</h1>
          <a href="{{ route('admin.penjahits.index') }}"
             class="text-sm text-gray-600 hover:underline">← Kembali</a>
        </div>
  
        <form method="POST"
              action="{{ route('admin.penjahits.update',$penjahit) }}"
              class="bg-white p-6 rounded-lg shadow space-y-4">
          @csrf @method('PUT')
  
          <!-- Alamat -->
          <div>
            <x-input-label for="address" :value="__('Alamat')" />
            <x-text-input id="address" name="address" type="text"
                          class="block w-full"
                          :value="old('address',$penjahit->address)" required />
            <x-input-error :messages="$errors->get('address')" class="mt-1" />
          </div>
  
          <!-- Email -->
          <div>
            <x-input-label for="email" :value="__('Email Penjahit')" />
            <x-text-input id="email" name="email" type="email"
                          class="block w-full"
                          :value="old('email',$penjahit->email)" required />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
          </div>
  
          <!-- No. HP -->
          <div>
            <x-input-label for="phone" :value="__('No. HP')" />
            <x-text-input id="phone" name="phone" type="text"
                          class="block w-full"
                          :value="old('phone',$penjahit->phone)" required />
            <x-input-error :messages="$errors->get('phone')" class="mt-1" />
          </div>
  
          <!-- Status -->
          <div>
            <x-input-label for="status" :value="__('Status Pekerjaan')" />
            <select id="status" name="status" required
                    class="block w-full mt-1 rounded-md border-gray-300 shadow-sm">
              <option value="available"
                {{ old('status',$penjahit->status)=='available'?'selected':'' }}>
                Available
              </option>
              <option value="busy"
                {{ old('status',$penjahit->status)=='busy'?'selected':'' }}>
                Busy
              </option>
              <option value="inactive"
                {{ old('status',$penjahit->status)=='inactive'?'selected':'' }}>
                Inactive
              </option>
            </select>
            <x-input-error :messages="$errors->get('status')" class="mt-1" />
          </div>
  
          <div class="flex justify-end space-x-2">
            <a href="{{ route('admin.penjahits.index') }}"
               class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-50">
              Batal
            </a>
            <x-primary-button>
              Simpan Perubahan
            </x-primary-button>
          </div>
        </form>
      </main>
    </div>
  </x-app-layout>
  