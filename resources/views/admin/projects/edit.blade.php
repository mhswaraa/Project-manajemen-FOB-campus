{{-- resources/views/admin/projects/edit.blade.php --}}
<x-app-layout>
    <div class="flex h-screen bg-gray-100 text-gray-800">
        @include('admin.partials.sidebar')
        {{-- Main Content --}}
        <main class="flex-1 overflow-y-auto p-6">
            {{-- Header --}}
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-indigo-700">Edit Proyek</h1>
                <a href="{{ route('admin.projects.index') }}"
                   class="text-sm text-gray-600 hover:underline">
                    ← Kembali ke Daftar Proyek
                </a>
            </div>

            {{-- Form Edit Proyek --}}
            <form method="POST"
                  action="{{ route('admin.projects.update', $project) }}"
                  enctype="multipart/form-data"
                  class="bg-white p-6 rounded-lg shadow mb-8 space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Nama Proyek --}}
                    <div>
                        <x-input-label for="name" :value="__('Nama Proyek')" />
                        <x-text-input id="name"
                                      name="name"
                                      type="text"
                                      class="block w-full"
                                      :value="old('name', $project->name)"
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
                                      :value="old('price_per_piece', $project->price_per_piece)"
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
                                      :value="old('quantity', $project->quantity)"
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
                                      :value="old('profit', $project->profit)"
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
                                      :value="old('deadline', \Carbon\Carbon::parse($project->deadline)->format('Y-m-d'))"
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
                                {{ old('status', $project->status) === \App\Models\Project::STATUS_ACTIVE ? 'selected' : '' }}>
                                Active
                            </option>
                            <option value="{{ \App\Models\Project::STATUS_INACTIVE }}"
                                {{ old('status', $project->status) === \App\Models\Project::STATUS_INACTIVE ? 'selected' : '' }}>
                                Inactive
                            </option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-1" />
                    </div>
                </div>

                {{-- Gambar Lama & Upload Baru --}}
                <div class="mt-4">
                    <x-input-label for="image" :value="__('Gambar Proyek (opsional)')" />
                    @if($project->image)
                        <div class="mb-2">
                            <img src="{{ asset('storage/'.$project->image) }}"
                                 alt="Existing Project Image"
                                 class="h-20 w-20 object-cover rounded">
                        </div>
                    @endif
                    <input id="image"
                           name="image"
                           type="file"
                           class="block w-full mt-1"
                           accept="image/*" />
                    <x-input-error :messages="$errors->get('image')" class="mt-1" />
                </div>

                {{-- Actions --}}
                <div class="flex justify-end space-x-2 mt-6">
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
