@extends('layouts.app')

@section('title', $title)

@section('content')
<style>
    /* Style khusus untuk halaman galeri semua foto */
    .gallery-container {
        padding: 0 2rem 2rem 2rem;
    }
    .photo-grid-all {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 1rem;
    }
    .gallery-item {
        flex: 1 1 auto;
        max-width: none;
        border-radius: 8px;
    }
    .gallery-item img {
        height: 180px;
        object-fit: cover;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
    }
    .photo-group {
        margin-bottom: 2.5rem;
    }
    .photo-group:last-child {
        margin-bottom: 0;
    }
    .photo-group-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
        padding-left: 0.75rem;
        border-left: 3px solid var(--accent-blue);
        color: var(--text-primary);
    }
    .gallery-caption {
        padding: 0.75rem 0.5rem;
        font-size: 0.8rem;
    }
</style>

<div class="page-header">
    <img src="{{ asset('images/PT.PNG') }}" alt="Logo Telkom Akses" class="app-logo" style="height: 50px;">
    
    {{-- PERUBAHAN DI SINI: Baris kode h2 yang menampilkan judul dihapus --}}
    
    <a href="{{ route('project.show', ['rowIndex' => $rowIndex]) }}" class="back-button">
        &larr; Kembali ke Detail
    </a>
</div>

<div class="gallery-container">
    @forelse ($groupedGallery as $groupName => $photos)
        <div class="photo-group">
            @php
                $titleClass = '';
                if (stripos($groupName, 'Before') !== false) $titleClass = 'title-before';
                elseif (stripos($groupName, 'Progress') !== false) $titleClass = 'title-progress';
                elseif (stripos($groupName, 'After') !== false) $titleClass = 'title-after';
            @endphp
            <h3 class="photo-group-title {{ $titleClass }}">{{ htmlspecialchars($groupName) }}</h3>
            <div class="photo-grid-all">
                @foreach ($photos as $photo)
                    <div class="gallery-item">
                        <a href="{{ asset('storage/' . $photo['path']) }}" target="_blank" title="Klik untuk memperbesar">
                            <img src="{{ asset('storage/' . $photo['path']) }}" alt="{{ htmlspecialchars($photo['caption']) }}">
                        </a>
                        <p class="gallery-caption">
                            {{ htmlspecialchars($photo['caption'] ?: 'Keterangan foto') }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="detail-card">
            <p>Tidak ada foto untuk ditampilkan.</p>
        </div>
    @endforelse
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
});
</script>
@endpush