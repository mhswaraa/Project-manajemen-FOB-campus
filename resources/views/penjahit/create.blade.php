<x-app-layout>
  <div class="flex h-screen bg-gray-100 text-gray-800">
      {{-- Sidebar --}}
      <aside class="w-64 bg-white border-r shadow-lg hidden md:flex flex-col">
          <div class="p-6 text-2xl font-bold text-teal-600 tracking-tight border-b">
              🪡 Penjahit Panel
          </div>
          <nav class="flex-1 px-4 py-6 space-y-2">
              @php $active = request()->routeIs('penjahit.dashboard'); @endphp
              <a href="{{ route('penjahit.dashboard') }}"
                 class="flex items-center gap-3 py-2 px-3 rounded-lg transition
                    {{ $active 
                       ? 'bg-teal-200 text-teal-800' 
                       : 'text-gray-700 hover:bg-teal-100 hover:text-teal-700' }}">
                  <svg xmlns="http://www.w3.org/2000/svg"
                       class="w-5 h-5 text-teal-500"
                       fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6"/>
                  </svg>
                  Dashboard
              </a>
              @php $active = request()->routeIs('penjahits.create'); @endphp
              <a href="{{ route('penjahits.create') }}"
                 class="flex items-center gap-3 py-2 px-3 rounded-lg transition
                    {{ $active 
                       ? 'bg-teal-200 text-teal-800' 
                       : 'text-gray-700 hover:bg-teal-100 hover:text-teal-700' }}">
                  <svg xmlns="http://www.w3.org/2000/svg"
                       class="w-5 h-5 text-teal-500"
                       fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path d="M12 4v16m8-8H4"/>
                  </svg>
                  Buat Data
              </a>
          </nav>
      </aside>

      {{-- Form Content --}}
      <main class="flex-1 overflow-y-auto p-6">
          <div class="mb-8">
              <h1 class="text-3xl font-semibold text-teal-700">Tambah Data Penjahit</h1>
              <p class="text-gray-500">Silakan lengkapi informasi di bawah.</p>
          </div>

          <form method="POST" action="{{ route('penjahits.store') }}" class="space-y-6 bg-white p-6 rounded-lg shadow">
              @csrf

              <!-- Nama (readonly) -->
              <div>
                  <x-input-label for="name" :value="__('Nama')" />
                  <x-text-input id="name" type="text" name="name"
                                :value="Auth::user()->name"
                                readonly class="bg-gray-100 cursor-not-allowed" />
              </div>

              <!-- Email (readonly) -->
              <div>
                  <x-input-label for="email" :value="__('Email')" />
                  <x-text-input id="email" type="email" name="email"
                                :value="Auth::user()->email"
                                readonly class="bg-gray-100 cursor-not-allowed" />
              </div>

              <!-- Alamat -->
              <div>
                  <x-input-label for="address" :value="__('Alamat')" />
                  <x-text-input id="address" type="text" name="address"
                                :value="old('address')" required />
                  <x-input-error :messages="$errors->get('address')" class="mt-1" />
              </div>

              <!-- No. HP -->
              <div>
                  <x-input-label for="phone" :value="__('No. HP')" />
                  <x-text-input id="phone" type="text" name="phone"
                                :value="old('phone')" required />
                  <x-input-error :messages="$errors->get('phone')" class="mt-1" />
              </div>

              <!-- Status Pekerjaan -->
              <div>
                  <x-input-label for="status" :value="__('Status Pekerjaan')" />
                  <select id="status" name="status" required
                          class="block w-full mt-1 rounded-md border-gray-300 shadow-sm">
                      <option value="available" {{ old('status')=='available'?'selected':'' }}>Available</option>
                      <option value="busy"      {{ old('status')=='busy'      ?'selected':'' }}>Busy</option>
                      <option value="inactive"  {{ old('status')=='inactive'  ?'selected':'' }}>Inactive</option>
                  </select>
                  <x-input-error :messages="$errors->get('status')" class="mt-1" />
              </div>

              <div class="flex justify-end">
                  <x-primary-button>
                      Simpan
                  </x-primary-button>
              </div>
          </form>
      </main>
  </div>
</x-app-layout>
