<?php

// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;

// Rute untuk menampilkan semua data (halaman utama)
Route::get('/', [ProjectController::class, 'index'])->name('project.index');

// Rute untuk menampilkan detail data
Route::get('/project/{rowIndex}', [ProjectController::class, 'show'])->name('project.show');

// Rute BARU untuk menampilkan semua foto
Route::get('/project/{rowIndex}/gallery', [ProjectController::class, 'showAllGallery'])->name('project.gallery');

// Rute untuk menambah material secara manual
Route::post('/project/{rowIndex}/add-material', [ProjectController::class, 'addMaterial'])->name('project.addMaterial');

// =================== TAMBAHKAN RUTE INI ===================
// Rute untuk upload material dari template Excel
Route::post('/project/{rowIndex}/upload-material-excel', [ProjectController::class, 'uploadMaterialExcel'])->name('project.uploadMaterialExcel');
// ==========================================================

// Rute untuk export material ke Excel
Route::get('/project/{rowIndex}/export-material', [ProjectController::class, 'exportMaterial'])->name('project.exportMaterial');