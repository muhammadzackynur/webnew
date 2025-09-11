@extends('layouts.app')

@section('title', $title)

@section('page-styles')
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
@endsection

@section('content')
<div class="page-header">
    <div class="header-left">
        <span class="app-title">ProyekApp</span>
    </div>
    <div class="header-center">
        <h2 class="page-title">{{ $title }}</h2>
    </div>
    <div class="header-right">
        <a href="{{ route('project.show', ['rowIndex' => $rowIndex]) }}" class="back-button">
            &larr; Kembali ke Detail
        </a>
    </div>
</div>

<div class="detail-container">
    <div class="media-column" style="flex: 1; min-width: 100%;">
        <div class="detail-card">
            <div class="photo-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                @forelse ($allPhotos as $item)
                    <div class="gallery-item" style="max-width: none;">
                        <a href="{{ asset('storage/' . $item['path']) }}" target="_blank" title="Klik untuk memperbesar">
                            <img src="{{ asset('storage/' . $item['path']) }}" alt="{{ htmlspecialchars($item['caption']) }}">
                        </a>
                        <p class="gallery-caption">
                            {{ htmlspecialchars($item['caption'] ?: 'Keterangan foto') }}
                        </p>
                    </div>
                @empty
                    <p>Tidak ada foto untuk ditampilkan.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection