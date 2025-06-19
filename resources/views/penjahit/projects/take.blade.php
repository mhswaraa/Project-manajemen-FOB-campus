{{-- resources/views/penjahit/projects/take.blade.php --}}
<x-app-layout>
  <div class="flex h-screen bg-gray-100 text-gray-800">
    {{-- Sidebar --}}
    @include('penjahit.partials.sidebar')

    <main class="flex-1 p-6">
      <div class="max-w-md mx-auto bg-white shadow rounded-lg p-6 space-y-6">
        {{-- Judul --}}
        <h1 class="text-2xl font-semibold text-teal-700">
          Ambil Tugas: {{ $project->name }}
        </h1>

        {{-- Ringkasan Kuota --}}
        <div class="space-y-2 text-gray-700">
          <p>
            <span class="font-medium">Total Qty (approved invest):</span>
            {{ $totalInvested }} pcs
          </p>
          <p>
            <span class="font-medium">Sudah Diambil:</span>
            {{ $alreadyTaken }} pcs
          </p>
          <p>
            <span class="font-medium">Sisa:</span>
            {{ $remaining }} pcs
          </p>
        </div>

        {{-- Form Ambil --}}
        <form action="{{ route('penjahit.projects.store', $project) }}"
              method="POST"
              class="space-y-4">
          @csrf

          <div>
            <x-input-label for="qty" :value="__('Jumlah Qty yang Diambil')" />
            <x-text-input
              id="qty"
              name="qty"
              type="number"
              min="1"
              max="{{ $remaining }}"
              class="mt-1 block w-full"
              value="{{ old('qty') }}"
              required
            />
            <x-input-error :messages="$errors->get('qty')" class="mt-1"/>
            <p class="text-xs text-gray-500 mt-1">
              Masukkan antara 1 dan {{ $remaining }} pcs
            </p>
          </div>

          <div class="flex justify-end">
            <a href="{{ route('penjahit.projects.index') }}"
               class="px-4 py-2 mr-2 border rounded hover:bg-gray-50">
              Batal
            </a>
            <x-primary-button>
              Ambil Tugas
            </x-primary-button>
          </div>
        </form>
      </div>
    </main>
  </div>
</x-app-layout>
