<x-app-layout>
    <div class="flex h-screen bg-gray-100 text-gray-800">
        {{-- Sidebar --}}
        @include('penjahit.partials.sidebar')

        {{-- Main Content --}}
        <main class="flex-1 p-6 bg-gray-50">
            <h1 class="text-2xl font-semibold mb-4">Detail Tugas</h1>

            {{-- Proyek Info --}}
            <div class="bg-white p-6 rounded shadow mb-6">
                <h2 class="text-xl font-bold">{{ $assignment->project->name }}</h2>
                <p class="text-gray-600">
                    Qty ditugaskan: {{ $assignment->assigned_qty }} pcs
                </p>
            </div>

            {{-- Form Update Progress --}}
            <div class="bg-white p-6 rounded shadow mb-6">
                <h3 class="font-semibold mb-3">Update Progress Hari Ini</h3>

                @if(session('success'))
                    <div class="mb-3 p-3 bg-green-100 text-green-800 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('penjahit.tasks.progress.store', $assignment) }}"
                      method="POST"
                      class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="quantity_done" :value="__('Output (pcs)')" />
                        <x-text-input id="quantity_done"
                                      name="quantity_done"
                                      type="number"
                                      min="1"
                                      class="mt-1 block w-full"
                                      required />
                        <x-input-error :messages="$errors->get('quantity_done')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="notes" :value="__('Catatan (opsional)')" />
                        <textarea id="notes"
                                  name="notes"
                                  rows="3"
                                  class="mt-1 block w-full border-gray-300 rounded"></textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-1" />
                    </div>
                    <div class="flex justify-end">
                        <x-primary-button>{{ __('Simpan Progress') }}</x-primary-button>
                    </div>
                </form>
            </div>

            {{-- Riwayat Progress --}}
            <div class="bg-white p-6 rounded shadow">
                <h3 class="font-semibold mb-3">Riwayat Progress</h3>

                @if($assignment->progress->isEmpty())
                    <p class="text-gray-600">Belum ada record progress.</p>
                @else
                    <ul class="divide-y divide-gray-200">
                        @foreach($assignment->progress as $prog)
                            <li class="py-2 flex justify-between">
                                <div>
                                    <p class="font-medium">{{ $prog->date }}</p>
                                    <p class="text-gray-600">{{ $prog->quantity_done }} pcs</p>
                                </div>
                                @if($prog->notes)
                                    <p class="text-sm text-gray-500">{{ $prog->notes }}</p>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </main>
    </div>
</x-app-layout>
