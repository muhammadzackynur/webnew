<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;

// Rute untuk menampilkan semua data (halaman utama)
Route::get('/', [ProjectController::class, 'index'])->name('project.index');

// Rute untuk menampilkan detail data
Route::get('/project/{rowIndex}', [ProjectController::class, 'show'])->name('project.show');

// Rute BARU untuk menampilkan semua foto
Route::get('/project/{rowIndex}/gallery', [ProjectController::class, 'showAllGallery'])->name('project.gallery');