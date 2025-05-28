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
        'Direccion', // Agrega esta línea
    ];
}
