@extends('adminlte::page') // O 'layouts.app' si es tu layout personalizado de AdminLTE

@section('title', 'Gestión de Usuarios')

@section('content_header')
    <h1 class="m-0 text-dark">Gestión de Usuarios y Roles</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Listado de Usuarios</h3>
                    <div class="card-tools">
                        <a href="{{ route('usuarios.create') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Crear Nuevo Usuario
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr class="text-uppercase">
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th class="text-center">Rol</th>
                                    <th class="text-center">Almacén Asignado</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td class="text-center">
                                            {{-- Uso de badges de AdminLTE --}}
                                            <span class="badge 
                                                @if ($user->role == 'admin') badge-danger 
                                                @else badge-success @endif">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            {{ $user->almacen ? $user->almacen->Nombre : 'No Asignado' }}
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                {{-- Botón Editar --}}
                                                <a href="{{ route('usuarios.edit', $user->id) }}" title="Editar Usuario" class="btn btn-info">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                {{-- Botón Eliminar --}}
                                                <form action="{{ route('usuarios.destroy', $user->id) }}" method="POST" onsubmit="return confirm('¿Está seguro de que desea eliminar a este usuario ({{ $user->name }})?');" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" title="Eliminar Usuario" class="btn btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No se encontraron usuarios en el sistema.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
@stop