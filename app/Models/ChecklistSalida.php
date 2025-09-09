<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Salida;

// class ChecklistSalida extends Model
// {
//     protected $table = 'checklist_salidas';
//     protected $primaryKey = 'id_checklist_salida';
//     public $incrementing = true;
//     public $timestamps = true;

//     protected $fillable = [
//           'No_orden_salida',
//         'documentos_completos',
//         'accesorios_completos',
//         'estado_exterior',
//         'estado_interior',
//         'pdi_realizada',
//         'seguro_vigente',
//         'nfc_instalado',
//         'gps_instalado',
//         'folder_viajero',
//         'observaciones',
//         'recibido_por',
//         'fecha_revision',
//     ];

//     // Relación con la salida
//     public function salida()
//     {
//         return $this->belongsTo(Salida::class, 'No_orden_salida', 'No_orden_salida');
//     }
// }


class ChecklistSalida extends Model
{
    protected $table = 'checklist_salidas';
    protected $primaryKey = 'id_checklist_salida';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'No_orden_salida',
        'documentos_completos',
        'accesorios_completos',
        'estado_exterior',
        'estado_interior',
        'pdi_realizada',
        'seguro_vigente',
        'nfc_instalado',
        'gps_instalado',
        'folder_viajero',
        'observaciones',
        'recibido_por',
        'fecha_revision',
    ];

    // Relación con la salida
    public function salida()
    {
        return $this->belongsTo(Salida::class, 'No_orden_salida', 'No_orden_salida');
    }
}