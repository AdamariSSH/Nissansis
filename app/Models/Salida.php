<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Almacen; 
use App\Models\ChecklistSalida; 

class Salida extends Model
{
    protected $table = 'salidas';
    protected $primaryKey = 'No_orden_salida';
    public $incrementing = true;
   //  ACTIVA los timestamps
    public $timestamps = true;

    //  indica que estos campos son de tipo fecha (Carbon)
    protected $dates = ['created_at', 'updated_at'];

    protected $fillable = [
        'VIN',
        'Motor',
        'Caracteristicas',
        'Color',
        'Tipo_salida',
        'estatus',
        'Almacen_salida',
        'Fecha',
        'Modelo',
        'No_orden_entrada',
        'Almacen_entrada'
    ];


    public function checklistSalida()
    {
        // La relación usa el primary key de Salida ('No_orden_salida') 
        // y busca en la columna 'No_orden_salida' en la tabla de ChecklistSalida
        return $this->hasOne(ChecklistSalida::class, 'No_orden_salida', 'No_orden_salida');
    }

 

    // Relación con almacén de salida
    public function almacenSalida()
    {
        return $this->belongsTo(Almacen::class, 'Almacen_salida', 'Id_Almacen');
    }
    
    // Relación con almacén de entrada (si lo necesitas)
    public function almacenEntrada()
    {
        return $this->belongsTo(Almacen::class, 'Almacen_entrada', 'Id_Almacen');
    }
    
    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'Almacen_salida', 'Id_Almacen');
    }
     public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'VIN', 'VIN');
    }


public function ultimoChecklistSalida()
{
    // Relación: una salida tiene un checklist (último) por No_orden_salida
    return $this->hasOne(ChecklistSalida::class, 'No_orden_salida', 'No_orden_salida')
                ->latest('id_checklist_salida'); // ordena por id para traer el último
}
}