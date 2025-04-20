<x-app-layout>
    <div class="flex h-screen bg-gray-100 text-gray-800">
        {{-- Sidebar --}}

        {{-- Main Content --}}
        <main class="flex-1 overflow-y-auto p-6">
            {{-- Header --}}
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-indigo-700">Manajemen Proyek</h1>
                @if(session('success'))
                    <div class="text-green-600">{{ session('success') }}</div>
                @endif
            </div>

            {{-- Form Tambah Proyek --}}
            <form method="POST" action="{{ route('admin.projects.store') }}"
                  enctype="multipart/form-data"
                  class="bg-white p-6 rounded-lg shadow mb-8 space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Nama Proyek -->
                    <div>
                        <x-input-label for="name" :value="__('Nama Proyek')" />
                        <x-text-input id="name" name="name"
                                      class="block w-full"
                                      :value="old('name')" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>
                    <!-- Anggaran -->
                    <div>
                        <x-input-label for="budget" :value="__('Anggaran (Rp)')" />
                        <x-text-input id="budget" name="budget" type="number" step="0.01"
                                      class="block w-full"
                                      :value="old('budget')" required />
                        <x-input-error :messages="$errors->get('budget')" class="mt-1" />
                    </div>
                    <!-- Deadline -->
                    <div>
                        <x-input-label for="deadline" :value="__('Deadline')" />
                        <x-text-input id="deadline" name="deadline" type="date"
                                      class="block w-full"
                                      :value="old('deadline')" required />
                        <x-input-error :messages="$errors->get('deadline')" class="mt-1" />
                    </div>
                    <!-- Status -->
                    <div>
                        <x-input-label for="status" :value="__('Status')" />
                        <select id="status" name="status" required
                                class="block w-full mt-1 rounded-md border-gray-300 shadow-sm">
                            <option value="pending" {{ old('status')=='pending'?'selected':'' }}>
                                Pending
                            </option>
                            <option value="on_progress" {{ old('status')=='on_progress'?'selected':'' }}>
                                On Progress
                            </option>
                            <option value="completed" {{ old('status')=='completed'?'selected':'' }}>
                                Completed
                            </option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-1" />
                    </div>
                    <!-- Image -->
                    <div class="md:col-span-2">
                        <x-input-label for="image" :value="__('Gambar Proyek (opsional)')" />
                        <input id="image" name="image" type="file"
                               class="block w-full mt-1" accept="image/*" />
                        <x-input-error :messages="$errors->get('image')" class="mt-1" />
                    </div>
                </div>

                <div class="flex justify-end">
                    <x-primary-button>Tambah Proyek</x-primary-button>
                </div>
            </form>

            {{-- Tabel Daftar Proyek --}}
            <div class="bg-white shadow rounded-lg overflow-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Anggaran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deadline</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($projects as $project)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $project->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $project->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                Rp {{ number_format($project->budget,0,',','.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $project->deadline }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ ucfirst($project->status) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                <a href="{{ route('admin.projects.edit',$project) }}"
                                   class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                <form action="{{ route('admin.projects.destroy',$project) }}"
                                      method="POST" class="inline-block"
                                      onsubmit="return confirm('Hapus proyek ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </main>
    </div>
</x-app-layout>
