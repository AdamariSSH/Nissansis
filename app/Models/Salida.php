<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;



class Salida extends Model
{
    protected $table = 'salidas';
    protected $primaryKey = 'No_orden_salida';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'VIN',
        'Motor',
        'Caracteristicas',
        'Color','
        Tipo_salida',
        'estatus',
        'Almacen_salida'
        ,'Fecha',
        'Modelo',
        'No_orden_entrada',
        'Almacen_entrada'
    ];


    public function checklistSalida()
    {
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

}
