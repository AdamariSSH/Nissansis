<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Entrada extends Model
{
    protected $table = 'entradas';
    protected $primaryKey = 'No_orden';

    public $incrementing = true; //  porque No_orden es autoincremental
    protected $keyType = 'int'; //  porque es de tipo entero
    public $timestamps = true;


    protected $fillable = [
        'VIN', 'Almacen_entrada', 'Fecha_entrada', 'Estado',
        'Tipo', 'Almacen_salida', 'Coordinador_Logistica','estatus',
        'Proximo_mantenimiento', 'Kilometraje_entrada', 'Movimientos'
    ];

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'VIN', 'VIN');
    }

    

    public function checklist()
    {
        return $this->hasOne(Checklist::class, 'No_orden_entrada', 'No_orden');
    }
    // app/Models/Entrada.php
    public function checklistSalida()
    {
        return $this->hasOne(ChecklistSalida::class, 'No_orden_salida', 'No_orden');
    }






    // Relación con el almacén de entrada
public function almacenEntrada()
{
    return $this->belongsTo(Almacen::class, 'Almacen_entrada', 'Id_Almacen');
}

// Relación con el almacén de salida (si aplica)
public function almacenSalida()
{
    return $this->belongsTo(Almacen::class, 'Almacen_salida', 'Id_Almacen');
}

   // Relación con almacén
    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'Almacen_entrada', 'Id_Almacen');
    }
}
