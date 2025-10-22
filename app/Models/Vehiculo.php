<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Entrada;
use App\Models\Salida;   
use App\Models\Almacen;

class Vehiculo extends Model
{
    protected $table = 'vehiculos';
    protected $primaryKey = 'VIN';
    public $incrementing = false; // Porque VIN no es autoincremental
    protected $keyType = 'string';

    protected $fillable = [
        'VIN', 'Motor', 'Caracteristicas', 'Color', 'Modelo',
        'Almacen_entrada', 'Historial',
        'Proximo_mantenimiento', 'Tipo', 'Estado','estatus',
        'Coordinador_Logistica', 'Almacen_actual'
    ];

    // Relación con las entradas del vehículo
    public function entradas()
    {
        return $this->hasMany(Entrada::class, 'VIN', 'VIN');
    }

    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'Almacen_actual', 'Id_Almacen');
    }
   public function ultimaEntradatipo()
    {
         return $this->hasOne(Entrada::class, 'VIN', 'VIN')->latestOfMany('created_at');
    }
    
     public function salidas()
    {
        return $this->hasMany(Salida::class, 'VIN', 'VIN');
    }


}
