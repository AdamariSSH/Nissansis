<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Entrada; 
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EntradasImport;
use App\Models\Almacen;
use App\Models\Checklist;
use App\Models\Vehiculo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Writer;
use App\Models\Salida;

class Entradas extends Controller
{
        public function index(Request $request)
{
    $user = auth()->user();
    $estatus = $request->get('estatus'); // 👈 aquí recibimos si viene "pendiente"

    $query = Entrada::with('almacen')
                    ->orderBy('Fecha_entrada', 'desc');

    if ($user->role !== 'admin') {
        // Usuario normal solo ve las entradas de su almacén
        $query->where('Almacen_entrada', $user->almacen_id);
    }

    if ($estatus) {
        // Si se pide filtrar por estatus (ej: pendientes)
        $query->where('estatus', $estatus);
    }

    $entradas = $query->paginate(10);

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
        Excel::import(new \App\Imports\EntradasImport, $request->file('archivo'));

        return redirect()->route('admin.vehiculos')
                         ->with('success', 'Entradas importadas correctamente.');
    } catch (\Exception $e) {
        return redirect()->route('admin.vehiculos')
                         ->with('error', 'Importación cancelada: ' . $e->getMessage());
    }
}



    public function create()
    {
        if (Auth::user()->isAdmin()) {
            $almacenes = Almacen::all();
        } else {
            $almacenes = Almacen::where('Id_Almacen', Auth::user()->almacen_id)->get();
        }

        return view('entradascreate', compact('almacenes'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $almacenId = $user->isAdmin()
            ? $request->input('Almacen_entrada')
            : $user->almacen_id;

        $validated = $request->validate([
            'VIN' => 'required|string|max:17',
            'Motor' => 'required|string|max:17',
            'Caracteristicas' => 'required|string|max:255',
            'Color' => 'required|string|max:50',
            'Modelo' => 'required|string|max:50',
            'Kilometraje_entrada' => 'nullable|numeric|min:0',
            'Fecha_entrada' => 'required|date',
            'Tipo' => 'required|in:Madrina,Traspaso',
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

        $proximoMantenimiento = Carbon::parse($validated['Fecha_entrada'])->addDays(30)->toDateString();
        $validated['Coordinador_Logistica'] = $user->name;

        // Crear o actualizar vehículo
        $vehiculo = Vehiculo::updateOrCreate(
            ['VIN' => $validated['VIN']],
            [
                'Motor' => $validated['Motor'],
                'Caracteristicas' => $validated['Caracteristicas'],
                'Color' => $validated['Color'],
                'Modelo' => $validated['Modelo'],
                'Coordinador_Logistica' => $validated['Coordinador_Logistica'],
                'Proximo_mantenimiento' => $proximoMantenimiento,
                'Almacen_actual' => $almacenId,
                'estatus' => 'pendiente salida',// 👈 igual aquí
                 
            ]
        );

        $vehiculo->Estado = $request->Estado ?? 'Mantenimiento';
        $vehiculo->save();

        // Crear entrada
        $entrada = Entrada::create([
            'VIN' => $vehiculo->VIN,
            'Kilometraje_entrada' => $validated['Kilometraje_entrada'],
            'Almacen_entrada' => $almacenId,
            'Fecha_entrada' => $validated['Fecha_entrada'],
            'Tipo' => $validated['Tipo'],
            'Observaciones' => $validated['Observaciones'],
            'Coordinador_Logistica' => $validated['Coordinador_Logistica'],
            'estatus' => 'pendiente', // 👈 igual aquí
        ]);

        // Crear checklist
        Checklist::create([
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

        return redirect()->route('admin.entradas')->with('success', 'Entrada creada correctamente, pendiente de confirmación.');
    }

        public function confirmar($id)
        {
            $entrada = Entrada::findOrFail($id);
            $vehiculo = Vehiculo::findOrFail($entrada->VIN);

            // Actualizar entrada
            $entrada->estatus = 'confirmada';
            $entrada->save();

            // Actualizar salida correspondiente
            $salida = Salida::where('VIN', $entrada->VIN)
                            ->where('estatus', 'pendiente')
                            ->first();
            if ($salida) {
                $salida->estatus = 'confirmada'; //  válido en salidas
                $salida->save();
            }

            // Actualizar vehículo → pasa al almacén destino
            $vehiculo->Almacen_actual = $entrada->Almacen_entrada;
            $vehiculo->estatus = 'En almacén'; //  válido en vehiculos
            $vehiculo->save();

            return redirect()->route('admin.entradas')
                            ->with('success', 'Entrada confirmada y vehículo en almacén destino.');
        }

        public function rechazar($id)
        {
            $entrada = Entrada::findOrFail($id);
            $vehiculo = Vehiculo::findOrFail($entrada->VIN);

            // Actualizar entrada
            $entrada->estatus = 'rechazada';
            $entrada->save();

            // Actualizar salida correspondiente
            $salida = Salida::where('VIN', $entrada->VIN)
                            ->where('estatus', 'pendiente')
                            ->first();

            if ($salida) {
                $salida->estatus = 'rechazada'; //  válido en salidas
                $salida->save();
            }

            // Actualizar vehículo → regresa al almacén origen
            $vehiculo->Almacen_actual = $entrada->Almacen_salida;
            $vehiculo->estatus = 'En almacén'; //  válido en vehiculos
            $vehiculo->save();

            return redirect()->route('admin.entradas')
                            ->with('success', 'Entrada rechazada y vehículo regresó al almacén origen.');
        }



        public function edit($id)
        {
            $entrada = Entrada::with('checklist')->findOrFail($id);
            $almacenes = Almacen::all();
            $checklist = $entrada->checklist; // esto será null o el objeto Checklist
            $vehiculos = Vehiculo::all();




            return view('entradasedit', compact('entrada', 'almacenes', 'checklist', 'vehiculos'));
        }



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
        'Estado' => $request->input('Estado'), 


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


        public function imprimirOrden($id)
        {
            $entrada = Entrada::with(['checklist', 'checklistSalida', 'vehiculo', 'almacenEntrada'])
                ->findOrFail($id);

            //  Elegir último checklist entre entrada y salida
            $checklistEntrada = $entrada->checklist;
            $checklistSalida  = $entrada->checklistSalida;

            $checklist = null;
            $tipoChecklist = null;

            if ($checklistEntrada && $checklistSalida) {
                if ($checklistEntrada->created_at > $checklistSalida->created_at) {
                    $checklist = $checklistEntrada;
                    $tipoChecklist = 'Entrada';
                } else {
                    $checklist = $checklistSalida;
                    $tipoChecklist = 'Salida';
                }
            } elseif ($checklistEntrada) {
                $checklist = $checklistEntrada;
                $tipoChecklist = 'Entrada';
            } elseif ($checklistSalida) {
                $checklist = $checklistSalida;
                $tipoChecklist = 'Salida';
            }

            //  Generar QR
            $renderer = new ImageRenderer(
                new \BaconQrCode\Renderer\RendererStyle\RendererStyle(180),
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);
            $qrSvg = $writer->writeString($entrada->VIN);
            $qrBase64 = base64_encode($qrSvg);

            return view('entradasimprimir', compact('entrada', 'qrBase64', 'checklist', 'tipoChecklist'));
        }







}
