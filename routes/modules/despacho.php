<?php

use App\Http\Controllers\DespachoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DespachoController::class, 'index'])->name('index');
Route::get('/lote/{lote}', [DespachoController::class, 'create'])->name('create');
Route::post('/lote/{lote}', [DespachoController::class, 'store'])->name('store');
Route::get('/remito/{remito}', [DespachoController::class, 'show'])->name('show');