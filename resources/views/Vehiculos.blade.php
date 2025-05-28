@extends('adminlte::page')

@section('title', 'Vehículos')

@section('content_header')
    <h1>Listado de Vehículos</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h4>🚗 Vehículos Registrados</h4>
            </div>
            <div class="card-body">

                {{-- FORMULARIO DE FILTROS --}}
                <form method="GET" action="{{ route('admin.vehiculos') }}" class="mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <input type="text" name="vin" class="form-control" placeholder="Buscar por VIN" value="{{ request('vin') }}">
                        </div>
                        
                        <div class="col-md-3">
                            <select name="estado" class="form-control">
                                <option value="">-- Estado --</option>
                                <option value="Disponible" {{ request('estado') == 'Disponible' ? 'selected' : '' }}>Disponible</option>
                                <option value="En mantenimiento" {{ request('estado') == 'En mantenimiento' ? 'selected' : '' }}>En mantenimiento</option>
                                <option value="Entregado" {{ request('estado') == 'Entregado' ? 'selected' : '' }}>Entregado</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">🔎 Buscar</button>
                        <a href="{{ route('admin.vehiculos') }}" class="btn btn-secondary">❌ Limpiar</a>
                    </div>
                </form>

                {{-- TABLA DE VEHÍCULOS --}}
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>VIN</th>
                                <th>Motor</th>
                                <th>Versión</th>
                                <th>Color</th>
                                <th>Modelo</th>
                                <th>Fecha Entrada</th>
                                <th>Estado</th>
                                <th>Movimientos</th>
                                <th>Almacen Actual</th>
                                <th>Tipo</th>
                                <th>Coordinador Logística</th>
                                <th>Próximo Mantenimiento</th>
                                <th>Historial</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vehiculos as $vehiculo)
                                <tr>
                                    <td>{{ $vehiculo->No_orden }}</td>
                                    <td>{{ $vehiculo->VIN }}</td>
                                    <td>{{ $vehiculo->Motor }}</td>
                                    <td>{{ $vehiculo->Version }}</td>
                                    <td>{{ $vehiculo->Color }}</td>
                                    <td>{{ $vehiculo->Modelo }}</td>
                                    <td>{{ $vehiculo->Fecha_entrada }}</td>
                                    <td>{{ $vehiculo->Estado }}</td>
                                    <td>{{ $vehiculo->Movimientos }}</td>
                                    <td>{{ $vehiculo->almacen->Nombre ?? 'No asignado' }}</td>
                                    <td>{{ $vehiculo->Tipo }}</td>
                                    <td>{{ $vehiculo->Coordinador_Logistica }}</td>
                                    <td>{{ $vehiculo->Proximo_mantenimiento }}</td>
                                    <td>{{ $vehiculo->Historial }}</td>
                                    <td>
                                        <a href="#" class="btn btn-warning btn-sm" title="Editar"><i class="fas fa-edit"></i></a>
                                        <button class="btn btn-danger btn-sm ml-1" title="Eliminar"><i class="fas fa-trash"></i></button>
                                        <a href="{{ route('vehiculosimprimir', ['No_orden' => $vehiculo->No_orden]) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-print"></i>
                                          </a>

                                        </a>
                                        
                                    </td>
                                    {{-- <td>
                                        <a href="#" class="btn btn-warning btn-sm" title="Editar"><i class="fas fa-edit"></i></a>
                                        <button class="btn btn-danger btn-sm ml-1" title="Eliminar"><i class="fas fa-trash"></i></button>
                                        
                                    </td> --}}
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="15" class="text-center">No se encontraron vehículos.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINACIÓN (opcional si usas ->paginate()) --}}
                {{ $vehiculos->links() }}

            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log("Vehículos cargados correctamente!"); </script>
@stop
