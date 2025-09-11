<?php


// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use App\Models\Salida;
// use App\Models\Almacen;
// use App\Models\Vehiculo;
// use App\Models\Entrada;
// use App\Models\Checklist;
// use Illuminate\Database\QueryException; // <-- Esta línea

// use Illuminate\Support\Facades\DB;

// class SalidaController extends Controller
// {
//  public function index()
//         {
//             $user = auth()->user();

//             if ($user->role === 'admin') {
//                 // Admin ve todas
//                 $salidas = Salida::with('almacen')
//                                 ->orderBy('Fecha', 'desc')
//                                 ->paginate(10);
//             } else {
//                 // Usuario ve solo las salidas de su almacén
//                 $salidas = Salida::with('almacen')
//                                 ->where('Almacen_salida', $user->almacen_id)
//                                 ->orderBy('Fecha', 'desc')
//                                 ->paginate(10);
//             }

//             return view('salidas', compact('salidas'));
//         }



//     public function create(Request $request)
//     {
//         $almacenes = Almacen::all();
//         $vehiculo = null;
//         $ultimoChecklist = null;

//         $vin = $request->query('vin');

//         if ($vin) {
//             $vehiculo = Vehiculo::with('almacen')->find($vin);

//             if ($vehiculo) {
//                 $ultimaEntrada = Entrada::where('VIN', $vin)
//                     ->latest('Fecha_entrada')
//                     ->first();

//                 if ($ultimaEntrada) {
//                     $ultimoChecklist = Checklist::where('No_orden_entrada', $ultimaEntrada->No_orden)
//                         ->latest('fecha_revision')
//                         ->first();
//                 }
//             }
//         }

//         return view('vehiculossalidas', compact('almacenes', 'vehiculo', 'ultimoChecklist'));
//     }

//     public function getVehiculoData($vin)
//     {
//         $vehiculo = Vehiculo::with('almacen')->find($vin);

//         if (!$vehiculo) {
//             return response()->json(['error' => 'Vehículo no encontrado']);
//         }

//         $ultimaEntrada = Entrada::where('VIN', $vin)
//             ->latest('Fecha_entrada')
//             ->first();

//         $ultimoChecklist = null;
//         if ($ultimaEntrada) {
//             $ultimoChecklist = Checklist::where('No_orden_entrada', $ultimaEntrada->No_orden)
//                 ->latest('fecha_revision')
//                 ->first();

//             if ($ultimoChecklist) {
//                 $ultimoChecklist->fecha_revision = $ultimoChecklist->fecha_revision
//                     ? \Carbon\Carbon::parse($ultimoChecklist->fecha_revision)->format('Y-m-d')
//                     : null;
//             }
//         }

//         return response()->json([
//             'vehiculo' => $vehiculo,
//             'checklist' => $ultimoChecklist
//         ]);
//     }





//     public function store(Request $request)
// {
//     $request->validate([
//         'VIN' => 'required|exists:vehiculos,VIN',
//         'Motor' => 'required|string',
//         'Caracteristicas' => 'required|string',
//         'Color' => 'required|string',
//         'Tipo_salida' => 'required|in:Venta,Traspaso,Devolucion',
//         'Almacen_salida' => 'required|integer',
//         'Almacen_entrada' => 'required|integer',
//         'Fecha' => 'required|date',
//         'Modelo' => 'required|string',
//     ]);

//     DB::beginTransaction();

//     try {
//         $vehiculo = Vehiculo::findOrFail($request->VIN);

//         // No permitir salidas si ya está vendido
//         if ($vehiculo->Estado === 'Vendido') {
//             throw new \Exception("El vehículo ya fue vendido y no puede generar más salidas.");
//         }

//         // Verificación especial si la última salida fue un traspaso
//         $ultimaSalida = Salida::where('VIN', $request->VIN)
//             ->latest('Fecha')
//             ->first();

