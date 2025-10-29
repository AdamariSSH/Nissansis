@extends('adminlte::page')

@section('title', 'Crear Almacén')

@section('content')
    <h1>Nuevo Almacén</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('almacen.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="Nombre">Nombre:</label>
            <input type="text" name="Nombre" id="Nombre" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="Direccion">Dirección:</label>
            <input type="text" name="Direccion" id="Direccion" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="{{ route('almacen.index') }}" class="btn btn-secondary">Cancelar</a>

    </form>
@stop
