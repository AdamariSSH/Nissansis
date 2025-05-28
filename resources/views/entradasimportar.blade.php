@extends('adminlte::page')

@section('title', 'Importar Entradas')

@section('content_header')
    <h1>Importar Entradas</h1>
@stop

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h4><i class="fas fa-upload"></i> Importar Entradas desde Excel/CSV</h4>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('entradas.procesarImportacion') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="archivo" class="form-label">Seleccione el archivo Excel o CSV:</label>
                        <input type="file" class="form-control" id="archivo" name="archivo" accept=".xlsx,.csv" required>
                    </div>
                    <button type="submit" class="btn btn-success"><i class="fas fa-file-import"></i> Importar</button>
                    <a href="{{ route('admin.entradas') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Cancelar</a>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log('Formulario de importación cargado!'); </script>
@stop