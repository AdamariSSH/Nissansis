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
        // Obtiene el usuario actual y su almacén
        $user = Auth::user();
        $userAlmacenId = $user->almacen_id;
        $isAdmin = $user->role === 'admin'; 

        foreach ($rows as $row) {
            // Valida que el VIN, motor y modelo existan.
            $validator = Validator::make($row->toArray(), [
                'vin' => 'required|string',
                'motor' => 'required|string',
                'modelo' => 'required|string',
                'tipo' => 'required|string', // Aseguramos que el tipo exista
            ]);

            if ($validator->fails()) {
                Log::warning('Fila de importación inválida:', $row->toArray());
                Log::warning('Errores:', $validator->errors()->toArray());
                continue; 
            }

            $vin = $row['vin'] ?? null;
            $tipoEntrada = $row['tipo'];

            // VALIDACIÓN DE EXISTENCIA CORREGIDA
            // La validación anterior era incorrecta para Traspasos. Ahora solo chequeamos si existe 
            // y lanzamos un error claro si un Traspaso no tiene vehículo en inventario.
            if (($tipoEntrada === 'Traspaso' || $tipoEntrada === 'Devolucion') && !Vehiculo::where('VIN', $vin)->exists()) {
                throw new \Exception("El VIN {$vin} no existe en el inventario (tabla vehiculos). No se puede crear una entrada de tipo '{$tipoEntrada}'.");
            }
            // Para Madrina, permitimos que pase, ya que lo crearemos temporalmente a continuación.


            // --- Validación de almacén ---
            $almacenEntrada = $row['almacen_entrada'] ?? null;

            if (!$isAdmin) {
                // Si NO es admin, validamos que coincida con su almacén asignado
                if (empty($almacenEntrada) || (int)$almacenEntrada !== (int)$userAlmacenId) {
                    $vin = $row['vin'] ?? 'Desconocido';
                    throw new \Exception(
                        "El VIN {$vin} fue rechazado porque el almacén de entrada ({$almacenEntrada}) 
                        no coincide con tu almacén asignado ({$userAlmacenId})."
                    );
                }
            } else {
                // Si es admin y no viene definido el almacén, usamos uno por defecto
                if (empty($almacenEntrada)) {
                    $almacenEntrada = $userAlmacenId ?? 1;
                }
            }
            // --- Fin validación de almacén ---

            // Normalizar valores booleanos
            $bool = fn($value) => filter_var($value, FILTER_VALIDATE_BOOLEAN);

            // Fechas
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

             if ($vin && strlen($vin) > 17) {
                // Lanzar una excepción específica para el error de longitud
                throw new \Exception(
                    "El VIN {$vin} fue rechazado: Su longitud es de " . strlen($vin) . " caracteres. El VIN debe tener un máximo de 17 caracteres."
                );
            }

            $proximoMantenimiento = Carbon::parse($fechaEntrada)->addDays(30)->toDateString();

            // ----------------------------------------------------
            //  LÓGICA CLAVE PARA EVITAR EL ERROR 
            // ----------------------------------------------------
            if ($tipoEntrada === 'Madrina' || $tipoEntrada === 'Otro') {
                // Insertamos el vehículo en 'vehiculos' (tabla madre) con estatus temporal.

                $vehiculo = Vehiculo::updateOrCreate(
                ['VIN' => $vin],
                [
                    'Motor' => $row['motor'],
                    'Caracteristicas' => $row['caracteristicas'] ?? $row['modelo'], 
                    'Color' => $row['color'] ?? 'No especificado',
                    'Modelo' => $row['modelo'],
                    'Proximo_mantenimiento' => $proximoMantenimiento,
                    'Estado' => $row['estado'] ?? 'Pendiente de Revisión', 
                    'estatus' => 'En almacén', // Estatus logístico actualizado
                    'Coordinador_Logistica' => Auth::user()->name ?? 'Sistema',
                    'Almacen_actual' => $almacenEntrada, // ESTE ES EL CAMPO CLAVE
                    'tipo' => $tipoEntrada, // Almacenamos el tipo asociado
                ]
            );
                
            }
            
            // 2. Crear entrada (Ahora el VIN existe en vehiculos, por lo que pasa la FK)
            $entrada = Entrada::create([
                'VIN' => $vin,
                'Kilometraje_entrada' => $row['kilometraje_entrada'] ?? 0,
                'Almacen_entrada' => $almacenEntrada,
                'Fecha_entrada' => $fechaEntrada ?? now(),
                'Tipo' => $tipoEntrada,
                'Observaciones' => $row['observaciones'] ?? null,
                'Coordinador_Logistica' => Auth::user()->name ?? 'Sistema',
                'estatus' => 'pendiente', // Se crea como pendiente para la revisión manual
            ]);

            // Crear checklist
            Checklist::create([
                'No_orden_entrada' => $entrada->No_orden,
                'tipo_checklist' => $tipoEntrada,
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