//         if ($ultimaSalida && $ultimaSalida->Tipo_salida === 'Traspaso') {
//             $nuevaEntrada = Entrada::where('VIN', $request->VIN)
//                 ->where('Fecha_entrada', '>', $ultimaSalida->Fecha)
//                 ->where('Almacen_entrada', $ultimaSalida->Almacen_entrada) // 👈 **CORRECCIÓN:** Se valida la entrada en el almacén de destino de la última salida.
//                 ->first();

//             if (!$nuevaEntrada) {
//                 throw new \Exception("El vehículo ya fue traspasado y no puede volver a salir hasta que se registre la entrada en el almacén de destino.");
//             }
//         }

//         // Obtener última entrada (para relación con salida)
//         $ultimaEntrada = Entrada::where('VIN', $request->VIN)
//             ->latest('Fecha_entrada')
//             ->first();

//         // Crear salida
//         $salida = Salida::create([
//             'VIN' => $request->VIN,
//             'Motor' => $request->Motor,
//             'Caracteristicas' => $request->Caracteristicas,
//             'Color' => $request->Color,
//             'Tipo_salida' => $request->Tipo_salida,
//             'Almacen_salida' => $request->Almacen_salida,
//             'Almacen_entrada' => $request->Almacen_entrada,
//             'Fecha' => $request->Fecha,
//             'Modelo' => $request->Modelo,
//             'No_orden_entrada' => $ultimaEntrada ? $ultimaEntrada->No_orden : null,
//             'estatus' => 'pendiente',
//         ]);

//         // 📋 Crear checklist de salida
//         $salida->checklistSalida()->create([
//             'No_orden_salida' => $salida->No_orden_salida,
//             'documentos_completos' => $request->documentos_completos,
//             'accesorios_completos' => $request->accesorios_completos,
//             'estado_exterior' => $request->estado_exterior,
//             'estado_interior' => $request->estado_interior,
//             'pdi_realizada' => $request->pdi_realizada,
//             'seguro_vigente' => $request->seguro_vigente,
//             'nfc_instalado' => $request->nfc_instalado,
//             'gps_instalado' => $request->gps_instalado,
//             'folder_viajero' => $request->folder_viajero,
//             'observaciones' => $request->observaciones_checklist,
//             'recibido_por' => $request->recibido_por,
//             'fecha_revision' => $request->fecha_revision,
//         ]);

//         // 🔄 Actualizar estatus según tipo de salida
//         if ($request->Tipo_salida === 'Venta') {
//             $salida->estatus = 'confirmada';
//             $salida->save();

//             $vehiculo->Estado = 'Vendido';
//             $vehiculo->estatus = 'vendido';
//             $vehiculo->save();

//         } elseif ($request->Tipo_salida === 'Traspaso' || $request->Tipo_salida === 'Devolucion') {
//             $vehiculo->estatus = 'En tránsito';
//             $vehiculo->save();

//             Entrada::create([
//                 'VIN' => $request->VIN,
//                 'Almacen_entrada' => $request->Almacen_entrada,
//                 'Almacen_salida' => $request->Almacen_salida,
//                 'Fecha_entrada' => now(),
//                 'Tipo' => $request->Tipo_salida,
//                 'estatus' => 'pendiente',
//                 'Coordinador_Logistica' => auth()->user()->name,
//             ]);

//             $salida->estatus = 'pendiente';
//             $salida->save();
//         }

//         DB::commit();

//         return redirect()->route('admin.vehiculos')
//             ->with('success', 'Salida registrada correctamente con su checklist.');

//     } catch (QueryException $e) {
//         DB::rollBack();

//         if ($e->getCode() == 23000) {
//             return redirect()->back()
//                 ->with('error', '¡Ups! Este vehículo ya tiene una salida registrada. Verifica la información.');
//         }

//         return redirect()->back()->with('error', 'Ocurrió un error inesperado: ' . $e->getMessage());
//     } catch (\Exception $e) {
//         DB::rollBack();
//         return redirect()->back()->with('error', $e->getMessage());
//     }
// }

// }



// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use App\Models\Salida;
// use App\Models\Almacen;
// use App\Models\Vehiculo;
// use App\Models\Entrada;
// use App\Models\Checklist;
// use App\Models\ChecklistSalida;
// use Illuminate\Database\QueryException; 
// use Illuminate\Support\Facades\DB;

