{{-- 
  File Partial untuk Form Edit Progres
  Variabel yang dibutuhkan: $progress
--}}
<div class="bg-white p-8 rounded-xl shadow-md">
    <form method="POST" action="{{ route('penjahit.tasks.progress.update', $progress) }}">
        @csrf
        @method('PUT')

        {{-- Jumlah Selesai --}}
        <div class="mb-4">
            <x-input-label for="quantity_done" :value="__('Jumlah Selesai (pcs)')" />
            <x-text-input id="quantity_done" name="quantity_done" type="number" class="mt-1 block w-full" :value="old('quantity_done', $progress->quantity_done)" required />
            <x-input-error :messages="$errors->get('quantity_done')" class="mt-2" />
        </div>

        {{-- Catatan --}}
        <div class="mb-6">
            <x-input-label for="notes" :value="__('Catatan (Opsional)')" />
            <textarea id="notes" name="notes" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes', $progress->notes) }}</textarea>
            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
        </div>

        {{-- Tombol Aksi --}}
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('penjahit.tasks.show', $progress->assignment_id) }}" class="text-sm text-gray-600 hover:underline">Batal</a>
            <x-primary-button>
                {{ __('Simpan Perubahan') }}
            </x-primary-button>
        </div>
    </form>
</div>
