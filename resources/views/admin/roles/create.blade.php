@extends('adminlte::page')

@section('title', 'Crear Nuevo Rol')

@section('content_header')
    <h1 class="m-0 text-dark">Crear Nuevo Rol</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Definir Rol y Asignar Permisos</h3>
                </div>
                
                {{-- Formulario para crear un rol, apunta a la ruta store --}}
                <form action="{{ route('admin.roles.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        
                        {{-- Campo para el Nombre del Rol --}}
                        <div class="form-group">
                            <label for="name">Nombre del Rol</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Ej: supervisor-almacen" value="{{ old('name') }}">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Sección de Permisos --}}
                        <div class="form-group">
                            <label>Permisos Disponibles</label>
                            <div class="row">
                                {{-- Iteramos sobre la variable $permissions que viene del controlador --}}
                                @foreach ($permissions as $permission)
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="permission_{{ $permission->id }}" 
                                                {{-- Usamos old() para mantener la selección si hay un error de validación --}}
                                                {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('permissions')
                                <span class="text-danger">Por favor, selecciona permisos válidos.</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Rol</button>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-default">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop