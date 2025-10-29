<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use BaconQrCode\Writer;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use Maatwebsite\Excel\Validators\ValidationException;

use App\Models\Entrada;
use App\Models\Almacen;
use App\Models\Checklist;
use App\Models\Vehiculo;
use App\Models\Salida;
use App\Imports\EntradasImport;
use App\Models\ChecklistSalida;

class Entradas extends Controller
{
    /* =========================================================
        INDEX - LISTADO DE ENTRADAS
    ========================================================= */
    public function index(Request $request)
    {
        $user = auth()->user();
        $estatus = $request->get('estatus');

        $query = Entrada::with('almacenEntrada')
            ->orderBy('created_at', 'desc');

        if ($user->role !== 'admin') {
            // Usuario normal solo ve las entradas de su almacén
            $query->where('Almacen_entrada', $user->almacen_id);
        }

        if ($estatus) {
            $query->where('estatus', $estatus);
        }

        $entradas = $query->paginate(10);

        return view('entradas.index', compact('entradas'));
    }

        



    /* =========================================================
        FORMULARIO DE IMPORTACIÓN
    ========================================================= */
    public function mostrarFormularioImportacion()
    {
        return view('entradas.import');
    }


    public function importar(Request $request)
        {
            // 1. Validación de archivo (correcta)
            $request->validate([
                'archivo' => 'required|mimes:xlsx,csv|max:2048',
            ]);

            try {
                // 2. Ejecutar la importación
                Excel::import(new EntradasImport, $request->file('archivo'));

                // 3. Redirección de éxito
                return redirect()
                    ->route('vehiculos.index')
                    ->with('success', 'Entradas importadas correctamente.');

            } catch (ValidationException $e) {
                // 4. Captura ERRORES de VALIDACIÓN de Maatwebsite/Excel (si se usa ToModel)
                $failures = $e->failures();
                $errorCount = count($failures);
                
                // Muestra el primer error para no abrumar al usuario
                $firstError = $failures[0]->errors()[0] ?? 'Error desconocido en la importación.';
                $row = $failures[0]->row();

                return redirect()
                    ->route('vehiculos.index')
                    ->with('error', "Importación fallida con {$errorCount} errores. Primer error en Fila {$row}: {$firstError}");

            } catch (\Exception $e) {
                // 5.  Captura TUS excepciones personalizadas (VIN, almacén, fecha, etc.)
                
                // El mensaje $e->getMessage() es el que lanzaste en EntradasImport.php
                return redirect()
                    ->route('vehiculos.index')
                    ->with('error', 'Importación cancelada: ' . $e->getMessage());
            }
        }

    /* =========================================================
        CREATE - FORMULARIO DE NUEVA ENTRADA
    ========================================================= */
    public function create()
    {
        if (Auth::user()->isAdmin()) {
            $almacenes = Almacen::all();
        } else {
            $almacenes = Almacen::where('Id_Almacen', Auth::user()->almacen_id)->get();
        }

        return view('entradas.create', compact('almacenes'));
    }


