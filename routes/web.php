<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;

// Rute untuk menampilkan semua data (sebelumnya index.php)
Route::get('/', [ProjectController::class, 'index'])->name('project.index');

// Rute untuk menampilkan detail data (sebelumnya detail.php)
Route::get('/project/{row}', [ProjectController::class, 'show'])->name('project.show');