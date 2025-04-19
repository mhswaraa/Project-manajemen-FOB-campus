@props([
    // terima data badge jika mau
    'projectCount' => 0,
    'taskCount'    => 0,
])

@php
    $role = Auth::user()->role;
    $menus = [
        'admin' => [
            ['name'=>'Dashboard',     'route'=>'dashboard',          'icon'=>'home'],
            ['name'=>'Manajemen User','route'=>'admin.users.index', 'icon'=>'users'],
            ['name'=>'Proyek',        'route'=>'projects.index',    'icon'=>'folder', 'badge'=>$projectCount],
            ['name'=>'Laporan',       'route'=>'reports.index',     'icon'=>'chart-bar'],
        ],
        'ceo' => [
            ['name'=>'Dashboard',        'route'=>'dashboard','icon'=>'home'],
            ['name'=>'Progress Proyek',  'route'=>'projects.index','icon'=>'clipboard-list','badge'=>$projectCount],
            ['name'=>'Laporan Keuangan', 'route'=>'reports.finance.index','icon'=>'currency-dollar'],
        ],
        'investor' => [
            ['name'=>'Dashboard',      'route'=>'dashboard','icon'=>'home'],
            ['name'=>'Investasi Saya', 'route'=>'investments.index','icon'=>'cash','badge'=>$projectCount],
        ],
        'penjahit' => [
            ['name'=>'Dashboard', 'route'=>'dashboard','icon'=>'home'],
            ['name'=>'Tugas Saya','route'=>'tasks.index',   'icon'=>'pencil-alt','badge'=>$taskCount],
        ],
    ];
@endphp

<aside class="flex-shrink-0 w-64 bg-white border-r border-gray-200 flex flex-col">
    {{-- Profile --}}
    <div class="flex items-center p-4 border-b border-gray-100">
        <img src="{{ Auth::user()->profile_photo_url ?? asset('images/avatar-placeholder.png') }}"
             alt="Avatar" class="h-10 w-10 rounded-full ring-2 ring-indigo-500">
        <div class="ml-3">
            <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
            <p class="text-xs text-gray-500">{{ ucfirst($role) }}</p>
        </div>
    </div>

    {{-- Menu --}}
    <nav class="flex-1 p-4 overflow-y-auto">
        <ul class="space-y-1">
            @foreach($menus[$role] as $item)
                @php
                    $isActive = request()->routeIs($item['route'].'*');
                @endphp
                <li>
                    <a href="{{ route($item['route']) }}"
                       class="group flex items-center p-2 rounded-md
                              {{ $isActive
                                  ? 'bg-indigo-50 text-indigo-700'
                                  : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        {{-- Icon --}}
                        <x-heroicon-o-{{ $item['icon'] }}
                            class="h-5 w-5 flex-shrink-0
                                   {{ $isActive ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-500' }}" />
                        {{-- Label --}}
                        <span class="ml-3 flex-1 text-sm font-medium">{{ $item['name'] }}</span>
                        {{-- Badge --}}
                        @if(!empty($item['badge']))
                            <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-medium
                                         {{ $isActive 
                                            ? 'bg-indigo-100 text-indigo-800'
                                            : 'bg-gray-200 text-gray-700' }}
                                         rounded-full">
                                {{ $item['badge'] }}
                            </span>
                        @endif
                    </a>
                </li>
            @endforeach

            {{-- Logout --}}
            <li class="mt-4 pt-4 border-t border-gray-100">
                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   class="flex items-center p-2 text-sm text-red-600 hover:bg-red-50 rounded-md">
                    <x-heroicon-o-logout class="h-5 w-5 text-red-600" />
                    <span class="ml-3">Logout</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
            </li>
        </ul>
    </nav>
</aside>
