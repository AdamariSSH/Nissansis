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
        $userAlmacenId = $user->almacen_id;

        // --- Totales Generales (Para Admin) o Totales del Almacén (Para Usuario Normal) ---

        // Consulta base para Vehiculos, aplicando el filtro de almacén si no es admin
        $vehiculoQuery = Vehiculo::query();
        if ($user->role !== 'admin') {
            // Asumimos que los totales deben respetar el ámbito de visibilidad
            $vehiculoQuery->where('Almacen_actual', $userAlmacenId);
        }

        // Totales
        $cantidadVehiculos = $vehiculoQuery->count();
        $cantidadAlmacenes = Almacen::count(); // Almacenes siempre es el total de la empresa

        // Stock actual: vehículos cuyo campo 'estatus' sea 'En almacén'
        // NOTA: Usa la consulta base filtrada por Almacen_actual (si no es admin)
        $stockActual = (clone $vehiculoQuery)->where('estatus', 'En almacén')->count();

        // Entradas y salidas del día
        $entradasHoyQuery = Entrada::whereDate('created_at', Carbon::today());
        $salidasHoyQuery = Salida::whereDate('Fecha', Carbon::today());
        
        // Aplicar filtro de almacén a las transacciones del día
        if ($user->role !== 'admin') {
            $entradasHoyQuery->where('Almacen_entrada', $userAlmacenId);
            $salidasHoyQuery->where('Almacen_salida', $userAlmacenId);
        }
        
        // CORRECCIÓN SOLICITADA: Contar solo VINs ÚNICOS ingresados hoy
        $entradasHoy = $entradasHoyQuery->distinct('VIN')->count();
        
        // Las salidas de hoy (generalmente) cuentan transacciones
        $salidasHoy = $salidasHoyQuery->count();


        // Vehículos con mantenimiento HOY o en los próximos 30 días
        $hoy = Carbon::today();
        $vehiculosMantenimiento = (clone $vehiculoQuery)->whereBetween(
            'Proximo_mantenimiento',
            [$hoy, $hoy->copy()->addDays(30)]
        )->count();

        // Entradas por mes (año actual)
        $entradasMes = Entrada::selectRaw('MONTH(created_at) as mes, COUNT(*) as total')
            ->whereYear('created_at', Carbon::now()->year)
            // Filtro de Almacen
            ->when($user->role !== 'admin', function ($query) use ($userAlmacenId) {
                $query->where('Almacen_entrada', $userAlmacenId);
            })
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total','mes');

        // Salidas por mes (año actual)
        $salidasMes = Salida::selectRaw('MONTH(Fecha) as mes, COUNT(*) as total')
            ->whereYear('Fecha', Carbon::now()->year)
            // Filtro de Almacen
            ->when($user->role !== 'admin', function ($query) use ($userAlmacenId) {
                $query->where('Almacen_salida', $userAlmacenId);
            })
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total','mes');

        // Vehículos por almacén (Nombre => count)
        $vehiculosPorAlmacen = Almacen::withCount([
            'vehiculos' => function ($query) use ($user, $userAlmacenId) {
                // Si el usuario no es admin, solo cuenta los vehículos en su almacén actual
                if ($user->role !== 'admin') {
                    $query->where('Almacen_actual', $userAlmacenId);
                }
            }
        ])->pluck('vehiculos_count','Nombre');


        // === CÁLCULO DE PENDIENTES (CORRECCIÓN CLAVE) ===
        if ($user->role === 'admin') {
            // Admin ve todas las transacciones pendientes de VINs únicos
            $entradasPendientes = Entrada::where('estatus', 'pendiente')
                                         ->distinct('VIN')
                                         ->count();
            
            // Si el admin debe contar transacciones, deja solo count().
            $salidasPendientes  = Salida::where('estatus', 'pendiente')->count();
        } else {
            // Usuario normal ve solo los VINs únicos con entradas pendientes que le corresponden

            // Contamos VINs ÚNICOS (Vehículos) y aplicamos la lógica OR
            $entradasPendientes = Entrada::select('VIN')
                ->where('estatus', 'pendiente')
                // Aplicar la lógica OR: Entradas hechas por él O vehículos que AÚN están en su almacén
                ->where(function ($query) use ($userAlmacenId) {
                    
                    // 1. Entradas creadas en su almacén (Su historial)
                    $query->where('Almacen_entrada', $userAlmacenId);
                    
                    // 2. O Vehículos que actualmente están en su almacén (Traspasados a él)
                    $query->orWhereHas('vehiculo', function ($q) use ($userAlmacenId) {
                        // Usamos withoutGlobalScope para que el vehículo se pueda encontrar sin importar el filtro.
                        $q->withoutGlobalScope('almacen_restriccion')
                          ->where('Almacen_actual', $userAlmacenId);
                    });
                })
                ->distinct('VIN') // <-- Evita contar transacciones duplicadas
                ->count();        // Cuenta el total de VINs únicos


            // Salidas Pendientes (Aquí se mantiene el conteo de Transacciones de Salida)
            $salidasPendientes  = Salida::where('Almacen_salida', $userAlmacenId)
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