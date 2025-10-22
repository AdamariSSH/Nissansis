@extends('adminlte::page')

@section('title', 'Salidas de Vehículos')

@section('content_header')
    <h1 class="mb-3">📤 Salidas de Vehículos</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="card shadow">
        <!-- Encabezado -->
        <div class="card-header bg-gradient-secondary text-white d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">Listado de Salidas</h3>
            <a href="{{ route('salidas.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nueva Salida
            </a>
        </div>

        <!-- Tabla -->
        <div class="card-body">
                

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="thead-dark text-center">
                        <tr>
                            <th>ID</th>
                            <th>VIN</th>
                            <th>Motor</th>
                            <th>Características</th>
                            <th>Color</th>
                            <th>Modelo</th>
                            <th>Almacén</th>
                            <th>Fecha</th>
                            <th>Tipo de Salida</th>
                            <th>Estatus</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salidas as $salida)
                            <tr class="align-middle">
                                <td>{{ $salida->No_orden_salida}}</td>
                                <td>{{ $salida->VIN }}</td>
                                <td>{{ $salida->Motor }}</td>
                                <td>{{ $salida->Caracteristicas }}</td>
                                <td>{{ $salida->Color }}</td>
                                <td>{{ $salida->Modelo }}</td>
                                <!-- Aquí se corrige la relación -->
                                <td>{{ optional($salida->almacenSalida)->Nombre ?? 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($salida->Fecha)->format('d/m/Y') }}</td>
                                <td>{{ $salida->Tipo_salida ?? '-' }}</td>
                                <td>
                                    @switch($salida->estatus)
                                        @case('pendiente')
                                            <span class="badge bg-warning">Pendiente</span>
                                            @break
                                        @case('confirmada')
                                            <span class="badge bg-success">Confirmada</span>
                                            @break
                                             @case('rechazada')
                                            <span class="badge bg-success">Rechazada</span>
                                            @break
                                        @case('cancelada')
                                            <span class="badge bg-danger">Cancelada</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ $salida->estatus ?? 'N/A' }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    {{-- Botones de acción --}}
                                    {{-- <a href="{{ route('entradas.edit', $entrada->No_orden) }}" class="btn btn-sm btn-primary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a> --}}
                                    {{-- <a href="{{ route('salidasimprimir', ['id' => $salida->No_orden_salida]) }}" target="_blank" class="btn btn-sm btn-secondary" title="Imprimir">
                                        <i class="fas fa-print"></i>
                                    </a> --}}
                                    {{-- <a href="{{ route('salidasimprimir', ['id' => $salida->No_orden_salida]) }}" target="_blank" class="btn btn-sm btn-secondary">
                                        <i class="fas fa-print"></i>
                                    </a> --}}
                                    <a href="{{ route('salidasimprimir', ['id' => $salida->No_orden_salida]) }}" 
                                        class="btn btn-sm btn-secondary" title="Imprimir">
                                        <i class="fas fa-print"></i>
                                    </a>

  

                                </td>
                                <td class="text-center">
                                    @if($salida->estatus == 'pendiente')
                                        <a href="{{ route('entradas.index') }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Revisar Entrada
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted">
                                    No hay salidas registradas en tu almacén.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación (si aplica) -->
            {{ $salidas->links() ?? '' }}

        </div>
    </div>
</div>
@stop


             
