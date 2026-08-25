<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'WargaDigi 21 — Digitalisasi Gotong Royong RW 21 Tanimulya. Platform digital untuk membangun lingkungan yang lebih baik melalui teknologi.')">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'WargaDigi 21') — Digital Gotong Royong</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    {{-- Navbar --}}
    @include('components.navbar')

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('components.footer')

    {{-- Page Scripts --}}
    @stack('scripts')
</body>
</html>
