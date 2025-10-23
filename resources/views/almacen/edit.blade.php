@extends('adminlte::page')

@section('title', 'Editar Almacén')

@section('content_header')
    <h1>Editar Almacén</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('almacen.update', $almacen->Id_Almacen) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="Nombre">Nombre:</label>
                    <input type="text" class="form-control" id="Nombre" name="Nombre" value="{{ $almacen->Nombre }}" required>
                </div>

                <div class="form-group">
                    <label for="Direccion">Dirección:</label>
                    <input type="text" class="form-control" id="Direccion" name="Direccion" value="{{ $almacen->Direccion }}" required>
                </div>

                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                <a href="{{ route('almacen') }}" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log('Hi!'); </script>
@stop