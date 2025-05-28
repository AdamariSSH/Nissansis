<?php

namespace App\Imports;

use App\Models\Entrada;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class EntradasImport implements ToModel, WithHeadingRow
{
    // public function model(array $fila)
    // {
    //     // Normaliza las claves
    //     $fila = array_change_key_case($fila, CASE_LOWER);

    //     // 🔥 Primero convierto fecha_entrada antes de validar
    //     if (isset($fila['fecha_entrada'])) {
    //         if (is_numeric($fila['fecha_entrada'])) {
    //             try {
    //                 $fila['fecha_entrada'] = Date::excelToDateTimeObject($fila['fecha_entrada'])->format('Y-m-d');
    //             } catch (\Exception $e) {
    //                 $fila['fecha_entrada'] = null;
    //             }
    //         }
    //     }

    //     // 🔥 Convierto modelo a string si no lo es
    //     if (isset($fila['modelo']) && !is_string($fila['modelo'])) {
    //         $fila['modelo'] = (string) $fila['modelo'];
    //     }

    //     // Ahora sí hago la validación
    //     $validator = Validator::make($fila, [
    //         'vin' => 'nullable|unique:entradas,vin',
    //         'motor' => 'required|string',
    //         'version' => 'required|string',
    //         'color' => 'required|string',
    //         'modelo' => 'required|string',
    //         'almacen_entrada' => 'nullable|integer',
    //         'almacen_salida' => 'nullable|integer',
    //         'fecha_entrada' => 'nullable|date',
    //         'estado' => 'nullable|string',
    //         //'movimientos' => 'nullable|string',
    //         'tipo' => 'nullable|string',
    //         'coordinador_logistica' => 'nullable|string',
    //     ]);

    //     if ($validator->fails()) {
    //         Log::warning('Fila de importación inválida: ' . json_encode($fila) . ' - Errores: ' . json_encode($validator->errors()));
    //         return null;
    //     }

    //     return new Entrada([
    //         'VIN' => $fila['vin'] ?? null,
    //         'Motor' => $fila['motor'],
    //         'Version' => $fila['version'],
    //         'Color' => $fila['color'],
    //         'Modelo' => $fila['modelo'],
    //         'Almacen_entrada' => $fila['almacen_entrada'] ?? null,
    //         'Almacen_salida' => $fila['almacen_salida'] ?? null,
    //         'Fecha_entrada' => $fila['fecha_entrada'] ?? null,
    //         'Estado' => $fila['estado'] ?? null,
    //         //'Movimientos' => $fila['movimientos'] ?? null,
    //         'Tipo' => $fila['tipo'] ?? null,
    //         'Coordinador_Logistica' => $fila['coordinador_logistica'] ?? null
    //     ]);
    // }

    // public function headingRow(): int
    // {
    //     return 1;
    // }





    // esto lo puse el 01/04/2025 
    public function model(array $fila)
{
    $fila = array_change_key_case($fila, CASE_LOWER);
    unset($fila['movimientos']); // 👈 Elimina campo no existente en la tabla

    if (isset($fila['fecha_entrada'])) {
        if (is_numeric($fila['fecha_entrada'])) {
            try {
                $fila['fecha_entrada'] = Date::excelToDateTimeObject($fila['fecha_entrada'])->format('Y-m-d');
            } catch (\Exception $e) {
                $fila['fecha_entrada'] = null;
            }
        }
    }

    if (isset($fila['modelo']) && !is_string($fila['modelo'])) {
        $fila['modelo'] = (string) $fila['modelo'];
    }

    $validator = Validator::make($fila, [
        'vin' => 'nullable|unique:entradas,vin',
        'motor' => 'required|string',
        'version' => 'required|string',
        'color' => 'required|string',
        'modelo' => 'required|string',
        'almacen_entrada' => 'nullable|integer',
        'almacen_salida' => 'nullable|integer',
        'fecha_entrada' => 'nullable|date',
        'estado' => 'nullable|string',
        'tipo' => 'nullable|string',
        'coordinador_logistica' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        Log::warning('Fila de importación inválida: ' . json_encode($fila) . ' - Errores: ' . json_encode($validator->errors()));
        return null;
    }

    return new Entrada([
        'VIN' => $fila['vin'] ?? null,
        'Motor' => $fila['motor'],
        'Version' => $fila['version'],
        'Color' => $fila['color'],
        'Modelo' => $fila['modelo'],
        'Almacen_entrada' => $fila['almacen_entrada'] ?? null,
        'Almacen_salida' => $fila['almacen_salida'] ?? null,
        'Fecha_entrada' => $fila['fecha_entrada'] ?? null,
        'Estado' => $fila['estado'] ?? null,
        'Tipo' => $fila['tipo'] ?? null,
        'Coordinador_Logistica' => $fila['coordinador_logistica'] ?? null
    ]);
}

}
