<?php

use App\Http\Controllers\EsterilizacionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [EsterilizacionController::class, 'index'])->name('index');
Route::get('/nuevo', [EsterilizacionController::class, 'create'])->name('create');
Route::post('/', [EsterilizacionController::class, 'store'])->name('store');
Route::get('/{esterilizacion}', [EsterilizacionController::class, 'show'])->name('show');
Route::post('/{esterilizacion}/fin-aireacion', [EsterilizacionController::class, 'finAireacion'])->name('fin-aireacion');