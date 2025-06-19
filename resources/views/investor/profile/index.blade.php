{{-- resources/views/investor/profile/index.blade.php --}}
<x-app-layout>
  <div class="flex h-screen bg-gray-50 text-gray-800">
    {{-- Sidebar --}}
    @include('investor.partials.sidebar')

    {{-- Main Content --}}
    <main class="flex-1 p-6 overflow-y-auto">
      <div class="max-w-3xl mx-auto space-y-6">

        {{-- Success Alert --}}
        @if(session('success'))
          <div class="px-4 py-3 bg-green-100 text-green-800 rounded shadow-sm">
            {{ session('success') }}
          </div>
        @endif

        {{-- Jika data investor belum ada --}}
        @if(!$investor)
          <div class="bg-white shadow-lg rounded-lg p-6">
            <h2 class="text-2xl font-bold text-green-700 mb-4">Lengkapi Data Diri</h2>
            <form action="{{ route('investor.profile.update') }}" method="POST" class="space-y-5">
              @csrf

              {{-- Nama --}}
              <div>
                <x-input-label for="name" :value="__('Nama')" />
                <x-text-input id="name" name="name" type="text"
                              class="mt-1 block w-full"
                              :value="old('name', $user->name)" required />
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
              </div>

              {{-- Email --}}
              <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" name="email" type="email"
                              class="mt-1 block w-full"
                              :value="old('email', $user->email)" required />
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
              </div>

              {{-- Phone --}}
              <div>
                <x-input-label for="phone" :value="__('No. HP')" />
                <x-text-input id="phone" name="phone" type="text"
                              class="mt-1 block w-full"
                              :value="old('phone')" required />
                <x-input-error :messages="$errors->get('phone')" class="mt-1" />
              </div>

              {{-- Password (opsional) --}}
              <div>
                <x-input-label for="password" :value="__('Password Baru (opsional)')" />
                <x-text-input id="password" name="password" type="password"
                              class="mt-1 block w-full" autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
              </div>

              {{-- Confirm Password --}}
              <div>
                <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
                <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                              class="mt-1 block w-full" autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
              </div>

              <div class="flex justify-end">
                <x-primary-button>{{ __('Simpan Profil') }}</x-primary-button>
              </div>
            </form>
          </div>
        @else
          {{-- Tampilkan Profil dan Form Edit --}}
          <div class="bg-white shadow-lg rounded-lg p-6 space-y-6">
            {{-- Detail Info --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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

              {{-- Statistik Singkat (opsional) --}}
              <div class="border-l md:pl-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-3">Statistik Singkat</h3>
                <dl class="space-y-4">
                  <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-600">Total Investasi</dt>
                    <dd class="font-bold">
                      Rp {{ number_format($investor->amount ?? 0, 0, ',', '.') }}
                    </dd>
                  </div>
                  <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-600">Didaftarkan Pada</dt>
                    <dd>{{ \Carbon\Carbon::parse($investor->registered_at)->format('d M Y') }}</dd>
                  </div>
                </dl>
              </div>
            </div>

            {{-- Tombol Edit --}}
            <div class="text-right">
              <button
                onclick="document.getElementById('edit-form').scrollIntoView({ behavior: 'smooth' });"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                Edit Profil
              </button>

              {{-- Form Edit disembunyikan di bawah --}}
              <div id="edit-form" class="mt-8">
                <h2 class="text-xl font-bold text-green-700 mb-4">Ubah Profil</h2>
                <form action="{{ route('investor.profile.update') }}" method="POST" class="space-y-5">
                  @csrf

                  {{-- Nama --}}
                  <div>
                    <x-input-label for="name" :value="__('Nama')" />
                    <x-text-input id="name" name="name" type="text"
                                  class="mt-1 block w-full"
                                  :value="old('name',$investor->name)" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                  </div>

                  {{-- Email --}}
                  <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" name="email" type="email"
                                  class="mt-1 block w-full"
                                  :value="old('email',$investor->email)" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                  </div>

                  {{-- Phone --}}
                  <div>
                    <x-input-label for="phone" :value="__('No. HP')" />
                    <x-text-input id="phone" name="phone" type="text"
                                  class="mt-1 block w-full"
                                  :value="old('phone',$investor->phone)" required />
                    <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                  </div>

                  {{-- Password Baru --}}
                  <div>
                    <x-input-label for="password" :value="__('Password Baru (opsional)')" />
                    <x-text-input id="password" name="password" type="password"
                                  class="mt-1 block w-full" autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                  </div>

                  {{-- Konfirmasi Password --}}
                  <div>
                    <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                                  class="mt-1 block w-full" autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                  </div>

                  <div class="flex justify-end">
                    <x-primary-button>{{ __('Simpan Perubahan') }}</x-primary-button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        @endif

      </div>
    </main>
  </div>
</x-app-layout>
