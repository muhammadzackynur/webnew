<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

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

        if ($data && isset($data['values']) && count($data['values']) > 0) {
            $header = array_shift($data['values']);
            $rows = $data['values'];
        }

        return view('projects.index', compact('header', 'rows'));
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

                // --- LOGIKA BARU UNTUK MENGELOMPOKKAN GAMBAR ---
                $groupedGallery = [];
                $allPhotos = [];
                foreach ($galleryItems as $item) {
                    if (empty($item['caption']) || empty($item['path'])) continue;
                    
                    // Ambil kata pertama dari caption (Before, Progress, After)
                    $captionParts = explode(' ', trim($item['caption']));
                    $groupName = ucfirst(strtolower($captionParts[0]));

                    // Hapus kata pertama dari caption asli untuk tampilan
                    $item['caption'] = implode(' ', array_slice($captionParts, 1));
                    
                    // Masukkan item ke dalam grupnya
                    $groupedGallery[$groupName][] = $item;
                    $allPhotos[] = $item;
                }
                // ---------------------------------------------

                // Kirim data yang sudah dikelompokkan dan semua foto ke view
                return view('projects.detail', compact('header', 'selectedRow', 'groupedGallery', 'allPhotos'));
            }
        }

        return view('projects.not-found');
    }
}