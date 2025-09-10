<?php
// namespace App\Imports;

// use App\Models\Entrada;
// use App\Models\Vehiculo;
// use App\Models\Checklist;
// use Illuminate\Support\Carbon;
// use Illuminate\Support\Collection;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Facades\Validator;
// use Maatwebsite\Excel\Concerns\ToCollection;
// use Maatwebsite\Excel\Concerns\WithHeadingRow;
// use PhpOffice\PhpSpreadsheet\Shared\Date;

// class EntradasImport implements ToCollection, WithHeadingRow
// {
    
//     public function collection(Collection $rows)
// {
//     $userAlmacenId = Auth::user()->almacen_id;

//     foreach ($rows as $row) {
//         // Validar almacén del registro
//         if ((int)($row['almacen_entrada'] ?? 0) !== (int)$userAlmacenId) {
//             throw new \Exception(
//                 "El VIN {$row['vin']} fue rechazado porque el almacén de entrada ({$row['almacen_entrada']}) no coincide con tu almacén asignado ({$userAlmacenId})."
//             );
//         }

//         // Validar los campos básicos requeridos
//         $validator = Validator::make($row->toArray(), [
//             'vin' => 'required|string',
//             'motor' => 'required|string',
//             'modelo' => 'required|string',
//         ]);

//         if ($validator->fails()) {
//             Log::warning('Fila de importación inválida:', $row->toArray());
//             Log::warning('Errores:', $validator->errors()->toArray());
//             continue; // Saltar esta fila inválida
//         }

//         // Normalizar valores booleanos
//         $bool = fn($value) => filter_var($value, FILTER_VALIDATE_BOOLEAN);

//         // Fecha de entrada convertida
//         $fechaEntrada = $this->transformarFecha($row['fecha_entrada']);
//         $fechaRevision = $this->transformarFecha($row['fecha_revision'] ?? $row['fecha_entrada']);

//         if (!$fechaEntrada) {
//                 throw new \Exception("El VIN {$row['vin']} fue rechazado porque la fecha de entrada es inválida.");
//             }

//             // Validar que no sea pasada ni futura
//             $hoy = Carbon::today()->format('Y-m-d');
//             if ($fechaEntrada < $hoy || $fechaEntrada > $hoy) {
//                 throw new \Exception("El VIN {$row['vin']} fue rechazado porque la fecha de entrada ({$fechaEntrada}) no es válida. Solo se permiten fechas del día actual ({$hoy}).");
//             }

//         $proximoMantenimiento = Carbon::parse($fechaEntrada)->addDays(30)->toDateString();
//         //$proximoMantenimiento = $fechaEntrada ? Carbon::parse($fechaEntrada)->addDays(30)->toDateString() : null;

//         // Crear o actualizar vehículo
//         $vehiculo = Vehiculo::updateOrCreate(
//             ['VIN' => $row['vin']],
//             [
//                 'Motor' => $row['motor'],
//                 'Caracteristicas' => $row['caracteristicas'] ?? null,
//                 'Color' => $row['color'] ?? null,
//                 'Modelo' => $row['modelo'],
//                 'Coordinador_Logistica' => Auth::user()->name ?? 'Sistema',
//                 'Proximo_mantenimiento' => $proximoMantenimiento,
//                 'Almacen_actual' => $row['almacen_entrada'] ?? null,
//                 'Estado' => $row['estado'] ?? 'Mantenimiento',
//             ]
//         );

//         // Crear entrada
//         $entrada = Entrada::create([
//             'VIN' => $vehiculo->VIN,
//             'Kilometraje_entrada' => $row['kilometraje_entrada'] ?? 0,
//             'Almacen_entrada' => $row['almacen_entrada'] ?? null,
//             'Fecha_entrada' => $fechaEntrada ?? now(),
//             'Tipo' => $row['tipo'] ?? 'Desconocido',
//             'Observaciones' => $row['observaciones'] ?? null,
//             'Coordinador_Logistica' => Auth::user()->name ?? 'Sistema',
//         ]);

//         // Crear checklist
//         Checklist::create([
//             'No_orden_entrada' => $entrada->No_orden,
//             'tipo_checklist' => $row['tipo'] ?? 'Desconocido',
//             'documentos_completos' => $bool($row['documentos_completos'] ?? false),
//             'accesorios_completos' => $bool($row['accesorios_completos'] ?? false),
//             'estado_exterior' => $row['estado_exterior'] ?? null,
//             'estado_interior' => $row['estado_interior'] ?? null,
//             'pdi_realizada' => $bool($row['pdi_realizada'] ?? false),
//             'seguro_vigente' => $bool($row['seguro_vigente'] ?? false),
//             'nfc_instalado' => $bool($row['nfc_instalado'] ?? false),
//             'gps_instalado' => $bool($row['gps_instalado'] ?? false),
//             'folder_viajero' => $bool($row['folder_viajero'] ?? false),
//             'recibido_por' => $row['recibido_por'] ?? Auth::user()->name ?? 'Sistema',
//             'fecha_revision' => $fechaRevision ?? now(),
//             'observaciones' => $row['observaciones_checklist'] ?? null,
//         ]);
//     }
// }


