@extends('adminlte::page') {{-- Usando el layout de AdminLTE --}}

@section('title', 'Gestión de Roles')

@section('content_header')
    <h1>
        Gestión de Roles
        {{-- Botón de aplicación o acción --}}
        <a href="{{ route('admin.roles.create') }}" class="btn btn-app float-right">
            <i class="fas fa-plus"></i> Crear Rol
        </a>
    </h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Listado de Roles</h3>
                </div>
                <div class="card-body">
                    <table id="rolesTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 10px">ID</th>
                                <th>Nombre del Rol</th>
                                <th>Permisos Asignados</th>
                                <th style="width: 150px">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $role)
                            <tr>
                                <td>{{ $role->id }}</td>
                                <td>{{ $role->name }}</td>
                                <td>
                                    @forelse($role->permissions as $permission)
                                        <span class="badge bg-success mr-1">{{ $permission->name }}</span>
                                    @empty
                                        <span class="text-muted">Sin permisos</span>
                                    @endforelse
                                </td>
                                <td>
                                    {{-- Botón Editar --}}
                                    <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    
                                    {{-- Botón Eliminar (Formulario) --}}
                                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Confirma la eliminación del rol: {{ $role->name }}?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                </div>
            </div>
    </div>
@stop

@section('js')
    {{-- Puede añadir aquí la inicialización de DataTables si la está usando --}}
    {{-- <script> $('#rolesTable').DataTable(); </script> --}}
@stop