<x-app-layout>
    <div class="flex h-screen bg-gray-50">
        @include('admin.partials.sidebar')

        <main class="flex-1 overflow-y-auto p-6 lg:p-8">
            <div class="max-w-4xl mx-auto">
                {{-- Header --}}
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">Edit Proyek</h1>
                        <p class="text-gray-500 mt-1">Perbarui detail untuk proyek: <span class="font-semibold text-indigo-600">{{ $project->name }}</span></p>
                    </div>
                     <a href="{{ route('admin.projects.show', $project) }}" class="flex-shrink-0 inline-flex items-center gap-2 px-4 py-2 border border-gray-300 bg-white text-gray-700 font-semibold rounded-lg hover:bg-gray-50 shadow-sm">
                        <x-heroicon-s-arrow-left class="h-5 w-5"/>
                        Kembali ke Detail
                    </a>
                </div>

                <div class="bg-white p-8 rounded-xl shadow-md">
                    <form method="POST" action="{{ route('admin.projects.update', $project) }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        {{-- Nama Proyek --}}
                        <div>
                            <x-input-label for="name" :value="__('Nama Proyek')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $project->name)" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        <div>
    <x-input-label for="nominal_proyek" :value="__('Nominal Proyek dari Buyer (Rp)')" />
    <x-text-input id="nominal_proyek" name="nominal_proyek" type="number" step="1000" class="mt-1 block w-full" :value="old('nominal_proyek', $project->nominal_proyek)" required />
    <x-input-error :messages="$errors->get('nominal_proyek')" class="mt-2" />
</div>
                        {{-- Kuantitas & Deadline --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="quantity" :value="__('Total Kuantitas (pcs)')" />
                                <x-text-input id="quantity" name="quantity" type="number" class="mt-1 block w-full" :value="old('quantity', $project->quantity)" required />
                                <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="deadline" :value="__('Deadline')" />
                                {{-- FIX: Parse the deadline to ensure it's a Carbon object before formatting --}}
                                <x-text-input id="deadline" name="deadline" type="date" class="mt-1 block w-full" :value="old('deadline', \Carbon\Carbon::parse($project->deadline)->format('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('deadline')" class="mt-2" />
                            </div>
                        </div>

                        {{-- Rincian Finansial --}}
                        <div class="pt-4 border-t">
                            <h4 class="text-md font-semibold text-gray-700 mb-3">Rincian Finansial</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div>
                                    <x-input-label for="price_per_piece" :value="__('Modal Investor/pcs')" />
                                    <x-text-input id="price_per_piece" name="price_per_piece" type="number" step="50" class="mt-1 block w-full" :value="old('price_per_piece', $project->price_per_piece)" required />
                                    <x-input-error :messages="$errors->get('price_per_piece')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="material_cost" :value="__('Biaya Bahan/pcs')" />
                                    <x-text-input id="material_cost" name="material_cost" type="number" step="50" class="mt-1 block w-full" :value="old('material_cost', $project->material_cost)" required />
                                    <x-input-error :messages="$errors->get('material_cost')" class="mt-2" />
                                </div>
                                 <div>
                                    <x-input-label for="wage_per_piece" :value="__('Upah Jahit/pcs')" />
                                    <x-text-input id="wage_per_piece" name="wage_per_piece" type="number" step="50" class="mt-1 block w-full" :value="old('wage_per_piece', $project->wage_per_piece)" required />
                                    <x-input-error :messages="$errors->get('wage_per_piece')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="profit" :value="__('Profit Investor/pcs')" />
                                    <x-text-input id="profit" name="profit" type="number" step="50" class="mt-1 block w-full" :value="old('profit', $project->profit)" required />
                                    <x-input-error :messages="$errors->get('profit')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="convection_profit" :value="__('Profit Konveksi/pcs')" />
                                    <x-text-input id="convection_profit" name="convection_profit" type="number" step="50" class="mt-1 block w-full" :value="old('convection_profit', $project->convection_profit)" required />
                                    <x-input-error :messages="$errors->get('convection_profit')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        {{-- Gambar & Status --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t">
                            <div>
                                <x-input-label for="image" :value="__('Ganti Gambar Proyek (Opsional)')" />
                                <input type="file" name="image" id="image" class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"/>
                                <x-input-error :messages="$errors->get('image')" class="mt-2" />
                                @if ($project->image)
                                    <div class="mt-4">
                                        <p class="text-sm text-gray-500 mb-2">Gambar saat ini:</p>
                                        <img src="{{ asset('storage/' . $project->image) }}" alt="{{ $project->name }}" class="h-24 w-auto rounded-md">
                                    </div>
                                @endif
                            </div>
                            <div>
                                <x-input-label for="status" :value="__('Status Proyek')" />
                                <select name="status" id="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="active" @selected(old('status', $project->status) == 'active')>Aktif</option>
                                    <option value="inactive" @selected(old('status', $project->status) == 'inactive')>Tidak Aktif</option>
                                </select>
                            </div>
                        </div>
                        
                        {{-- Tombol Aksi --}}
                        <div class="flex items-center justify-end pt-6 border-t">
                            <x-primary-button>
                                {{ __('Simpan Perubahan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</x-app-layout>
