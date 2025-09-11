<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    // Kredensial API Anda. Untuk keamanan, lebih baik simpan di file .env
    private $apiKey = 'AIzaSyC6lnm-I1v7-09PAeKvfkVcnUGiUx-ECvE'; // Ganti dengan API Key Anda
    private $spreadsheetId = '1eQDfqKZ63i2wowohqkmClkgX132wSRsJmbYgaIIlLJE'; // Ganti dengan ID Spreadsheet Anda
    private $sheetName = 'Data';

    /**
     * Fungsi privat untuk mengambil data dari Google Sheet.
     */
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
            // Tangani error jika API tidak dapat diakses
            report($e);
            return null;
        }
    }

    /**
     * Menampilkan daftar semua data (halaman utama).
     */
    public function index(): View
    {
        $data = $this->getSheetData();
        $header = [];
        $rows = [];
        $datelList = [];
        $stoList = [];
        $planCount = 0;
        $progressCount = 0;
        $doneCount = 0;

        if ($data && isset($data['values']) && count($data['values']) > 1) {
            $header = array_shift($data['values']);
            $rows = $data['values'];

            // Cari index kolom yang relevan
            $datelIndex = array_search('DATEL', $header);
            $stoIndex = array_search('STO', $header);
            $statusIndex = array_search('STATUS PEKERJAAN', $header); // Kolom untuk status

            // Ambil semua nilai unik dari kolom DATEL dan STO untuk filter
            if ($datelIndex !== false) {
                $datelList = collect($rows)->pluck($datelIndex)->unique()->filter()->sort()->values();
            }
            if ($stoIndex !== false) {
                $stoList = collect($rows)->pluck($stoIndex)->unique()->filter()->sort()->values();
            }

            // Hitung jumlah proyek berdasarkan status
            if ($statusIndex !== false) {
                $statuses = collect($rows)->pluck($statusIndex);
                $planCount = $statuses->filter(fn($value) => stripos($value, 'PLAN') !== false)->count();
                $progressCount = $statuses->filter(fn($value) => stripos($value, 'PROGRESS') !== false)->count();
                $doneCount = $statuses->filter(fn($value) => stripos($value, 'DONE') !== false)->count();
            }
        }

        return view('projects.index', compact(
            'header', 'rows', 'datelList', 'stoList',
            'planCount', 'progressCount', 'doneCount'
        ));
    }

    /**
     * Menampilkan detail satu data proyek.
     */
    public function show(int $rowIndex): View
    {
        $data = $this->getSheetData();

        if ($data && isset($data['values'])) {
            $header = array_shift($data['values']);
            $allRows = $data['values'];

            if (isset($allRows[$rowIndex])) {
                $selectedRow = $allRows[$rowIndex];
                $galleryItems = [];

                // Kumpulkan semua item galeri seperti biasa
                foreach ($header as $index => $title) {
                    $value = $selectedRow[$index] ?? '';
                    if (stripos($title, 'Path FOTO') !== false) {
                        preg_match('/\d+$/', $title, $matches);
                        $id = $matches[0] ?? count($galleryItems);
                        if (!empty($value)) $galleryItems[$id]['path'] = $value;
                    } elseif (stripos($title, 'Keterangan FOTO') !== false) {
                        preg_match('/\d+$/', $title, $matches);
                        $id = $matches[0] ?? count($galleryItems);
                        $galleryItems[$id]['caption'] = $value;
                    }
                }
                ksort($galleryItems);

                // --- LOGIKA UNTUK MENGELOMPOKKAN GAMBAR ---
                $groupedGallery = [];
                $allPhotos = [];
                foreach ($galleryItems as $item) {
                    if (empty($item['path'])) continue;
                    
                    $allPhotos[] = $item; // Kumpulkan semua foto valid untuk tombol "Lihat Semua"
                    
                    if (empty($item['caption'])) continue;
                    
                    $captionParts = explode(' ', trim($item['caption']));
                    $groupName = ucfirst(strtolower($captionParts[0]));

                    $displayCaption = implode(' ', array_slice($captionParts, 1));
                    
                    $groupedGallery[$groupName][] = [
                        'path' => $item['path'],
                        'caption' => $displayCaption ?: $groupName
                    ];
                }
                // ---------------------------------------------

                return view('projects.detail', compact('header', 'selectedRow', 'groupedGallery', 'allPhotos', 'rowIndex'));
            }
        }

        return view('projects.not-found');
    }

    /**
     * Menampilkan semua foto di halaman terpisah.
     */
    public function showAllGallery(int $rowIndex): View
    {
        $data = $this->getSheetData();
        $title = "Semua Foto Proyek";

        if ($data && isset($data['values'])) {
            $header = array_shift($data['values']);
            $allRows = $data['values'];

            if (isset($allRows[$rowIndex])) {
                $selectedRow = $allRows[$rowIndex];
                $galleryItems = [];

                // Kumpulkan semua path dan keterangan foto
                foreach ($header as $index => $title) {
                    $value = $selectedRow[$index] ?? '';
                    if (stripos($title, 'Path FOTO') !== false) {
                        preg_match('/\d+$/', $title, $matches);
                        $id = $matches[0] ?? count($galleryItems);
                        if (!empty($value)) $galleryItems[$id]['path'] = $value;
                    } elseif (stripos($title, 'Keterangan FOTO') !== false) {
                        preg_match('/\d+$/', $title, $matches);
                        $id = $matches[0] ?? count($galleryItems);
                        $galleryItems[$id]['caption'] = $value;
                    }
                }
                ksort($galleryItems);
                
                // Filter items yang tidak punya path
                $galleryItems = array_filter($galleryItems, fn($item) => !empty($item['path']));

                // Kelompokkan gambar berdasarkan keterangan (Before, Progress, After, dll.)
                $groupedGallery = [];
                foreach ($galleryItems as $item) {
                    if (empty($item['caption'])) continue;
                    
                    $captionParts = explode(' ', trim($item['caption']));
                    $groupName = ucfirst(strtolower($captionParts[0]));
                    $displayCaption = implode(' ', array_slice($captionParts, 1));
                    
                    $groupedGallery[$groupName][] = [
                        'path' => $item['path'],
                        'caption' => $displayCaption ?: $groupName
                    ];
                }

                return view('projects.gallery-all', compact('groupedGallery', 'title', 'rowIndex'));
            }
        }
        return view('projects.not-found');
    }
}