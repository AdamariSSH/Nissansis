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
        INDEX - LISTADO DE ENTRADAS (Última entrada única por vehículo)
    ========================================================= */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // 1. Definir Ordenamiento
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        // --- CONSTRUCCIÓN DE LA SUBCONSULTA para obtener los IDs únicos (sin error 1055) ---
        $latestOrderQuery = Entrada::query();

        // Aplicar el filtro de Rol con lógica OR
        if ($user->role !== 'admin') {
            $userAlmacenId = $user->almacen_id;
            
            // El usuario ve: (Entradas creadas en su almacén) O (Vehículos que actualmente están en su almacén)
            $latestOrderQuery->where(function ($query) use ($userAlmacenId) {
                
                // 1. Entradas creadas en su almacén (Su historial)
                $query->where('Almacen_entrada', $userAlmacenId);
                
                // 2. O Entradas de vehículos que actualmente están en su almacén (Traspasados a él)
                $query->orWhereHas('vehiculo', function ($q) use ($userAlmacenId) {
                    // Es vital usar withoutGlobalScope para poder consultar el Vehículo
                    $q->withoutGlobalScope('almacen_restriccion')
                      ->where('Almacen_actual', $userAlmacenId);
                });
            });
        }

        // --- Aplicamos los Filtros del Usuario (desde el formulario) a la subconsulta ---
        
        // Aplicamos los filtros condicionalmente a la consulta base
        if ($request->filled('fecha')) {
            $latestOrderQuery->whereDate('created_at', $request->input('fecha'));
        }
        if ($request->filled('almacen_entrada_id')) {
            $latestOrderQuery->where('Almacen_entrada', $request->input('almacen_entrada_id'));
        }
        if ($request->filled('estatus')) {
            $latestOrderQuery->where('estatus', $request->input('estatus'));
        }
        
        // Aplicamos el filtro de estado de vehículo (whereHas) si existe.
        if ($request->filled('estado_vehiculo')) {
            $latestOrderQuery->whereHas('vehiculo', function ($q) use ($request) {
                // Mantenemos withoutGlobalScope aquí también
                $q->withoutGlobalScope('almacen_restriccion')
                  ->where('Estado', $request->input('estado_vehiculo'));
            });
        }

        // Seleccionamos el ID de la entrada más reciente por cada VIN.
        $latestOrderIds = $latestOrderQuery
            ->selectRaw('MAX(No_orden)')
            ->groupBy('VIN');
            
        // --- CONSTRUCCIÓN DE LA CONSULTA PRINCIPAL ---
        $query = Entrada::query();
        
        // Filtramos la consulta principal para incluir SÓLO los IDs de la última entrada de cada VIN.
        $query->whereIn('No_orden', $latestOrderIds);

        // Cargamos las relaciones con la corrección del Global Scope
        $query->with([
            'almacenSalida',
            'almacenEntrada',
            'vehiculo' => function ($q) {
                $q->withoutGlobalScope('almacen_restriccion');
            }
        ]);
        
        // Aplicar Ordenamiento
        $query->orderBy($sortBy, $sortOrder);

        // Ejecutar la consulta para OBTENER TODOS los registros ÚNICOS (sin paginación)
        $entradas = $query->get();

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
            // 4. Captura ERRORES de VALIDACIÓN de Maatwebsite/Excel
            $failures = $e->failures();
            $errorCount = count($failures);
            
            // Muestra el primer error para no abrumar al usuario
            $firstError = $failures[0]->errors()[0] ?? 'Error desconocido en la importación.';
            $row = $failures[0]->row();

            return redirect()
                ->route('vehiculos.index')
                ->with('error', "Importación fallida con {$errorCount} errores. Primer error en Fila {$row}: {$firstError}");

        } catch (\Exception $e) {
            // 5. Captura TUS excepciones personalizadas
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
            
            // Checkboxes y select de Checklist
            'documentos_completos' => 'nullable|string', 
            'accesorios_completos' => 'nullable|string',
            'estado_exterior' => 'nullable|in:Excelente,Bueno,Regular,Malo',
            'estado_interior' => 'nullable|in:Excelente,Bueno,Regular,Malo',
            'pdi_realizada' => 'nullable|string',
            'seguro_vigente' => 'nullable|string',
            'nfc_instalado' => 'nullable|string',
            'gps_instalado' => 'nullable|string',
            'folder_viajero' => 'nullable|string',
            
            'recibido_por' => 'nullable|string|max:50',
            'fecha_revision' => 'nullable|date_format:Y-m-d\TH:i',
            'observaciones_checklist' => 'nullable|string', // Nombre usado en Blade
            'Observaciones' => 'nullable|string', // Nombre usado para observaciones generales de la entrada
            'Estado' => 'required|string|max:50', // Estado del vehículo
        ]);

        $proximoMantenimiento = Carbon::now()->addDays(30)->toDateString();
        $validated['Coordinador_Logistica'] = $user->name;

        DB::beginTransaction();
        try {
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
                    'estatus' => $validated['Tipo'] === 'Madrina' ? 'En almacén' : 'En tránsito',
                    //'estatus' => 'En almacén', // Al crear o actualizar, asumimos que está en el almacén de entrada
                    'Estado' => $validated['Estado'], // Usamos el estado del checklist
                ]
            );

            // Crear entrada
            $entrada = Entrada::create([
                'VIN' => $vehiculo->VIN,
                'Kilometraje_entrada' => $validated['Kilometraje_entrada'],
                'Almacen_entrada' => $almacenId,
                'Tipo' => $validated['Tipo'],
                'Observaciones' => $validated['Observaciones'],
                'Coordinador_Logistica' => $validated['Coordinador_Logistica'],
                'estatus' => 'pendiente',
            ]);

            // Crear checklist inicial
            Checklist::create([
                'No_orden_entrada' => $entrada->No_orden,
                'tipo_checklist' => $validated['Tipo'],
                // 🟢 CORRECCIÓN: Usar $request->has() para los checkboxes.
                'documentos_completos' => $request->has('documentos_completos') ? 1 : 0, 
                'accesorios_completos' => $request->has('accesorios_completos') ? 1 : 0,
                'estado_exterior' => $validated['estado_exterior'] ?? null,
                'estado_interior' => $validated['estado_interior'] ?? null,
                'pdi_realizada' => $request->has('pdi_realizada') ? 1 : 0,
                'seguro_vigente' => $request->has('seguro_vigente') ? 1 : 0,
                'nfc_instalado' => $request->has('nfc_instalado') ? 1 : 0,
                'gps_instalado' => $request->has('gps_instalado') ? 1 : 0,
                'folder_viajero' => $request->has('folder_viajero') ? 1 : 0,
                'recibido_por' => $validated['recibido_por'] ?? null,
                'fecha_revision' => $validated['fecha_revision'] ?? null,
                'observaciones' => $validated['observaciones_checklist'] ?? null, // Usar 'observaciones' como nombre de columna
            ]);

            DB::commit();
            return redirect()
                ->route('entradas.index')
                ->with('success', 'Entrada y Checklist iniciales registrados. Vehículo en Almacén pendiente de revisión.');

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
    DB::beginTransaction();

    try {
        $entrada = Entrada::findOrFail($id);
        
        // Usamos where('VIN', ...) y firstOrFail() porque el VIN no es la PK de la tabla,
        // Y usamos withoutGlobalScope para ignorar la restricción del almacén.
        $vehiculo = Vehiculo::withoutGlobalScope('almacen_restriccion')
            ->where('VIN', $entrada->VIN) 
            ->firstOrFail(); 

        // 1. Actualizar Entrada
        $entrada->estatus = 'confirmada';
        $entrada->save();

        // 2. Cerrar Salida (Busca la orden de salida que puso el vehículo 'En transito')
        $salida = Salida::where('VIN', $entrada->VIN)
            ->where('estatus', 'En transito') 
            ->where('Almacen_entrada', $entrada->Almacen_entrada) 
            ->first();

        if ($salida) {
            $salida->estatus = 'finalizada';
            $salida->save();
        }

        // 3. Actualización de Inventario (Mover el vehículo al almacén de destino)
        $vehiculo->Almacen_actual = $entrada->Almacen_entrada;
        $vehiculo->estatus = 'En almacén';
        $vehiculo->save();

        DB::commit();

        return redirect()
            ->route('entradas.index')
            ->with('success', 'Entrada confirmada y vehículo en almacén destino.');

    } catch (\Exception $e) {
        DB::rollBack();
        
        // El try/catch evita la falla silenciosa y muestra el error exacto si lo hay
        return redirect()->back()->with('error', 'Error al confirmar la entrada: ' . $e->getMessage()); 
    }
}


    public function rechazar($id)
    {
        $entrada = Entrada::findOrFail($id);
        $vehiculo = Vehiculo::findOrFail($entrada->VIN);

        $entrada->estatus = 'rechazada';
        $entrada->save();

        //MEJORA: Buscamos la Salida que iba a este destino para cancelarla/rechazarla
        $salida = Salida::where('VIN', $entrada->VIN)
            ->where('estatus', 'En transito')
            ->where('Almacen_entrada', $entrada->Almacen_entrada) 
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
    // 1. CARGA DE ENTRADA CORREGIDA: Usar una función anónima para ignorar el Global Scope del modelo Vehiculo
    $entrada = Entrada::with([
        'checklist', 
        'vehiculo' => function($query) {
            // Esto le dice a Laravel: Carga la relación Vehiculo, ignorando la restricción de almacén.
            // Si el nombre de tu scope es diferente, ajústalo. Si no tiene nombre, usa withoutGlobalScopes().
            $query->withoutGlobalScope('almacen_restriccion'); 
        }
    ])->findOrFail($id);

    // 2. Bloqueo por Estatus (se mantiene)
    if ($entrada->estatus === 'confirmada') {
        return redirect()->route('entradas.index')->with('error', ' Esta orden de entrada ya ha sido confirmada y es inmutable.');
    }

    // 3. Eliminamos el código de 'crear vehículo si no existe'.
    // Si el vehículo existe en la tabla, ahora $entrada->vehiculo NO será null y los datos se cargarán.
    // Si la relación sigue siendo null (muy raro en un traspaso), hay un problema de VIN o de BD.
    
    // 4. Se mantiene la carga de datos para la vista
    $almacenes = Almacen::all();
    $checklist = $entrada->checklist; 

    return view('entradas.edit', compact('entrada', 'almacenes', 'checklist'));
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
            // Checklist: Validar como string, luego convertir a booleano/entero
            'documentos_completos' => 'nullable|string', 
            'accesorios_completos' => 'nullable|string',
            'estado_exterior' => 'nullable|string',
            'estado_interior' => 'nullable|string',
            'pdi_realizada' => 'nullable|string',
            'seguro_vigente' => 'nullable|string',
            'nfc_instalado' => 'nullable|string',
            'gps_instalado' => 'nullable|string',
            'folder_viajero' => 'nullable|string',
            'observaciones_checklist' => 'nullable|string', // Nombre usado en Blade
            'recibido_por' => 'nullable|string|max:255',
            'fecha_revision' => 'nullable|date_format:Y-m-d\TH:i',
            'Observaciones' => 'nullable|string', // Observaciones generales de la Entrada
        ]);

        DB::beginTransaction();
        try {
            // 1. Actualizar SOLO los campos de la ORDEN DE ENTRADA
            $entrada->update([
                'Kilometraje_entrada' => $data['Kilometraje_entrada'],
                'Almacen_entrada' => $data['Almacen_entrada'],
                'Tipo' => $data['Tipo'],
                'Observaciones' => $data['Observaciones'] ?? null,
                //'estatus' => $entrada->Tipo === 'Madrina' ? 'En almacén' : 'En tránsito',
                //'estatus' => 'pendiente',
            ]);

            // 2. Actualizar/Crear Checklist
            $entrada->checklist()->updateOrCreate(
                ['No_orden_entrada' => $entrada->No_orden],
                [
                    //CORRECCIÓN: Usar $request->has() para los checkboxes.
                    'documentos_completos' => $request->has('documentos_completos') ? 1 : 0, 
                    'accesorios_completos' => $request->has('accesorios_completos') ? 1 : 0,
                    'estado_exterior' => $data['estado_exterior'] ?? null,
                    'estado_interior' => $data['estado_interior'] ?? null,
                    'pdi_realizada' => $request->has('pdi_realizada') ? 1 : 0,
                    'seguro_vigente' => $request->has('seguro_vigente') ? 1 : 0,
                    'nfc_instalado' => $request->has('nfc_instalado') ? 1 : 0,
                    'gps_instalado' => $request->has('gps_instalado') ? 1 : 0,
                    'folder_viajero' => $request->has('folder_viajero') ? 1 : 0,
                    'observaciones' => $data['observaciones_checklist'] ?? null, 
                    'recibido_por' => $data['recibido_por'] ?? null,
                    'fecha_revision' => $data['fecha_revision'] ?? null,
                    'tipo_checklist' => $data['Tipo'],
                ]
            );

            // 3. Actualizar/Crear el Vehículo en el Inventario con los datos del formulario
            $vehiculoInventarioData = [
                'Motor' => $request->input('Motor'),
                'Caracteristicas' => $request->input('Caracteristicas'),
                'Color' => $request->input('Color'),
                'Modelo' => $request->input('Modelo'),
                'Estado' => $request->input('Estado'), // Estado del Checklist
                //'Almacen_actual' => $entrada->Almacen_entrada, 
                //'estatus' => $entrada->Tipo === 'Madrina' ? 'En almacén' : 'En tránsito', 
            ];

            if ($entrada->Tipo === 'Madrina') {
             // Si es Madrina, se registra en el almacén de entrada como 'En almacén'
            $vehiculoInventarioData['Almacen_actual'] = $entrada->Almacen_entrada;
            $vehiculoInventarioData['estatus'] = 'En almacén';
        }
        // Si es Traspaso, $vehiculoInventarioData NO contiene 'Almacen_actual' ni 'estatus', 
        // manteniendo los valores previos ('En tránsito' en el almacén de salida/tránsito).

            Vehiculo::withoutGlobalScope('almacen_restriccion')->updateOrCreate(
                ['VIN' => $entrada->VIN],
                $vehiculoInventarioData
            );
            
            // 4. Se confirma la transacción
            DB::commit();
            
            return redirect()
                ->route('entradas.index') 
                ->with('success', 'Entrada y datos del Vehículo actualizados correctamente. Recuerda presionar el botón de Confirmar para cerrar el ciclo si es un Traspaso.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al actualizar la entrada: ' . $e->getMessage());
        }
    }

    /* =========================================================
        ELIMINAR ENTRADA Y CHECKLIST
    ========================================================= */
    public function destroy($id)
    {
        $entrada = Entrada::findOrFail($id);

        // BLOQUEA LA ELIMINACIÓN SI EL TRASPASO CONCLUYÓ
        if ($entrada->estatus === 'confirmada') {
            return redirect()->back()->with('error', ' No puedes eliminar una Orden de Entrada que ya ha sido confirmada.');
        }

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
                // MEJORA: Buscar la salida que esté en tránsito o confirmada, para que coincida con el proceso.
                ->whereIn('estatus', ['confirmada', 'finalizada', 'En transito']) 
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