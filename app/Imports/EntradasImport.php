<?php
namespace App\Imports;

use App\Models\Entrada;
use App\Models\Vehiculo;
use App\Models\Checklist;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class EntradasImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Validar los campos básicos requeridos
            $validator = Validator::make($row->toArray(), [
                'vin' => 'required|string',
                'motor' => 'required|string',
                'modelo' => 'required|string',
            ]);

            if ($validator->fails()) {
                Log::warning('Fila de importación inválida:', $row->toArray());
                Log::warning('Errores:', $validator->errors()->toArray());
                continue; // Saltar esta fila inválida
            }

            // Normalizar valores booleanos
            $bool = fn($value) => filter_var($value, FILTER_VALIDATE_BOOLEAN);

            // Fecha de entrada convertida
            $fechaEntrada = $this->transformarFecha($row['fecha_entrada']);
            $fechaRevision = $this->transformarFecha($row['fecha_revision'] ?? $row['fecha_entrada']);
            $proximoMantenimiento = $fechaEntrada ? Carbon::parse($fechaEntrada)->addDays(30)->toDateString() : null;

            // Crear o actualizar vehículo
            $vehiculo = Vehiculo::updateOrCreate(
                ['VIN' => $row['vin']],
                [
                    'Motor' => $row['motor'],
                    'Caracteristicas' => $row['caracteristicas'] ?? null,
                    'Color' => $row['color'] ?? null,
                    'Modelo' => $row['modelo'],
                    'Coordinador_Logistica' => Auth::user()->name ?? 'Sistema',
                    'Proximo_mantenimiento' => $proximoMantenimiento,
                    'Almacen_actual' => $row['almacen_entrada'] ?? null,
                    'Estado' => $row['estado'] ?? 'Mantenimiento',
                ]
            );

            // Crear entrada
            $entrada = Entrada::create([
                'VIN' => $vehiculo->VIN,
                'Kilometraje_entrada' => $row['kilometraje_entrada'] ?? 0,
                'Almacen_entrada' => $row['almacen_entrada'] ?? null,
                'Fecha_entrada' => $fechaEntrada ?? now(),
                'Tipo' => $row['tipo'] ?? 'Desconocido',
                'Observaciones' => $row['observaciones'] ?? null,
                'Coordinador_Logistica' => Auth::user()->name ?? 'Sistema',
            ]);

            // Crear checklist
            Checklist::create([
                'No_orden_entrada' => $entrada->No_orden,
                'tipo_checklist' => $row['tipo'] ?? 'Desconocido',
                'documentos_completos' => $bool($row['documentos_completos'] ?? false),
                'accesorios_completos' => $bool($row['accesorios_completos'] ?? false),
                'estado_exterior' => $row['estado_exterior'] ?? null,
                'estado_interior' => $row['estado_interior'] ?? null,
                'pdi_realizada' => $bool($row['pdi_realizada'] ?? false),
                'seguro_vigente' => $bool($row['seguro_vigente'] ?? false),
                'nfc_instalado' => $bool($row['nfc_instalado'] ?? false),
                'gps_instalado' => $bool($row['gps_instalado'] ?? false),
                'folder_viajero' => $bool($row['folder_viajero'] ?? false),
                'recibido_por' => $row['recibido_por'] ?? Auth::user()->name ?? 'Sistema',
                'fecha_revision' => $fechaRevision ?? now(),
                'observaciones' => $row['observaciones_checklist'] ?? null,
            ]);
        }
    }

    private function transformarFecha($valor)
    {
        if (is_numeric($valor)) {
            try {
                return Date::excelToDateTimeObject($valor)->format('Y-m-d');
            } catch (\Exception $e) {
                Log::error("Error al convertir fecha Excel: {$valor}");
                return null;
            }
        }

        try {
            return Carbon::parse($valor)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::error("Fecha inválida: {$valor}");
            return null;
        }
    }
}
