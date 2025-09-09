<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehiculo;
use App\Models\Almacen; // <-- Importar el modelo de almacén
use Illuminate\Support\Facades\Auth;



class VehiculosController extends Controller
{
   public function index(Request $request)
    {
        $query = Vehiculo::with(['almacen', 'ultimaEntrada']);

        // Filtrar por VIN
        if ($request->filled('vin')) {
            $query->where('VIN', 'like', '%' . $request->vin . '%');
        }

        // Filtrar por Estado
        if ($request->filled('estado')) {
            $query->where('Estado', $request->estado);
        }

        // Si el usuario NO es admin, limitar por su almacén
        if (!Auth::user()->isAdmin()) {
            $query->where('Almacen_actual', Auth::user()->almacen_id);
    //       
        }

        // Si el usuario es admin, puede elegir almacén desde el request
        if ($request->filled('almacen_id') && Auth::user()->isAdmin()) {
            $query->where('Almacen_actual', $request->almacen_id);
        }

        $vehiculos = $query->paginate(10)->appends($request->all());
        $almacenes = Almacen::all();

        return view('Vehiculos', compact('vehiculos', 'almacenes'));
    }

    public function destroy($vin)
    {
        $vehiculo = Vehiculo::with('entradas.checklist')->findOrFail($vin);

        // Eliminar entradas y sus checklists
        foreach ($vehiculo->entradas as $entrada) {
            if ($entrada->checklist) {
                $entrada->checklist->delete();
            }
            $entrada->delete();
        }

        // Finalmente eliminar el vehículo
        $vehiculo->delete();

        return redirect()->route('admin.vehiculos')->with('success', 'Vehículo eliminado correctamente');
    }

}
