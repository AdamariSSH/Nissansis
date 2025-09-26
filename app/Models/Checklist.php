<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Entrada; // 

class Checklist extends Model
{
    protected $table = 'checklist';
    protected $primaryKey = 'id_checklist';
    public $incrementing = true;
    public $timestamps = true; // ✔️ la tabla sí tiene created_at / updated_at


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


