@extends('layouts.app')

@section('title', 'Dashboard Proyek')

@section('content')
    {{-- Header Utama Halaman --}}
    <div class="main-header">
        <div class="header-left">
            <h1>Data dari Google Sheet</h1>
            <p>Data berikut diambil langsung dari Google Sheet API. Klik pada salah satu baris untuk melihat detail.</p>
        </div>
        <div class="header-right">
            <div class="search-bar">
                <i data-feather="search" class="icon"></i>
                <input type="text" placeholder="Cari data proyek...">
            </div>
            <select class="filter-dropdown">
                <option>Semua STO</option>
                @foreach ($stoList as $sto)
                    <option>{{ $sto }}</option>
                @endforeach
            </select>
            <select class="filter-dropdown">
                <option>Semua Datel</option>
                @foreach ($datelList as $datel)
                    <option>{{ $datel }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="summary-cards">
        <div class="card">
            <div class="icon-container blue">
                <i data-feather="clipboard"></i>
            </div>
            <div class="card-content">
                <p class="value">{{ count($rows) }}</p>
                <p class="label">Total Proyek</p>
            </div>
        </div>
        <div class="card">
            <div class="icon-container green">
                <i data-feather="map-pin"></i>
            </div>
            <div class="card-content">
                <p class="value">{{ count($datelList) }}</p>
                <p class="label">Area Datel</p>
            </div>
        </div>
        <div class="card">
            <div class="icon-container purple">
                <i data-feather="archive"></i>
            </div>
            <div class="card-content">
                <p class="value">{{ count($stoList) }}</p>
                <p class="label">STO Aktif</p>
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="table-wrapper">
        <div class="table-header">
            <h2>Data Proyek ({{ count($rows) }} item)</h2>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    @php
                        // Kolom yang ingin disembunyikan dari data asli
                        $hiddenColumns = ['File ID', 'Path FOTO', 'STATUS', 'ALPRO', 'Keterangan FOTO', 'Link Google Maps'];
                    @endphp
                    @foreach ($header as $col)
                        @if (!in_array($col, $hiddenColumns))
                            <th>{{ htmlspecialchars($col) }}</th>
                        @endif
                    @endforeach
                    <th>Google Maps</th> {{-- Tambah header manual untuk link --}}
                </tr>
            </thead>
            <tbody>
                @php
                    // Cari index kolom Link Google Maps untuk mengambil datanya
                    $linkMapsIndex = array_search('Link Google Maps', $header);
                @endphp
                @forelse ($rows as $index => $row)
                    @if (trim(implode('', $row)) === '')
                        @continue
                    @endif
                    <tr class="data-row" data-row-index="{{ $index }}">
                        @foreach ($header as $colIndex => $colName)
                            @if (!in_array($colName, $hiddenColumns))
                                <td>{{ htmlspecialchars($row[$colIndex] ?? '') }}</td>
                            @endif
                        @endforeach
                        {{-- Tambah sel untuk link Google Maps --}}
                        <td>
                            @if($linkMapsIndex !== false && !empty($row[$linkMapsIndex]))
                                <a href="{{ $row[$linkMapsIndex] }}" target="_blank" class="google-maps-link">
                                    <i data-feather="map" width="16" height="16"></i>
                                    <span>Buka Maps</span>
                                </a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($header) - count($hiddenColumns) + 1 }}" style="text-align: center; padding: 2rem;">Tidak ada data untuk ditampilkan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace(); // Pastikan ikon dirender ulang
    const dataRows = document.querySelectorAll('.data-row');
    dataRows.forEach(row => {
        row.addEventListener('click', function(e) {
            // Pastikan klik bukan pada link
            if (e.target.closest('a')) {
                return;
            }
            const rowIndex = this.getAttribute('data-row-index');
            window.location.href = `{{ url('/project') }}/${rowIndex}`;
        });
    });
});
</script>
@endpush