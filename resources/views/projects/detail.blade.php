@extends('layouts.app')

@section('title', 'Detail Data Proyek')

@section('page-styles')
    <link rel="stylesheet" href="{{ asset('style.css') }}">
@endsection

@section('content')
{{-- Header Halaman Khusus Detail --}}
<div class="page-header">
    <div class="header-left">
        {{-- PERUBAHAN DI SINI: Mengganti teks dengan gambar logo --}}
        <img src="{{ asset('images/PT.PNG') }}" alt="Logo Telkom Akses" class="app-logo">
    </div>
    <div class="header-center">
        <h2 class="page-title">Detail Proyek</h2>
    </div>
    <div class="header-right">
        <a href="{{ route('project.index') }}" class="back-button">
            &larr; Kembali ke Daftar
        </a>
    </div>
</div>

{{-- Container Utama --}}
<div class="detail-container">
    {{-- Kolom Informasi Teks --}}
    <div class="info-column">
        <div class="detail-card">
            <div class="definition-list">
                @foreach ($header as $index => $title)
                    @php
                        $value = $selectedRow[$index] ?? '';
                        // Sembunyikan field yang tidak ingin ditampilkan
                        if (stripos($title, 'FOTO') !== false || stripos($title, 'File ID') !== false || trim($value) === '' || $value === '-') {
                            continue;
                        }
                    @endphp
                    <div class="definition-item">
                        <dt>{{ htmlspecialchars($title) }}</dt>
                        <dd>
                            @if (filter_var($value, FILTER_VALIDATE_URL))
                                <a href="{{ $value }}" target="_blank" rel="noopener noreferrer">{{ $value }}</a>
                            @else
                                {!! nl2br(e($value)) !!}
                            @endif
                        </dd>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Kolom Galeri Foto --}}
    <div class="media-column">
        <div class="detail-card">
            
            <div class="media-header">
                <h3 class="foto-proyek-title">
                    <i data-feather="image" style="width:20px; height:20px; margin-right: 8px;"></i>
                    Foto Proyek
                </h3>
                {{-- Tombol "Lihat Semua" dipindahkan ke sini --}}
                @if (count($allPhotos) > 3)
                    <a href="{{ route('project.gallery', ['rowIndex' => $rowIndex]) }}" class="view-all-link">
                        Lihat Semua ({{ count($allPhotos) }})
                        <i data-feather="arrow-right" class="icon-sm"></i>
                    </a>
                @endif
            </div>

            @if (!empty($allPhotos))
                @foreach ($groupedGallery as $groupName => $items)
                    <div class="photo-group">
                        @php
                            $titleClass = '';
                            if (stripos($groupName, 'Before') !== false) $titleClass = 'title-before';
                            elseif (stripos($groupName, 'Progress') !== false) $titleClass = 'title-progress';
                            elseif (stripos($groupName, 'After') !== false) $titleClass = 'title-after';
                        @endphp
                        <h4 class="photo-group-title {{ $titleClass }}">{{ $groupName }}</h4>
                        <div class="photo-grid">
                            @foreach (array_slice($items, 0, 3) as $item)
                                <div class="gallery-item">
                                    <a href="{{ asset('storage/' . $item['path']) }}" target="_blank" title="Klik untuk memperbesar">
                                        <img src="{{ asset('storage/' . $item['path']) }}" alt="{{ htmlspecialchars($item['caption']) }}">
                                    </a>
                                    <p class="gallery-caption">
                                        {{ htmlspecialchars($item['caption'] ?: 'Keterangan foto') }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @else
                <p>Tidak ada foto tersedia untuk proyek ini.</p>
            @endif
        </div>
    </div>
</div>
@endsection