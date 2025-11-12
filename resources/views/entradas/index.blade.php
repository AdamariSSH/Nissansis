@extends('adminlte::page')

@section('title', 'Entradas')

{{-- Agregar los estilos de DataTables --}}
@section('css')
    {{-- La directiva de AdminLTE para cargar el plugin de DataTables (si está configurado) --}}
    @section('plugins.Datatables', true)
@endsection

@section('content_header')
    <h1>Listado de Entradas</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                <h4>📋 Entradas Registradas</h4>
                <div>
                    {{-- Usar 'me-2' (margin-right: 2) de Bootstrap para separar los botones --}}
                    <a href="{{ route('entradas.create') }}" class="btn btn-sm btn-secondary me-2">
                        <i class="fas fa-plus"></i> Crear Entrada
                    </a>

                    <a href="{{ route('entradas.importar') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-file-import"></i> Importar Entradas
                    </a>
                </div>
            </div>
            <div class="card-body">

                {{-- Bloque de Filtros --}}
                <div class="card-body bg-light mb-3 p-3 border-bottom">
                    {{-- Elimino el 'd-flex justify-content-center' del final de los botones para que el formulario se mantenga limpio --}}
                    <form action="{{ route('entradas.index') }}" method="GET" class="row g-3 align-items-end">

                        {{-- Filtro por Fecha --}}
                        <div class="col-md-3">
                            <label for="fecha" class="form-label">Fecha de Entrada</label>
                            <input type="date" class="form-control form-control-sm" id="fecha" name="fecha" value="{{ request('fecha') }}">
                        </div>

                        {{-- Filtro por Almacén de Entrada (Asumiendo que tienes una lista de almacenes) --}}
                        <div class="col-md-3">
                            <label for="almacen_entrada_id" class="form-label">Almacén de Entrada</label>
                            <select class="form-control form-control-sm" id="almacen_entrada_id" name="almacen_entrada_id">
                                <option value="">-- Seleccionar Almacén --</option>
                                {{-- Si $almacenes está disponible, úsalo --}}
                                @foreach(/* $almacenes */ [['id' => 1, 'Nombre' => 'Almacen A'], ['id' => 2, 'Nombre' => 'Almacen B']] as $almacen)
                                    <option value="{{ $almacen['id'] }}" {{ (string)$almacen['id'] === request('almacen_entrada_id') ? 'selected' : '' }}>
                                        {{ $almacen['Nombre'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filtro por Estatus de Entrada --}}
                        <div class="col-md-2">
                            <label for="estatus" class="form-label">Estatus Entrada</label>
                            <select class="form-control form-control-sm" id="estatus" name="estatus">
                                <option value="">-- Todos --</option>
                                <option value="pendiente" {{ request('estatus') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="confirmada" {{ request('estatus') === 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                                <option value="rechazada" {{ request('estatus') === 'rechazada' ? 'selected' : '' }}>Rechazada</option>
                            </select>
                        </div>

                        {{-- Filtro por Estado del Vehículo (Asumiendo que tienes los valores) --}}
                        <div class="col-md-2">
                            <label for="estado_vehiculo" class="form-label">Estado Vehículo</label>
                            <select class="form-control form-control-sm" id="estado_vehiculo" name="estado_vehiculo">
                                <option value="">-- Todos --</option>
                                <option value="disponible" {{ strtolower(request('estado_vehiculo')) === 'disponible' ? 'selected' : '' }}>Disponible</option>
                                <option value="dañado" {{ strtolower(request('estado_vehiculo')) === 'dañado' ? 'selected' : '' }}>Dañado</option>
                                {{-- Agrega otros estados si existen --}}
                            </select>
                        </div>

                        {{-- Botones de Acción --}}
                        <div class="col-md-2 d-flex"> {{-- Usa d-flex para alinear horizontalmente --}}
                            <button type="submit" class="btn btn-primary btn-sm me-2">
                                <i class="fas fa-filter"></i> Aplicar
                            </button>
                            <a href="{{ route('entradas.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-redo"></i> Limpiar
                            </a>
                        </div>
                    </form>
                </div>
                {{-- Fin Bloque de Filtros --}}
                
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        {{-- Cambié 'data-bs-dismiss' a 'data-dismiss' que es más común en AdminLTE con Bootstrap 4/5 --}}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                
                {{-- Agregamos el ID 'entradas-table' y la clase de DataTables para inicializarla --}}
                <table id="entradas-table" class="table table-striped table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th># Orden</th>
                            <th>VIN</th>
                            <th>Almacén Salida</th>
                            <th>Almacén Entrada</th>
                            <th>Fecha Entrada</th>
                            <th>Estado Vehículo</th>
                            <th>Estatus Entrada</th>
                            <th>Tipo</th>
                            <th>Coordinador_Logistica</th>
                            <th data-orderable="false" data-searchable="false">Acciones</th> {{-- Deshabilita ordenar/buscar en Acciones --}}
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
                                    
                                    {{-- Usamos el target="_blank" en el de imprimir para que se abra en una nueva pestaña, lo agregué de nuevo --}}
                                    <a href="{{ route('entradasimprimir', ['id' => $entrada->No_orden]) }}" 
                                        target="_blank" class="btn btn-sm btn-secondary" title="Imprimir">
                                        <i class="fas fa-print"></i>
                                    </a>

                                    {{-- Confirmar/Rechazar solo si está pendiente y NO es Madrina --}}
                                    @if($entrada->estatus == 'pendiente' && $entrada->Tipo !== 'Madrina')
                                        <form action="{{ route('entradas.confirmar', $entrada->No_orden) }}" 
                                            method="POST" style="display:inline;">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-success btn-sm" title="Confirmar">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>

                                        <form action="{{ route('entradas.rechazar', $entrada->No_orden) }}" 
                                            method="POST" style="display:inline;">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Rechazar">
                                                <i class="fas fa-times"></i>
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
                
                {{-- Eliminamos la paginación de Blade ya que DataTables la maneja --}}
                {{-- <div class="d-flex justify-content-center mt-3">
                    {{ $entradas->links('pagination::bootstrap-4') }}
                </div> --}}

            </div>
        </div>
    </div>
@stop

{{-- Sección para scripts de DataTables --}}
@section('js')
    {{-- La directiva de AdminLTE para cargar el plugin de DataTables (si está configurado) --}}
    @section('plugins.Datatables', true)
    
    <script>
        $(document).ready(function() {
            // Inicialización de DataTables
            $('#entradas-table').DataTable({
                "paging": true, // Habilitar paginación
                "ordering": true, // Habilitar ordenamiento
                "info": true, // Mostrar información de la tabla
                "searching": true, // Habilitar búsqueda
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json" // Traducir a español
                },
                "responsive": true, // Hacerla responsiva, si las extensiones están cargadas
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]], // Opciones de mostrar filas
                // Puedes agregar más opciones como "dom" si necesitas botones de exportación, etc.
            });

            // Agrega un listener para los filtros fuera de DataTables (si usas el formulario)
            // Esto es necesario si quieres que los filtros del bloque superior se envíen al servidor
            // y luego se apliquen en la nueva carga de la página.
            // Si quieres que DataTables maneje todo el filtrado, deberías eliminar el bloque de filtros <form>
            // y cargar todos los datos, o usar DataTables con procesamiento del lado del servidor (Server-Side Processing).
        });
    </script>
@endsection