<x-app-layout>
  <div class="flex h-screen bg-gray-50">
    @include('admin.partials.sidebar')

    <main class="flex-1 overflow-y-auto p-6 lg:p-8">
      
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Kelola Spesialisasi Penjahit</h1>
        <p class="text-gray-500 mt-1">Tambah, ubah, atau hapus daftar keahlian yang bisa dipilih oleh penjahit.</p>
      </div>

      {{-- Pesan Sukses atau Error --}}
      @if(session('success'))
        <div role="alert" class="mb-6 rounded-xl border border-gray-100 bg-white p-4 shadow-sm"><div class="flex items-start gap-4"><span class="text-green-600"><x-heroicon-s-check-circle class="h-6 w-6"/></span><div class="flex-1"><strong class="block font-medium text-gray-900">Sukses!</strong><p class="mt-1 text-sm text-gray-700">{{ session('success') }}</p></div></div></div>
      @endif
      @if(session('error'))
        <div role="alert" class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 shadow-sm"><div class="flex items-start gap-4"><span class="text-red-600"><x-heroicon-s-x-circle class="h-6 w-6"/></span><div class="flex-1"><strong class="block font-medium text-red-900">Gagal!</strong><p class="mt-1 text-sm text-red-700">{{ session('error') }}</p></div></div></div>
      @endif

      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        {{-- Kolom Kiri: Form --}}
        <div class="md:col-span-1" x-data="{ isEditing: false, formAction: '{{ route('admin.specializations.store') }}', formMethod: 'POST', specializationName: '', title: 'Tambah Spesialisasi Baru' }">
          <div class="bg-white p-6 rounded-lg shadow-md sticky top-8">
            <h3 class="text-lg font-medium text-gray-900" x-text="title"></h3>
            <form :action="formAction" method="POST" class="mt-4 space-y-4">
              @csrf
              <input type="hidden" name="_method" :value="formMethod">
              
              <div>
                <x-input-label for="name" value="Nama Spesialisasi" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" x-model="specializationName" required />
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
              </div>
              
              <div class="flex items-center gap-2">
                <x-primary-button type="submit" x-text="isEditing ? 'Simpan Perubahan' : 'Tambah'"></x-primary-button>
                <x-secondary-button type="button" x-show="isEditing" @click="isEditing = false; formAction = '{{ route('admin.specializations.store') }}'; formMethod = 'POST'; specializationName = ''; title = 'Tambah Spesialisasi Baru';">Batal</x-secondary-button>
              </div>
            </form>
          </div>
        </div>

        {{-- Kolom Kanan: Tabel --}}
        <div class="md:col-span-2 bg-white shadow-md rounded-lg overflow-hidden">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Keahlian</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Digunakan Oleh</th>
                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              @forelse ($specializations as $spec)
              <tr x-data="{ id: {{ $spec->id }}, name: '{{ $spec->name }}' }">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $spec->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $spec->tailors()->count() }} Penjahit</td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <button @click="isEditing = true; specializationName = name; formAction = '{{ route('admin.specializations.update', $spec) }}'; formMethod = 'PUT'; title = 'Edit Spesialisasi';" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                  <form action="{{ route('admin.specializations.destroy', $spec) }}" method="POST" class="inline-block ml-4" onsubmit="return confirm('Yakin ingin menghapus keahlian ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                  </form>
                </td>
              </tr>
              @empty
              <tr><td colspan="3" class="px-6 py-12 text-center text-gray-500">Belum ada spesialisasi yang ditambahkan.</td></tr>
              @endforelse
            </tbody>
          </table>
          <div class="p-4 border-t">{{ $specializations->links() }}</div>
        </div>
      </div>
    </main>
  </div>
</x-app-layout>
