@extends('layouts.app')

{{-- Judul Halaman --}}
@section('page_title')
    <h1>Data dari Google Sheet</h1>
@endsection

@section('content')
<p class="page-desc">
    Data berikut diambil langsung dari Google Sheet API. Klik pada salah satu baris untuk melihat detail.
</p>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                @foreach ($header as $col)
                    @php
                        // Sembunyikan kolom tertentu
                        $hiddenColumns = ['File ID', 'Path FOTO', 'STATUS', 'ALPRO', 'Keterangan FOTO'];
                        $shouldHide = false;
                        foreach ($hiddenColumns as $keyword) {
                            if (stripos($col, $keyword) !== false) {
                                $shouldHide = true;
                                break;
                            }
                        }
                    @endphp

                    @if (!$shouldHide)
                        <th>{{ htmlspecialchars($col) }}</th>
                    @endif
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $index => $row)
                @if (trim(implode('', $row)) === '')
                    @continue
                @endif
                <tr class="data-row" data-row-index="{{ $index }}">
                    @foreach ($header as $colIndex => $colName)
                        @php
                            $shouldHide = false;
                            foreach ($hiddenColumns as $keyword) {
                                if (stripos($colName, $keyword) !== false) {
                                    $shouldHide = true;
                                    break;
                                }
                            }
                        @endphp

                        @if (!$shouldHide)
                            <td>{{ htmlspecialchars($row[$colIndex] ?? '') }}</td>
                        @endif
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="empty-msg">Tidak ada data untuk ditampilkan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dataRows = document.querySelectorAll('.data-row');
    dataRows.forEach(row => {
        row.addEventListener('click', function() {
            const rowIndex = this.getAttribute('data-row-index');
            window.location.href = `{{ url('/project') }}/${rowIndex}`;
        });
    });
});
</script>
@endsection
