<?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use App\Models\Salida;
// use App\Models\Almacen;
// use App\Models\Vehiculo;
// use App\Models\Entrada;
// use App\Models\Checklist;
// use Illuminate\Support\Facades\DB;

// class SalidaController extends Controller
// {
//     public function index()
//     {
//         $salidas = Salida::all(); // O con relaciones si lo necesitas
//         return view('salidas', compact('salidas'));
//     }

//     /**
//      * Formulario de crear salida de vehículo
//      */
//     public function create(Request $request)
//     {
//         $almacenes = Almacen::all();
//         $vehiculo = null;
//         $ultimoChecklist = null;

//         // VIN por query string: /salidas/create?vin=XXXX
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

//     /**
//      * Endpoint para obtener info de vehículo + checklist por AJAX
//      */
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

//         //  Verificación: si el vehículo ya está vendido, no puede salir más
//         if ($vehiculo->Estado === 'Vendido') {
//             throw new \Exception("El vehículo ya fue vendido y no puede generar más salidas.");
//         }

//         //  Verificación: si ya tuvo un traspaso pero no tiene nueva entrada, bloquear
//         $ultimaSalida = Salida::where('VIN', $request->VIN)
//             ->latest('Fecha')
//             ->first();

//         if ($ultimaSalida && $ultimaSalida->Tipo_salida === 'Traspaso') {
//             $nuevaEntrada = Entrada::where('VIN', $request->VIN)
//                 ->where('Fecha_entrada', '>', $ultimaSalida->Fecha)
//                 ->exists();

//             if (!$nuevaEntrada) {
//                 throw new \Exception("El vehículo ya fue traspasado y no puede volver a salir hasta que se registre una nueva entrada.");
//             }
//         }

//         $ultimaEntrada = Entrada::where('VIN', $request->VIN)
//             ->latest('Fecha_entrada')
//             ->first();

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
//         ]);

//         $salida->checklistSalida()->create([
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

//         // 📌 Reglas según tipo de salida
//         if ($request->Tipo_salida === 'Venta') {
//             $vehiculo->Estado = 'Vendido';
//             $vehiculo->save();

//         } elseif ($request->Tipo_salida === 'Traspaso') {
//             // Cambiar almacén actual
//             $vehiculo->Almacen_id = $request->Almacen_entrada;
//             $vehiculo->save();
//         } 
//         // Devolución: aquí podrías decidir si solo mueves de almacén o pones estado diferente

//         DB::commit();

//         return redirect()->route('admin.vehiculos')
//             ->with('success', 'Salida registrada correctamente con su checklist.');

//     } catch (\Exception $e) {
//          DB::rollBack();
//         return redirect()->back()->with('error', $e->getMessage());

        
//     }

//     return redirect()->back()->with('success', 'Salida registrada correctamente.');
// }

// }



// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use App\Models\Salida;
// use App\Models\Almacen;
// use App\Models\Vehiculo;
// use App\Models\Entrada;
// use App\Models\Checklist;
// use Illuminate\Support\Facades\DB;

// class SalidaController extends Controller
// {
//     public function index()
//     {
//         $salidas = Salida::all();
//         return view('salidas', compact('salidas'));
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

//             if ($vehiculo->Estado === 'Vendido') {
//                 throw new \Exception("El vehículo ya fue vendido y no puede generar más salidas.");
//             }

//             $ultimaSalida = Salida::where('VIN', $request->VIN)
//                 ->latest('Fecha')
//                 ->first();

//             if ($ultimaSalida && $ultimaSalida->Tipo_salida === 'Traspaso') {
//                 $nuevaEntrada = Entrada::where('VIN', $request->VIN)
//                     ->where('Fecha_entrada', '>', $ultimaSalida->Fecha)
//                     ->exists();

//                 if (!$nuevaEntrada) {
//                     throw new \Exception("El vehículo ya fue traspasado y no puede volver a salir hasta que se registre una nueva entrada.");
//                 }
//             }

//             $ultimaEntrada = Entrada::where('VIN', $request->VIN)
//                 ->latest('Fecha_entrada')
//                 ->first();

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
//             ]);

//             $salida->checklistSalida()->create([
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

//             if ($request->Tipo_salida === 'Venta') {
//                 $salida->estatus = 'confirmada';
//                 $salida->save();

//                 $vehiculo->Estado = 'Vendido';
//                 $vehiculo->estatus = 'En almacén';
//                 $vehiculo->save();

//                 $vehiculo->delete();

//             } elseif ($request->Tipo_salida === 'Traspaso') {
//                 $vehiculo->estatus = 'Pendiente salida';
//                 $vehiculo->save();

//                 Entrada::create([
//                     'VIN' => $request->VIN,
//                     'Almacen_entrada' => $request->Almacen_entrada,
//                     'Almacen_salida' => $request->Almacen_salida,
//                     'Fecha_entrada' => now(),
//                     'Tipo' => 'Traspaso',
//                     'estatus' => 'pendiente',
//                     'Coordinador_Logistica' => auth()->user()->name,
//                 ]);

//                 $salida->estatus = 'pendiente';
//                 $salida->save();

//             } elseif ($request->Tipo_salida === 'Devolucion') {
//                 $vehiculo->estatus = 'Pendiente salida';
//                 $vehiculo->save();

//                 Entrada::create([
//                     'VIN' => $request->VIN,
//                     'Almacen_entrada' => $request->Almacen_entrada,
//                     'Almacen_salida' => $request->Almacen_salida,
//                     'Fecha_entrada' => now(),
//                     'Tipo' => 'Devolucion',
//                     'estatus' => 'pendiente',
//                     'Coordinador_Logistica' => auth()->user()->name,
//                 ]);

