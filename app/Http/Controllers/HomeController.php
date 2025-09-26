<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Almacen;
use App\Models\Vehiculo;
use App\Models\Entrada;
use App\Models\Salida;
use Carbon\Carbon;
use DB;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
{
    $user = auth()->user();

    // Totales
    $cantidadVehiculos = Vehiculo::count();
    $cantidadAlmacenes = Almacen::count();

    // Stock actual: vehículos cuyo campo 'estatus' sea 'En almacén'
    $stockActual = Vehiculo::where('estatus', 'En almacén')->count();

    // Entradas y salidas del día
    $entradasHoy = Entrada::whereDate('Fecha_entrada', Carbon::today())->count();
    $salidasHoy = Salida::whereDate('Fecha', Carbon::today())->count();

    // Vehículos con mantenimiento HOY o en los próximos 7 días
    $hoy = Carbon::today();
    $vehiculosMantenimiento = Vehiculo::whereBetween(
    'Proximo_mantenimiento',
    [$hoy, $hoy->copy()->addDays(30)]
    )->count();
    //$vehiculosMantenimiento = Vehiculo::whereDate('Proximo_mantenimiento', '>=', $hoy)->count();

    // $vehiculosHoy = Vehiculo::whereDate('Proximo_mantenimiento', $hoy)->count();
    // $vehiculosProximos = Vehiculo::whereBetween('Proximo_mantenimiento', [$hoy, $hoy->copy()->addDays(7)])->count();

    // $vehiculosMantenimiento = $vehiculosHoy + $vehiculosProximos;

    // Entradas por mes (año actual)
    $entradasMes = Entrada::selectRaw('MONTH(Fecha_entrada) as mes, COUNT(*) as total')
        ->whereYear('Fecha_entrada', Carbon::now()->year)
        ->groupBy('mes')
        ->orderBy('mes')
        ->pluck('total','mes');

    // Salidas por mes (año actual)
    $salidasMes = Salida::selectRaw('MONTH(Fecha) as mes, COUNT(*) as total')
        ->whereYear('Fecha', Carbon::now()->year)
        ->groupBy('mes')
        ->orderBy('mes')
        ->pluck('total','mes');

    // Vehículos por almacén (Nombre => count)
    $vehiculosPorAlmacen = Almacen::withCount('vehiculos')->pluck('vehiculos_count','Nombre');

    // === NUEVOS CAMPOS ===
    if ($user->role === 'admin') {
        // Admin ve todas las pendientes
        $entradasPendientes = Entrada::where('estatus', 'pendiente')->count();
        $salidasPendientes  = Salida::where('estatus', 'pendiente')->count();
    } else {
        // Usuario normal ve solo las pendientes de su almacén
        $entradasPendientes = Entrada::where('Almacen_entrada', $user->almacen_id)
                                     ->where('estatus', 'pendiente')
                                     ->count();

        $salidasPendientes  = Salida::where('Almacen_salida', $user->almacen_id)
                                    ->where('estatus', 'pendiente')
                                    ->count();
    }

    return view('home', compact(
        'cantidadVehiculos',
        'cantidadAlmacenes',
        'stockActual',
        'entradasHoy',
        'salidasHoy',
        'vehiculosMantenimiento',
        'entradasMes',
        'salidasMes',
        'vehiculosPorAlmacen',
        'entradasPendientes',
        'salidasPendientes'
    ));
}

}
