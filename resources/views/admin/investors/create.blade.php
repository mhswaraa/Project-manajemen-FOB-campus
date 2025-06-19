{{-- resources/views/admin/investors/create.blade.php --}}
{{-- Layout --}}
<x-app-layout>
    <div class="flex h-screen bg-gray-100 text-gray-800">
        {{-- Sidebar --}}
         @include('admin.partials.sidebar') {{-- sesuaikan include --}}

        <main class="flex-1 overflow-y-auto p-6">
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-indigo-700">Tambah Investor</h1>
                <a href="{{ route('admin.investors.index') }}"
                   class="text-sm text-gray-600 hover:underline">← Kembali</a>
            </div>

            <form method="POST" action="{{ route('admin.investors.store') }}"
                  class="bg-white p-6 rounded-lg shadow space-y-4">
                @csrf
                <!-- Nama -->
                <div>
                    <x-input-label for="name" :value="__('Nama')" />
                    <x-text-input id="name" name="name" type="text"
                                  class="block w-full"
                                  :value="old('name')" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                <!-- Email -->
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" name="email" type="email"
                                  class="block w-full"
                                  :value="old('email')" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <!-- Phone -->
                <div>
                    <x-input-label for="phone" :value="__('No. HP')" />
                    <x-text-input id="phone" name="phone" type="text"
                                  class="block w-full"
                                  :value="old('phone')" required />
                    <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                </div>

                <!-- Amount -->
                <div>
                    <x-input-label for="amount" :value="__('Jumlah Investasi')" />
                    <x-text-input id="amount" name="amount" type="number" step="0.01"
                                  class="block w-full"
                                  :value="old('amount')" required />
                    <x-input-error :messages="$errors->get('amount')" class="mt-1" />
                </div>

                <!-- Deadline -->
                <div>
                    <x-input-label for="deadline" :value="__('Deadline')" />
                    <x-text-input id="deadline" name="deadline" type="date"
                                  class="block w-full"
                                  :value="old('deadline')" required />
                    <x-input-error :messages="$errors->get('deadline')" class="mt-1" />
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
