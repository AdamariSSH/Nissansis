<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entrada; 
use Maatwebsite\Excel\Facades\Excel; // Importa la fachada de Excel
use App\Imports\EntradasImport; // Importa la clase de importación
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Facades\Redirect;
use App\Models\Almacen;


use BaconQrCode\Renderer\Image\SvgImageBackEnd; //muestra el qr
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Writer;



class Entradas extends Controller
{
    public function index()
    {
        $entradas = Entrada::all();
        $entradas = Entrada::with(['almacenEntrada', 'almacenSalida'])->get();
        return view('entradas', compact('entradas')); // Ahora apunta a resources/views/entradas.blade.php
    }

    public function mostrarFormularioImportacion()
    {
    return view('entradasimportar'); 
    }


    //Funcion para importar
    public function importar(Request $request)
    {
    $request->validate([
        'archivo' => 'required|mimes:xlsx,csv',
    ]);

    try {
        Excel::import(new EntradasImport, $request->file('archivo'));

        return redirect()->route('admin.entradas')->with('success', '¡Entradas importadas exitosamente!');
    } catch (\Maatwebsite\Excel\Exceptions\NoTypeDetectedException $e) {
        return back()->withErrors(['archivo' => 'El formato del archivo no es válido.']);
    } catch (\Exception $e) {
        Log::error('Error durante la importación: ' . $e->getMessage());
        return back()->withErrors(['importar_error' => 'Ocurrió un error durante la importación. Por favor, revisa el archivo e intenta nuevamente.']);
    }
    }
    //Funcion para crear
    public function create()
    {
        return view('entradascreate'); 
    }
    //Este método recibe una instancia de Request, que contiene todos los datos enviados por el formulario.
    public function store(Request $request)
{
    // Validar los datos del formulario
    $request->validate([
        'VIN' => 'nullable|string|max:17|unique:entradas,VIN',
        'Motor' => 'required|string|max:50',
        'Version' => 'required|string|max:20',
        'Color' => 'required|string|max:30',
        'Modelo' => 'required|string|max:100',
        'Almacen_entrada' => 'nullable|integer',
        'Almacen_salida' => 'nullable|integer',
        'Fecha_entrada' => 'nullable|date',
        'Estado' => 'nullable|string|max:100',
        'Tipo' => 'nullable|string|max:50',
        'Coordinador_Logistica' => 'nullable|string|max:255', // Agrega la validación para el nuevo campo
    ]);

    // Crear una nueva instancia del modelo Entrada y asignar los valores del formulario
    $entrada = new Entrada();
    $entrada->VIN = $request->VIN;
    $entrada->Motor = $request->Motor;
    $entrada->Version = $request->Version;
    $entrada->Color = $request->Color;
    $entrada->Modelo = $request->Modelo;
    $entrada->Almacen_entrada = $request->Almacen_entrada;
    $entrada->Almacen_salida = $request->Almacen_salida;
    $entrada->Fecha_entrada = $request->Fecha_entrada;
    $entrada->Estado = $request->Estado;
    $entrada->Tipo = $request->Tipo;
    $entrada->Coordinador_Logistica = $request->Coordinador_Logistica; // Asigna el valor del nuevo campo

    // Guardar la nueva entrada en la base de datos
    $entrada->save();

    // Redirigir al usuario a la lista de entradas con un mensaje de éxito
    return Redirect::route('admin.entradas')->with('success', 'Entrada creada exitosamente.');
    //return redirect()->route('entradas.imprimir', ['id' => $entrada->id]);
}






public function edit($No_orden)
{
    $entrada = Entrada::where('No_orden', $No_orden)->firstOrFail();
    $almacenes = Almacen::all();
    return view('entradaseditar', compact('entrada', 'almacenes'));
}

public function update(Request $request, $No_orden)
{
    $entrada = Entrada::where('No_orden', $No_orden)->firstOrFail();
    $entrada->update($request->all());

        return redirect()->route('admin.entradas')->with('status', 'Entrada actualizada correctamente.');

}



//entradas eliminar 
public function destroy($No_orden)
{
    $entrada = Entrada::findOrFail($No_orden);
    $entrada->delete();

    return redirect()->route('admin.entradas')->with('success', 'Entrada eliminada correctamente.');
}

// public function imprimir($No_orden) {
//     $entradas = Entrada::where('No_orden', $No_orden)->firstOrFail();

//     // Configuración del renderizador (corregido)
//     $renderer = new ImageRenderer(
//         new \BaconQrCode\Renderer\RendererStyle\RendererStyle(400), // Tamaño
//         new SvgImageBackEnd() // Paréntesis cerrado correctamente
//     );

//     $writer = new Writer($renderer);
//     $qrImage = $writer->writeString($entradas->VIN); // Genera el QR como SVG

//     // Verificación del QR (corregido)
//     if (empty($qrImage)) { // Paréntesis cerrado
//         abort(500, "No se pudo generar el QR con BaconQrCode."); // Código HTTP 500
//     }

//     return view('entradasimprimir', [
//         'entrada' => $entradas,
//         'qrBase64' => base64_encode($qrImage), // Codifica a base64
//     ]);
// }

public function imprimir($No_orden)
    {
        $entradas = Entrada::where('No_orden', $No_orden)->firstOrFail();

        // Configuración del renderizador
        $renderer = new ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(400),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        $qrImage = $writer->writeString($entradas->VIN);

        if (empty($qrImage)) {
            abort(500, "No se pudo generar el QR con BaconQrCode.");
        }

        return view('entradasimprimir', [
            'entrada' => $entradas,
            'qrBase64' => base64_encode($qrImage),
        ]);
    }

}
