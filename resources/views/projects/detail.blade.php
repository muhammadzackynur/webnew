@extends('layouts.app')

@section('title', 'Detail Data Proyek')

@section('content')

{{-- Header Halaman --}}
<div class="page-header">
    <div class="header-left">
        <span class="app-title">ProyekApp</span>
    </div>
    <div class="header-center">
        <span class="divider">|</span>
        <h2 class="page-title">Detail Proyek</h2>
    </div>
    <div class="header-right">
        <a href="{{ route('project.index') }}" class="back-button-header">
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
                        if (stripos($title, 'FOTO') !== false || stripos($title, 'File ID') !== false || trim($value) === '' || $value === '-') {
                            continue;
                        }
                    @endphp
                    <div class="definition-item">
                        <dt>{{ htmlspecialchars($title) }}</dt>
                        <dd>
                            @if (stripos($title, 'Link Google Maps') !== false && filter_var($value, FILTER_VALIDATE_URL))
                                <a href="{{ $value }}" target="_blank" rel="noopener noreferrer">{{ $value }}</a>
                            @else
                                {!! nl2br(htmlspecialchars($value)) !!}
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
            <h3 class="foto-proyek-title">📷 Foto Proyek</h3>

            @if (!empty($groupedGallery))
                @foreach ($groupedGallery as $groupName => $items)
                    @php
                        $groupId = 'gallery-group-' . Str::slug($groupName);
                        $initialLimit = 4; // Jumlah gambar yang tampil di awal
                        $loadIncrement = 4; // Jumlah gambar yang dimuat setiap kali tombol diklik
                        $totalItems = count($items);
                    @endphp
                    <div class="photo-group">
                        <h4 class="photo-group-title">{{ $groupName }}</h4>
                        <div class="photo-group-items" id="{{ $groupId }}">
                            @foreach ($items as $loopIndex => $item)
                                @if (!empty($item['path']))
                                    {{-- Tambahkan class 'hidden-initially' jika gambar melebihi batas awal --}}
                                    <div class="gallery-item {{ $loopIndex >= $initialLimit ? 'hidden-initially' : '' }}">
                                        <p class="gallery-caption">
                                            {{ htmlspecialchars($item['caption'] ?: 'Keterangan foto') }}
                                        </p>
                                        <a href="{{ asset('storage/' . $item['path']) }}" title="Klik untuk memperbesar">
                                            <img src="{{ asset('storage/' . $item['path']) }}" alt="{{ $groupName }} - {{ htmlspecialchars($item['caption']) }}">
                                        </a>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        
                        {{-- Tombol "Muat Lebih Banyak" akan muncul jika total gambar lebih dari batas awal --}}
                        @if ($totalItems > $initialLimit)
                            <div class="load-more-container">
                                <button class="load-more-button" data-target="#{{ $groupId }}" data-increment="{{ $loadIncrement }}">
                                    Muat Lebih Banyak
                                </button>
                            </div>
                        @endif
                    </div>
                @endforeach
            @else
                <p>Tidak ada foto tersedia.</p>
            @endif
        </div>
    </div>
</div>

{{-- ## LOGIKA JAVASCRIPT BARU UNTUK 'LOAD MORE' ## --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const loadMoreButtons = document.querySelectorAll('.load-more-button');

    loadMoreButtons.forEach(button => {
        button.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target');
            const increment = parseInt(this.getAttribute('data-increment'), 10);
            const galleryGroup = document.querySelector(targetId);

            if (galleryGroup) {
                // Temukan hanya item yang saat ini masih tersembunyi
                const hiddenItems = galleryGroup.querySelectorAll('.gallery-item.hidden-initially');
                
                // Tampilkan gambar sejumlah 'increment'
                for (let i = 0; i < increment && i < hiddenItems.length; i++) {
                    hiddenItems[i].classList.remove('hidden-initially');
                }

                // Cek lagi apakah masih ada gambar yang tersembunyi
                const remainingHidden = galleryGroup.querySelectorAll('.gallery-item.hidden-initially');
                if (remainingHidden.length === 0) {
                    // Jika sudah tidak ada, sembunyikan container tombol
                    if (this.parentElement) {
                        this.parentElement.style.display = 'none';
                    }
                }
            }
        });
    });
});
</script>
@endpush

@endsection

