{{-- resources/views/admin/projects/index.blade.php --}}
<x-app-layout>
    <div class="flex h-screen bg-gray-100 text-gray-800">
        @include('admin.partials.sidebar')
        {{-- Main Content --}}
        <main class="flex-1 overflow-y-auto p-6">
            {{-- Header --}}
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-indigo-700">Manajemen Proyek</h1>
                @if(session('success'))
                    <div class="px-4 py-2 bg-green-100 text-green-800 rounded">
                        {{ session('success') }}
                    </div>
                @endif
            </div>

            {{-- Form Tambah Proyek --}}
            <form method="POST"
                  action="{{ route('admin.projects.store') }}"
                  enctype="multipart/form-data"
                  class="bg-white p-6 rounded-lg shadow mb-8 space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Nama Proyek --}}
                    <div>
                        <x-input-label for="name" :value="__('Nama Proyek')" />
                        <x-text-input id="name"
                                      name="name"
                                      type="text"
                                      class="block w-full"
                                      :value="old('name')"
                                      required />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    {{-- Harga per pcs --}}
                    <div>
                        <x-input-label for="price_per_piece" :value="__('Harga per pcs (Rp)')" />
                        <x-text-input id="price_per_piece"
                                      name="price_per_piece"
                                      type="number"
                                      step="0.01"
                                      class="block w-full"
                                      :value="old('price_per_piece')"
                                      required />
                        <x-input-error :messages="$errors->get('price_per_piece')" class="mt-1" />
                    </div>

                    {{-- Total Qty --}}
                    <div>
                        <x-input-label for="quantity" :value="__('Total Qty')" />
                        <x-text-input id="quantity"
                                      name="quantity"
                                      type="number"
                                      class="block w-full"
                                      :value="old('quantity')"
                                      required />
                        <x-input-error :messages="$errors->get('quantity')" class="mt-1" />
                    </div>

                    {{-- Profit --}}
                    <div>
                        <x-input-label for="profit" :value="__('Profit (Rp)')" />
                        <x-text-input id="profit"
                                      name="profit"
                                      type="number"
                                      step="0.01"
                                      class="block w-full"
                                      :value="old('profit')"
                                      required />
                        <x-input-error :messages="$errors->get('profit')" class="mt-1" />
                    </div>

                    {{-- Deadline --}}
                    <div>
                        <x-input-label for="deadline" :value="__('Deadline')" />
                        <x-text-input id="deadline"
                                      name="deadline"
                                      type="date"
                                      class="block w-full"
                                      :value="old('deadline')"
                                      required />
                        <x-input-error :messages="$errors->get('deadline')" class="mt-1" />
                    </div>

                    {{-- Status --}}
                    <div>
                        <x-input-label for="status" :value="__('Status')" />
                        <select id="status"
                                name="status"
                                required
                                class="block w-full mt-1 rounded-md border-gray-300 shadow-sm">
                            <option value="{{ \App\Models\Project::STATUS_ACTIVE }}"
                                {{ old('status') === \App\Models\Project::STATUS_ACTIVE ? 'selected' : '' }}>
                                Active
                            </option>
                            <option value="{{ \App\Models\Project::STATUS_INACTIVE }}"
                                {{ old('status') === \App\Models\Project::STATUS_INACTIVE ? 'selected' : '' }}>
                                Inactive
                            </option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-1" />
                    </div>
                </div>

                <div class="flex justify-end">
                    <x-primary-button>
                        Tambah Proyek
                    </x-primary-button>
                </div>
            </form>

            {{-- Tabel Daftar Proyek --}}
            <div class="bg-white shadow rounded-lg overflow-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga/pcs</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Profit</th>
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
                                    Rp {{ number_format($project->price_per_piece, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $project->quantity }} pcs
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    Rp {{ number_format($project->profit, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $project->deadline }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ ucfirst($project->status) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                    <a href="{{ route('admin.projects.edit', $project) }}"
                                       class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                    <form action="{{ route('admin.projects.destroy', $project) }}"
                                          method="POST"
                                          class="inline-block"
                                          onsubmit="return confirm('Hapus proyek ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-red-600 hover:text-red-900">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach

                        @if($projects->isEmpty())
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                    Belum ada proyek terdaftar.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</x-app-layout>
