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

// Confirmar entrada
Route::put('/entradas/{id}/confirmar', [Entradas::class, 'confirmar'])->name('entradas.confirmar');

    // Rechazar entrada
 Route::put('/entradas/{id}/rechazar', [Entradas::class, 'rechazar'])->name('entradas.rechazar');



//Ruta para eliminar entradAS

Route::delete('/vehiculos/{vin}', [VehiculosController::class, 'destroy'])->name('vehiculos.destroy');














//------------------------------------------------------------------------------------------------\\
//ruta para ir ala pantalla de view vehiculos



// Route::group(['prefix' => 'admin', 'middleware' => ['auth']], function() {
//     // Ruta para listar vehículos
//     Route::get('/vehiculos', [VehiculosController::class, 'index'])
//          ->name('admin.vehiculos');
//            // Ruta para imprimir un vehículo específico

//      Route::get('/vehiculos/vehiculosimprimir/{No_orden}', [VehiculosController::class, 'imprimir'])
//      ->name('vehiculosimprimir');



//         // NEW: Ruta para crear una nueva entrada (vehículo, assuming 'entradas' relates to vehicle entries)
//         Route::get('/entradas/create', [Entradas::class, 'create'])
//         ->name('admin.entradas.create'); // Nota el prefijo 'admin.'
//      Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

//          // Ruta para mostrar el formulario de salidas (blade en blanco)
//     Route::get('/salidas/form', [App\Http\Controllers\SalidaController::class, 'form'])
//          ->name('salidas.form');
 
//        });


Route::group(['prefix' => 'admin', 'middleware' => ['auth']], function() {
    // Ruta para listar vehículos
    Route::get('/vehiculos', [VehiculosController::class, 'index'])
         ->name('admin.vehiculos');

    // Ruta para imprimir un vehículo específico
    Route::get('/vehiculos/vehiculosimprimir/{No_orden}', [VehiculosController::class, 'imprimir'])
         ->name('vehiculosimprimir');

    // Ruta para crear una nueva entrada
    Route::get('/entradas/create', [Entradas::class, 'create'])
         ->name('admin.entradas.create');

    Route::get('/vehiculos/{vin}/edit', [VehiculosController::class, 'edit'])
     ->name('vehiculos.edit');

Route::put('/vehiculos/{vin}', [VehiculosController::class, 'update'])
     ->name('vehiculos.update');


    // Ruta home
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])
         ->name('home');

    // Ruta para mostrar el formulario de salidas (blade en blanco)
//    Route::get('/salidas/form', [SalidaController::class, 'form'])->name('salidas.form');
     // Mostrar formulario de salida (pasando el VIN opcionalmente)
// web.php
// web.php (dentro del grupo /admin)
// Formulario de salida SIN necesidad de VIN en la URL
// Formulario de salida SIN necesidad de VIN en la URL
//   Route::get('/salidas/create', [SalidaController::class, 'create'])->name('salidas.create');
//     Route::post('/salidas/store', [SalidaController::class, 'store'])->name('salidas.store');
//     Route::get('/salidas/vehiculo/{vin}', [SalidaController::class, 'getVehiculoData']);

//     Route::get('/salidas/create', [SalidaController::class, 'create'])->name('salidas.create');
//     Route::post('/salidas/store', [SalidaController::class, 'store'])->name('salidas.store');
//     // Ruta para buscar datos de vehículo por VIN
// Route::get('/salidas/vehiculo/{vin}', [SalidaController::class, 'getVehiculoData'])->name('salidas.vehiculo');



// Listado de salidas
Route::get('/salidas', [SalidaController::class, 'index'])->name('salidas.index');

// Formulario de crear salida
Route::get('/salidas/create', [SalidaController::class, 'create'])->name('salidas.create');

// Guardar salida
Route::post('/salidas/store', [SalidaController::class, 'store'])->name('salidas.store');

// Endpoint AJAX para obtener info del vehículo por VIN
Route::get('/salidas/vehiculo/{vin}', [SalidaController::class, 'getVehiculoData'])->name('salidas.vehiculo');

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





// // Ruta protegida con autenticación
// Route::group(['prefix' => 'admin', 'middleware' => ['auth']], function() {
//     Route::get('/salidas', [SalidaController::class, 'index'])->name('admin.salidas');
//     Route::post('/salidas', [SalidaController::class, 'store'])->name('salidas.store');

  
// });





///////checklist Controller 

// Rutas para checklist
Route::get('/checklist/{tipo}', [ChecklistController::class, 'getChecklist']);