// class SalidaController extends Controller
// {
//     public function index()
//     {
//         $user = auth()->user();

//         if ($user->role === 'admin') {
//             // Admin ve todas
//             $salidas = Salida::with('almacen')
//                             ->orderBy('Fecha', 'desc')
//                             ->paginate(10);
//         } else {
//             // Usuario ve solo las salidas de su almacén
//             $salidas = Salida::with('almacen')
//                             ->where('Almacen_salida', $user->almacen_id)
//                             ->orderBy('Fecha', 'desc')
//                             ->paginate(10);
//         }

//         return view('salidas', compact('salidas'));
//     }

//     // 🔧 Nuevo helper para obtener último checklist
//     private function obtenerUltimoChecklist($vin)
//     {
//         // Última entrada
//         $ultimaEntrada = Entrada::where('VIN', $vin)
//             ->latest('Fecha_entrada')
//             ->first();

//         // Última salida
//         $ultimaSalida = Salida::where('VIN', $vin)
//             ->latest('Fecha')
//             ->first();

//         $checkEntrada = null;
//         $checkSalida = null;

//         if ($ultimaEntrada) {
//             $checkEntrada = Checklist::where('No_orden_entrada', $ultimaEntrada->No_orden)
//                 ->latest('fecha_revision')
//                 ->first();
//         }

//         if ($ultimaSalida) {
//             $checkSalida = ChecklistSalida::where('No_orden_salida', $ultimaSalida->No_orden_salida)
//                 ->latest('fecha_revision')
//                 ->first();
//         }

//         // Comparar por fecha de revisión
//         if ($checkEntrada && $checkSalida) {
//             return $checkEntrada->fecha_revision > $checkSalida->fecha_revision
//                 ? $checkEntrada
//                 : $checkSalida;
//         }

//         return $checkEntrada ?? $checkSalida;
//     }

//     public function create(Request $request)
//     {
//         $almacenes = Almacen::all();
//         $vehiculo = null;
//         $ultimoChecklist = null;

//         $vin = $request->query('vin');

//         if ($vin) {
//             $vehiculo = Vehiculo::with('almacen')->find($vin);

//             if ($vehiculo) {
//                 $ultimoChecklist = $this->obtenerUltimoChecklist($vin);
//             }
//         }

//         return view('vehiculossalidas', compact('almacenes', 'vehiculo', 'ultimoChecklist'));
//     }

//     public function getVehiculoData($vin)
//     {
//         $vehiculo = Vehiculo::with('almacen')->find($vin);

//         if (!$vehiculo) {
//             return response()->json(['error' => 'Vehículo no encontrado']);
//         }

//         $ultimoChecklist = $this->obtenerUltimoChecklist($vin);

//         if ($ultimoChecklist) {
//             $ultimoChecklist->fecha_revision = $ultimoChecklist->fecha_revision
//                 ? \Carbon\Carbon::parse($ultimoChecklist->fecha_revision)->format('Y-m-d')
//                 : null;
//         }

//         return response()->json([
//             'vehiculo' => $vehiculo,
//             'checklist' => $ultimoChecklist
//         ]);
//     }

//     public function store(Request $request)
//     {
//         $request->validate([
//             'VIN' => 'required|exists:vehiculos,VIN',
//             'Motor' => 'required|string',
//             'Caracteristicas' => 'required|string',
//             'Color' => 'required|string',
//             'Tipo_salida' => 'required|in:Venta,Traspaso,Devolucion',
//             'Almacen_salida' => 'required|integer',
//             'Almacen_entrada' => 'required|integer',
//             'Fecha' => 'required|date',
//             'Modelo' => 'required|string',
//         ]);

//         DB::beginTransaction();

//         try {
//             $vehiculo = Vehiculo::findOrFail($request->VIN);

//             // No permitir salidas si ya está vendido
//             if ($vehiculo->Estado === 'Vendido') {
//                 throw new \Exception("El vehículo ya fue vendido y no puede generar más salidas.");
//             }

