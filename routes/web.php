<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\InstitucionController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');



    Route::prefix('recepcion')->name('recepcion.')->middleware(['auth', 'recepcion'])->group(function () {
        require __DIR__.'/modules/recepcion.php';
    });
    // ACONDICIONAMIENTO
    // ------------------------------------------------------------------
     Route::prefix('acondicionamiento')->name('acondicionamiento.')->middleware(['auth', 'acondicionamiento'])->group(function () {
        require __DIR__.'/modules/acondicionamiento.php';
     });
 
    // ------------------------------------------------------------------
    // ESTERILIZACIÓN
    // ------------------------------------------------------------------
    Route::prefix('esterilizacion')->name('esterilizacion.')->middleware(['auth', 'esterilizacion'])->group(function () {
         require __DIR__.'/modules/esterilizacion.php';
    });
 
    // ------------------------------------------------------------------
    // CONTROL DE CALIDAD
    // ------------------------------------------------------------------
    // Route::prefix('calidad')->name('calidad.')->group(function () {
    //     require __DIR__.'/modules/calidad.php';
    // });
 
    // ------------------------------------------------------------------
    // DESPACHO
    // ------------------------------------------------------------------
    // Route::prefix('despacho')->name('despacho.')->group(function () {
    //     require __DIR__.'/modules/despacho.php';
    // });
 
    // ------------------------------------------------------------------
    // FACTURACIÓN
    // ------------------------------------------------------------------
    // Route::prefix('facturacion')->name('facturacion.')->group(function () {
    //     require __DIR__.'/modules/facturacion.php';
    // });
 
    // ------------------------------------------------------------------
    // AUDITOR (solo lectura)
    // ------------------------------------------------------------------
    // Route::prefix('auditor')->name('auditor.')->group(function () {
    //     require __DIR__.'/modules/auditor.php';
    // });
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
 
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
 
    // Usuarios
    Route::resource('usuarios', UsuarioController::class);
 
    // Instituciones
    Route::resource('instituciones', InstitucionController::class);
    Route::post('usuarios/{usuario}/reset-password', [UsuarioController::class, 'resetPassword'])->name('usuarios.reset-password');
 
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
