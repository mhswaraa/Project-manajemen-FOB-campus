{{-- resources/views/investor/projects/invest.blade.php --}}
<x-app-layout>
  <div class="flex h-screen bg-gray-50 text-gray-800">
    {{-- Sidebar --}}
    @include('investor.partials.sidebar')

    {{-- Main Content --}}
    <main class="flex-1 p-6 overflow-y-auto">
      <div
        class="max-w-md mx-auto bg-white shadow rounded-lg p-6"
        x-data="{
          qty: 1,
          price: {{ $project->price_per_piece }},
          get total() { return this.qty * this.price },
          formattedTotal() { return new Intl.NumberFormat('id-ID').format(this.total) }
        }"
      >
        <h1 class="text-2xl font-semibold text-green-700 mb-4">
          Investasikan ke: {{ $project->name }}
        </h1>

        {{-- Ringkasan Proyek --}}
        <div class="grid grid-cols-2 gap-4 text-sm text-gray-600 mb-6">
          <div>
            <span class="font-medium">Harga per pcs:</span><br>
            Rp {{ number_format($project->price_per_piece, 0, ',', '.') }}
          </div>
          <div>
            <span class="font-medium">Total Qty tersedia:</span><br>
            {{ $project->quantity }} pcs
          </div>
        </div>

        <form
          action="{{ route('investor.projects.store', $project) }}"
          method="POST"
          enctype="multipart/form-data"
          class="space-y-6"
        >
          @csrf

          {{-- Input Jumlah Qty --}}
          <div>
            <x-input-label for="qty" :value="__('Jumlah Qty')" />
            <input
              id="qty"
              name="qty"
              type="number"
              x-model.number="qty"
              :max="{{ $project->quantity }}"
              min="1"
              class="block w-full border-gray-300 rounded shadow-sm"
              required
            />
            <x-input-error :messages="$errors->get('qty')" class="mt-1" />
            <p class="text-xs text-gray-500 mt-1">
              Masukkan antara 1 dan {{ $project->quantity }} pcs
            </p>
          </div>

          {{-- Total Investasi (otomatis) --}}
          <div>
            <x-input-label :value="__('Total Investasi (Rp)')" />
            <div class="block w-full border border-gray-300 rounded bg-gray-100 p-2">
              Rp <span x-text="formattedTotal()"></span>
            </div>
            {{-- hidden input yang benar-benar ter-bind --}}
            <input type="hidden" name="amount" x-bind:value="total" />
          </div>

          {{-- Pesan (opsional) --}}
          <div>
            <x-input-label for="message" :value="__('Pesan (opsional)')" />
            <textarea
              id="message"
              name="message"
              class="block w-full border-gray-300 rounded"
              rows="3"
            >{{ old('message') }}</textarea>
            <x-input-error :messages="$errors->get('message')" class="mt-1" />
          </div>

          {{-- Upload Bukti (opsional) --}}
          <div>
            <x-input-label
              for="receipt"
              :value="__('Upload Bukti (jpg/png/pdf)')"
            />
            <x-text-input
              id="receipt"
              name="receipt"
              type="file"
              class="block w-full"
            />
            <x-input-error
              :messages="$errors->get('receipt')"
              class="mt-1"
            />
          </div>

          {{-- Aksi --}}
          <div class="flex justify-between">
            <a
              href="{{ route('investor.projects.index') }}"
              class="px-4 py-2 border rounded hover:bg-gray-100"
            >
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
