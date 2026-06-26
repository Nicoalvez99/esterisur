<?php

use App\Http\Controllers\RecepcionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RecepcionController::class, 'index'])->name('index');
Route::get('/nuevo', [RecepcionController::class, 'create'])->name('create');
Route::post('/', [RecepcionController::class, 'store'])->name('store');
Route::get('/{lote}', [RecepcionController::class, 'show'])->name('show');