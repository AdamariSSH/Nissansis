@extends('adminlte::page')

@section('title', 'Crear Nuevo Usuario')

@section('content_header')
    <h1 class="m-0 text-dark">Crear Nuevo Usuario</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Datos del Usuario</h3>
                </div>
                
                {{-- El formulario apunta al método store del resource --}}
                <form action="{{ route('usuarios.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        
                        {{-- Campo Nombre --}}
                        <div class="form-group">
                            <label for="name">Nombre Completo</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Nombre completo" value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Campo Email --}}
                        <div class="form-group">
                            <label for="email">Correo Electrónico</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="email" placeholder="ejemplo@dominio.com" value="{{ old('email') }}" required>
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Campo Rol --}}
                        <div class="form-group">
                            <label for="role">Rol</label>
                            <select name="role" id="role" class="form-control @error('role') is-invalid @enderror" required>
                                <option value="">Seleccione un Rol</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrador</option>
                                <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>Usuario General</option>
                            </select>
                            @error('role')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Campo Almacén (Nullable) --}}
                        <div class="form-group">
                            <label for="almacen_id">Almacén Asignado (Opcional)</label>
                            <select name="almacen_id" id="almacen_id" class="form-control @error('almacen_id') is-invalid @enderror">
                                <option value="">--- Sin Asignar ---</option>
                                @foreach ($almacenes as $almacen)
                                    {{-- Asumo que la columna para el nombre del almacén es 'Nombre' y el ID es 'Id_Almacen' --}}
                                    <option value="{{ $almacen->Id_Almacen }}" {{ old('almacen_id') == $almacen->Id_Almacen ? 'selected' : '' }}>
                                        {{ $almacen->Nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('almacen_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Campo Contraseña --}}
                        <div class="form-group">
                            <label for="password">Contraseña</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="password" required>
                            @error('password')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Campo Confirmar Contraseña --}}
                        <div class="form-group">
                            <label for="password_confirmation">Confirmar Contraseña</label>
                            <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" required>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Crear Usuario</button>
                        <a href="{{ route('usuarios.index') }}" class="btn btn-default float-right">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop