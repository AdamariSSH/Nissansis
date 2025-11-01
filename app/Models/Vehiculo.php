<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
// use App\Models\Entrada;
// use App\Models\Salida;   
// use App\Models\Almacen;

// class Vehiculo extends Model
// {
//     protected $table = 'vehiculos';
//     protected $primaryKey = 'VIN';
//     public $incrementing = false; // Porque VIN no es autoincremental
//     protected $keyType = 'string';

//     protected $fillable = [
//         'VIN', 'Motor', 'Caracteristicas', 'Color', 'Modelo',
//         'Almacen_entrada', 'Historial',
//         'Proximo_mantenimiento', 'Tipo', 'Estado','estatus',
//         'Coordinador_Logistica', 'Almacen_actual'
//     ];

//     // Relación con las entradas del vehículo
//     public function entradas()
//     {
//         return $this->hasMany(Entrada::class, 'VIN', 'VIN');
//     }

//     public function almacen()
//     {
//         return $this->belongsTo(Almacen::class, 'Almacen_actual', 'Id_Almacen');
//     }
//    public function ultimaEntradatipo()
//     {
//          return $this->hasOne(Entrada::class, 'VIN', 'VIN')->latestOfMany('created_at');
//     }
    
//      public function salidas()
//     {
//         return $this->hasMany(Salida::class, 'VIN', 'VIN');
//     }


// }




namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth; // <-- Importación necesaria para usar Auth
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
    
    /**
     * Aplica un scope global para restringir la visibilidad de los vehículos.
     * Los usuarios que no son 'admin' solo pueden ver los vehículos
     * que tienen el mismo 'Almacen_actual' que su 'almacen_id' asignado.
     */
    protected static function booted()
    {
        // Verificar si hay un usuario logueado
        if (Auth::check()) {
            $user = Auth::user();

            // Aplicar el filtro GLOBAL si el usuario NO es un 'admin'
            if ($user->role !== 'admin') {
                $userAlmacenId = $user->almacen_id;
                
                // Restricción: solo vehículos que coincidan con el ID de almacén del usuario
                static::addGlobalScope('almacen_restriccion', function (Builder $builder) use ($userAlmacenId) {
                    $builder->where('Almacen_actual', $userAlmacenId);
                });
            }
        }
    }

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