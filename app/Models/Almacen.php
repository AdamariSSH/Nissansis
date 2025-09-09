<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Almacen extends Model
{
    use HasFactory;

    protected $table = 'almacen'; 
    protected $primaryKey = 'Id_Almacen'; 
    public $timestamps = false; 

    protected $fillable = [
        'Nombre',
        'Direccion',
    ];

        // Relación: un almacén tiene muchos usuarios
    public function usuarios()
    {
        return $this->hasMany(User::class, 'almacen_id', 'Id_Almacen');
    }

    // Relación: un almacén tiene muchos vehículos
    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class, 'Almacen_actual', 'Id_Almacen');
    }

    // Relación: un almacén tiene muchas entradas
    public function entradas()
    {
        return $this->hasMany(Entrada::class, 'Almacen_entrada', 'Id_Almacen');
    }


}
