<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehiculo;
use App\Models\Almacen;
use App\Models\Entrada;
use Illuminate\Support\Facades\Auth;

class VehiculosController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehiculo::with(['almacen', 'ultimaEntrada']);

        if ($request->filled('vin')) {
            $query->where('VIN', 'like', '%' . $request->vin . '%');
        }

        if ($request->filled('estado')) {
            $query->where('Estado', $request->estado);
        }

        if (!Auth::user()->isAdmin()) {
            $query->where('Almacen_actual', Auth::user()->almacen_id);
        }

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

        foreach ($vehiculo->entradas as $entrada) {
            if ($entrada->checklist) {
                $entrada->checklist->delete();
            }
            $entrada->delete();
        }

        $vehiculo->delete();

        return redirect()->route('admin.vehiculos')->with('success', 'Vehículo eliminado correctamente');
    }

    public function edit($vin)
    {
        $vehiculo = Vehiculo::findOrFail($vin);
        $almacenes = Almacen::all();

        // Traer entradas y salidas relacionadas con este vehículo
        $entradas = $vehiculo->entradas()->with('almacenEntrada')->get();
        $salidas = $vehiculo->salidas()->with('almacenSalida')->get();

        //  Pasar entradas y salidas a la vista
        return view('vehiculosedit', compact('vehiculo', 'almacenes', 'entradas', 'salidas'));
    }

    public function update(Request $request, $vin)
    {
        $vehiculo = Vehiculo::findOrFail($vin);

        $request->validate([
            'Motor' => 'required|string|max:50',
            'Caracteristicas' => 'required|string|max:30',
            'Color' => 'required|string|max:30',
            'Modelo' => 'required|string|max:100',
            'Proximo_mantenimiento' => 'nullable|date',
            'Estado' => 'nullable|string|max:50',
            'estatus' => 'required|in:En almacén,En tránsito,Pendiente salida,Vendido,Rechazado',
            'Coordinador_Logistica' => 'nullable|string|max:50',
            'Almacen_actual' => 'nullable|exists:almacen,Id_Almacen',
            'tipo' => 'required|in:Madrina,Traspaso,Devolucion,Otro',
        ]);

        $vehiculo->update($request->except('VIN'));

        return redirect()->route('admin.vehiculos')
                         ->with('success', 'Vehículo actualizado correctamente');
    }

    //  Nuevo método show para poder usar $entradas y $salidas en una vista separada
    public function show($vin)
    {
        $vehiculo = Vehiculo::with(['entradas.almacen', 'salidas.almacenSalida'])->findOrFail($vin);

        $entradas = $vehiculo->entradas;
        $salidas = $vehiculo->salidas;

        return view('vehiculos.show', compact('vehiculo', 'entradas', 'salidas'));
    }
}