//     private function transformarFecha($valor)
//     {
//         if (is_numeric($valor)) {
//             try {
//                 return Date::excelToDateTimeObject($valor)->format('Y-m-d');
//             } catch (\Exception $e) {
//                 Log::error("Error al convertir fecha Excel: {$valor}");
//                 return null;
//             }
//         }

//         try {
//             return Carbon::parse($valor)->format('Y-m-d');
//         } catch (\Exception $e) {
//             Log::error("Fecha inválida: {$valor}");
//             return null;
//         }
//     }
// }




// namespace App\Imports;

// use App\Models\Entrada;
// use App\Models\Vehiculo;
// use App\Models\Checklist;
// use Illuminate\Support\Carbon;
// use Illuminate\Support\Collection;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Facades\Validator;
// use Maatwebsite\Excel\Concerns\ToCollection;
// use Maatwebsite\Excel\Concerns\WithHeadingRow;
// use PhpOffice\PhpSpreadsheet\Shared\Date;

// class EntradasImport implements ToCollection, WithHeadingRow
// {
//     public function collection(Collection $rows)
//     {
//         $userAlmacenId = Auth::user()->almacen_id;

//         foreach ($rows as $row) {
//             // 🚨 Validar que exista almacén en el Excel
//             if (empty($row['almacen_entrada'])) {
//                 throw new \Exception("El VIN {$row['vin']} fue rechazado porque no tiene definido un almacén de entrada en el archivo.");
//             }

//             // Validar almacén del registro
//             if ((int)$row['almacen_entrada'] !== (int)$userAlmacenId) {
//                 throw new \Exception(
//                     "El VIN {$row['vin']} fue rechazado porque el almacén de entrada ({$row['almacen_entrada']}) no coincide con tu almacén asignado ({$userAlmacenId})."
//                 );
//             }

//             // Validar los campos básicos requeridos
//             $validator = Validator::make($row->toArray(), [
//                 'vin' => 'required|string',
//                 'motor' => 'required|string',
//                 'modelo' => 'required|string',
//             ]);

//             if ($validator->fails()) {
//                 Log::warning('Fila de importación inválida:', $row->toArray());
//                 Log::warning('Errores:', $validator->errors()->toArray());
//                 continue; // Saltar esta fila inválida
//             }

//             // Normalizar valores booleanos
//             $bool = fn($value) => filter_var($value, FILTER_VALIDATE_BOOLEAN);

//             // Fecha de entrada convertida
//             $fechaEntrada = $this->transformarFecha($row['fecha_entrada']);
//             $fechaRevision = $this->transformarFecha($row['fecha_revision'] ?? $row['fecha_entrada']);

//             if (!$fechaEntrada) {
//                 throw new \Exception("El VIN {$row['vin']} fue rechazado porque la fecha de entrada es inválida.");
//             }

//             // Validar que no sea pasada ni futura (solo hoy)
//             $hoy = Carbon::today('America/Hermosillo')->format('Y-m-d');
//                 if ($fechaEntrada !== $hoy) {
//                     throw new \Exception("El VIN {$row['vin']} fue rechazado porque la fecha de entrada ({$fechaEntrada}) no es válida. Solo se permiten fechas del día actual ({$hoy}).");
//             }

//             $proximoMantenimiento = Carbon::parse($fechaEntrada)->addDays(30)->toDateString();

//             // Crear o actualizar vehículo
//             $vehiculo = Vehiculo::updateOrCreate(
//                 ['VIN' => $row['vin']],
//                 [
//                     'Motor' => $row['motor'],
//                     'Caracteristicas' => $row['caracteristicas'] ?? null,
//                     'Color' => $row['color'] ?? null,
//                     'Modelo' => $row['modelo'],
//                     'Coordinador_Logistica' => Auth::user()->name ?? 'Sistema',
//                     'Proximo_mantenimiento' => $proximoMantenimiento,
//                     'Almacen_actual' => $row['almacen_entrada'],
//                     'Estado' => $row['estado'] ?? 'Mantenimiento',
//                 ]
//             );

//             // Crear entrada
//             $entrada = Entrada::create([
//                 'VIN' => $vehiculo->VIN,
//                 'Kilometraje_entrada' => $row['kilometraje_entrada'] ?? 0,
//                 'Almacen_entrada' => $row['almacen_entrada'],
//                 'Fecha_entrada' => $fechaEntrada ?? now(),
//                 'Tipo' => $row['tipo'] ?? 'Desconocido',
//                 'Observaciones' => $row['observaciones'] ?? null,
//                 'Coordinador_Logistica' => Auth::user()->name ?? 'Sistema',
//             ]);

