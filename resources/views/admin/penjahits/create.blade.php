{{-- resources/views/admin/penjahits/create.blade.php --}}
{{-- @extends('layouts.app') --}}
<x-app-layout>
    <div class="flex h-screen bg-gray-100 text-gray-800">
      {{-- Sidebar --}}
       @include('admin.partials.sidebar') {{-- sesuaikan include --}}
      <main class="flex-1 overflow-y-auto p-6">
        <div class="mb-6 flex items-center justify-between">
          <h1 class="text-2xl font-semibold text-teal-700">Tambah Penjahit</h1>
          <a href="{{ route('admin.penjahits.index') }}"
             class="text-sm text-gray-600 hover:underline">← Kembali</a>
        </div>
  
        <form method="POST" action="{{ route('admin.penjahits.store') }}"
              class="bg-white p-6 rounded-lg shadow space-y-4">
          @csrf
  
          <!-- Alamat -->
          <div>
            <x-input-label for="address" :value="__('Alamat')" />
            <x-text-input id="address" name="address" type="text"
                          class="block w-full"
                          :value="old('address')" required autofocus />
            <x-input-error :messages="$errors->get('address')" class="mt-1" />
          </div>
  
          <!-- Email -->
          <div>
            <x-input-label for="email" :value="__('Email Penjahit')" />
            <x-text-input id="email" name="email" type="email"
                          class="block w-full"
                          :value="old('email')" required />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
          </div>
  
          <!-- No. HP -->
          <div>
            <x-input-label for="phone" :value="__('No. HP')" />
            <x-text-input id="phone" name="phone" type="text"
                          class="block w-full"
                          :value="old('phone')" required />
            <x-input-error :messages="$errors->get('phone')" class="mt-1" />
          </div>
  
          <!-- Status -->
          <div>
            <x-input-label for="status" :value="__('Status Pekerjaan')" />
            <select id="status" name="status" required
                    class="block w-full mt-1 rounded-md border-gray-300 shadow-sm">
              <option value="available" {{ old('status')=='available'?'selected':'' }}>Available</option>
              <option value="busy"      {{ old('status')=='busy'     ?'selected':'' }}>Busy</option>
              <option value="inactive"  {{ old('status')=='inactive'?'selected':'' }}>Inactive</option>
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
  