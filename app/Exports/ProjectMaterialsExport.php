<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProjectMaterialsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $materials;

    public function __construct(array $materials)
    {
        $this->materials = $materials;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return new Collection($this->materials);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        // Ini akan menjadi baris header di file Excel
        return [
            'No',
            'Jenis Material',
            'Uraian Pekerjaan',
            'Satuan',
            'Volume',
        ];
    }

    /**
     * @param mixed $material
     * @return array
     */
    public function map($material): array
    {
        // Fungsi ini mengatur data untuk setiap baris
        return [
            $material['No'],
            $material['Jenis Material'],
            $material['Uraian Pekerjaan'],
            $material['Satuan'],
            $material['Volume'],
        ];
    }
}