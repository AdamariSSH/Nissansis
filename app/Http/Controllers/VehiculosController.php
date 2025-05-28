<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehiculos;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;


use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Writer;






class VehiculosController extends Controller
{
    
    public function index(Request $request)
    {
        // Creamos el query base con la relación 'almacen'
        $query = Vehiculos::with('almacen');

        // Aplicamos filtros si vienen en la solicitud
        if ($request->vin) {
            $query->where('VIN', 'like', '%' . $request->vin . '%');
        }

        if ($request->modelo) {
            $query->where('Modelo', 'like', '%' . $request->modelo . '%');
        }

        if ($request->color) {
            $query->where('Color', 'like', '%' . $request->color . '%');
        }

        if ($request->estado) {
            $query->where('Estado', $request->estado);
        }

        // Paginamos los resultados y mantenemos los filtros en la paginación
        $vehiculos = $query->paginate(10)->appends($request->all());

        // Retornamos la vista con los datos
        return view('vehiculos', compact('vehiculos'));
  
}

// public function imprimir($No_orden)
// {
//     // Intenta buscar por No_orden primero
//     $vehiculo = \App\Models\Vehiculos::where('No_orden', $No_orden)->first();

//     // Si no encuentra por No_orden, intenta por ID
//     if (!$vehiculo && is_numeric($No_orden)) {
//         $vehiculo = \App\Models\Vehiculos::find($No_orden);
//     }

//     // Si aún no encuentra, lanza error 404
//     if (!$vehiculo) {
//         abort(404, 'Vehículo no encontrado.');
//     }

//     return view('vehiculosimprimir', compact('vehiculo'));
// }

// public function imprimir($No_orden)
// {
//     $vehiculo = Vehiculos::where('No_orden', $No_orden)->firstOrFail();

//     $options = new QROptions([
//         'outputType' => QRCode::OUTPUT_IMAGE_PNG,
//         'eccLevel'   => QRCode::ECC_L,
//         'scale'      => 5,
//         'margin'     => 2,
//     ]);

//     $qrcode = new QRCode($options);

//     // Generar la imagen PNG en binario
//     $imageData = $qrcode->render($vehiculo->VIN);

//     // Codificar a base64 para insertar en HTML
//     $qrBase64 = base64_encode($imageData);

//     return view('vehiculosimprimir', compact('vehiculo', 'qrBase64'));
// }



// public function imprimir($No_orden) {
//     $vehiculo = Vehiculos::where('No_orden', $No_orden)->firstOrFail();

//     $options = new QROptions([
//         'outputType' => QRCode::OUTPUT_MARKUP_SVG, // Usar SVG es más confiable
//         'scale' => 5,
//     ]);

//     $qrCode = new QRCode($options);
//     $qrImage = $qrCode->render($vehiculo->VIN);

//     // Debug: Verifica si $qrImage es un SVG/PNG válido
//     if (strpos($qrImage, '<svg') === false && strpos($qrImage, 'PNG') === false) {
//         abort(500, "El QR generado no es válido.");
//     }

//     return view('vehiculosimprimir', [
//         'vehiculo' => $vehiculo,
//         'qrBase64' => base64_encode($qrImage),
//     ]);
// }


public function imprimir($No_orden) {
    $vehiculo = Vehiculos::where('No_orden', $No_orden)->firstOrFail();

    // Configuración del renderizador (corregido)
    $renderer = new ImageRenderer(
        new \BaconQrCode\Renderer\RendererStyle\RendererStyle(400), // Tamaño
        new SvgImageBackEnd() // Paréntesis cerrado correctamente
    );

    $writer = new Writer($renderer);
    $qrImage = $writer->writeString($vehiculo->VIN); // Genera el QR como SVG

    // Verificación del QR (corregido)
    if (empty($qrImage)) { // Paréntesis cerrado
        abort(500, "No se pudo generar el QR con BaconQrCode."); // Código HTTP 500
    }

    return view('vehiculosimprimir', [
        'vehiculo' => $vehiculo,
        'qrBase64' => base64_encode($qrImage), // Codifica a base64
    ]);
}
}




