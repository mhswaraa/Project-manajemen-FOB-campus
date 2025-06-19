{{-- resources/views/penjahit/dashboard.blade.php --}}
{{-- @extends('layouts.app') --}}
<x-app-layout>
  {{-- 1) Slot HEADER --}}
  <x-slot name="header">
    <div class="flex items-center justify-between">
      {{-- Logo + Judul --}}
      <div class="flex items-center gap-2">
        <x-application-mark class="block h-8 w-auto text-teal-600" />
        <h2 class="font-semibold text-xl text-gray-800">
          Dashboard
        </h2>
      </div>
      {{-- User Dropdown --}}
      <x-dropdown align="right" width="48">
        <x-slot name="trigger">
          <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700">
            <div>{{ Auth::user()->name }}</div>
            <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M19 9l-7 7-7-7" />
            </svg>
          </button>
        </x-slot>
        <x-slot name="content">
          {{-- Logout --}}
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-dropdown-link href="{{ route('logout') }}"
              onclick="event.preventDefault(); this.closest('form').submit();">
              {{ __('Logout') }}
            </x-dropdown-link>
          </form>
        </x-slot>
      </x-dropdown>
    </div>
  </x-slot>

  {{-- 2) Struktur utama: sidebar + konten --}}
  <div class="flex h-screen bg-gray-100 text-gray-800">
    {{-- Sidebar --}}
    @include('penjahit.partials.sidebar')

    {{-- Main content --}}
    <main class="flex-1 overflow-y-auto p-6">
      {{-- ini isi lama @section('content') Anda --}}
      @foreach($assignments as $task)
        <div class="bg-white rounded-lg shadow p-4 mb-4">
          <h2 class="text-lg font-semibold">{{ $task->project->name }}</h2>
          <p>Status: {{ ucfirst($task->status) }}</p>
          <p>Qty ditugaskan: {{ $task->assigned_qty }}</p>
          <p>Total progress: {{ $task->progress->sum('quantity_done') }} pcs</p>
        </div>
      @endforeach
    </main>
  </div>
</x-app-layout>
