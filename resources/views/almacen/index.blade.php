@extends('adminlte::page')

@section('title', 'Listado de Almacenes')

@section('content_header')
    <h1>Listado de Almacenes</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('almacen.create') }}" class="btn btn-danger">Crear Nuevo Almacén</a>
            
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Dirección</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($almacenes as $almacen)
                    <tr>
                        <td>{{ $almacen->Id_Almacen }}</td>
                        <td>{{ $almacen->Nombre }}</td>
                        <td>{{ $almacen->Direccion }}</td>
                        <td>
                             <a href="{{ route('almacen.edit', $almacen->Id_Almacen) }}" class="btn btn-sm btn-secondary">Editar</a> 
                            <form action="{{ route('almacen.destroy', $almacen->Id_Almacen) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log('Hi!'); </script>
@stop