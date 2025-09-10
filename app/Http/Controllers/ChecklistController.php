<?php



namespace App\Http\Controllers;

use App\Models\Checklist;
use App\Models\Entrada;
use Illuminate\Http\Request;

class ChecklistController extends Controller
{
    // Mostrar formulario de creación de checklist para una entrada
    public function create($no_orden)
    {
        $entrada = Entrada::findOrFail($no_orden);
        return view('checklists.create', compact('entrada'));
    }

    // Guardar un checklist en la base de datos
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

        Checklist::create($validated);

        return redirect()->route('entradas.show', $validated['No_orden_entrada'])
                         ->with('success', 'Checklist guardado correctamente.');
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

        $checklist->update($validated);

        return redirect()->route('checklists.show', $checklist->id_checklist)
                         ->with('success', 'Checklist actualizado correctamente.');
    }

    // ✅ Retornar estructura de checklist para tipo dinámico (AJAX)
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
