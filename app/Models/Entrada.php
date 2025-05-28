<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entrada extends Model
{
    //
    // protected $table = 'entradas'; // Nombre de la tabla en la BD

    // protected $primaryKey = 'No_orden'; // Llave primaria

    // public $timestamps = false; // Si la tabla no tiene created_at y updated_at

    // protected $fillable = [
    //     'VIN',
    //     'Motor',
    //    'Version',
    //     'Color',
    //     'Modelo',
    //     'Almacen_entrada',
    //     'Fecha_entrada',
    //     'Estado',
    //     'Acciones',
    //     'Fecha_modificacion'
    // ];

    

    protected $table = 'entradas'; // Este es el nombre de la tabla en la BD
    protected $primaryKey = 'No_orden'; // Según la imagen de tu tabla
    public $timestamps = false; // Si no usas `created_at` y `updated_at`

    protected $fillable = [
        
        'VIN', 
        'Motor', 
        'Version', 
        'Color', 
        'Modelo', 
        'Almacen_entrada', 
        'Fecha_entrada', 
        'Estado', 
        //'Movimientos', 
        'Tipo',
        'Almacen_salida',
        'Coordinador_Logistica',
    ];

    public function almacenEntrada()
{
    return $this->belongsTo(Almacen::class, 'Almacen_entrada');
}

public function almacenSalida()
{
    return $this->belongsTo(Almacen::class, 'Almacen_salida');
}
}
