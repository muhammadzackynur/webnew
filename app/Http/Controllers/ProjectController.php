<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use App\Services\GoogleSheetsService; // Pastikan ini ada

class ProjectController extends Controller
{
    // Kredensial API Anda.
    private $apiKey = 'AIzaSyC6lnm-I1v7-09PAeKvfkVcnUGiUx-ECvE'; // Ganti dengan API Key Anda
    private $spreadsheetId = '1DcneJQUGCp1NHXGI7LUlgAd53aBnOULY7w6xqT02dSk'; // Ganti dengan ID Spreadsheet Anda
    private $sheetName = 'Data';
    private $materialSheetName = 'Data Material'; 

    private function getSheetData($sheet)
    {
        $apiUrl = "https://sheets.googleapis.com/v4/spreadsheets/{$this->spreadsheetId}/values/{$sheet}?key={$this->apiKey}";
        try {
            $response = Http::get($apiUrl);
            if ($response->successful()) {
                return $response->json();
            }
            return null;
        } catch (\Exception $e) {
            report($e);
            return null;
        }
    }

    public function index(Request $request): View
    {
        $data = $this->getSheetData($this->sheetName);
        $header = [];
        $allRows = [];
        $datelList = [];
        $stoList = [];
        
        if ($data && isset($data['values']) && count($data['values']) > 1) {
            $header = array_shift($data['values']);
            $allRows = $data['values'];

            $datelIndex = array_search('DATEL', $header);
            $stoIndex = array_search('STO', $header);

            if ($datelIndex !== false) {
                $datelList = collect($allRows)->pluck($datelIndex)->unique()->filter()->sort()->values();
            }
            if ($stoIndex !== false) {
                $stoList = collect($allRows)->pluck($stoIndex)->unique()->filter()->sort()->values();
            }
        }

        $selectedDatel = $request->query('datel');
        $selectedSto = $request->query('sto');

        $filteredRows = collect($allRows)->filter(function ($row) use ($header, $selectedDatel, $selectedSto) {
            $datelIndex = array_search('DATEL', $header);
            $stoIndex = array_search('STO', $header);

            $datelMatch = !$selectedDatel || (isset($row[$datelIndex]) && $row[$datelIndex] == $selectedDatel);
            $stoMatch = !$selectedSto || (isset($row[$stoIndex]) && $row[$stoIndex] == $selectedSto);
            
            return $datelMatch && $stoMatch;
        })->values()->all();

        $statusIndex = array_search('STATUS PEKERJAAN', $header);
        $planCount = 0;
        $progressCount = 0;
        $doneCount = 0;
        if ($statusIndex !== false) {
            $statuses = collect($filteredRows)->pluck($statusIndex);
            $planCount = $statuses->filter(fn($value) => stripos($value, 'PLAN') !== false)->count();
            $progressCount = $statuses->filter(fn($value) => stripos($value, 'PROGRESS') !== false)->count();
            $doneCount = $statuses->filter(fn($value) => stripos($value, 'CLOSE') !== false)->count();
        }

        return view('projects.index', [
            'header' => $header,
            'rows' => $filteredRows,
            'datelList' => $datelList,
            'stoList' => $stoList,
            'planCount' => $planCount,
            'progressCount' => $progressCount,
            'doneCount' => $doneCount,
            'selectedDatel' => $selectedDatel,
            'selectedSto' => $selectedSto,
        ]);
    }

