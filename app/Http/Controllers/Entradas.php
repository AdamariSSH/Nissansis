<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Entrada; 
use Maatwebsite\Excel\Facades\Excel; // Importa la fachada de Excel
use App\Imports\EntradasImport; // Importa la clase de importación
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Facades\Redirect;
use App\Models\Almacen;
use App\Models\Checklist;
use App\Models\Vehiculo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use BaconQrCode\Renderer\Image\SvgImageBackEnd; //muestra el qr
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Writer;



class Entradas extends Controller
{
    public function index()
    {
        $entradas = Entrada::all();
        $entradas = Entrada::with(['almacenEntrada', 'almacenSalida'])->get();
        return view('entradas', compact('entradas')); 
    }

    public function mostrarFormularioImportacion()
    {
    return view('entradasimportar'); 
    }


    public function importar(Request $request)
    {
     $request->validate([
        'archivo' => 'required|mimes:xlsx,csv|max:2048',
         ]);

            try {
                Excel::import(new EntradasImport, $request->file('archivo'));

                return redirect()->route('admin.entradas')
                                ->with('success', 'Entradas importadas correctamente.');
            } catch (\Exception $e) {
                return back()->withErrors(['error' => 'Error al importar: ' . $e->getMessage()]);
            }
    }
   

    public function create()
{
    $almacenes = Almacen::all();
    //(dd($almacenes);
    return view('entradascreate', compact('almacenes'));
}
public function store(Request $request)
{
    // Validar todo
    $validated = $request->validate([
        // Datos del vehículo
        'VIN' => 'required|string|max:17',
        'Motor' => 'required|string|max:17',
        'Caracteristicas' => 'required|string|max:255',
        'Color' => 'required|string|max:50',
        'Modelo' => 'required|string|max:50',

        // Datos de la entrada
       // 'Coordinador_Logistica' => 'nullable|string|max:50',
        'Kilometraje_entrada' => 'nullable|numeric|min:0',
        'Almacen_entrada' => 'required|exists:almacen,Id_Almacen',
        'Fecha_entrada' => 'required|date',

        'Tipo' => 'required|in:Madrina,Traspaso',

        // Datos del checklist
        'documentos_completos' => 'nullable|boolean',
        'accesorios_completos' => 'nullable|boolean',
        'estado_exterior' => 'nullable|in:Excelente,Bueno,Regular,Malo',
        'estado_interior' => 'nullable|in:Excelente,Bueno,Regular,Malo',
        'pdi_realizada' => 'nullable|boolean',
        'seguro_vigente' => 'nullable|boolean',
        'nfc_instalado' => 'nullable|boolean',
        'gps_instalado' => 'nullable|boolean',
        'folder_viajero' => 'nullable|boolean',
        'recibido_por' => 'nullable|string|max:50',
        'fecha_revision' => 'nullable|date',
        'observaciones_checklist' => 'nullable|string',
        'Observaciones' => 'nullable|string',
    ]);

       // Calcular próximo mantenimiento
    $proximoMantenimiento = Carbon::parse($validated['Fecha_entrada'])->addDays(30)->toDateString();
        //
    
    // Agrega el nombre del usuario logueado
    $validated['Coordinador_Logistica'] = Auth::user()->name;

   
        $vehiculo = \App\Models\Vehiculo::updateOrCreate(
           ['VIN' => $validated['VIN']],

        [
            'Motor' => $validated['Motor'],
            'Caracteristicas' => $validated['Caracteristicas'],
            'Color' => $validated['Color'],
            'Modelo' => $validated['Modelo'],
            'Coordinador_Logistica' => $validated['Coordinador_Logistica'] ?? null,
            'Proximo_mantenimiento' => $proximoMantenimiento,
            'Almacen_actual' => $validated['Almacen_entrada'], // opcional

        ]
    );
    // Aquí actualizamos el estado del vehículo
$vehiculo->Estado = $request->Estado ?? 'Mantenimiento';
$vehiculo->save();
    

    
    // Crear la entrada
    $entrada = \App\Models\Entrada::create([
        'VIN' => $vehiculo->VIN,
        'Kilometraje_entrada' => $validated['Kilometraje_entrada'],
        'Almacen_entrada' => $validated['Almacen_entrada'],
        'Fecha_entrada' => $validated['Fecha_entrada'],
        'Tipo' => $validated['Tipo'],
        'Observaciones' => $validated['Observaciones'],
        'Coordinador_Logistica' => $validated['Coordinador_Logistica'], 

    ]);

    // Crear el checklist vinculado a esta entrada
    \App\Models\Checklist::create([
        'No_orden_entrada' => $entrada->No_orden,
        'tipo_checklist' => $validated['Tipo'],
        'documentos_completos' => $request->has('documentos_completos'),
        'accesorios_completos' => $request->has('accesorios_completos'),
        'estado_exterior' => $validated['estado_exterior'] ?? null,
        'estado_interior' => $validated['estado_interior'] ?? null,
        'pdi_realizada' => $request->has('pdi_realizada'),
        'seguro_vigente' => $request->has('seguro_vigente'),
        'nfc_instalado' => $request->has('nfc_instalado'),
        'gps_instalado' => $request->has('gps_instalado'),
        'folder_viajero' => $request->has('folder_viajero'),
        'recibido_por' => $validated['recibido_por'] ?? null,
        'fecha_revision' => $validated['fecha_revision'] ?? null,
        'observaciones' => $validated['observaciones_checklist'] ?? null,
    ]);

    return redirect()->route('admin.entradas')->with('success', 'Entrada y checklist creados correctamente.');
}


public function imprimirOrden($id)
{
    $entrada = Entrada::with(['checklist', 'vehiculo', 'almacenEntrada'])->findOrFail($id);

    $renderer = new ImageRenderer(
        new \BaconQrCode\Renderer\RendererStyle\RendererStyle(180),
        new SvgImageBackEnd()
    );
    $writer = new Writer($renderer);
    $qrSvg = $writer->writeString($entrada->VIN);
    $qrBase64 = base64_encode($qrSvg);

    return view('entradasimprimir', compact('entrada', 'qrBase64'));
}


// public function edit($id)
// {
//     $entrada = Entrada::findOrFail($id);
//     $almacenes = Almacen::all();

//     // Obtén checklist asociado o vacíos para mostrar
//     $checklist = $entrada->checklist ?? null;
//     return view('entradasedit', compact('entrada', 'almacenes', 'checklist'));


// }
public function edit($id)
{
    $entrada = Entrada::with('checklist')->findOrFail($id);
    $almacenes = Almacen::all();
    $checklist = $entrada->checklist; // esto será null o el objeto Checklist
     $vehiculos = Vehiculo::all(); // 👈 Agrega esta línea


    return view('entradasedit', compact('entrada', 'almacenes', 'checklist', 'vehiculos'));
}
// public function update(Request $request, $id)
// {
//     $entrada = Entrada::findOrFail($id);

//     $request->validate([
//         'Motor' => 'required|max:17',
//         'Caracteristicas' => 'required|string',
//         'Color' => 'required|string',
//         'Modelo' => 'required|string',
//         'Kilometraje_entrada' => 'nullable|integer',
//         'Almacen_entrada' => 'required|exists:almacen,Id_Almacen',
//         'Fecha_entrada' => 'nullable|date',
//         'Tipo' => 'required|string',
//         'Estado' => 'required|string',
//         'documentos_completos' => 'nullable|boolean',
//         'accesorios_completos' => 'nullable|boolean',
//         'estado_exterior' => 'nullable|string',
//         'estado_interior' => 'nullable|string',
//         'pdi_realizada' => 'nullable|boolean',
//         'seguro_vigente' => 'nullable|boolean',
//         'nfc_instalado' => 'nullable|boolean',
//         'gps_instalado' => 'nullable|boolean',
//         'folder_viajero' => 'nullable|boolean',
//         'observaciones' => 'nullable|string',
//         'recibido_por' => 'nullable|string|max:255',
//         'fecha_revision' => 'nullable|date',
//     ]);

//     // Guardar datos de entrada
//     $entrada->update($request->only([
//         'Kilometraje_entrada',
//         'Almacen_entrada',
//         'Fecha_entrada',
//         'Tipo',
//         'Estado',
//         'observaciones',
//     ]));

//     // Actualizar vehículo
//     $vehiculo = Vehiculo::findOrFail($entrada->VIN);
//     $vehiculo->update([
//         'Motor' => $request->Motor,
//         'Caracteristicas' => $request->Caracteristicas,
//         'Color' => $request->Color,
//         'Modelo' => $request->Modelo,
//         'Estado' => $request->Estado,
//     ]);

//     // Actualizar checklist
//     $entrada->checklist()->updateOrCreate(
//         ['No_orden_entrada' => $entrada->No_orden],
//         $request->only([
//             'documentos_completos',
//             'accesorios_completos',
//             'estado_exterior',
//             'estado_interior',
//             'pdi_realizada',
//             'seguro_vigente',
//             'nfc_instalado',
//             'gps_instalado',
//             'folder_viajero',
//             'observaciones',
//             'recibido_por',
//             'fecha_revision',
//         ])
//     );

//     return redirect()->route('admin.vehiculos')->with('success', 'Entrada actualizada correctamente');
// }

public function update(Request $request, $id)
{
    $entrada = Entrada::findOrFail($id);

    $data = $request->validate([
        // 'VIN' => 'required|max:17', // Ya no validamos ni actualizamos VIN
        'Motor' => 'required|max:17',
        'Caracteristicas' => 'required|string',
        'Color' => 'required|string',
        'Modelo' => 'required|string',
        'Kilometraje_entrada' => 'nullable|integer',
        'Almacen_entrada' => 'required|exists:almacen,Id_Almacen',
        'Fecha_entrada' => 'nullable|date',
        'Tipo' => 'required|string',
        'Estado' => 'required|string',
        'documentos_completos' => 'nullable|boolean',
        'accesorios_completos' => 'nullable|boolean',
        'estado_exterior' => 'nullable|string',
        'estado_interior' => 'nullable|string',
        'pdi_realizada' => 'nullable|boolean',
        'seguro_vigente' => 'nullable|boolean',
        'nfc_instalado' => 'nullable|boolean',
        'gps_instalado' => 'nullable|boolean',
        'folder_viajero' => 'nullable|boolean',
        'observaciones' => 'nullable|string',
        'recibido_por' => 'nullable|string|max:255',
        'fecha_revision' => 'nullable|date',
    ]);

    // Actualizar solo campos permitidos en entrada excepto VIN
    $entrada->update($data);

    // Actualizar vehículo con el VIN original (sin cambiar VIN)
    $vehiculo = Vehiculo::findOrFail($entrada->VIN);

    $vehiculo->update([
        // No actualizar VIN
        'Motor' => $request->input('Motor'),
        'Caracteristicas' => $request->input('Caracteristicas'),
        'Color' => $request->input('Color'),
        'Modelo' => $request->input('Modelo'),
        'Estado' => $request->input('Estado'), // ✅ línea nueva

    ]);

    // Actualizar o crear checklist igual que antes
    $entrada->checklist()->updateOrCreate(
        ['No_orden_entrada' => $entrada->No_orden],
        $request->only([
            'documentos_completos',
            'accesorios_completos',
            'estado_exterior',
            'estado_interior',
            'pdi_realizada',
            'seguro_vigente',
            'nfc_instalado',
            'gps_instalado',
            'folder_viajero',
            'observaciones',
            'recibido_por',
            'fecha_revision',
        ])
    );

    return redirect()->route('admin.vehiculos')->with('success', 'Entrada actualizada correctamente');
}

public function destroy($id)
{
    $entrada = Entrada::findOrFail($id);

    // Paso 1: Eliminar checklist relacionado si existe
    if ($entrada->checklist) {
        $entrada->checklist->delete();
    }

    // Paso 2: Eliminar la entrada
    $entrada->delete();

    return redirect()->back()->with('success', 'Entrada eliminada correctamente');
}



}