    /* =========================================================
        STORE - REGISTRA ENTRADA Y CHECKLIST INICIAL
    ========================================================= */
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
            'fecha_revision' => 'nullable|date_format:Y-m-d\TH:i',
            'observaciones_checklist' => 'nullable|string',
            'Observaciones' => 'nullable|string',
        ]);

        $proximoMantenimiento = Carbon::now()->addDays(30)->toDateString();
        $validated['Coordinador_Logistica'] = $user->name;

        DB::beginTransaction();
        try {
            //  Crear o actualizar vehículo
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
                    'estatus' => 'pendiente salida',
                ]
            );

            $vehiculo->Estado = $request->Estado ?? 'Mantenimiento';
            $vehiculo->save();

            //  Crear entrada
            $entrada = Entrada::create([
                'VIN' => $vehiculo->VIN,
                'Kilometraje_entrada' => $validated['Kilometraje_entrada'],
                'Almacen_entrada' => $almacenId,
                'Tipo' => $validated['Tipo'],
                'Observaciones' => $validated['Observaciones'],
                'Coordinador_Logistica' => $validated['Coordinador_Logistica'],
                'estatus' => 'pendiente',
            ]);

            //  Crear checklist inicial
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

            DB::commit();
            return redirect()
                ->route('entradas.index')
                ->with('success', 'Entrada y Checklist iniciales registrados. Vehículo en Mantenimiento pendiente de revisión.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al registrar la entrada: ' . $e->getMessage());
        }
    }


            /* =========================================================
            CONFIRMAR Y RECHAZAR ENTRADAS
        ========================================================= */
        public function confirmar($id)
        {
            $entrada = Entrada::findOrFail($id);
            // NOTA: Este método 'confirmar' también podría fallar si es Madrina. 
            // Si usas el 'update' para confirmar, te recomiendo deshabilitar/eliminar esta ruta.
            $vehiculo = Vehiculo::findOrFail($entrada->VIN); 

            $entrada->estatus = 'confirmada';
            $entrada->save();

            $salida = Salida::where('VIN', $entrada->VIN)
                ->where('estatus', 'pendiente')
                ->first();

            if ($salida) {
                $salida->estatus = 'confirmada';
                $salida->save();
            }

            $vehiculo->Almacen_actual = $entrada->Almacen_entrada;
            $vehiculo->estatus = 'En almacén';
            $vehiculo->save();

            return redirect()
                ->route('entradas.index')
                ->with('success', 'Entrada confirmada y vehículo en almacén destino.');
        }


        public function rechazar($id)
        {
            $entrada = Entrada::findOrFail($id);
            $vehiculo = Vehiculo::findOrFail($entrada->VIN);

            $entrada->estatus = 'rechazada';
            $entrada->save();

            $salida = Salida::where('VIN', $entrada->VIN)
                ->where('estatus', 'pendiente')
                ->first();

            if ($salida) {
                $salida->estatus = 'rechazada';
                $salida->save();
            }

            $vehiculo->Almacen_actual = $entrada->Almacen_salida;
            $vehiculo->estatus = 'En almacén';
            $vehiculo->save();

            return redirect()
                ->route('entradas.index')
                ->with('success', 'Entrada rechazada y vehículo regresó al almacén origen.');
        }


        /* =========================================================
            EDITAR Y ACTUALIZAR ENTRADAS
        ========================================================= */
        public function edit($id)
        {
            $entrada = Entrada::with(['checklist', 'vehiculo'])->findOrFail($id);

                if ($entrada->estatus === 'confirmada') {
            return redirect()->route('entradas.index')->with('error', ' Esta orden de entrada ya ha sido confirmada y es inmutable.');
            }
            $almacenes = Almacen::all();
            $checklist = $entrada->checklist;
            $vehiculos = Vehiculo::all();

            return view('entradas.edit', compact('entrada', 'almacenes', 'checklist', 'vehiculos'));
        }

        public function update(Request $request, $id)
        {
            $entrada = Entrada::findOrFail($id);

            $data = $request->validate([
                'Motor' => 'required|max:17',
                'Caracteristicas' => 'required|string',
                'Color' => 'required|string',
                'Modelo' => 'required|string',
                'Kilometraje_entrada' => 'nullable|integer',
                'Almacen_entrada' => 'required|exists:almacen,Id_Almacen',
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
                'fecha_revision' => 'nullable|date_format:Y-m-d\TH:i',
            ]);

            DB::beginTransaction();
            try {
                // 1. Actualizar SOLO los datos de la ORDEN DE ENTRADA
                $entrada->update($data);

                // 2. Actualizar/Crear Checklist (Esto ocurre siempre que se guarda la edición)
                $entrada->checklist()->updateOrCreate(
                    ['No_orden_entrada' => $entrada->No_orden],
                    $request->only([
                        'documentos_completos', 'accesorios_completos', 'estado_exterior', 'estado_interior',
                        'pdi_realizada', 'seguro_vigente', 'nfc_instalado', 'gps_instalado',
                        'folder_viajero', 'observaciones', 'recibido_por', 'fecha_revision',
                    ])
                );

                // 3. LÓGICA DE CONFIRMACIÓN: Mueve el vehículo al inventario si la orden estaba pendiente
                if ($entrada->estatus === 'pendiente') {

                    // 3.1. Preparar los datos del vehículo para el INVENTARIO (tabla vehiculos)
                    $vehiculoInventarioData = [
                        'Motor' => $request->input('Motor'),
                        'Caracteristicas' => $request->input('Caracteristicas'),
                        'Color' => $request->input('Color'),
                        'Modelo' => $request->input('Modelo'),
                        // El estado viene del checklist ('disponible' o 'mantenimiento')
                        'Estado' => $request->input('Estado'), 
                        'estatus' => 'En almacén',
                        'Almacen_actual' => $entrada->Almacen_entrada, // Almacén de destino
                        'tipo' => $entrada->Tipo,
                    ];

                    // 3.2. Flujo Clave: Crear o Actualizar el Vehículo en el Inventario
                    if ($entrada->Tipo === 'Madrina' || $entrada->Tipo === 'Otro') {
                        // Si es MADRINA, es un vehículo NUEVO: INSERTAR o actualizar si ya existe (seguridad)
                        Vehiculo::updateOrCreate(
                            ['VIN' => $entrada->VIN],
                            $vehiculoInventarioData
                        );
                    } else {
                        // Si es TRASPASO/DEVOLUCIÓN (ya debe existir): SÓLO ACTUALIZAMOS
                        $vehiculo = Vehiculo::findOrFail($entrada->VIN);
                        $vehiculo->update($vehiculoInventarioData);
                    }

                    // 3.3. Marcar la entrada como confirmada
                    $entrada->estatus = 'confirmada';
                    $entrada->save();

                    // 3.4. Confirmar Salida Pendiente (si es Traspaso)
                    if ($entrada->Tipo === 'Traspaso') {
                        $salidaPendiente = Salida::where('VIN', $entrada->VIN)
                            ->where('estatus', 'pendiente')
                            ->first();
                        if ($salidaPendiente) {
                            $salidaPendiente->estatus = 'confirmada';
                            $salidaPendiente->save();
                        }
                    }
                }

                DB::commit();
                return redirect()
                    ->route('entradas.index') // Redirigir a la lista de entradas
                    ->with('success', 'Entrada y Checklist finalizados correctamente. Vehículo en inventario activo.');

            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Error al finalizar el Checklist: ' . $e->getMessage());
            }
        }

    /* =========================================================
        ELIMINAR ENTRADA Y CHECKLIST
    ========================================================= */
    public function destroy($id)
        {
            $entrada = Entrada::findOrFail($id);

            //  BLOQUEA LA ELIMINACIÓN SI EL TRASPASO CONCLUYÓ
            if ($entrada->estatus === 'confirmada') {
                return redirect()->back()->with('error', ' No puedes eliminar una Orden de Entrada que ya ha sido confirmada.');
            }

            // ... Continúa con la eliminación si no está confirmada ...
            if ($entrada->checklist) {
                $entrada->checklist->delete();
            }

            $entrada->delete();

            return redirect()->back()->with('success', 'Entrada eliminada correctamente');
        }


    /* =========================================================
        IMPRIMIR ORDEN DE ENTRADA
    ========================================================= */
    public function imprimirOrden($id)
    {
        $entrada = Entrada::with(['checklist', 'vehiculo', 'almacenEntrada'])
            ->where('No_orden', $id)
            ->firstOrFail();

        $checklistAPrint = $entrada->checklist;
        $tipoChecklistTitulo = 'Checklist de Entrada (Vehículo Nuevo)';

        // 🔹 Si tiene almacén de salida, es un traspaso
        if ($entrada->Almacen_salida !== null) {
            $salida = Salida::where('VIN', $entrada->VIN)
                ->whereIn('estatus', ['confirmada', 'pendiente'])
                ->orderByDesc('created_at')
                ->first();

            if ($salida) {
                $salidaChecklist = ChecklistSalida::where('No_orden_salida', $salida->No_orden_salida)->first();

                if ($salidaChecklist) {
                    $checklistAPrint = $salidaChecklist;
                    $tipoChecklistTitulo = 'Checklist de Salida (Para Verificación de Traspaso)';
                }
            }
        }

        // 🔹 Generar código QR
        $renderer = new ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(180),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        $qrSvg = $writer->writeString($entrada->VIN);
        $qrBase64 = base64_encode($qrSvg);

        return view('ordenes.entradasimprimir', [
            'tipo' => 'entrada',
            'tipoChecklist' => $tipoChecklistTitulo,
            'entrada' => $entrada,
            'qrBase64' => $qrBase64,
            'checklist' => $checklistAPrint,
        ]);
    }
}
