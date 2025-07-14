<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehiculos;
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
    $query = Vehiculos::with('almacen');

    if ($request->filled('vin')) {
        $query->whereRaw('RIGHT(VIN, 10) LIKE ?', ['%' . $request->vin . '%']);
    }

    if ($request->filled('estado')) {
        $query->where('Estado', $request->estado);
    }

    if ($request->filled('almacen_id')) {
        $query->where('almacen_id', $request->almacen_id);
    }

    $vehiculos = $query->paginate(10)->appends($request->all());

    $almacenes = Almacen::all();

    return view('vehiculos', compact('vehiculos', 'almacenes'));
}


    public function imprimir($No_orden)
    {
        $vehiculo = Vehiculos::where('No_orden', $No_orden)->firstOrFail();

        // Configuración del renderizador (corregido)
        $renderer = new ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(400),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        $qrImage = $writer->writeString($vehiculo->VIN); // Genera el QR como SVG

        if (empty($qrImage)) {
            abort(500, "No se pudo generar el QR con BaconQrCode.");
        }

        return view('vehiculosimprimir', [
            'vehiculo' => $vehiculo,
            'qrBase64' => base64_encode($qrImage), // Codifica a base64
        ]);
    }
}
