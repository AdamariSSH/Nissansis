<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salida extends Model
{
    protected $table = 'salidas';

    protected $primaryKey = 'id_salida';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'VIN',
        'Motor',
        'Version',
        'Color',
        'Tipo_salida',
        'Almacen_salida',
        'Almacen_entrada',
        'Fecha',
        'Modelo'
    ];
}
