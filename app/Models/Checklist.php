<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Entrada; // 👈 Importación necesaria

class Checklist extends Model
{
    protected $table = 'checklist';
    protected $primaryKey = 'id_checklist';


    protected $fillable = [
        'No_orden_entrada', 'tipo_checklist', 'documentos_completos',
        'accesorios_completos', 'estado_exterior', 'estado_interior',
        'pdi_realizada', 'seguro_vigente', 'nfc_instalado', 'gps_instalado',
        'observaciones', 'recibido_por', 'fecha_revision', 'folder_viajero'
    ];

    public function entrada()
    {
        return $this->belongsTo(Entrada::class, 'No_orden_entrada', 'No_orden');
    }
}