//             // Crear checklist
//             Checklist::create([
//                 'No_orden_entrada' => $entrada->No_orden,
//                 'tipo_checklist' => $row['tipo'] ?? 'Desconocido',
//                 'documentos_completos' => $bool($row['documentos_completos'] ?? false),
//                 'accesorios_completos' => $bool($row['accesorios_completos'] ?? false),
//                 'estado_exterior' => $row['estado_exterior'] ?? null,
//                 'estado_interior' => $row['estado_interior'] ?? null,
//                 'pdi_realizada' => $bool($row['pdi_realizada'] ?? false),
//                 'seguro_vigente' => $bool($row['seguro_vigente'] ?? false),
//                 'nfc_instalado' => $bool($row['nfc_instalado'] ?? false),
//                 'gps_instalado' => $bool($row['gps_instalado'] ?? false),
//                 'folder_viajero' => $bool($row['folder_viajero'] ?? false),
//                 'recibido_por' => $row['recibido_por'] ?? Auth::user()->name ?? 'Sistema',
//                 'fecha_revision' => $fechaRevision ?? now(),
//                 'observaciones' => $row['observaciones_checklist'] ?? null,
//             ]);
//         }
//     }

//     private function transformarFecha($valor)
//     {
//         if (is_numeric($valor)) {
//             try {
//                 return Date::excelToDateTimeObject($valor)->format('Y-m-d');
//             } catch (\Exception $e) {
//                 Log::error("Error al convertir fecha Excel: {$valor}");
//                 return null;
//             }
//         }

//         try {
//             return Carbon::parse($valor)->format('Y-m-d');
//         } catch (\Exception $e) {
//             Log::error("Fecha inválida: {$valor}");
//             return null;
//         }
//     }
// }



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
        // Obtiene el ID del almacén del usuario actual
        $userAlmacenId = Auth::user()->almacen_id;

        foreach ($rows as $row) {
            // Valida que el VIN, motor y modelo existan.
            $validator = Validator::make($row->toArray(), [
                'vin' => 'required|string',
                'motor' => 'required|string',
                'modelo' => 'required|string',
            ]);

            if ($validator->fails()) {
                Log::warning('Fila de importación inválida:', $row->toArray());
                Log::warning('Errores:', $validator->errors()->toArray());
                continue; // Salta esta fila si no tiene datos básicos
            }

            // --- Inicio de la validación corregida ---
            // Usa el operador ?? para evitar errores si la columna no existe o está vacía.
            // La librería Maatwebsite/Excel convierte los encabezados a minúsculas por defecto.
            $almacenEntrada = $row['almacen_entrada'] ?? null;

            // Lanza una excepción si el valor del almacén es nulo o no coincide con el del usuario
            if (empty($almacenEntrada) || (int)$almacenEntrada !== (int)$userAlmacenId) {
                $vin = $row['vin'] ?? 'Desconocido';
                throw new \Exception(
                    "El VIN {$vin} fue rechazado porque el almacén de entrada ({$almacenEntrada}) no coincide con tu almacén asignado ({$userAlmacenId})."
                );
            }
            // --- Fin de la validación corregida ---

            // Normalizar valores booleanos
            $bool = fn($value) => filter_var($value, FILTER_VALIDATE_BOOLEAN);

            // Fecha de entrada convertida
            $fechaEntrada = $this->transformarFecha($row['fecha_entrada']);
            $fechaRevision = $this->transformarFecha($row['fecha_revision'] ?? $row['fecha_entrada']);

            if (!$fechaEntrada) {
                throw new \Exception("El VIN {$row['vin']} fue rechazado porque la fecha de entrada es inválida.");
            }

            // Validar que no sea pasada ni futura (solo hoy)
            $hoy = Carbon::today('America/Hermosillo')->format('Y-m-d');
            if ($fechaEntrada !== $hoy) {
                throw new \Exception("El VIN {$row['vin']} fue rechazado porque la fecha de entrada ({$fechaEntrada}) no es válida. Solo se permiten fechas del día actual ({$hoy}).");
            }

            $proximoMantenimiento = Carbon::parse($fechaEntrada)->addDays(30)->toDateString();

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
                    'Almacen_actual' => $row['almacen_entrada'],
                    'Estado' => $row['estado'] ?? 'Mantenimiento',
                ]
            );

            // Crear entrada
            $entrada = Entrada::create([
                'VIN' => $vehiculo->VIN,
                'Kilometraje_entrada' => $row['kilometraje_entrada'] ?? 0,
                'Almacen_entrada' => $row['almacen_entrada'],
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