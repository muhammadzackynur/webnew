<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    // Kredensial API Anda.
    private $apiKey = 'AIzaSyC6lnm-I1v7-09PAeKvfkVcnUGiUx-ECvE'; // Ganti dengan API Key Anda
    private $spreadsheetId = '1DcneJQUGCp1NHXGI7LUlgAd53aBnOULY7w6xqT02dSk'; // Ganti dengan ID Spreadsheet Anda
    private $sheetName = 'Data';

    private function getSheetData()
    {
        $apiUrl = "https://sheets.googleapis.com/v4/spreadsheets/{$this->spreadsheetId}/values/{$this->sheetName}?key={$this->apiKey}";
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

    // --- FUNGSI INDEX DIPERBARUI UNTUK MENERIMA REQUEST FILTER ---
    public function index(Request $request): View
    {
        $data = $this->getSheetData();
        $header = [];
        $allRows = []; // Ganti nama variabel agar lebih jelas
        $datelList = [];
        $stoList = [];
        
        if ($data && isset($data['values']) && count($data['values']) > 1) {
            $header = array_shift($data['values']);
            $allRows = $data['values'];

            $datelIndex = array_search('DATEL', $header);
            $stoIndex = array_search('STO', $header);

            // Ambil semua nilai unik dari kolom DATEL dan STO untuk filter dropdown
            if ($datelIndex !== false) {
                $datelList = collect($allRows)->pluck($datelIndex)->unique()->filter()->sort()->values();
            }
            if ($stoIndex !== false) {
                $stoList = collect($allRows)->pluck($stoIndex)->unique()->filter()->sort()->values();
            }
        }

        // --- LOGIKA FILTER BARU ---
        $selectedDatel = $request->query('datel');
        $selectedSto = $request->query('sto');

        $filteredRows = collect($allRows)->filter(function ($row) use ($header, $selectedDatel, $selectedSto) {
            $datelIndex = array_search('DATEL', $header);
            $stoIndex = array_search('STO', $header);

            $datelMatch = !$selectedDatel || (isset($row[$datelIndex]) && $row[$datelIndex] == $selectedDatel);
            $stoMatch = !$selectedSto || (isset($row[$stoIndex]) && $row[$stoIndex] == $selectedSto);
            
            return $datelMatch && $stoMatch;
        })->values()->all();
        // --- AKHIR LOGIKA FILTER ---


        // Hitung status berdasarkan data yang sudah difilter
        $statusIndex = array_search('STATUS PEKERJAAN', $header);
        $planCount = 0;
        $progressCount = 0;
        $doneCount = 0;
        if ($statusIndex !== false) {
            $statuses = collect($filteredRows)->pluck($statusIndex);
            $planCount = $statuses->filter(fn($value) => stripos($value, 'PLAN') !== false)->count();
            $progressCount = $statuses->filter(fn($value) => stripos($value, 'PROGRESS') !== false)->count();
            $doneCount = $statuses->filter(fn($value) => stripos($value, 'DONE') !== false)->count();
        }

        // Kirim data yang sudah difilter ke view
        return view('projects.index', [
            'header' => $header,
            'rows' => $filteredRows, // Kirim data yang sudah difilter
            'datelList' => $datelList,
            'stoList' => $stoList,
            'planCount' => $planCount,
            'progressCount' => $progressCount,
            'doneCount' => $doneCount,
            'selectedDatel' => $selectedDatel, // Untuk menandai filter aktif
            'selectedSto' => $selectedSto,     // Untuk menandai filter aktif
        ]);
    }

    public function show($rowIndex): View
    {
        $data = $this->getSheetData();

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

                return view('projects.detail', compact('header', 'selectedRow', 'groupedGallery', 'allPhotos', 'rowIndex'));
            }
        }

        return view('projects.not-found');
    }

    public function showAllGallery($rowIndex): View
    {
        $data = $this->getSheetData();
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
}