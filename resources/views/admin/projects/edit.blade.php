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

        {{-- Main Content --}}
        <main class="flex-1 overflow-y-auto p-6">
            {{-- Header --}}
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-indigo-700">Edit Proyek</h1>
                <a href="{{ route('admin.projects.index') }}"
                   class="text-sm text-gray-600 hover:underline">← Kembali ke Daftar Proyek</a>
            </div>

            {{-- Form Edit Proyek --}}
            <form method="POST"
                  action="{{ route('admin.projects.update', $project) }}"
                  enctype="multipart/form-data"
                  class="bg-white p-6 rounded-lg shadow mb-8 space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Nama Proyek -->
                    <div>
                        <x-input-label for="name" :value="__('Nama Proyek')" />
                        <x-text-input id="name" name="name"
                                      class="block w-full"
                                      :value="old('name', $project->name)"
                                      required />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    <!-- Anggaran -->
                    <div>
                        <x-input-label for="budget" :value="__('Anggaran (Rp)')" />
                        <x-text-input id="budget" name="budget" type="number" step="0.01"
                                      class="block w-full"
                                      :value="old('budget', $project->budget)"
                                      required />
                        <x-input-error :messages="$errors->get('budget')" class="mt-1" />
                    </div>

                    <!-- Deadline -->
                    <div>
                        <x-input-label for="deadline" :value="__('Deadline')" />
                        <x-text-input id="deadline" name="deadline" type="date"
                        :value="old('deadline', \Carbon\Carbon::parse($project->deadline)->format('Y-m-d'))"/>
                        <x-input-error :messages="$errors->get('deadline')" class="mt-1" />
                    </div>

                    <!-- Status -->
                    <div>
                        <x-input-label for="status" :value="__('Status')" />
                        <select id="status" name="status" required
                                class="block w-full mt-1 rounded-md border-gray-300 shadow-sm">
                            <option value="pending"
                                {{ old('status', $project->status)=='pending' ? 'selected' : '' }}>
                                Pending
                            </option>
                            <option value="on_progress"
                                {{ old('status', $project->status)=='on_progress' ? 'selected' : '' }}>
                                On Progress
                            </option>
                            <option value="completed"
                                {{ old('status', $project->status)=='completed' ? 'selected' : '' }}>
                                Completed
                            </option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-1" />
                    </div>

                    <!-- Gambar Lama & Upload Baru -->
                    <div class="md:col-span-2">
                        <x-input-label for="image" :value="__('Gambar Proyek (opsional)')" />
                        @if($project->image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/'.$project->image) }}"
                                     alt="Existing Project Image"
                                     class="h-20 w-20 object-cover rounded">
                            </div>
                        @endif
                        <input id="image" name="image" type="file"
                               class="block w-full mt-1"
                               accept="image/*" />
                        <x-input-error :messages="$errors->get('image')" class="mt-1" />
                    </div>
                </div>

                <div class="flex justify-end space-x-2">
                    <a href="{{ route('admin.projects.index') }}"
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
