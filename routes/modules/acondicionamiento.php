<?php

use App\Http\Controllers\AcondicionamientoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AcondicionamientoController::class, 'index'])->name('index');
Route::get('/lote/{lote}', [AcondicionamientoController::class, 'create'])->name('create');
Route::post('/lote/{lote}', [AcondicionamientoController::class, 'store'])->name('store');
Route::get('/lote/{lote}/detalle', [AcondicionamientoController::class, 'show'])->name('show');