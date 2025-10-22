@extends('adminlte::page')

@section('title', 'Editar Rol: ' . $role->name)

@section('content_header')
    <h1>Editar Rol: **{{ $role->name }}**</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-9">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Modificar Datos</h3>
                </div>
                {{--Usamos la ruta update con el método PUT --}}
                <form action="{{ route('admin.roles.update', $role) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- ¡Crucial para la actualización! --}}
                    
                    <div class="card-body">
                        
                        {{-- Campo Nombre del Rol --}}
                        <div class="form-group">
                            <label for="name">Nombre del Rol</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $role->name) }}" required>
                            @error('name')
                                <span class="error invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <hr>
                        
                        {{-- Sección de Permisos --}}
                        <div class="form-group">
                            <label>Asignar Permisos</label>
                            <div class="row">
                                @foreach($permissions as $permission)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        @php
                                            // 1. Prioridad: permisos seleccionados del old()
                                            // 2. Si no hay old(), usa los permisos que tiene el rol ($rolePermissions).
                                            $checked = in_array($permission->id, old('permissions', $rolePermissions));
                                        @endphp
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="permission-{{ $permission->id }}"
                                            {{ $checked ? 'checked' : '' }}>
                                        <label class="form-check-label" for="permission-{{ $permission->id }}">
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @error('permissions')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                    <div class="card-footer">
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-default">Cancelar</a>
                        <button type="submit" class="btn btn-info float-right">Actualizar Rol</button>
                    </div>
                </form>
            </div>
            </div>
    </div>
@stop