<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Entradas;
use App\Http\Controllers\VehiculosController;
use App\Http\Controllers\AlmacenController;
use App\Http\Controllers\SalidaController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\RoleController;

// ----------------------------------------------------------------------
// 1. RUTAS DE AUTENTICACIÓN Y PÁGINA PRINCIPAL
// ----------------------------------------------------------------------

Auth::routes();

// Ruta de inicio (landing)
Route::get('/', function () {
    return view('auth.login'); 
});

// Autenticación de Google
Route::get('/auth/google', [LoginController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/auth/google/callback', [LoginController::class, 'handleGoogleCallback']);

// Ruta HOME principal después de iniciar sesión
Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware('auth');

// Rutas de búsqueda (accesibles si estás autenticado)
Route::get('/buscar-vin', [VehiculosController::class, 'buscarVin'])->name('buscar.vin')->middleware('auth');

// Rutas para checklist (accesibles si estás autenticado)
Route::get('/checklist/{tipo}', [ChecklistController::class, 'getChecklist'])->middleware('auth');


// ----------------------------------------------------------------------
// 2. GRUPO DE RUTAS DE USUARIO GENERAL (Solo requieren 'auth')
//    Incluye Entradas, Salidas y Vehículos (operación diaria)
// ----------------------------------------------------------------------

Route::group(['middleware' => ['auth']], function() {
    
    // --- Rutas de ENTRADAS ---
    // DEFINICIÓN ÚNICA: Aseguramos que solo haya una definición para esta URL y nombre.
    Route::get('/entradas', [Entradas::class, 'index'])->name('entradas.index');
    
    Route::get('/entradas/create', [Entradas::class, 'create'])->name('entradas.create');
    Route::post('/entradas', [Entradas::class, 'store'])->name('entradas.store');
    Route::get('/entradas/importar', [Entradas::class, 'mostrarFormularioImportacion'])->name('entradas.importar');
    Route::post('/entradas/importar', [Entradas::class, 'importar'])->name('entradas.procesarImportacion');
    Route::get('/ordenes/entradas/imprimir/{id}', [Entradas::class, 'imprimirOrden'])->name('entradasimprimir');
    Route::get('/entradas/{entrada}/edit', [Entradas::class, 'edit'])->name('entradas.edit');
    Route::put('/entradas/{entrada}', [Entradas::class, 'update'])->name('entradas.update');
    Route::delete('/entradas/{id}', [Entradas::class, 'destroy'])->name('entradas.destroy');
    // Acciones de confirmación (pueden requerir un middleware de autorización más estricto, pero las dejo aquí por ahora)
    Route::put('/entradas/{id}/confirmar', [Entradas::class, 'confirmar'])->name('entradas.confirmar');
    Route::put('/entradas/{id}/rechazar', [Entradas::class, 'rechazar'])->name('entradas.rechazar');

    // --- Rutas de SALIDAS ---
     // --- Rutas de SALIDAS (Rutas Generales: /salidas) ---
    Route::get('/salidas', [SalidaController::class, 'index'])->name('salidas.index');
    Route::get('/salidas/create', [SalidaController::class, 'create'])->name('salidas.create');
    Route::post('/salidas/store', [SalidaController::class, 'store'])->name('salidas.store');
    Route::get('/ordenes/salidas/imprimir/{id}', [SalidaController::class, 'imprimirOrdenSalida'])->name('salidasimprimir');
    Route::get('/salidas/vehiculo/{vin}', [SalidaController::class, 'getVehiculoData'])->name('salidas.vehiculo');




    // --- Rutas de VEHÍCULOS ---
    Route::get('/vehiculos', [VehiculosController::class, 'index'])->name('vehiculos.index'); 
    Route::get('/vehiculos/{vin}/imprimir', [VehiculosController::class, 'ImprimirVehiculo'])->name('vehiculoimprimir');
    Route::delete('/vehiculos/{vin}', [VehiculosController::class, 'destroy'])->name('vehiculos.destroy');
    Route::get('/vehiculos/{vin}/edit', [VehiculosController::class, 'edit'])->name('vehiculos.edit');
    Route::put('/vehiculos/{vin}', [VehiculosController::class, 'update'])->name('vehiculos.update');
    Route::get('/vehiculos/{vin}/historial/imprimir', [VehiculosController::class, 'imprimirHistorial'])->name('vehiculos.imprimirHistorial');

});


Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'role:admin']], function () {
    
    // Rutas del CRUD de Usuarios
    Route::resource('usuarios', UserController::class);

    // RUTAS PARA ROLES Y PERMISOS (Nueva Ruta)

    Route::resource('roles', RoleController::class)->names('admin.roles');
  
    
    
    // Rutas del CRUD de Almacenes (MOVIDAS AQUÍ PARA PROTEGERLAS)
    // DEFINICIÓN ÚNICA: Aseguramos que solo haya una definición para esta URL y nombre.
    Route::get('/almacen', [AlmacenController::class, 'index'])->name('almacen.index');
    
    Route::get('/almacen/create', [AlmacenController::class, 'create'])->name('almacen.create');
    Route::post('/almacen', [AlmacenController::class, 'store'])->name('almacen.store');
    Route::delete('/almacen/{almacen}', [AlmacenController::class, 'destroy'])->name('almacen.destroy');
    Route::get('/almacen/{id}/editar', [AlmacenController::class, 'edit'])->name('almacen.edit'); 
    Route::put('/almacen/{id}', [AlmacenController::class, 'update'])->name('almacen.update');

    // Ruta Raíz del Panel de Administración (ejemplo)
    Route::get('/', function () {
        return view('admin.dashboard'); 
    })->name('admin.dashboard');


});

