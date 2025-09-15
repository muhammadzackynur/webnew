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

    {{-- CSS Digabungkan Langsung ke dalam HTML --}}
    <style>
    /* === DARI APP.CSS (Dengan penyesuaian ukuran card) === */
    :root {
        --bg-primary: #F3F4F6;
        --bg-secondary: #FFFFFF;
        --border-color: #E5E7EB;
        --text-primary: #111827;
        --text-secondary: #6B7280;
        --accent-blue: #3B82F6;
        --accent-blue-dark: #2563EB;
    }

    body {
        background-color: var(--bg-primary);
        color: var(--text-primary);
        font-family: 'Inter', sans-serif;
        margin: 0;
    }

    .main-container {
        padding: 2rem;
    }

    .main-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .header-left h1 {
        margin: 0 0 0.5rem 0;
        font-size: 1.875rem;
        color: var(--text-primary);
    }

    .header-left p {
        margin: 0;
        font-size: 1rem;
        color: var(--text-secondary);
    }

    .header-right {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .search-bar {
        position: relative;
    }

    .search-bar .icon {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-secondary);
    }

    .search-bar input, .filter-dropdown {
        background-color: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 0.75rem;
        color: var(--text-primary);
        font-size: 0.875rem;
        font-family: 'Inter', sans-serif;
    }

    .search-bar input {
        padding-left: 2.5rem;
    }

    /* === SUMMARY CARDS (PERUBAHAN DI SINI) === */
    .summary-cards {
        display: grid;
        /* Mengubah minmax agar card bisa lebih kecil */
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
        gap: 1rem; /* Mengurangi jarak antar card */
        margin-bottom: 2rem;
    }

    .card {
        background-color: var(--bg-secondary);
        padding: 1rem; /* Mengurangi padding di dalam card */
        border-radius: 12px;
        border: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .card .icon-container {
        padding: 0.6rem; /* Mengecilkan padding ikon */
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .card .icon-container.blue { background-color: rgba(59, 130, 246, 0.1); color: #3B82F6; }
    .card .icon-container.green { background-color: rgba(16, 185, 129, 0.1); color: #10B981; }
    .card .icon-container.purple { background-color: rgba(139, 92, 246, 0.1); color: #8B5CF6; }

    .card-content .value {
        font-size: 1.5rem; /* Mengecilkan ukuran angka */
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }

    .card-content .label {
        font-size: 0.8rem; /* Mengecilkan ukuran label */
        color: var(--text-secondary);
        margin: 0;
    }
    /* === AKHIR PERUBAHAN SUMMARY CARDS === */

    .table-wrapper {
        background-color: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 1.5rem;
        overflow-x: auto;
    }

    .table-header {
        margin-bottom: 1rem;
    }

    .table-header h2 {
        font-size: 1.25rem;
        margin: 0;
        color: var(--text-primary);
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table thead th {
        text-align: left;
        padding: 0.75rem 1rem;
        color: var(--text-secondary);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid var(--border-color);
        white-space: nowrap;
    }

    .data-table tbody tr {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .data-table tbody tr:hover {
        background-color: #F9FAFB;
    }

    .data-table tbody td {
        padding: 1rem;
        border-bottom: 1px solid var(--border-color);
        font-size: 0.875rem;
        color: var(--text-primary);
        white-space: nowrap;
    }

    .data-table tbody tr:last-child td {
        border-bottom: none;
    }

    .google-maps-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--accent-blue);
        text-decoration: none;
        font-weight: 500;
    }

    .google-maps-link:hover {
        text-decoration: underline;
    }

    /* === DARI STYLE.CSS === */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 2rem;
        background-color: var(--bg-secondary);
        border-bottom: 1px solid var(--border-color);
        margin-bottom: 2rem;
    }

    .page-header .app-title {
        font-size: 1.25rem;
        font-weight: 700;
    }

    .page-header .page-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .page-header .back-button {
        display: inline-block;
        padding: 0.5rem 1rem;
        background-color: var(--accent-blue);
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 500;
        transition: background-color 0.2s ease;
    }

    .page-header .back-button:hover {
        background-color: var(--accent-blue-dark);
    }

    .detail-container {
        display: flex;
        gap: 2rem;
        padding: 0 2rem;
        align-items: flex-start;
        flex-wrap: wrap;
    }

    .info-column {
        flex: 3;
        min-width: 400px;
        background-color: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    }

    .media-column {
        flex: 2;
        min-width: 350px;
    }

    .detail-card {
        padding: 2rem;
    }

    .definition-list .definition-item {
        display: flex;
        padding: 1rem 0;
        border-bottom: 1px solid var(--border-color);
        font-size: 0.9rem;
    }

    .definition-list .definition-item:last-child {
        border-bottom: none;
    }

    .definition-item dt {
        width: 200px;
        flex-shrink: 0;
        color: var(--text-primary);
        font-weight: 500;
    }

    .definition-item dd {
        margin: 0;
        font-weight: 600;
    }

    .definition-item dd a {
        color: var(--accent-blue);
        text-decoration: none;
        font-weight: 600;
    }

    .definition-item dd a:hover {
        text-decoration: underline;
    }

    .media-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid var(--accent-blue);
        padding-bottom: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .media-column .foto-proyek-title {
        display: flex;
        align-items: center;
        font-size: 1.25rem;
        margin: 0;
        padding: 0;
        border: none;
    }

    /* === PERUBAHAN STYLE BUTTON "LIHAT SEMUA" === */
    .view-all-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem; /* Sedikit menambah jarak antara teks dan ikon */
        text-decoration: none;
        background-color: var(--accent-blue); /* Memberi warna latar */
        color: white; /* Memberi warna teks */
        font-weight: 500;
        font-size: 0.875rem;
        padding: 0.5rem 1rem; /* Menambah padding agar terlihat seperti tombol */
        border-radius: 8px; /* Membuat sudut tombol melengkung */
        transition: background-color 0.2s ease, transform 0.2s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); /* Menambah bayangan halus */
    }

    .view-all-link:hover {
        background-color: var(--accent-blue-dark); /* Menggelapkan warna saat disentuh mouse */
        color: white;
        transform: translateY(-1px); /* Efek tombol sedikit terangkat */
    }

    .view-all-link .icon-sm {
        width: 16px;
        height: 16px;
    }
    
    .photo-group {
        margin-bottom: 1.25rem;
    }

    .photo-group-title {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.75rem;
        padding-left: 0.75rem;
        border-left: 3px solid var(--accent-blue);
    }

    .photo-grid {
        display: flex;
        overflow-x: auto;
        gap: 0.75rem;
        padding-bottom: 1rem;
    }

    .gallery-item {
        flex: 0 0 150px;
        display: flex;
        flex-direction: column;
        background-color: var(--bg-secondary);
        border-radius: 12px;
        border: 1px solid var(--border-color);
        box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .gallery-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    }

    .gallery-item img {
        width: 100%;
        aspect-ratio: 1 / 1;
        object-fit: cover;
        display: block;
    }

    .gallery-caption {
        margin: 0;
        padding: 0.5rem;
        font-size: 0.75rem;
        font-weight: 500;
        text-align: center;
        color: var(--text-primary);
        margin-top: auto;
    }

    /* === WARNA JUDUL GRUP GALERI === */
    .photo-group-title.title-before {
        border-left-color: #EF4444 !important;
    }
    .photo-group-title.title-progress {
        border-left-color: #F59E0B !important; 
    }
    .photo-group-title.title-after {
        border-left-color: #10B981 !important; 
    }
</style>

</head>
<body>
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