<x-app-layout>
    <div class="flex h-screen bg-gray-100 text-gray-800">
        {{-- Sidebar (sama seperti di dashboard) --}}
        <aside class="w-64 bg-white border-r shadow-lg hidden md:flex flex-col">
            <div class="p-6 text-2xl font-bold text-indigo-600 tracking-tight border-b">
                🧮 Investor Panel
            </div>
            <nav class="flex-1 px-4 py-6 space-y-2">
                @php $active = request()->routeIs('investor.dashboard'); @endphp
                <a href="{{ route('investor.dashboard') }}"
                   class="flex items-center gap-3 py-2 px-3 rounded-lg transition
                      {{ $active 
                         ? 'bg-indigo-200 text-indigo-800' 
                         : 'text-gray-700 hover:bg-indigo-100 hover:text-indigo-700' }}">
                    <!-- ikon Home -->
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-5 h-5 text-indigo-500"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6"/>
                    </svg>
                    Dashboard
                </a>
                @php $active = request()->routeIs('investors.create'); @endphp
                <a href="{{ route('investors.create') }}"
                   class="flex items-center gap-3 py-2 px-3 rounded-lg transition
                      {{ $active 
                         ? 'bg-indigo-200 text-indigo-800' 
                         : 'text-gray-700 hover:bg-indigo-100 hover:text-indigo-700' }}">
                    <!-- ikon Plus -->
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-5 h-5 text-indigo-500"
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
                <h1 class="text-3xl font-semibold text-indigo-700">Tambah Data Investor</h1>
                <p class="text-gray-500">Silakan lengkapi informasi di bawah.</p>
            </div>

            <form method="POST" action="{{ route('investors.store') }}" class="space-y-6 bg-white p-6 rounded-lg shadow">
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

                <!-- No. HP -->
                <div>
                    <x-input-label for="phone" :value="__('No. HP')" />
                    <x-text-input id="phone" type="text" name="phone"
                                  :value="old('phone')" required />
                    <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                </div>

                <!-- Jumlah Investasi -->
                <div>
                    <x-input-label for="amount" :value="__('Jumlah Investasi')" />
                    <x-text-input id="amount" type="number" name="amount" step="0.01"
                                  :value="old('amount')" required />
                    <x-input-error :messages="$errors->get('amount')" class="mt-1" />
                </div>

                <!-- Deadline -->
                <div>
                    <x-input-label for="deadline" :value="__('Deadline')" />
                    <x-text-input id="deadline" type="date" name="deadline"
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
