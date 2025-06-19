{{-- resources/views/layouts/penjahit.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ config('app.name') }} – Penjahit</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="font-sans antialiased">
  <div id="app" class="min-h-screen flex">
    {{-- Sisipkan sidebar penjahit --}}
    @include('penjahit.partials.sidebar')
    <div class="flex-1 bg-gray-50">
      {{-- Konten utama --}}
      <main class="p-6">
        @yield('content')
      </main>
    </div>
  </div>
</body>
</html>
