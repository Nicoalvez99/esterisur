<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\InstitucionController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\ProtocoloController;
use App\Http\Controllers\FicharController;
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/fichar', [FicharController::class, 'index']);
Route::post('/fichar', [FicharController::class, 'store'])->name('fichar.store');

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
    Route::prefix('calidad')->name('calidad.')->group(function () {
       require __DIR__.'/modules/calidad.php';
    });
 
    // ------------------------------------------------------------------
    // DESPACHO
    // ------------------------------------------------------------------
    Route::prefix('despacho')->name('despacho.')->group(function () {
        require __DIR__.'/modules/despacho.php';
    });
 
    // ------------------------------------------------------------------
    // FACTURACIÓN
    // ------------------------------------------------------------------
     Route::prefix('facturacion')->name('facturacion.')->group(function () {
         require __DIR__.'/modules/facturacion.php';
     });
<<<<<<< HEAD


     Route::prefix('reportes')->name('reportes.')->group(function () {
        require __DIR__.'/modules/reportes.php';
     });
=======
>>>>>>> 094ab2b00cae04dbe1123501368046a12e0c7e81
 
    // ------------------------------------------------------------------
    // AUDITOR (solo lectura)
    // ------------------------------------------------------------------
<<<<<<< HEAD
    Route::prefix('auditor')->name('auditor.')->group(function () {
        require __DIR__.'/modules/auditor.php';
     });
=======
    // Route::prefix('auditor')->name('auditor.')->group(function () {
    //     require __DIR__.'/modules/auditor.php';
    // });
>>>>>>> 094ab2b00cae04dbe1123501368046a12e0c7e81
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
 
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
 
    // Usuarios
    Route::resource('usuarios', UsuarioController::class);
 
    // Instituciones
    Route::resource('instituciones', InstitucionController::class);
    Route::post('usuarios/{usuario}/reset-password', [UsuarioController::class, 'resetPassword'])->name('usuarios.reset-password');
    
    // Equipos
    Route::resource('equipos', EquipoController::class);
    
    // Protocolos
    Route::resource('protocolos', ProtocoloController::class);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
