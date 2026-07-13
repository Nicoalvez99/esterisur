<?php

use App\Http\Controllers\ReporteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ReporteController::class, 'index'])->name('index');
Route::get('/trazabilidad', [ReporteController::class, 'trazabilidad'])->name('trazabilidad');
Route::get('/por-institucion', [ReporteController::class, 'porInstitucion'])->name('por-institucion');
Route::get('/controles', [ReporteController::class, 'controles'])->name('controles');
Route::get('/devoluciones', [ReporteController::class, 'devoluciones'])->name('devoluciones');
Route::get('/kpis', [ReporteController::class, 'kpis'])->name('kpis');