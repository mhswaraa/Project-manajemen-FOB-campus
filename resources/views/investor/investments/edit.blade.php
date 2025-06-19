{{-- resources/views/investor/investments/edit.blade.php --}}
<x-app-layout>
  <div class="flex h-screen bg-gray-50 text-gray-800">
    @include('investor.partials.sidebar')
    <main class="flex-1 p-6 overflow-y-auto">
      <h1 class="text-2xl font-semibold text-green-700 mb-4">Edit Investasi #{{ $investment->id }}</h1>

      <form action="{{ route('investor.investments.update', $investment) }}"
            method="POST" enctype="multipart/form-data"
            class="bg-white shadow rounded-lg p-6 space-y-4">
        @csrf @method('PUT')

        <div>
          <x-input-label for="qty" :value="__('Jumlah Qty')" />
          <x-text-input id="qty"
                        name="qty"
                        type="number"
                        min="1"
                        max="{{ $investment->project->quantity }}"
                        :value="old('qty', $investment->qty)"
                        class="block w-full"
                        required />
          <x-input-error :messages="$errors->get('qty')" class="mt-1" />
          <p class="text-xs text-gray-500 mt-1">
            Masukkan antara 1 dan {{ $investment->project->quantity }} pcs
          </p>
        </div>

        <div>
          <x-input-label for="message" :value="__('Pesan (opsional)')" />
          <textarea id="message" name="message"
                    class="block w-full border-gray-300 rounded"
                    rows="3">{{ old('message', $investment->message) }}</textarea>
          <x-input-error :messages="$errors->get('message')" class="mt-1" />
        </div>

        <div>
          <x-input-label for="receipt" :value="__('Upload Bukti (jpg/png/pdf)')" />
          <x-text-input id="receipt" name="receipt" type="file" class="block w-full" />
          <x-input-error :messages="$errors->get('receipt')" class="mt-1" />
          @if($investment->receipt)
            <p class="text-sm text-gray-500 mt-1">Bukti saat ini: {{ basename($investment->receipt) }}</p>
          @endif
        </div>

        <div class="flex justify-between">
          <a href="{{ route('investor.investments.index') }}"
             class="px-4 py-2 border rounded hover:bg-gray-100">Batal</a>
          <x-primary-button>Simpan Perubahan</x-primary-button>
        </div>
      </form>
    </main>
  </div>
</x-app-layout>
