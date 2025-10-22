<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Vehiculo;
use App\Models\Almacen;
use App\Models\Entrada;
use App\Models\Salida;
use App\Models\Checklist;
use App\Models\ChecklistSalida;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;



class VehiculosController extends Controller
{
    public function index(Request $request)
{
    $query = Vehiculo::with('almacen');

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

    return view('vehiculos.index', compact('vehiculos', 'almacenes'));
}


    public function imprimir($No_orden)
    {
        $query = Vehiculo::with(['almacen', 'ultimaEntradatipo']);

        if ($request->filled('vin')) {
            $query->where('VIN', 'like', '%' . $request->vin . '%');
        }

        if ($request->filled('estado')) {
            $query->where('Estado', $request->estado);
        }

        if (!Auth::user()->isAdmin()) {
            $query->where('Almacen_actual', Auth::user()->almacen_id);
        }

        if ($request->filled('almacen_id') && Auth::user()->isAdmin()) {
            $query->where('Almacen_actual', $request->almacen_id);
        }

        $vehiculos = $query->paginate(10)->appends($request->all());
        $almacenes = Almacen::all();

        return view('Vehiculos.index', compact('vehiculos', 'almacenes'));
    }

    public function destroy($vin)
    {
        $vehiculo = Vehiculo::with('entradas.checklist')->findOrFail($vin);

        foreach ($vehiculo->entradas as $entrada) {
            if ($entrada->checklist) {
                $entrada->checklist->delete();
            }
            $entrada->delete();
        }

        $vehiculo->delete();

        return redirect()->route('vehiculos.index')->with('success', 'Vehículo eliminado correctamente');
    }

    public function edit($vin)
    {
        $vehiculo = Vehiculo::findOrFail($vin);
        $almacenes = Almacen::all();

        // Traer entradas y salidas relacionadas con este vehículo
        $entradas = $vehiculo->entradas()->with('almacenEntrada')->get();
        $salidas = $vehiculo->salidas()->with('almacenSalida')->get();

        // Pasar entradas y salidas a la vista
        return view('vehiculos.edit', compact('vehiculo', 'almacenes', 'entradas', 'salidas'));
    }

    public function update(Request $request, $vin)
    {
        $vehiculo = Vehiculo::findOrFail($vin);

        $request->validate([
            'Motor' => 'required|string|max:50',
            'Caracteristicas' => 'required|string|max:30',
            'Color' => 'required|string|max:30',
            'Modelo' => 'required|string|max:100',
            'Proximo_mantenimiento' => 'nullable|date',
            'Estado' => 'nullable|string|max:50',
            'estatus' => 'required|in:En almacén,En tránsito,Pendiente salida,Vendido,Rechazado',
            'Coordinador_Logistica' => 'nullable|string|max:50',
            'Almacen_actual' => 'nullable|exists:almacen,Id_Almacen',
            'tipo' => 'required|in:Madrina,Traspaso,Devolucion,Otro',
        ]);

        $vehiculo->update($request->except('VIN'));

        return redirect()->route('vehiculos.index')
                         ->with('success', 'Vehículo actualizado correctamente');
    }

    // Nuevo método show para poder usar $entradas y $salidas en una vista separada
    public function show($vin)
    {
        $vehiculo = Vehiculo::with(['entradas.almacen', 'salidas.almacenSalida'])->findOrFail($vin);

        $entradas = $vehiculo->entradas;
        $salidas = $vehiculo->salidas;

        return view('vehiculos.show', compact('vehiculo', 'entradas', 'salidas'));
    }




        public function ImprimirVehiculo($vin)
        {
            // Buscamos el vehículo
            $vehiculo = Vehiculo::with('almacen')->findOrFail($vin);

            // CORRECCIÓN 1: Usamos 'updated_at'. Este es el campo más preciso para rastrear
            // el momento real en que el movimiento fue CONFIRMADO (es decir, cuando terminó el traspaso).
            $ultimaEntrada = Entrada::where('VIN', $vin)->latest('updated_at')->first();
            $ultimaSalida  = Salida::where('VIN', $vin)->latest('updated_at')->first();

            //Log de depuración (Ajustar para updated_at)
            Log::info("=== DEPURACIÓN ImprimirVehiculo ===", [
                'VIN' => $vin,
                'entrada_fecha' => optional($ultimaEntrada)->updated_at, // Mostrar updated_at
                'salida_fecha' => optional($ultimaSalida)->updated_at,   // Mostrar updated_at
            ]);

            $ultimoMovimiento = null;
            $tipoMovimiento = null;
            $checklist = null;

            //  Comparación de fechas
            if ($ultimaEntrada && $ultimaSalida) {
                //  CORRECCIÓN 2: Usamos el comparador MAYOR O IGUAL (>=).
                // Esto garantiza que si las fechas son idénticas (empate),
                // se priorice la ENTRADA, ya que es el estado funcional final del vehículo.
                if ($ultimaEntrada->updated_at >= $ultimaSalida->updated_at) {
                    $ultimoMovimiento = $ultimaEntrada;
                    $tipoMovimiento = 'entrada';
                    $checklist = Checklist::where('No_orden_entrada', $ultimaEntrada->No_orden)->first();
                } else {
                    // Esto solo se ejecutará si la Salida es estrictamente posterior a la Entrada
                    $ultimoMovimiento = $ultimaSalida;
                    $tipoMovimiento = 'salida';
                    $checklist = ChecklistSalida::where('No_orden_salida', $ultimaSalida->No_orden_salida)->first();
                }
            } elseif ($ultimaEntrada) {
                $ultimoMovimiento = $ultimaEntrada;
                $tipoMovimiento = 'entrada';
                $checklist = Checklist::where('No_orden_entrada', $ultimaEntrada->No_orden)->first();
            } elseif ($ultimaSalida) {
                $ultimoMovimiento = $ultimaSalida;
                $tipoMovimiento = 'salida';
                $checklist = ChecklistSalida::where('No_orden_salida', $ultimaSalida->No_orden_salida)->first();
            }

            // Log del resultado final (Ajustar para updated_at)
            Log::info("Resultado del último movimiento", [
                'tipo' => $tipoMovimiento,
                'No_orden' => optional($ultimoMovimiento)->No_orden ?? optional($ultimoMovimiento)->No_orden_salida,
                'fecha_movimiento' => optional($ultimoMovimiento)->updated_at,
            ]);

            if (!$ultimoMovimiento) {
                return back()->with('error', 'Este vehículo aún no tiene movimientos registrados.');
            }

            // Retornar vista
            return view('ordenes.vehiculoimprimir', compact('vehiculo', 'ultimoMovimiento', 'tipoMovimiento', 'checklist'));
        }

        public function buscarVin(Request $request)
        {
            $query = $request->input('query');

            $vehiculos = \App\Models\Vehiculo::where('VIN', 'LIKE', "%{$query}%")
                ->orWhere('Modelo', 'LIKE', "%{$query}%")
                ->orWhere('Color', 'LIKE', "%{$query}%")
                ->limit(10)
                ->get(['VIN', 'Modelo', 'Color']);

            return response()->json($vehiculos);
        }

}
