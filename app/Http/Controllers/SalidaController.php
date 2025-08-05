<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Salida;
use App\Models\Almacen;
use App\Models\Vehiculos;

class SalidaController extends Controller
{
    public function index()
    {
        $salidas = Salida::all();
        $vehiculos = Vehiculos::all();
        $almacenes = Almacen::all();

        return view('salidas', compact('salidas', 'vehiculos', 'almacenes'));
    }

    public function store(Request $request)
    {
        $vehiculo = vehiculos::where('VIN', $request->VIN)->first();

        if (!$vehiculo) {
            return back()->with('error', 'Vehículo no encontrado');
        }

        salida::create([
            'VIN' => $vehiculo->VIN,
            'Motor' => $vehiculo->Motor,
            'Version' => $vehiculo->Version,
            'Color' => $vehiculo->Color,
            'Modelo' => $vehiculo->Modelo,
            'Tipo_salida' => $request->Tipo_salida,
            'Almacen_salida' => $request->Almacen_salida,
            'Almacen_entrada' => $request->Almacen_entrada,
            'Fecha' => $request->Fecha,
        ]);

        return redirect()->route('admin.salidas')->with('success', 'Salida registrada correctamente');
    }
}