    public function show($rowIndex): View
    {
        $projectData = $this->getSheetData($this->sheetName);
        $materialData = $this->getSheetData($this->materialSheetName);

        if ($projectData && isset($projectData['values'])) {
            $projectHeader = array_shift($projectData['values']);
            $allProjectRows = $projectData['values'];

            if (isset($allProjectRows[$rowIndex])) {
                $selectedRow = $allProjectRows[$rowIndex];
                
                $projectLocationIndex = array_search('LOKASI/JALAN', $projectHeader);
                $currentProjectLocation = $selectedRow[$projectLocationIndex] ?? null;

                $projectMaterials = [];
                if ($materialData && isset($materialData['values']) && $currentProjectLocation) {
                    $materialHeader = array_shift($materialData['values']);
                    $allMaterialRows = $materialData['values'];
                    
                    $materialLocationIndex = array_search('LOKASI/JALAN', $materialHeader);
                    
                    if ($materialLocationIndex !== false) {
                        $projectMaterials = collect($allMaterialRows)->filter(function ($row) use ($materialLocationIndex, $currentProjectLocation) {
                            return isset($row[$materialLocationIndex]) && $row[$materialLocationIndex] === $currentProjectLocation;
                        })->map(function ($row) use ($materialHeader) {
                            return array_combine($materialHeader, array_pad($row, count($materialHeader), ''));
                        })->values()->all();
                    }
                }

                $galleryItems = [];
                foreach ($projectHeader as $index => $title) {
                    $value = $selectedRow[$index] ?? '';
                    if (stripos($title, 'URL FOTO') !== false) {
                        preg_match('/(\d+)$/', $title, $matches);
                        $id = $matches[1] ?? count($galleryItems);
                        if (!empty($value)) $galleryItems[$id]['url'] = $value;
                    } elseif (stripos($title, 'Keterangan FOTO') !== false) {
                        preg_match('/(\d+)$/', $title, $matches);
                        $id = $matches[1] ?? count($galleryItems);
                        $galleryItems[$id]['caption'] = $value;
                    }
                }
                ksort($galleryItems);

                $groupedGallery = [];
                $allPhotos = [];
                foreach ($galleryItems as $item) {
                    if (empty($item['url'])) continue;
                    $allPhotos[] = $item;
                    if (empty($item['caption'])) continue;
                    
                    $captionParts = explode(' ', trim($item['caption']));
                    $groupName = ucfirst(strtolower($captionParts[0]));
                    $displayCaption = implode(' ', array_slice($captionParts, 1));
                    
                    $groupedGallery[$groupName][] = [
                        'url' => $item['url'],
                        'caption' => $displayCaption ?: $groupName
                    ];
                }

                return view('projects.detail', [
                    'header' => $projectHeader,
                    'selectedRow' => $selectedRow,
                    'groupedGallery' => $groupedGallery,
                    'allPhotos' => $allPhotos,
                    'rowIndex' => $rowIndex,
                    'projectMaterials' => $projectMaterials
                ]);
            }
        }

        return view('projects.not-found');
    }

    public function showAllGallery($rowIndex): View
    {
        $data = $this->getSheetData($this->sheetName);
        $title = "Semua Foto Proyek";

        if ($data && isset($data['values'])) {
            $header = array_shift($data['values']);
            $allRows = $data['values'];

            if (isset($allRows[$rowIndex])) {
                $selectedRow = $allRows[$rowIndex];
                $galleryItems = [];

                foreach ($header as $index => $title) {
                    $value = $selectedRow[$index] ?? '';
                    if (stripos($title, 'URL FOTO') !== false) {
                        preg_match('/\d+$/', $title, $matches);
                        $id = $matches[0] ?? count($galleryItems);
                        if (!empty($value)) $galleryItems[$id]['url'] = $value;
                    } elseif (stripos($title, 'Keterangan FOTO') !== false) {
                        preg_match('/\d+$/', $title, $matches);
                        $id = $matches[0] ?? count($galleryItems);
                        $galleryItems[$id]['caption'] = $value;
                    }
                }
                ksort($galleryItems);
                
                $galleryItems = array_filter($galleryItems, fn($item) => !empty($item['url']));

                $groupedGallery = [];
                foreach ($galleryItems as $item) {
                    if (empty($item['caption'])) continue;
                    
                    $captionParts = explode(' ', trim($item['caption']));
                    $groupName = ucfirst(strtolower($captionParts[0]));
                    $displayCaption = implode(' ', array_slice($captionParts, 1));
                    
                    $groupedGallery[$groupName][] = [
                        'url' => $item['url'],
                        'caption' => $displayCaption ?: $groupName
                    ];
                }

                return view('projects.gallery-all', compact('groupedGallery', 'title', 'rowIndex'));
            }
        }
        return view('projects.not-found');
    }

    // FUNGSI BARU UNTUK MENAMBAHKAN MATERIAL
    public function addMaterial(Request $request, $rowIndex)
    {
        // 1. Validasi input dari form
        $validated = $request->validate([
            'id_project_posjar' => 'required|string',
            'lokasi_jalan'      => 'required|string',
            'no'                => 'required|string',
            'jenis_material'    => 'required|string',
            'uraian_pekerjaan'  => 'required|string',
            'satuan'            => 'required|string',
            'volume'            => 'required|numeric',
        ]);

        try {
            // 2. Panggil service untuk menulis ke Google Sheet
            $sheetsService = new GoogleSheetsService();
            $sheetsService->appendMaterial($validated);

            // 3. Kembali ke halaman detail dengan pesan sukses
            return redirect()->route('project.show', ['rowIndex' => $rowIndex])
                             ->with('success', 'Material berhasil ditambahkan!');

        } catch (\Exception $e) {
            // Jika gagal, kembali dengan pesan error
            report($e);
            return redirect()->route('project.show', ['rowIndex' => $rowIndex])
                             ->with('error', 'Gagal menambahkan material: ' . $e->getMessage());
        }
    }
}