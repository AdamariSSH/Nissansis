<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Entradas;
use App\Http\Controllers\VehiculosController;
use App\Http\Controllers\AlmacenController;
use App\Http\Controllers\SalidaController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ChecklistController;
use App\Imports\EntradasImport;
use App\Models\Entrada;
use App\Models\Vehiculos;
Auth::routes();
// Route::get('/', function () {
//     return view('login');
// });


Route::get('/', function () {
    return view('auth.login'); // Laravel buscará en resources/views/auth/login.blade.php
});


Route::get('/auth/google', [LoginController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/auth/google/callback', [LoginController::class, 'handleGoogleCallback']);


//-------------------------------------------------------------------------------------------------\\
//ruta para ir ala pantalla de entradas
Route::group(['prefix' => 'admin', 'middleware' => ['auth']], function() {
    Route::get('/entradas', [Entradas::class, 'index'])->name('admin.entradas');
});

//Ruta para importar entradas con excel
Route::get('/entradas/importar', [Entradas::class, 'mostrarFormularioImportacion'])->name('entradas.importar');
Route::post('/entradas/importar', [Entradas::class, 'importar'])->name('entradas.procesarImportacion');


//Ruta para imprimir ordenes de vehiculos //
Route::get('/entradas/imprimir/{id}', [Entradas::class, 'imprimirOrden'])->name('entradasimprimir');



// Ruta para mostrar el formulario de creación de entradas (GET)
Route::get('/entradas/create', [Entradas::class, 'create'])->name('entradas.create');
Route::post('/entradas', [Entradas::class, 'store'])->name('entradas.store');


Route::get('/entradas/{entrada}/edit', [Entradas::class, 'edit'])->name('entradas.edit');

Route::put('/entradas/{entrada}', [Entradas::class, 'update'])->name('entradas.update');
Route::delete('/entradas/{id}', [Entradas::class, 'destroy'])->name('entradas.destroy');


//Ruta para eliminar entradas
Route::delete('/vehiculos/{vin}', [VehiculosController::class, 'destroy'])->name('vehiculos.destroy');














//------------------------------------------------------------------------------------------------\\
//ruta para ir ala pantalla de view vehiculos



Route::group(['prefix' => 'admin', 'middleware' => ['auth']], function() {
    // Ruta para listar vehículos
    Route::get('/vehiculos', [VehiculosController::class, 'index'])
         ->name('admin.vehiculos');
           // Ruta para imprimir un vehículo específico

     Route::get('/vehiculos/vehiculosimprimir/{No_orden}', [VehiculosController::class, 'imprimir'])
     ->name('vehiculosimprimir');

        // NEW: Ruta para crear una nueva entrada (vehículo, assuming 'entradas' relates to vehicle entries)
        Route::get('/entradas/create', [Entradas::class, 'create'])
        ->name('admin.entradas.create'); // Nota el prefijo 'admin.'
     Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


       });


//-------------------------------------------------------------------------------------------------\\
//rutas para crear lamacenes 
Route::get('/almacen', [AlmacenController::class, 'index'])->name('almacen');
Route::get('/almacen/create', [AlmacenController::class, 'create'])->name('almacen.create');
Route::post('/almacen', [AlmacenController::class, 'store'])->name('almacen.store');


//ruta par eliminar el almacen 
Route::delete('/almacen/{almacen}', [AlmacenController::class, 'destroy'])->name('almacen.destroy');

//para editar almacen 
Route::get('/almacen/{id}/editar', [AlmacenController::class, 'edit'])->name('almacen.edit'); // Nueva ruta para editar
Route::put('/almacen/{id}', [AlmacenController::class, 'update'])->name('almacen.update');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');



// //Rutas para ir al blade de salidas
// Route::group(['prefix' => 'admin', 'middleware' => ['auth']], function() {
//     Route::get('/salidas', [SalidaController::class, 'index'])->name('admin.salidas');
// });


// Ruta protegida con autenticación
Route::group(['prefix' => 'admin', 'middleware' => ['auth']], function() {
    Route::get('/salidas', [SalidaController::class, 'index'])->name('admin.salidas');
    Route::post('/salidas', [SalidaController::class, 'store'])->name('salidas.store');

    // // Ruta para obtener datos de un vehículo por VIN (AJAX)
    // Route::get('/vehiculo/{vin}', function ($vin) {
    //     return \App\Models\Vehiculos::where('VIN', $vin)->first();
    // });
});





///////checklist Controller 

// Rutas para checklist
Route::get('/checklist/{tipo}', [ChecklistController::class, 'getChecklist']);
