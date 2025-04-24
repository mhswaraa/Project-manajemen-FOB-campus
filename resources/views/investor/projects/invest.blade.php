<x-app-layout>
    <div class="flex h-screen bg-gray-50 text-gray-800">
      {{-- Sidebar --}}
      @include('investor.partials.sidebar')
  
      {{-- Main Content --}}
      <main class="flex-1 p-6 overflow-y-auto">
        <div class="max-w-md mx-auto bg-white shadow rounded-lg p-6">
          <h1 class="text-2xl font-semibold text-green-700 mb-4">
            Investasikan ke: {{ $project->name }}
          </h1>
          <form action="{{ route('investor.projects.store', $project) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
  
            {{-- Nominal Investasi --}}
            <div>
              <x-input-label for="amount" :value="__('Jumlah Investasi (Rp)')" />
              <x-text-input id="amount" name="amount" type="number" min="1" max="{{ $project->budget }}"
                            class="block w-full" :value="old('amount')" required />
              <x-input-error :messages="$errors->get('amount')" class="mt-1" />
            </div>
  
            {{-- Deadline --}}
            <div>
              <x-input-label for="deadline" :value="__('Tanggal Deadline')" />
              <x-text-input id="deadline" name="deadline" type="date"
                            class="block w-full" :value="old('deadline')" required />
              <x-input-error :messages="$errors->get('deadline')" class="mt-1" />
            </div>
  
            {{-- Pesan (opsional) --}}
            <div>
              <x-input-label for="message" :value="__('Pesan (opsional)')" />
              <textarea id="message" name="message"
                        class="block w-full border-gray-300 rounded" rows="3">{{ old('message') }}</textarea>
              <x-input-error :messages="$errors->get('message')" class="mt-1" />
            </div>
  
            {{-- Upload Bukti (opsional) --}}
            <div>
              <x-input-label for="receipt" :value="__('Upload Bukti (jpg/png/pdf)')" />
              <x-text-input id="receipt" name="receipt" type="file"
                            class="block w-full" />
              <x-input-error :messages="$errors->get('receipt')" class="mt-1" />
            </div>
  
            {{-- Button --}}
            <div class="flex justify-between">
              <a href="{{ route('investor.projects.index') }}"
                 class="px-4 py-2 border rounded hover:bg-gray-100">
                Batal
              </a>
              <x-primary-button>
                Kirim Investasi
              </x-primary-button>
            </div>
          </form>
        </div>
      </main>
    </div>
  </x-app-layout>
  