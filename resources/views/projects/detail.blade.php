@extends('layouts.app')

@section('title', 'Detail Data Proyek')

@push('styles')
{{-- ### CSS UNTUK MEMPERBAIKI TABEL ### --}}
<style>
    .table-responsive {
        overflow-x: auto; /* Tambahkan scroll horizontal jika benar-benar diperlukan */
    }
    .data-table {
        table-layout: fixed; /* Paksa tabel untuk mematuhi lebar yang ditentukan */
        width: 100%; /* Pastikan tabel menggunakan lebar penuh container */
    }
    .data-table th,
    .data-table td {
        word-wrap: break-word; /* Memaksa teks untuk patah dan turun baris */
        vertical-align: top; /* Jaga agar konten selaras di bagian atas */
    }
    /* Atur lebar spesifik untuk setiap kolom agar rapi */
    .data-table th:nth-child(1), .data-table td:nth-child(1) { width: 5%; }  /* No */
    .data-table th:nth-child(2), .data-table td:nth-child(2) { width: 20%; } /* Jenis Material */
    .data-table th:nth-child(3), .data-table td:nth-child(3) { width: 45%; } /* Uraian Pekerjaan (paling lebar) */
    .data-table th:nth-child(4), .data-table td:nth-child(4) { width: 15%; } /* Satuan */
    .data-table th:nth-child(5), .data-table td:nth-child(5) { width: 15%; } /* Volume */
</style>
@endpush

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
        {{-- Menampilkan pesan sukses atau error --}}
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
                <div class="d-flex gap-2">
                    {{-- Tombol Export Excel --}}
                    <a href="{{ route('project.exportMaterial', ['rowIndex' => $rowIndex]) }}" class="btn btn-success d-flex align-items-center gap-2">
                        <i data-feather="download" style="width:16px; height:16px;"></i>
                        Export BoQ
                    </a>
                </div>
            </div>

            {{-- Form Upload Template Excel --}}
            <div style="border-top: 1px solid #e5e7eb; margin-top: 1.5rem; padding-top: 1.5rem;">
                <h4 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem;">Upload via Template Excel</h4>
                <p style="font-size: 0.875rem; color: #6B7280; margin-bottom: 1rem;">
                    Gunakan template untuk menambahkan beberapa material sekaligus.
                </p>
                <form action="{{ route('project.uploadMaterialExcel', ['rowIndex' => $rowIndex]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id_project_posjar" value="{{ $selectedRow[array_search('ID PROJECT POSJAR', $header)] ?? '' }}">
                    <input type="hidden" name="lokasi_jalan" value="{{ $selectedRow[array_search('LOKASI/JALAN', $header)] ?? '' }}">
                    <div class="input-group">
                        <input type="file" class="form-control" name="material_excel" required accept=".xlsx, .xls">
                        <button class="btn btn-primary" type="submit">Upload File</button>
                    </div>
                </form>
            </div>
            
            {{-- Tabel Material yang Sudah Ada (DENGAN PERBAIKAN) --}}
            @if (!empty($projectMaterials))
                <div class="table-responsive" style="margin-top: 1.5rem;">
                    <table class="data-table">
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
                                    <td>{{ htmlspecialchars($material['No'] ?? $material['NO'] ?? '') }}</td>
                                    <td>{{ htmlspecialchars($material['Jenis Material'] ?? $material['DESIGNATOR'] ?? '') }}</td>
                                    <td>{{ htmlspecialchars($material['Uraian Pekerjaan'] ?? '') }}</td>
                                    <td>{{ htmlspecialchars($material['Satuan'] ?? '') }}</td> 
                                    <td>{{ htmlspecialchars($material['Volume'] ?? $material['VOL'] ?? '') }}</td> 
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p style="padding: 1.5rem 0; text-align: center;">Belum ada data material untuk proyek ini.</p>
            @endif

            {{-- Form Baru untuk Tambah Material (Autofill) --}}
            <div style="border-top: 1px solid #e5e7eb; margin-top: 1.5rem; padding-top: 1.5rem;">
                <h4 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem;">Tambah Material Manual</h4>
                <form action="{{ route('project.addMaterial', ['rowIndex' => $rowIndex]) }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_project_posjar" value="{{ $selectedRow[array_search('ID PROJECT POSJAR', $header)] ?? '' }}">
                    <input type="hidden" name="lokasi_jalan" value="{{ $selectedRow[array_search('LOKASI/JALAN', $header)] ?? '' }}">
                    <input type="hidden" name="no" value="{{ count($projectMaterials) + 1 }}">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="jenis_material_input" class="form-label">Jenis Material (Designator)</label>
                            <input list="designator-options" class="form-control" id="jenis_material_input" name="jenis_material" required autocomplete="off">
                            <datalist id="designator-options">
                                @foreach (json_decode($boqData, true) as $designator => $data)
                                    <option value="{{ $designator }}">
                                @endforeach
                            </datalist>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="volume_input" class="form-label">Volume</label>
                            <input type="number" step="any" class="form-control" id="volume_input" name="volume" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="uraian_pekerjaan_input" class="form-label">Uraian Pekerjaan</label>
                        <textarea class="form-control" id="uraian_pekerjaan_input" name="uraian_pekerjaan" rows="2" readonly style="background-color: #e9ecef;"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="satuan_input" class="form-label">Satuan</label>
                        <input type="text" class="form-control" id="satuan_input" name="satuan" readonly style="background-color: #e9ecef;">
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan Material</button>
                </form>
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
                @if (count($allPhotos) > 3)
                    <a href="{{ route('project.gallery.all', ['rowIndex' => $rowIndex]) }}" class="view-all-link">
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
@endsection

@push('scripts')
{{-- JavaScript untuk Autofill --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const boqData = JSON.parse(@json($boqData));
        const jenisMaterialInput = document.getElementById('jenis_material_input');
        const uraianPekerjaanTextarea = document.getElementById('uraian_pekerjaan_input');
        const satuanInput = document.getElementById('satuan_input');

        jenisMaterialInput.addEventListener('input', function() {
            const selectedDesignator = this.value;
            const materialData = boqData[selectedDesignator];

            if (materialData) {
                uraianPekerjaanTextarea.value = materialData.uraian;
                satuanInput.value = materialData.satuan;
            } else {
                uraianPekerjaanTextarea.value = '';
                satuanInput.value = '';
            }
        });
    });
</script>
@endpush