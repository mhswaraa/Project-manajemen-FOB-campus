<x-app-layout>
    <div class="flex h-screen bg-gray-100 text-gray-800">
        {{-- Sidebar --}}

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
