<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehiculos extends Model
{
    //
    public function almacen()
{

    return $this->belongsTo(Almacen::class, 'Almacen_actual', 'Id_Almacen');
}



   
}
