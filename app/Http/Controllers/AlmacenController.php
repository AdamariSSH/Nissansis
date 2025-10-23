<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Almacen;

class AlmacenController extends Controller
{
    public function index()
    {
        $almacenes = Almacen::all();      
        return view('almacen.index', compact('almacenes'));
    }


    public function create()
    {
        return view('almacen.create'); // La vista para el formulario
    }

    public function store(Request $request)
    {
    // Validación de datos
    $validated = $request->validate([
        'Nombre' => 'required|string|max:100', // Corrección: 'varchar' -> 'string'
        'Direccion' => 'required|string|max:100', // Corrección: 'varchar' -> 'string'
    ]);

    // Crear el nuevo almacén
    Almacen::create([
        'Nombre' => $validated['Nombre'],
        'Direccion' => $validated['Direccion'],
    ]);

    // Redirigir a la lista de almacenes
    return redirect()->route('almacen.index')->with('success', 'Almacén creado exitosamente.'); // Asegúrate de que la ruta sea correcta
    }

    //Eliminar el almacen
    public function destroy($id){
        $almacen = Almacen::find($id);
        $almacen->delete();
        return redirect()->route('almacen.index');
    }


    // Mostrar el formulario de edición
    public function edit($id)
    {
        $almacen = Almacen::findOrFail($id); // Busca el almacén por ID o lanza una excepción si no existe
        return view('almacen.edit', compact('almacen')); // Asegúrate de que el nombre de la vista sea correcto
    }
  

    // Actualizar el almacén
    public function update(Request $request, $id)
    {
        // Validación de datos
        $validated = $request->validate([
            'Nombre' => 'required|string|max:100',
            'Direccion' => 'required|string|max:100',
        ]);

        // Encontrar y actualizar el almacén
        $almacen = Almacen::findOrFail($id);
        $almacen->update([
            'Nombre' => $validated['Nombre'],
            'Direccion' => $validated['Direccion'],
        ]);

        // Redirigir a la lista de almacenes con un mensaje de éxito
        return redirect()->route('almacen.index')->with('success', 'Almacén actualizado exitosamente.');
    }


    
}
