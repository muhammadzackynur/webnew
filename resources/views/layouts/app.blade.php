<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Proyek Dashboard')</title>
    
    {{-- Font & Icons --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>

    {{-- Logika untuk memuat CSS yang berbeda --}}
    @hasSection('page-styles')
        {{-- Jika view punya section 'page-styles', muat itu --}}
        @yield('page-styles')
    @else
        {{-- Jika tidak, muat CSS default dari Vite --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body>
    {{-- Hapus class dark-theme dari body agar tidak menjadi default --}}
    <div class="main-container">
        <main>
            @yield('content')
        </main>
    </div>

    {{-- Script untuk render icons dan script tambahan --}}
    <script>
      feather.replace();
    </script>
    @stack('scripts')
    @yield('scripts') 
</body>
</html>