@extends('layouts.app')

@section('title', 'Detail Data Proyek')

@section('content')
{{-- Header Halaman Khusus Detail --}}
<div class="page-header">
    <div class="header-left">
        <img src="{{ asset('images/PT.PNG') }}" alt="Logo Telkom Akses" class="app-logo" style="height: 50px;">
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
        {{-- Menampilkan pesan sukses atau error setelah tambah material --}}
        @if (session('success'))
            <div class="alert alert-success" style="background-color: #D1FAE5; color: #065F46; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
             <div class="alert alert-danger" style="background-color: #FEE2E2; color: #991B1B; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                {{ session('error') }}
            </div>
        @endif

        {{-- Card Informasi Utama Proyek --}}
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

        {{-- Card Untuk Menampilkan dan Menambah Material --}}
        <div class="detail-card" style="margin-top: 2rem;">
            <div class="media-header">
                <h3 class="foto-proyek-title">
                    <i data-feather="tool" style="width:20px; height:20px; margin-right: 8px;"></i>
                    Penggunaan Material
                </h3>
                {{-- Tombol untuk Membuka Form Tambah Material --}}
                <button type="button" class="view-all-link" data-bs-toggle="modal" data-bs-target="#addMaterialModal">
                    + Tambah Material
                </button>
            </div>
            
            @if (!empty($projectMaterials))
                <table class="data-table" style="margin-top: 1rem;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Jenis Material</th>
                            <th>Uraian Pekerjaan</th>
                            <th>Satuan</th> 
                            <th>Volume</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($projectMaterials as $material)
                            <tr>
                                <td>{{ htmlspecialchars($material['No']) }}</td>
                                <td>{{ htmlspecialchars($material['Jenis Material']) }}</td>
                                <td>{{ htmlspecialchars($material['Uraian Pekerjaan']) }}</td>
                                <td>{{ htmlspecialchars($material['Satuan']) }}</td> 
                                <td>{{ htmlspecialchars($material['Volume']) }}</td> 
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="padding: 1rem 0;">Belum ada data material untuk proyek ini.</p>
            @endif
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
                                    <a href="{{ $item['url'] }}" target="_blank" title="Klik untuk memperbesar">
                                        <img src="{{ $item['url'] }}" alt="{{ htmlspecialchars($item['caption']) }}">
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

{{-- Modal (Form Popup) untuk Tambah Material --}}
<div class="modal fade" id="addMaterialModal" tabindex="-1" aria-labelledby="addMaterialModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMaterialModalLabel">Tambah Material Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('project.addMaterial', ['rowIndex' => $rowIndex]) }}" method="POST">
                @csrf
                {{-- Hidden fields untuk mengirim data proyek --}}
                <input type="hidden" name="id_project_posjar" value="{{ $selectedRow[array_search('ID PROJECT POSJAR', $header)] ?? '' }}">
                <input type="hidden" name="lokasi_jalan" value="{{ $selectedRow[array_search('LOKASI/JALAN', $header)] ?? '' }}">

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="no" class="form-label">No</label>
                        <input type="text" class="form-control" id="no" name="no" required>
                    </div>
                    <div class="mb-3">
                        <label for="jenis_material" class="form-label">Jenis Material</label>
                        <input type="text" class="form-control" id="jenis_material" name="jenis_material" required>
                    </div>
                    <div class="mb-3">
                        <label for="uraian_pekerjaan" class="form-label">Uraian Pekerjaan</label>
                        <input type="text" class="form-control" id="uraian_pekerjaan" name="uraian_pekerjaan" required>
                    </div>
                    <div class="mb-3">
                        <label for="satuan" class="form-label">Satuan (e.g., meter, buah)</label>
                        <input type="text" class="form-control" id="satuan" name="satuan" required>
                    </div>
                     <div class="mb-3">
                        <label for="volume" class="form-label">Volume (hanya angka)</label>
                        <input type="number" step="any" class="form-control" id="volume" name="volume" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Material</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection