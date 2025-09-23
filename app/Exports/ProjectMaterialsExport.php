<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProjectMaterialsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $materials;

    public function __construct(array $materials)
    {
        $this->materials = $materials;
    }

    public function collection()
    {
        return new Collection($this->materials);
    }

    public function headings(): array
    {
        return [
            'NO',
            'DESIGNATOR',
            'URAIAN PEKERJAAN',
            'SATUAN',
            'VOL', // Menggunakan VOL sesuai template
        ];
    }

    /**
     * @param mixed $material
     * @return array
     */
    public function map($material): array
    {
        // ## KODE YANG DIPERBAIKI ADA DI BAWAH INI ##
        // Menggunakan kunci yang benar sesuai dengan data yang dikirim dari controller
        // dan sesuai dengan header di Google Sheet.
        return [
            $material['No'] ?? $material['NO'] ?? '',
            $material['Jenis Material'] ?? $material['DESIGNATOR'] ?? '', // Diperbaiki: Mencari 'Jenis Material' atau 'DESIGNATOR'
            $material['Uraian Pekerjaan'] ?? '',
            $material['Satuan'] ?? '',
            $material['Volume'] ?? $material['VOL'] ?? '', // Diperbaiki: Mencari 'Volume' atau 'VOL'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style untuk header (baris pertama)
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF4F81BD'], // Warna latar biru
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ]
        ]);

        // Tambahkan border ke seluruh data
        $lastRow = count($this->materials) + 1;
        $sheet->getStyle('A1:E' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);
    }
}