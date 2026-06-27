<?php

use App\Http\Controllers\CalidadController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CalidadController::class, 'index'])->name('index');
Route::get('/lote/{lote}', [CalidadController::class, 'show'])->name('show');
Route::post('/lote/{lote}/liberar', [CalidadController::class, 'liberar'])->name('liberar');
Route::patch('/control/{control}', [CalidadController::class, 'actualizarControl'])->name('control.update');