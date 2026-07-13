<?php

use App\Http\Controllers\AuditorController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuditorController::class, 'index'])->name('index');
Route::get('/lotes', [AuditorController::class, 'lotes'])->name('lotes');
Route::get('/lotes/{lote}', [AuditorController::class, 'show'])->name('show');
Route::get('/historial', [AuditorController::class, 'historial'])->name('historial');