<?php

use App\Http\Controllers\FacturacionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FacturacionController::class, 'index'])->name('index');
Route::post('/marcar', [FacturacionController::class, 'marcarFacturado'])->name('marcar');
Route::patch('/desmarcar/{remito}', [FacturacionController::class, 'desmarcarFacturado'])->name('desmarcar');