//             // Verificación especial si la última salida fue un traspaso
//             $ultimaSalida = Salida::where('VIN', $request->VIN)
//                 ->latest('Fecha')
//                 ->first();

//             if ($ultimaSalida && $ultimaSalida->Tipo_salida === 'Traspaso') {
//                 $nuevaEntrada = Entrada::where('VIN', $request->VIN)
//                     ->where('Fecha_entrada', '>', $ultimaSalida->Fecha)
//                     ->where('Almacen_entrada', $ultimaSalida->Almacen_entrada)
//                     ->first();

//                 if (!$nuevaEntrada) {
//                     throw new \Exception("El vehículo ya fue traspasado y no puede volver a salir hasta que se registre la entrada en el almacén de destino.");
//                 }
//             }

//             // Obtener última entrada (para relación con salida)
//             $ultimaEntrada = Entrada::where('VIN', $request->VIN)
//                 ->latest('Fecha_entrada')
//                 ->first();

//             // Crear salida
//             $salida = Salida::create([
//                 'VIN' => $request->VIN,
//                 'Motor' => $request->Motor,
//                 'Caracteristicas' => $request->Caracteristicas,
//                 'Color' => $request->Color,
//                 'Tipo_salida' => $request->Tipo_salida,
//                 'Almacen_salida' => $request->Almacen_salida,
//                 'Almacen_entrada' => $request->Almacen_entrada,
//                 'Fecha' => $request->Fecha,
//                 'Modelo' => $request->Modelo,
//                 'No_orden_entrada' => $ultimaEntrada ? $ultimaEntrada->No_orden : null,
//                 'estatus' => 'pendiente',
//             ]);

//             // 📋 Crear checklist de salida
//             $salida->checklistSalida()->create([
//                 'No_orden_salida' => $salida->No_orden_salida,
//                 'documentos_completos' => $request->documentos_completos,
//                 'accesorios_completos' => $request->accesorios_completos,
//                 'estado_exterior' => $request->estado_exterior,
//                 'estado_interior' => $request->estado_interior,
//                 'pdi_realizada' => $request->pdi_realizada,
//                 'seguro_vigente' => $request->seguro_vigente,
//                 'nfc_instalado' => $request->nfc_instalado,
//                 'gps_instalado' => $request->gps_instalado,
//                 'folder_viajero' => $request->folder_viajero,
//                 'observaciones' => $request->observaciones_checklist,
//                 'recibido_por' => $request->recibido_por,
//                 'fecha_revision' => $request->fecha_revision,
//             ]);

//             // 🔄 Actualizar estatus según tipo de salida
//             if ($request->Tipo_salida === 'Venta') {
//                 $salida->estatus = 'confirmada';
//                 $salida->save();

//                 $vehiculo->Estado = 'Vendido';
//                 $vehiculo->estatus = 'vendido';
//                 $vehiculo->save();

//             } elseif ($request->Tipo_salida === 'Traspaso' || $request->Tipo_salida === 'Devolucion') {
//                 $vehiculo->estatus = 'En tránsito';
//                 $vehiculo->save();

//                 Entrada::create([
//                     'VIN' => $request->VIN,
//                     'Almacen_entrada' => $request->Almacen_entrada,
//                     'Almacen_salida' => $request->Almacen_salida,
//                     'Fecha_entrada' => now(),
//                     'Tipo' => $request->Tipo_salida,
//                     'estatus' => 'pendiente',
//                     'Coordinador_Logistica' => auth()->user()->name,
//                 ]);

//                 $salida->estatus = 'pendiente';
//                 $salida->save();
//             }

//             DB::commit();

//             return redirect()->route('admin.vehiculos')
//                 ->with('success', 'Salida registrada correctamente con su checklist.');

//         } catch (QueryException $e) {
//             DB::rollBack();

//             if ($e->getCode() == 23000) {
//                 return redirect()->back()
//                     ->with('error', '¡Ups! Este vehículo ya tiene una salida registrada. Verifica la información.');
//             }

