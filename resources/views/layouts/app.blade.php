<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Website Tampilan Data')</title>
    
    <!-- Memanggil file CSS dari folder public -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div class="container">
        
        {{-- Placeholder ini hanya akan diisi oleh halaman yang mendefinisikannya (seperti index.blade.php) --}}
        @yield('page_title')
        
        {{-- Konten dari halaman lain (index atau detail) akan dimuat di sini --}}
        @yield('content')
        
        <p class="footer-text">
            &copy; {{ date("Y") }} Proyek Website Saya.
        </p>
    </div>

    {{-- Placeholder untuk script tambahan jika diperlukan --}}
    @yield('scripts') 
</body>
</html>

