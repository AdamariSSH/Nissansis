<?php



namespace App\Http\Controllers;

use App\Models\Checklist;
use App\Models\Entrada;
use Illuminate\Http\Request;
use App\Models\Vehiculo;
use Illuminate\Support\Facades\DB;

class ChecklistController extends Controller
{
    // Mostrar formulario de creación de checklist para una entrada
    public function create($no_orden)
    {
        $entrada = Entrada::findOrFail($no_orden);
        return view('checklists.create', compact('entrada'));
    }

        public function store(Request $request)
    {
        $validated = $request->validate([
            'No_orden_entrada'    => 'required|exists:entradas,No_orden',
            'tipo_checklist'      => 'required|in:Madrina,Traspaso,Recepcion',
            'documentos_completos'=> 'required|boolean',
            'accesorios_completos'=> 'required|boolean',
            'estado_exterior'     => 'nullable|in:Excelente,Bueno,Regular,Malo',
            'estado_interior'     => 'nullable|in:Excelente,Bueno,Regular,Malo',
            'pdi_realizada'       => 'required|boolean',
            'seguro_vigente'      => 'required|boolean',
            'nfc_instalado'       => 'required|boolean',
            'gps_instalado'       => 'required|boolean',
            'folder_viajero'      => 'required|boolean',
            'recibido_por'        => 'nullable|string|max:50',
            'observaciones'       => 'nullable|string',
            'fecha_revision'      => 'nullable|date',
        ]);

        //  Lógica de Transacción y Actualización
        DB::beginTransaction();
        try {
            //  Crear el registro del Checklist
            Checklist::create($validated);

            //  Determinar el nuevo estado del vehículo
            $newVehicleState = 'Disponible';
            // Si el exterior O el interior son Regular/Malo, el vehículo requiere mantenimiento
            if ($validated['estado_exterior'] === 'Regular' || $validated['estado_exterior'] === 'Malo' ||
                $validated['estado_interior'] === 'Regular' || $validated['estado_interior'] === 'Malo') {
                $newVehicleState = 'Mantenimiento';
            }
            
            //  Obtener la Entrada para el VIN
            $entrada = Entrada::findOrFail($validated['No_orden_entrada']);
            
            //  Actualizar la Entrada
            $entrada->estatus = 'confirmada'; // Cambia el estatus de la entrada a 'confirmada'
            $entrada->save();

            //  Actualizar el Vehículo (estado y estatus de inventario)
            Vehiculo::where('VIN', $entrada->VIN)->update([
                'Estado' => $newVehicleState,
                'estatus' => 'En almacén', // Pasa de 'pendiente salida' a 'En almacén'
            ]);

            DB::commit();

            return redirect()->route('entradas.show', $validated['No_orden_entrada'])
                             ->with('success', 'Checklist guardado y Entrada confirmada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Error al guardar el Checklist: ' . $e->getMessage());
        }
    }



    // Mostrar checklist individual
    public function show($id)
    {
        $checklist = Checklist::findOrFail($id);
        return view('checklists.show', compact('checklist'));
    }

    // Mostrar formulario de edición
    public function edit($id)
    {
        $checklist = Checklist::findOrFail($id);
        return view('checklists.edit', compact('checklist'));
    }


    // Actualizar checklist existente
    public function update(Request $request, $id)
    {
        $checklist = Checklist::findOrFail($id);

        $validated = $request->validate([
            // ... (validación sin cambios) ...
            'tipo_checklist'      => 'required|in:Madrina,Traspaso,Recepcion',
            'documentos_completos'=> 'required|boolean',
            'accesorios_completos'=> 'required|boolean',
            'estado_exterior'     => 'nullable|in:Excelente,Bueno,Regular,Malo',
            'estado_interior'     => 'nullable|in:Excelente,Bueno,Regular,Malo',
            'pdi_realizada'       => 'required|boolean',
            'seguro_vigente'      => 'required|boolean',
            'nfc_instalado'       => 'required|boolean',
            'gps_instalado'       => 'required|boolean',
            'folder_viajero'      => 'required|boolean',
            'recibido_por'        => 'nullable|string|max:50',
            'observaciones'       => 'nullable|string',
            'fecha_revision'      => 'nullable|date',
        ]);

        // Lógica de Transacción y Actualización
        DB::beginTransaction();
        try {
            // Actualizar el registro del Checklist
            $checklist->update($validated);
            
            // Determinar el nuevo estado del vehículo (la misma lógica que en store)
            $newVehicleState = 'Disponible';
            if ($validated['estado_exterior'] === 'Regular' || $validated['estado_exterior'] === 'Malo' ||
                $validated['estado_interior'] === 'Regular' || $validated['estado_interior'] === 'Malo') {
                $newVehicleState = 'Mantenimiento';
            }

            // Obtener la Entrada y el Vehículo relacionados
            $entrada = Entrada::findOrFail($checklist->No_orden_entrada);
            
            //  Actualizar el Vehículo (estado y estatus de inventario)
            Vehiculo::where('VIN', $entrada->VIN)->update([
                'Estado' => $newVehicleState,
                'estatus' => 'En almacén', // Asegurarse que el estatus de inventario sea 'En almacén'
            ]);
            
            DB::commit();

            return redirect()->route('checklists.show', $checklist->id_checklist)
                             ->with('success', 'Checklist y Estado del Vehículo actualizados correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Error al actualizar el Checklist: ' . $e->getMessage());
        }
    }

    //  Retornar estructura de checklist para tipo dinámico (AJAX)
    public function getChecklist($tipo)
    {
        if (!in_array($tipo, ['Madrina', 'Traspaso', 'Recepcion'])) {
            return response()->json(['error' => 'Tipo de checklist no válido.'], 400);
        }

        // Puedes personalizar estos valores por tipo si deseas
        $data = [
            'tipo_checklist' => $tipo,
            'documentos_completos' => false,
            'accesorios_completos' => false,
            'estado_exterior' => 'Bueno',
            'estado_interior' => 'Bueno',
            'pdi_realizada' => false,
            'seguro_vigente' => false,
            'nfc_instalado' => false,
            'gps_instalado' => false,
            'folder_viajero' => false,
            'recibido_por' => '',
            'observaciones' => '',
            'fecha_revision' => now()->format('Y-m-d'),
        ];

        return response()->json($data);
    }
}
