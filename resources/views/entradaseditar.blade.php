@extends('adminlte::page')

@section('title', 'Editar Entrada')

@section('content_header')
    <h1>Editar Entrada</h1>
@stop

@section('content')
<div class="container">
    <form action="{{ route('entradas.update', $entrada->No_orden) }}" method="POST">
    @csrf
    @method('PUT')
        

        <div class="mb-3">
            <label for="VIN" class="form-label">VIN</label>
            <input type="text" name="VIN" class="form-control" value="{{ $entrada->VIN }}" required>
        </div>

        <div class="mb-3">
            <label for="Motor" class="form-label">Motor</label>
            <input type="text" name="Motor" class="form-control" value="{{ $entrada->Motor }}" required>
        </div>

        <!-- Agrega los demás campos aquí según sea necesario -->

        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="{{ route('admin.entradas') }}" class="btn btn-secondary">Cancelar</a>

    </form>
</div>
@stop
