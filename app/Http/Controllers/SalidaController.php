<?php

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

use Illuminate\Routing\Controller;

use BaconQrCode\Writer;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;

class SalidaController extends Controller
{
    public function index(Request $request)
        {
            $user = auth()->user();
            $estatus = $request->get('estatus'); 

            $query = Salida::with('almacen')
                         ->orderBy('Fecha', 'desc');

            if ($user->role !== 'admin') {
                // Usuario normal solo ve las salidas de su almacén
                $query->where('Almacen_salida', $user->almacen_id);
            }

            if ($estatus) {
                // Si se pide filtrar por estatus (ej: pendientes)
                $query->where('estatus', $estatus);
            }

            $salidas = $query->paginate(10);

            return view('salidas.index', compact('salidas'));
        }

    
    // Nuevo helper para obtener último checklist
    private function obtenerUltimoChecklist($vin)
    {
        // Última entrada
        $ultimaEntrada = Entrada::where('VIN', $vin)
            ->latest('created_at')
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

        return view('salidas.create', compact('almacenes', 'vehiculo', 'ultimoChecklist'));
    }

    public function getVehiculoData($vin)
    {
    
        $vehiculo = Vehiculo::with('almacen')->where('VIN', $vin)->first();


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

                // Bloquear si el vehículo está en tránsito
            if ($vehiculo->estatus === 'En tránsito') {
                throw new \Exception("El vehículo está en tránsito y no puede generar otra salida hasta que se registre la entrada en el almacén de destino.");
            }


            // Bloquear si ya tiene una salida sin entrada posterior
            $ultimaSalida = Salida::where('VIN', $request->VIN)
                ->latest('Fecha')
                ->first();

            if ($ultimaSalida && in_array($ultimaSalida->Tipo_salida, ['Traspaso', 'Devolucion'])) {
                $entradaPosterior = Entrada::where('VIN', $request->VIN)
                    ->where('created_at', '>', $ultimaSalida->Fecha)
                    ->where('Almacen_entrada', $ultimaSalida->Almacen_entrada)
                    ->first();
                if (!$entradaPosterior) {
                    throw new \Exception("El vehículo ya tuvo una salida ({$ultimaSalida->Tipo_salida}) y no puede generar otra hasta que se registre la entrada en el almacén destino.");
                }
               
            }

            // No permitir salidas si ya está vendido
            if ($vehiculo->Estado === 'Vendido') {
                throw new \Exception("El vehículo ya fue vendido y no puede generar más salidas.");
            }
            // Obtener última entrada (para relación con salida)
            $ultimaEntrada = Entrada::where('VIN', $request->VIN)
                ->latest('created_at')
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

              // Crear checklist de salida
            $salida->checklistSalida()->create([
            'No_orden_salida' => $salida->No_orden_salida,
            'documentos_completos' => $request->input('documentos_completos', 0),
            'accesorios_completos' => $request->input('accesorios_completos', 0),
            'estado_exterior' => $request->estado_exterior,
            'estado_interior' => $request->estado_interior,
            'pdi_realizada' => $request->input('pdi_realizada', 0),
            'seguro_vigente' => $request->input('seguro_vigente', 0),
            'nfc_instalado' => $request->input('nfc_instalado', 0),
            'gps_instalado' => $request->input('gps_instalado', 0),
            'folder_viajero' => $request->input('folder_viajero', 0),
            //'observaciones' => $request->observaciones_checklist,
            'observaciones' => $request->observaciones,

            'recibido_por' => $request->recibido_por,
            'fecha_revision' => $request->fecha_revision,
            ]);

            // Actualizar estatus según tipo de salida
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
                    'created_at' => now(),
                    'Tipo' => $request->Tipo_salida,
                    'estatus' => 'pendiente',
                    'Coordinador_Logistica' => auth()->user()->name,
                ]);

               
                $salida->estatus = 'confirmada'; // La salida del almacén de origen se confirma.
                $salida->save();
            }

            DB::commit();

            return redirect()->route('salidas.index')
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



    public function imprimirOrdenSalida($id)
    {
        // Cargar la salida con vehículo y almacén
        $salida = Salida::with(['vehiculo', 'almacenSalida'])
            ->where('No_orden_salida', $id)
            ->first();

        if (!$salida) {
            dd("No se encontró la salida con ID: " . $id);
        }

        // Generar QR
        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(180),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
        );
        $writer = new \BaconQrCode\Writer($renderer);
        $qrSvg = $writer->writeString($salida->VIN);
        $qrBase64 = base64_encode($qrSvg);

        $tipoChecklist = 'SALIDA';

        // Traer el último checklist de salida del vehículo
        $checklist = $salida->ultimoChecklistSalida()->first();

        return view('ordenes.salidasimprimir', [
            'tipo' => 'salida',
            'salida' => $salida,
            'qrBase64' => $qrBase64,
            'checklist' => $checklist,
            'tipoChecklist' => $tipoChecklist,
        ]);
    }
}