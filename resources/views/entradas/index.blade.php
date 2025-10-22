@extends('adminlte::page')

@section('title', 'Entradas')

@section('content_header')
    <h1>Listado de Entradas</h1>
@stop

@section('content')
 <div class="container-fluid">
        <div class="card">
            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                <h4>📋 Entradas Registradas por diarias</h4>
                <div>
                    <a href="{{ route('entradas.create') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-file-import"></i> Crear Entradas
                    </a>

                    <a href="{{ route('entradas.importar') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-file-import"></i> Importar Entradas
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                    </div>
                @endif
                <table class="table table-striped table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th># Orden</th>
                            <th>VIN</th>
                            <th>Almacén Salida</th>
                            <th>Almacén Entrada</th>
                            <th>Fecha Entrada</th>
                            <th>Estado Vehículo</th>   {{-- viene del checklist --}}
                            <th>Estatus Entrada</th>  {{-- flujo del traspaso --}}
                            <th>Tipo</th>
                            <th>Coordinador_Logistica</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($entradas as $entrada)
                            <tr>
                                <td>{{ $entrada->No_orden }}</td>
                                <td>{{ $entrada->VIN }}</td>
                                <td>{{ $entrada->almacenSalida->Nombre ?? 'N/A' }}</td>
                                <td>{{ $entrada->almacenEntrada->Nombre ?? 'N/A' }}</td>
                                <td>{{ $entrada->created_at->format('d/m/Y H:i') }}</td>

                                {{-- Estado del vehículo (después del checklist) --}}
                                <td>
                                    @if($entrada->vehiculo && strtolower($entrada->vehiculo->Estado) === 'disponible')
                                        <span class="badge bg-success">Disponible</span>
                                    @elseif($entrada->vehiculo)
                                        <span class="badge bg-warning">{{ $entrada->vehiculo->Estado }}</span>
                                    @else
                                        <span class="badge bg-secondary">Sin registro</span>
                                    @endif
                                </td>

                                {{-- Estatus de la entrada (flujo del traspaso) --}}
                                <td>
                                    @if($entrada->estatus == 'pendiente')
                                        <span class="badge bg-warning">Pendiente</span>
                                    @elseif($entrada->estatus == 'confirmada')
                                        <span class="badge bg-success">Confirmada</span>
                                    @elseif($entrada->estatus == 'rechazada')
                                        <span class="badge bg-danger">Rechazada</span>
                                    @endif
                                </td>

                                <td>{{ $entrada->Tipo }}</td>
                                <td>{{ $entrada->Coordinador_Logistica ?? 'No registrado' }}</td>

                                <td>
                                    {{-- Botones de acción --}}
                                    <a href="{{ route('entradas.edit', $entrada->No_orden) }}" class="btn btn-sm btn-primary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    {{-- <a href="{{ route('entradasimprimir', ['id' => $entrada->No_orden]) }}" target="_blank" class="btn btn-sm btn-secondary" title="Imprimir">
                                        <i class="fas fa-print"></i>
                                    </a> --}}
                                   {{-- <a href="{{ route('entradasimprimir', ['id' => $entrada->No_orden]) }}" 
                                    target="_blank" class="btn btn-sm btn-secondary" title="Imprimir">
                                    <i class="fas fa-print"></i>
                                    </a> --}}
                                    <a href="{{ route('entradasimprimir', ['id' => $entrada->No_orden]) }}" 
                                        class="btn btn-sm btn-secondary" title="Imprimir">
                                        <i class="fas fa-print"></i>
                                    </a>

                                    {{-- Confirmar/Rechazar solo si está pendiente
                                    @if($entrada->estatus == 'pendiente')
                                        <form action="{{ route('entradas.confirmar', $entrada->No_orden) }}" 
                                              method="POST" style="display:inline;">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="fas fa-check"></i> Confirmar
                                            </button>
                                        </form>

                                        <form action="{{ route('entradas.rechazar', $entrada->No_orden) }}" 
                                              method="POST" style="display:inline;">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-times"></i> Rechazar
                                            </button>
                                        </form>
                                    @endif --}}
                                    {{-- Confirmar/Rechazar solo si está pendiente y NO es Madrina --}}
                                    @if($entrada->estatus == 'pendiente' && $entrada->Tipo !== 'Madrina')
                                        <form action="{{ route('entradas.confirmar', $entrada->No_orden) }}" 
                                            method="POST" style="display:inline;">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="fas fa-check"></i> Confirmar
                                            </button>
                                        </form>

                                        <form action="{{ route('entradas.rechazar', $entrada->No_orden) }}" 
                                            method="POST" style="display:inline;">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-times"></i> Rechazar
                                            </button>
                                        </form>
                                    @endif


                                    <form action="{{ route('entradas.destroy', $entrada->No_orden) }}" 
                                          method="POST" style="display:inline;" 
                                          onsubmit="return confirm('¿Seguro que quieres eliminar esta entrada?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm ml-1" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="d-flex justify-content-center mt-3">
                    {{ $entradas->links('pagination::bootstrap-4') }}
                </div>

            </div>
        </div>
    </div>
@stop
