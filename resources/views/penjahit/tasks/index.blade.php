<x-app-layout>
    <div class="flex h-screen bg-gray-100 text-gray-800">
        {{-- Sidebar --}}
        @include('penjahit.partials.sidebar')

        {{-- Main Content --}}
        <main class="flex-1 p-6 bg-gray-50">
            <h1 class="text-2xl font-semibold mb-4">Tugas Saya</h1>

            {{-- Flash Error --}}
            @if(session('error'))
                <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
                    {{ session('error') }}
                </div>
            @endif

            @if($assignments->isEmpty())
                <p class="text-gray-600">Belum ada tugas.</p>
            @else
                <ul class="space-y-4">
                    @foreach($assignments as $task)
                        @php
                            $done = $task->progress->sum('quantity_done');
                            $pct  = $task->assigned_qty
                                   ? round( ($done / $task->assigned_qty) * 100 )
                                   : 0;
                        @endphp
                        <li class="bg-white p-4 rounded shadow flex justify-between items-center">
                            <div>
                                <h2 class="font-medium text-lg">{{ $task->project->name }}</h2>
                                <p class="text-sm text-gray-600">
                                    {{ $done }}/{{ $task->assigned_qty }} pcs • {{ $pct }}%
                                </p>
                                <div class="w-full bg-gray-200 h-2 rounded mt-1">
                                    <div class="bg-orange-500 h-2 rounded" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                            <a href="{{ route('penjahit.tasks.show', $task) }}"
                               class="text-orange-600 hover:underline">
                                Detail
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </main>
    </div>
</x-app-layout>
