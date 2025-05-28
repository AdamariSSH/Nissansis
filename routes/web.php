<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Entradas;
use App\Http\Controllers\Vehiculoscontroller;
use App\Http\Controllers\Almacencontroller;
use App\Models\Entrada;

Auth::routes();
// Route::get('/', function () {
//     return view('login');
// });


Route::get('/', function () {
    return view('auth.login'); // Laravel buscará en resources/views/auth/login.blade.php
});


//-------------------------------------------------------------------------------------------------\\
//ruta para ir ala pantalla de entradas
Route::group(['prefix' => 'admin', 'middleware' => ['auth']], function() {
    Route::get('/entradas', [Entradas::class, 'index'])->name('admin.entradas');
});

//Ruta para importar entradas con excel
Route::get('/entradas/importar', [Entradas::class, 'mostrarFormularioImportacion'])->name('entradas.importar');
Route::post('/entradas/importar', [Entradas::class, 'importar'])->name('entradas.procesarImportacion');


//Ruta para imprimir entradas//
//Route::get('entradas/{No_orden}/imprimir', [Entradas::class, 'imprimir'])->name('entradas.imprimir');
Route::get('/entradas/entradassimprimir/{No_orden}', [Entradas::class, 'imprimir'])
     ->name('entradasimprimir'); // <--- Usa la clase de tu Controlador aquí, no la del Modelo



// Ruta para mostrar el formulario de creación de entradas (GET)
Route::get('/entradas/create', [Entradas::class, 'create'])->name('entradas.create');
Route::post('/entradas', [Entradas::class, 'store'])->name('entradas.store');



// Ruta para mostrar el formulario de edición
Route::get('/entradas/{No_orden}/edit', [Entradas::class, 'edit'])->name('entradas.edit');

// Ruta para actualizar los datos (después de enviar el formulario de edición)
Route::put('/entradas/{No_orden}', [Entradas::class, 'update'])->name('entradas.update');

//Ruta para eliminar entradas


Route::delete('/entradas/{No_orden}', [Entradas::class, 'destroy'])->name('entradas.eliminar');












//------------------------------------------------------------------------------------------------\\
//ruta para ir ala pantalla de view vehiculos



Route::group(['prefix' => 'admin', 'middleware' => ['auth']], function() {
    // Ruta para listar vehículos
    Route::get('/vehiculos', [VehiculosController::class, 'index'])
         ->name('admin.vehiculos');
           // Ruta para imprimir un vehículo específico

     Route::get('/vehiculos/vehiculosimprimir/{No_orden}', [VehiculosController::class, 'imprimir'])
     ->name('vehiculosimprimir');

       

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



