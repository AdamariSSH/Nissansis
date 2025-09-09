@extends('adminlte::page')

@section('title', 'Vehículos')

@section('content_header')
    <h1>Listado de Vehículos</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header bg-gradient-secondary text-white d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Vehículos Registrados</h4>
            </div>

            <div class="card-body">

                {{-- FORMULARIO DE FILTRO --}}
                <form method="GET" action="{{ route('admin.vehiculos') }}" class="mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <input type="text" name="vin" class="form-control" placeholder="Buscar por VIN" value="{{ request('vin') }}">
                        </div>

                        <div class="col-md-3">
                            <select name="estado" class="form-control">
                                <option value="">-- Estado --</option>
                                <option value="Disponible" {{ request('estado') == 'Disponible' ? 'selected' : '' }}>Disponible</option>
                                <option value="Mantenimiento" {{ request('estado') == 'Mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                                <option value="Entregado" {{ request('estado') == 'Entregado' ? 'selected' : '' }}>Entregado</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <select name="almacen_id" class="form-control">
                                <option value="">-- Almacén --</option>
                                @foreach($almacenes as $almacen)
                                    <option value="{{ $almacen->Id_Almacen }}" {{ request('almacen_id') == $almacen->Id_Almacen ? 'selected' : '' }}>
                                        {{ $almacen->Nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 d-flex align-items-start gap-2">
                            <button type="submit" class="btn btn-secondary">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                            <a href="{{ route('admin.vehiculos') }}" class="btn btn-secondary">
                                <i class="fas fa-eraser"></i> Limpiar
                            </a>
                        </div>
                    </div>
                </form>

                {{-- BOTONES PARA ACCIONES ADICIONALES --}}
                <div class="mb-4 d-flex gap-2">
                    <a href="{{ route('admin.entradas.create') }}" class="btn btn-secondary">
                        <i class="fas fa-plus"></i> Agregar Vehículo
                    </a>

                    <a href="{{ route('entradas.importar') }}" class="btn btn-secondary">
                        <i class="fas fa-file-import"></i> Importar Entradas
                    </a>

                    <a href="{{ route('salidas.create') }}" class="btn btn-secondary">
                        <i class="fas fa-sign-out-alt"></i> Realizar Salidas
                    </a>
                </div>

                {{-- TABLA DE VEHÍCULOS --}}
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>VIN</th>
                                <th>Motor</th>
                                <th>Caracteristicas</th>
                                <th>Color</th>
                                <th>Modelo</th>
                                <th>Fecha Entrada</th>
                                <th>Estado vehículo</th>
                                <th>Estatus vehículo</th>
                                <th>Almacén Actual</th>
                                <th>Tipo</th>
                                <th>Próximo Mantenimiento</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vehiculos as $vehiculo)
                                <tr>
                                    <td>{{ $vehiculo->VIN }}</td>
                                    <td>{{ $vehiculo->Motor }}</td>
                                    <td>{{ $vehiculo->Caracteristicas }}</td>
                                    <td>{{ $vehiculo->Color }}</td>
                                    <td>{{ $vehiculo->Modelo }}</td>
                                    <td>{{ optional($vehiculo->ultimaEntrada)->Fecha_entrada ?? 'Sin entrada' }}</td>

                                    {{-- Estado físico --}}
                                    <td>
                                        @if($vehiculo->Estado === 'disponible')
                                            <span class="badge badge-success">Disponible</span>
                                        @else
                                            <span class="badge badge-warning">Mantenimiento</span>
                                        @endif
                                    </td>

                                    {{-- Estatus del vehículo --}}
                                    <td>
                                        @if($vehiculo->estatus === 'En almacén')
                                            <span class="badge badge-success">En almacén</span>
                                        @elseif($vehiculo->estatus === 'En tránsito')
                                            <span class="badge badge-warning">En tránsito</span>
                                        @elseif($vehiculo->estatus === 'Pendiente salida')
                                            <span class="badge badge-info">Pendiente salida</span>
                                        @elseif(optional($vehiculo->ultimaEntrada)->estatus === 'pendiente')
                                            <span class="badge badge-secondary">Pendiente entrada</span>
                                        @else
                                            <span class="badge badge-light">Sin estado</span>
                                        @endif
                                    </td>


                                    <td>{{ optional(optional($vehiculo->ultimaEntrada)->almacenEntrada)->Nombre ?? 'No asignado' }}</td>
                                    <td>{{ optional($vehiculo->ultimaEntradatipo)->Tipo ?? 'Sin entrada' }}</td>
                                    <td>{{ $vehiculo->Proximo_mantenimiento }}</td>
                                    
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            @if ($vehiculo->ultimaEntrada)
                                                {{-- <a href="{{ route('entradas.edit', $vehiculo->ultimaEntrada->No_orden) }}" class="btn btn-warning btn-sm" title="Editar Entrada">
                                                    <i class="fas fa-edit"></i>
                                                </a> --}}
                                                <a href="{{ route('entradas.edit', $vehiculo->VIN) }}" class="btn btn-warning btn-sm" title="Editar Entrada">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <a href="{{ route('entradasimprimir', ['id' => $vehiculo->ultimaEntrada->No_orden]) }}" target="_blank" class="btn btn-primary btn-sm" title="Imprimir Entrada">
                                                    <i class="fas fa-print"></i>
                                                </a>

                                                {{-- Botón Eliminar --}}
                                                <form action="{{ route('vehiculos.destroy', $vehiculo->VIN) }}" method="POST" onsubmit="return confirm('¿Eliminar vehículo y todas sus entradas?')" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-sm" title="Eliminar Vehículo">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted" title="No hay entrada reciente">
                                                    <i class="fas fa-minus-circle"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center">No se encontraron vehículos.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINACIÓN --}}
                <div class="d-flex justify-content-center mt-4">
                    {{ $vehiculos->links('pagination::bootstrap-4') }}
                </div>

            </div>
        </div>
    </div>
@stop

@section('js')
    <script> console.log("Vehículos cargados correctamente!"); </script>
@stop
