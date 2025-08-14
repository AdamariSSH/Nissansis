<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehiculo;
use App\Models\Almacen; // <-- Importar el modelo de almacén
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Writer;

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

    // Filtrar por Almacén (nombre correcto en la BD)
    if ($request->filled('almacen_id')) {
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
