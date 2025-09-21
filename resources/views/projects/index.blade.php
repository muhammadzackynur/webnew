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
            {{-- Search bar tidak diubah --}}
            <div class="search-bar">
                <i data-feather="search" class="icon"></i>
                <input type="text" id="searchInput" placeholder="Cari data proyek...">
            </div>
            {{-- Dropdown STO --}}
            <select class="filter-dropdown" id="stoFilter">
                <option value="">Semua STO</option>
                @foreach ($stoList as $sto)
                    {{-- Tambahkan 'selected' jika STO ini yang sedang difilter --}}
                    <option value="{{ $sto }}" {{ $selectedSto == $sto ? 'selected' : '' }}>
                        {{ $sto }}
                    </option>
                @endforeach
            </select>
            {{-- Dropdown Datel --}}
            <select class="filter-dropdown" id="datelFilter">
                <option value="">Semua Datel</option>
                @foreach ($datelList as $datel)
                     {{-- Tambahkan 'selected' jika Datel ini yang sedang difilter --}}
                    <option value="{{ $datel }}" {{ $selectedDatel == $datel ? 'selected' : '' }}>
                        {{ $datel }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Summary Cards (tidak berubah) --}}
    <div class="summary-cards">
        <div class="card">
            <div class="icon-container blue"><i data-feather="briefcase"></i></div>
            <div class="card-content">
                <p class="value">{{ count($rows) }}</p>
                <p class="label">Total Proyek</p>
            </div>
        </div>
        <div class="card">
            <div class="icon-container purple"><i data-feather="list"></i></div>
            <div class="card-content">
                <p class="value">{{ $planCount }}</p>
                <p class="label">Proyek Plan</p>
            </div>
        </div>
        <div class="card">
            <div class="icon-container green"><i data-feather="activity"></i></div>
            <div class="card-content">
                <p class="value">{{ $progressCount }}</p>
                <p class="label">Proyek Progress</p>
            </div>
        </div>
        <div class="card">
            <div class="icon-container" style="background-color: rgba(245, 158, 11, 0.1); color: #F59E0B;"><i data-feather="check-square"></i></div>
            <div class="card-content">
                <p class="value">{{ $doneCount }}</p>
                <p class="label">Proyek Done</p>
            </div>
        </div>
    </div>

    {{-- Data Table (tidak berubah) --}}
    <div class="table-wrapper">
        <div class="table-header">
            <h2>Data Proyek ({{ count($rows) }} item)</h2>
        </div>
        <table class="data-table" id="projectTable">
            <thead>
                <tr>
                    @php
                        $hiddenColumns = ['File ID', 'URL FOTO', 'Keterangan FOTO', 'Link Google Maps'];
                    @endphp
                    @foreach ($header as $col)
                        @php
                            $isHidden = false;
                            foreach ($hiddenColumns as $hidden) {
                                if (stripos($col, $hidden) !== false) {
                                    $isHidden = true;
                                    break;
                                }
                            }
                        @endphp
                        @if (!$isHidden)
                            <th>{{ htmlspecialchars($col) }}</th>
                        @endif
                    @endforeach
                    <th>Google Maps</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $linkMapsIndex = array_search('Link Google Maps', $header);
                @endphp
                @forelse ($rows as $index => $row)
                    @if (trim(implode('', $row)) === '')
                        @continue
                    @endif
                    <tr class="data-row" data-row-index="{{ $index }}">
                        @foreach ($header as $colIndex => $colName)
                             @php
                                $isHidden = false;
                                foreach ($hiddenColumns as $hidden) {
                                    if (stripos($colName, $hidden) !== false) {
                                        $isHidden = true;
                                        break;
                                    }
                                }
                            @endphp
                            @if (!$isHidden)
                                <td>{{ htmlspecialchars($row[$colIndex] ?? '') }}</td>
                            @endif
                        @endforeach
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
    feather.replace();
    
    // --- SCRIPT BARU UNTUK FILTER ---
    const stoFilter = document.getElementById('stoFilter');
    const datelFilter = document.getElementById('datelFilter');

    function applyFilters() {
        const selectedSto = stoFilter.value;
        const selectedDatel = datelFilter.value;

        // Membuat URL baru dengan parameter query
        const url = new URL(window.location.href.split('?')[0]);
        if (selectedSto) {
            url.searchParams.set('sto', selectedSto);
        }
        if (selectedDatel) {
            url.searchParams.set('datel', selectedDatel);
        }
        
        // Memuat ulang halaman dengan URL filter
        window.location.href = url.toString();
    }

    stoFilter.addEventListener('change', applyFilters);
    datelFilter.addEventListener('change', applyFilters);
    // --- AKHIR SCRIPT FILTER ---


    // Script untuk klik baris (tidak berubah)
    const dataRows = document.querySelectorAll('.data-row');
    dataRows.forEach(row => {
        row.addEventListener('click', function(e) {
            if (e.target.closest('a')) return;
            const rowIndex = this.getAttribute('data-row-index');
            window.location.href = `{{ url('/project') }}/${rowIndex}`;
        });
    });

    // --- SCRIPT BARU UNTUK SEARCH ---
    const searchInput = document.getElementById('searchInput');
    const table = document.getElementById('projectTable');
    const tableRows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

    searchInput.addEventListener('keyup', function() {
        const filter = searchInput.value.toUpperCase();

        for (let i = 0; i < tableRows.length; i++) {
            let rowVisible = false;
            const cells = tableRows[i].getElementsByTagName('td');
            for (let j = 0; j < cells.length; j++) {
                if (cells[j]) {
                    const cellText = cells[j].textContent || cells[j].innerText;
                    if (cellText.toUpperCase().indexOf(filter) > -1) {
                        rowVisible = true;
                        break;
                    }
                }
            }
            tableRows[i].style.display = rowVisible ? '' : 'none';
        }
    });
     // --- AKHIR SCRIPT SEARCH ---
});
</script>
@endpush