//             return redirect()->back()->with('error', 'Ocurrió un error inesperado: ' . $e->getMessage());
//         } catch (\Exception $e) {
//             DB::rollBack();
//             return redirect()->back()->with('error', $e->getMessage());
//         }
//     }
// }




namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Salida;
use App\Models\Almacen;
use App\Models\Vehiculo;
use App\Models\Entrada;
use App\Models\Checklist;
use App\Models\ChecklistSalida;
use Illuminate\Database\QueryException; 
use Illuminate\Support\Facades\DB;

class SalidaController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            // Admin ve todas
            $salidas = Salida::with('almacen')
                            ->orderBy('Fecha', 'desc')
                            ->paginate(10);
        } else {
            // Usuario ve solo las salidas de su almacén
            $salidas = Salida::with('almacen')
                            ->where('Almacen_salida', $user->almacen_id)
                            ->orderBy('Fecha', 'desc')
                            ->paginate(10);
        }

        return view('salidas', compact('salidas'));
    }

    // 🔧 Nuevo helper para obtener último checklist
    private function obtenerUltimoChecklist($vin)
    {
        // Última entrada
        $ultimaEntrada = Entrada::where('VIN', $vin)
            ->latest('Fecha_entrada')
            ->first();

        // Última salida
        $ultimaSalida = Salida::where('VIN', $vin)
            ->latest('Fecha')
            ->first();

        $checkEntrada = null;
        $checkSalida = null;

        if ($ultimaEntrada) {
            $checkEntrada = Checklist::where('No_orden_entrada', $ultimaEntrada->No_orden)
                ->latest('fecha_revision')
                ->first();
        }

        if ($ultimaSalida) {
            $checkSalida = ChecklistSalida::where('No_orden_salida', $ultimaSalida->No_orden_salida)
                ->latest('fecha_revision')
                ->first();
        }

        // Comparar por fecha de revisión
        if ($checkEntrada && $checkSalida) {
            return $checkEntrada->fecha_revision > $checkSalida->fecha_revision
                ? $checkEntrada
                : $checkSalida;
        }

        return $checkEntrada ?? $checkSalida;
    }

    public function create(Request $request)
    {
        $almacenes = Almacen::all();
        $vehiculo = null;
        $ultimoChecklist = null;

        $vin = $request->query('vin');

        if ($vin) {
            $vehiculo = Vehiculo::with('almacen')->find($vin);

            if ($vehiculo) {
                $ultimoChecklist = $this->obtenerUltimoChecklist($vin);
            }
        }

        return view('vehiculossalidas', compact('almacenes', 'vehiculo', 'ultimoChecklist'));
    }

    public function getVehiculoData($vin)
    {
        $vehiculo = Vehiculo::with('almacen')->find($vin);

        if (!$vehiculo) {
            return response()->json(['error' => 'Vehículo no encontrado']);
        }

        $ultimoChecklist = $this->obtenerUltimoChecklist($vin);

        if ($ultimoChecklist) {
            $ultimoChecklist->fecha_revision = $ultimoChecklist->fecha_revision
                ? \Carbon\Carbon::parse($ultimoChecklist->fecha_revision)->format('Y-m-d')
                : null;
        }

        return response()->json([
            'vehiculo' => $vehiculo,
            'checklist' => $ultimoChecklist
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'VIN' => 'required|exists:vehiculos,VIN',
            'Motor' => 'required|string',
            'Caracteristicas' => 'required|string',
            'Color' => 'required|string',
            'Tipo_salida' => 'required|in:Venta,Traspaso,Devolucion',
            'Almacen_salida' => 'required|integer',
            'Almacen_entrada' => 'required|integer',
            'Fecha' => 'required|date',
            'Modelo' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $vehiculo = Vehiculo::findOrFail($request->VIN);

            // 🚫 Bloquear si el vehículo está en tránsito
            if ($vehiculo->estatus === 'En tránsito') {
                throw new \Exception("El vehículo está en tránsito y no puede generar otra salida hasta que se registre la entrada en el almacén de destino.");
            }

            // 🚫 Bloquear si ya tiene una salida sin entrada posterior
            $ultimaSalida = Salida::where('VIN', $request->VIN)
                ->latest('Fecha')
                ->first();

            if ($ultimaSalida && in_array($ultimaSalida->Tipo_salida, ['Traspaso', 'Devolucion'])) {
                $entradaPosterior = Entrada::where('VIN', $request->VIN)
                    ->where('Fecha_entrada', '>', $ultimaSalida->Fecha)
                    ->where('Almacen_entrada', $ultimaSalida->Almacen_entrada)
                    ->first();

                if (!$entradaPosterior) {
                    throw new \Exception("El vehículo ya tuvo una salida ({$ultimaSalida->Tipo_salida}) y no puede generar otra hasta que se registre la entrada en el almacén destino.");
                }
            }

            // 🚫 No permitir salidas si ya está vendido
            if ($vehiculo->Estado === 'Vendido') {
                throw new \Exception("El vehículo ya fue vendido y no puede generar más salidas.");
            }

            // Obtener última entrada (para relación con salida)
            $ultimaEntrada = Entrada::where('VIN', $request->VIN)
                ->latest('Fecha_entrada')
                ->first();

            // Crear salida
            $salida = Salida::create([
                'VIN' => $request->VIN,
                'Motor' => $request->Motor,
                'Caracteristicas' => $request->Caracteristicas,
                'Color' => $request->Color,
                'Tipo_salida' => $request->Tipo_salida,
                'Almacen_salida' => $request->Almacen_salida,
                'Almacen_entrada' => $request->Almacen_entrada,
                'Fecha' => $request->Fecha,
                'Modelo' => $request->Modelo,
                'No_orden_entrada' => $ultimaEntrada ? $ultimaEntrada->No_orden : null,
                'estatus' => 'pendiente',
            ]);

            // 📋 Crear checklist de salida
            $salida->checklistSalida()->create([
                'No_orden_salida' => $salida->No_orden_salida,
                'documentos_completos' => $request->documentos_completos,
                'accesorios_completos' => $request->accesorios_completos,
                'estado_exterior' => $request->estado_exterior,
                'estado_interior' => $request->estado_interior,
                'pdi_realizada' => $request->pdi_realizada,
                'seguro_vigente' => $request->seguro_vigente,
                'nfc_instalado' => $request->nfc_instalado,
                'gps_instalado' => $request->gps_instalado,
                'folder_viajero' => $request->folder_viajero,
                'observaciones' => $request->observaciones_checklist,
                'recibido_por' => $request->recibido_por,
                'fecha_revision' => $request->fecha_revision,
            ]);

            // 🔄 Actualizar estatus según tipo de salida
            if ($request->Tipo_salida === 'Venta') {
                $salida->estatus = 'confirmada';
                $salida->save();

                $vehiculo->Estado = 'Vendido';
                $vehiculo->estatus = 'vendido';
                $vehiculo->save();

            } elseif ($request->Tipo_salida === 'Traspaso' || $request->Tipo_salida === 'Devolucion') {
                $vehiculo->estatus = 'En tránsito';
                $vehiculo->save();

                Entrada::create([
                    'VIN' => $request->VIN,
                    'Almacen_entrada' => $request->Almacen_entrada,
                    'Almacen_salida' => $request->Almacen_salida,
                    'Fecha_entrada' => now(),
                    'Tipo' => $request->Tipo_salida,
                    'estatus' => 'pendiente',
                    'Coordinador_Logistica' => auth()->user()->name,
                ]);

                $salida->estatus = 'pendiente';
                $salida->save();
            }

            DB::commit();

            return redirect()->route('admin.vehiculos')
                ->with('success', 'Salida registrada correctamente con su checklist.');

        } catch (QueryException $e) {
            DB::rollBack();

            if ($e->getCode() == 23000) {
                return redirect()->back()
                    ->with('error', '¡Ups! Este vehículo ya tiene una salida registrada. Verifica la información.');
            }

            return redirect()->back()->with('error', 'Ocurrió un error inesperado: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
