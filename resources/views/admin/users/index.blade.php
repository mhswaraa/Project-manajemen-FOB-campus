<x-app-layout>
  <div class="flex h-screen bg-gray-50">
    @include('admin.partials.sidebar')

    <main class="flex-1 overflow-y-auto p-6 lg:p-8">
      
      <div class="flex flex-col sm:flex-row justify-between items-start mb-8">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">Manajemen Pengguna</h1>
          <p class="text-gray-500 mt-1">Kelola semua akun pengguna dalam sistem.</p>
        </div>
        <a href="{{ route('admin.users.create') }}"
           class="mt-4 sm:mt-0 flex-shrink-0 inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 shadow-sm">
          <x-heroicon-s-plus class="h-5 w-5"/>
          Tambah Pengguna Baru
        </a>
      </div>

      <!-- Navigasi Tab untuk filter Role -->
      <div class="mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-6 overflow-x-auto" aria-label="Tabs">
                {{-- Tab "Semua" --}}
                <a href="{{ route('admin.users.index') }}" @class([
                    'shrink-0 border-b-2 px-1 pb-4 text-sm font-medium whitespace-nowrap',
                    'border-indigo-500 text-indigo-600' => !$currentRole,
                    'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' => $currentRole,
                ])>
                    Semua <span class="ml-1 rounded-full bg-gray-200 px-2 py-0.5 text-xs font-medium text-gray-600">{{ $counts['all'] }}</span>
                </a>
                
                {{-- Loop untuk setiap role --}}
                @foreach (['admin', 'ceo', 'investor', 'penjahit'] as $role)
                    <a href="{{ route('admin.users.index', ['role' => $role]) }}" @class([
                        'shrink-0 border-b-2 px-1 pb-4 text-sm font-medium whitespace-nowrap',
                        'border-indigo-500 text-indigo-600' => $currentRole == $role,
                        'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' => $currentRole != $role,
                    ])>
                        {{ ucfirst($role) }} <span class="ml-1 rounded-full px-2 py-0.5 text-xs font-medium 
                            @switch($role)
                                @case('admin') bg-red-100 text-red-700 @break
                                @case('ceo') bg-purple-100 text-purple-700 @break
                                @case('investor') bg-green-100 text-green-700 @break
                                @case('penjahit') bg-blue-100 text-blue-700 @break
                            @endswitch
                        ">{{ $counts[$role] }}</span>
                    </a>
                @endforeach
            </nav>
        </div>
      </div>

      @if(session('success'))
        <div role="alert" class="mb-6 rounded-xl border border-gray-100 bg-white p-4 shadow-sm"><div class="flex items-start gap-4"><span class="text-green-600"><x-heroicon-s-check-circle class="h-6 w-6"/></span><div class="flex-1"><strong class="block font-medium text-gray-900">Sukses!</strong><p class="mt-1 text-sm text-gray-700">{{ session('success') }}</p></div></div></div>
      @endif
      @if(session('error'))
        <div role="alert" class="mb-6 rounded-xl border border-gray-100 bg-red-50 p-4 shadow-sm"><div class="flex items-start gap-4"><span class="text-red-600"><x-heroicon-s-x-circle class="h-6 w-6"/></span><div class="flex-1"><strong class="block font-medium text-gray-900">Gagal!</strong><p class="mt-1 text-sm text-gray-700">{{ session('error') }}</p></div></div></div>
      @endif

      <div class="bg-white shadow-md rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bergabung</th>
              <th class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($users as $user)
            <tr>
              <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                          <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=EBF4FF&color=4299E1" alt="Avatar">
                      </div>
                      <div class="ml-4">
                          <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                          <div class="text-sm text-gray-500">{{ $user->email }}</div>
                      </div>
                  </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                      @switch($user->role)
                          @case('admin') bg-red-100 text-red-800 @break
                          @case('ceo') bg-purple-100 text-purple-800 @break
                          @case('investor') bg-green-100 text-green-800 @break
                          @case('penjahit') bg-blue-100 text-blue-800 @break
                      @endswitch
                  ">
                      {{ ucfirst($user->role) }}
                  </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->created_at->isoFormat('D MMMM YYYY') }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <a href="{{ route('admin.users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                @if(auth()->id() !== $user->id) {{-- Mencegah user menghapus diri sendiri --}}
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-block ml-2" onsubmit="return confirm('Anda yakin ingin menghapus user ini? Aksi ini tidak dapat dibatalkan.');">
                  @csrf @method('DELETE')
                  <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                </form>
                @endif
              </td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-6 py-12 text-center text-gray-500">Tidak ada pengguna untuk role ini.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
       <div class="mt-6">
            {{ $users->links() }}
        </div>
    </main>
  </div>
</x-app-layout>
