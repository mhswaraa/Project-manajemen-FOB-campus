{{-- resources/views/penjahit/profile/index.blade.php --}}
<x-app-layout>
  <div class="flex h-screen bg-gray-100 text-gray-800">
  @include('penjahit.partials.sidebar')

  <main class="flex-1 overflow-y-auto p-6">
    @if(session('success'))
      <div class="mb-6 px-4 py-3 bg-teal-100 text-teal-800 rounded-lg">
        {{ session('success') }}
      </div>
    @endif

    <h1 class="text-2xl font-bold text-teal-700 mb-4">Profil Penjahit</h1>

    <form action="{{ route('penjahit.profile.update') }}" method="POST" class="bg-white shadow rounded-lg p-6 space-y-5">
      @csrf

      <!-- Alamat -->
      <div>
        <x-input-label for="address" :value="__('Alamat')" />
        <x-text-input
          id="address"
          name="address"
          type="text"
          class="mt-1 block w-full"
          :value="old('address', $tailor->address ?? '')"
          required
        />
        <x-input-error :messages="$errors->get('address')" class="mt-1"/>
      </div>

      <!-- Phone -->
      <div>
        <x-input-label for="phone" :value="__('No. HP')" />
        <x-text-input
          id="phone"
          name="phone"
          type="text"
          class="mt-1 block w-full"
          :value="old('phone', $tailor->phone ?? '')"
          required
        />
        <x-input-error :messages="$errors->get('phone')" class="mt-1"/>
      </div>

      <!-- Email -->
      <div>
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input
          id="email"
          name="email"
          type="email"
          class="mt-1 block w-full"
          :value="old('email', $tailor->email ?? Auth::user()->email)"
          required
        />
        <x-input-error :messages="$errors->get('email')" class="mt-1"/>
      </div>

      <!-- Status -->
      <div>
        <x-input-label for="status" :value="__('Status')" />
        <select
          id="status"
          name="status"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
          required
        >
          <option value="available" {{ old('status', $tailor->status ?? '')=='available'?'selected':'' }}>
            Available
          </option>
          <option value="busy" {{ old('status', $tailor->status ?? '')=='busy'?'selected':'' }}>
            Busy
          </option>
          <option value="inactive" {{ old('status', $tailor->status ?? '')=='inactive'?'selected':'' }}>
            Inactive
          </option>
        </select>
        <x-input-error :messages="$errors->get('status')" class="mt-1"/>
      </div>

      <!-- Password (opsional) -->
      <div>
        <x-input-label for="password" :value="__('Password Baru (opsional)')" />
        <x-text-input
          id="password"
          name="password"
          type="password"
          class="mt-1 block w-full"
        />
        <x-input-error :messages="$errors->get('password')" class="mt-1"/>
      </div>

      <div class="flex justify-end">
        <x-primary-button>
          Simpan Profil
        </x-primary-button>
      </div>
    </form>
  </main>
</x-app-layout>