//                 $salida->estatus = 'pendiente';
//                 $salida->save();
//             }

//             DB::commit();

//             return redirect()->route('admin.vehiculos')
//                 ->with('success', 'Salida registrada correctamente con su checklist.');

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



    public function create(Request $request)
    {
        $almacenes = Almacen::all();
        $vehiculo = null;
        $ultimoChecklist = null;

        $vin = $request->query('vin');

        if ($vin) {
            $vehiculo = Vehiculo::with('almacen')->find($vin);

            if ($vehiculo) {
                $ultimaEntrada = Entrada::where('VIN', $vin)
                    ->latest('Fecha_entrada')
                    ->first();

                if ($ultimaEntrada) {
                    $ultimoChecklist = Checklist::where('No_orden_entrada', $ultimaEntrada->No_orden)
                        ->latest('fecha_revision')
                        ->first();
                }
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

        $ultimaEntrada = Entrada::where('VIN', $vin)
            ->latest('Fecha_entrada')
            ->first();

        $ultimoChecklist = null;
        if ($ultimaEntrada) {
            $ultimoChecklist = Checklist::where('No_orden_entrada', $ultimaEntrada->No_orden)
                ->latest('fecha_revision')
                ->first();

            if ($ultimoChecklist) {
                $ultimoChecklist->fecha_revision = $ultimoChecklist->fecha_revision
                    ? \Carbon\Carbon::parse($ultimoChecklist->fecha_revision)->format('Y-m-d')
                    : null;
            }
        }

        return response()->json([
            'vehiculo' => $vehiculo,
            'checklist' => $ultimoChecklist
        ]);
    }

//     public function store(Request $request)
// {
//     // Validar datos de salida y checklist
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
//         // Validaciones para checklist
//         'documentos_completos' => 'required|boolean',
//         'accesorios_completos' => 'required|boolean',
//         'estado_exterior' => 'nullable|in:Excelente,Bueno,Regular,Malo',
//         'estado_interior' => 'nullable|in:Excelente,Bueno,Regular,Malo',
//         'pdi_realizada' => 'required|boolean',
//         'seguro_vigente' => 'required|boolean',
//         'nfc_instalado' => 'required|boolean',
//         'gps_instalado' => 'required|boolean',
//         'folder_viajero' => 'required|boolean',
//         'recibido_por' => 'nullable|string|max:50',
//         'observaciones_checklist' => 'nullable|string',
//         'fecha_revision' => 'nullable|date',
//     ]);

//     DB::beginTransaction();

//     try {
//         $vehiculo = Vehiculo::findOrFail($request->VIN);

//         if ($vehiculo->Estado === 'Vendido') {
//             throw new \Exception("El vehículo ya fue vendido y no puede generar más salidas.");
//         }

//         // Obtener la última entrada para relacionar con la salida
//         $ultimaEntrada = Entrada::where('VIN', $request->VIN)
//             ->latest('Fecha_entrada')
//             ->first();

//         // Crear la salida
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

//         // Crear checklist de salida usando la relación
//         $salida->checklistSalida()->create([
//             'No_orden_salida' => $salida->No_orden_salida, // ✅ clave foránea correcta
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
           

//         DB::commit();

//         return redirect()->route('admin.vehiculos')
//             ->with('success', 'Salida registrada correctamente con su checklist.');

//     } catch (\Exception $e) {
//         DB::rollBack();
//         return redirect()->back()->with('error', $e->getMessage());
//     }
// }


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

            if ($vehiculo->Estado === 'Vendido') {
                throw new \Exception("El vehículo ya fue vendido y no puede generar más salidas.");
            }

            $ultimaSalida = Salida::where('VIN', $request->VIN)
                ->latest('Fecha')
                ->first();

            if ($ultimaSalida && $ultimaSalida->Tipo_salida === 'Traspaso') {
                $nuevaEntrada = Entrada::where('VIN', $request->VIN)
                    ->where('Fecha_entrada', '>', $ultimaSalida->Fecha)
                    ->exists();

                if (!$nuevaEntrada) {
                    throw new \Exception("El vehículo ya fue traspasado y no puede volver a salir hasta que se registre una nueva entrada.");
                }
            }

            $ultimaEntrada = Entrada::where('VIN', $request->VIN)
                ->latest('Fecha_entrada')
                ->first();

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
                'estatus' => 'pendiente', // 👈 ENUM válido en salidas
            ]);


                $salida->checklistSalida()->create([
            'No_orden_salida' => $salida->No_orden_salida, // ✅ clave correcta
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

            if ($request->Tipo_salida === 'Venta') {
                $salida->estatus = 'confirmada'; // ✅ ENUM válido
                $salida->save();

                $vehiculo->Estado = 'Vendido';
                $vehiculo->estatus = 'En almacén'; // ✅ ENUM válido
                $vehiculo->save();

                $vehiculo->delete();

            } elseif ($request->Tipo_salida === 'Traspaso' || $request->Tipo_salida === 'Devolucion') {
                $vehiculo->estatus = 'En tránsito'; // ✅ ENUM válido
                $vehiculo->save();

                Entrada::create([
                    'VIN' => $request->VIN,
                    'Almacen_entrada' => $request->Almacen_entrada,
                    'Almacen_salida' => $request->Almacen_salida,
                    'Fecha_entrada' => now(),
                    'Tipo' => $request->Tipo_salida,
                    'estatus' => 'pendiente', // ✅ ENUM válido en entradas
                    'Coordinador_Logistica' => auth()->user()->name,
                ]);

                $salida->estatus = 'pendiente'; // ✅ ENUM válido
                $salida->save();
            }


            DB::commit();

            return redirect()->route('admin.vehiculos')
                ->with('success', 'Salida registrada correctamente con su checklist.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
