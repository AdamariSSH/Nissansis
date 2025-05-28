@extends('adminlte::page')

@section('title', 'Entradas')

@section('content_header')
    <h1>Listado de Entradas</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
            <h4>📋 Entradas Registradas</h4>
            <div>
                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modalCrearEntrada">
                    <i class="fas fa-plus"></i> Nueva Entrada
                </button>
                <a href="{{ route('entradas.importar') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-file-import"></i> Importar Entradas
                </a>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            @endif
            <table class="table table-striped table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th># Orden</th>
                        <th>VIN</th>
                        <th>Motor</th>
                        <th>Versión</th>
                        <th>Color</th>
                        <th>Modelo</th>
                        <th>Almacén Salida</th>
                        <th>Almacén Entrada</th>
                        <th>Fecha Entrada</th>
                        <th>Estado</th>
                        <th>Tipo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entradas as $entrada)
                        <tr>
                            <td>{{ $entrada->No_orden }}</td>
                            <td>{{ $entrada->VIN }}</td>
                            <td>{{ $entrada->Motor }}</td>
                            <td>{{ $entrada->Version }}</td>
                            <td>{{ $entrada->Color }}</td>
                            <td>{{ $entrada->Modelo }}</td>
                            <td>{{ $entrada->almacenSalida->Nombre ?? 'N/A' }}</td>
                            <td>{{ $entrada->almacenEntrada->Nombre ?? 'N/A' }}</td>
                            <td>{{ $entrada->Fecha_entrada }}</td>
                            <td><span class="badge bg-success">{{ $entrada->Estado }}</span></td>
                            <td>{{ $entrada->Tipo }}</td>
                            <td>
                                <a href="{{ route('entradas.edit', $entrada->No_orden) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('entradas.eliminar', $entrada->No_orden) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar entrada?')">
                                        <i class="fas fa-trash"></i>
                                        </button>
                                    </form>

                                
                                <a href="{{ route('entradasimprimir', ['No_orden' => $entrada->No_orden]) }}" class="btn btn-primary btn-sm"><i class="fas fa-print"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Crear Entrada (Wizard) -->
<div class="modal fade" id="modalCrearEntrada" tabindex="-1" role="dialog" aria-labelledby="modalCrearEntradaLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header bg-secondary text-white">
        <h5 class="modal-title">Crear Nueva Entrada</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>

      <form id="formCrearEntrada" action="{{ route('entradas.store') }}" method="POST">
        @csrf

        <div class="modal-body">
          <!-- Paso 1 -->
          <div id="paso1">
            <h5 class="text-primary">Paso 1: Datos del Vehículo</h5>
            <div class="row">
                <div class="col-md-6">
                    <label>VIN</label>
                    <input type="text" class="form-control" name="VIN" required minlength="17" maxlength="17">
                    <label>Motor</label>
                    <input type="text" class="form-control" name="Motor" required minlength="17" maxlength="17">
                    <label>Versión</label>
                    <input type="text" class="form-control" name="Version" required>
                    <label>Color</label>
                    <input type="text" class="form-control" name="Color" required>
                </div>
                <div class="col-md-6">
                    <label>Modelo</label>
                    <input type="text" class="form-control" name="Modelo" required>
                    <label>Tipo</label>
                    <input type="text" class="form-control" name="Tipo">
                    <label>Almacén Salida</label>
                    <input type="number" class="form-control" name="Almacen_salida">
                    <label>Almacén Entrada</label>
                    <input type="number" class="form-control" name="Almacen_entrada">
                </div>
                <div class="col-md-6 mt-2">
                    <label>Fecha Entrada</label>
                    <input type="datetime-local" class="form-control" name="Fecha_entrada">
                    <label>Estado</label>
                    <select class="form-control" name="Estado">
                        <option value="inspeccion">Inspección</option>
                        <option value="levantamiento">Tránsito</option>
                        <option value="disponible">Disponible</option>
                    </select>
                    <label>Coordinador Logística</label>
                    <input type="text" class="form-control" name="Coordinador_Logistica">
                </div>
            </div>
            <button type="button" class="btn btn-primary mt-3 float-right" onclick="mostrarPaso(2)">Siguiente</button>
          </div>

          <!-- Paso 2 -->
          <div id="paso2" style="display:none;">
            <h5 class="text-primary">Paso 2: Inspección (Checklist)</h5>
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>Ítem</th>
                        <th>Revisión A</th>
                        <th>Revisión B</th>
                        <th>Revisión C</th>
                        <th>Observaciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (['Documentos y accesorios originales', 'Carrocería', 'Interior', 'Revisión o Previa', 'Seguro', 'NFC/GPS'] as $index => $item)
                    <tr>
                        <td>{{ $item }}</td>
                        <td><input type="checkbox" class="form-check-input checklist" name="checklist[{{ $index }}][a]"></td>
                        <td><input type="checkbox" class="form-check-input checklist" name="checklist[{{ $index }}][b]"></td>
                        <td><input type="checkbox" class="form-check-input checklist" name="checklist[{{ $index }}][c]"></td>
                        <td><input type="text" class="form-control" name="checklist[{{ $index }}][observacion]"></td>
                    </tr>
                    @endforeach
                    <tr>
                        <td>Kilometraje</td>
                        <td colspan="4" class="text-left pl-4">
                            Inicial: <input type="text" name="km_inicial" style="width: 80px;"> km &nbsp;&nbsp;
                            Final: <input type="text" name="km_final" style="width: 80px;"> km
                        </td>
                    </tr>
                </tbody>
            </table>

            <button type="button" class="btn btn-secondary" onclick="mostrarPaso(1)">Atrás</button>
            <button type="button" class="btn btn-primary float-right" onclick="validarChecklist()">Finalizar</button>
          </div>

          <!-- Paso 3 -->
          
@stop

@section('css')
<style>
    .alert-box {
        position: fixed;
        top: 30%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: #fff3cd;
        border: 1px solid #ffeeba;
        padding: 20px;
        border-radius: 10px;
        z-index: 9999;
        width: 350px;
        display: none;
        text-align: center;
    }
    .alert-box.show { display: block; }
</style>
@stop

@section('js')
<script>
function mostrarPaso(n) {
    document.getElementById("paso1").style.display = n === 1 ? 'block' : 'none';
    document.getElementById("paso2").style.display = n === 2 ? 'block' : 'none';
    document.getElementById("paso3").style.display = n === 3 ? 'block' : 'none';
}

function validarChecklist() {
    const checks = document.querySelectorAll(".checklist");
    const todosMarcados = Array.from(checks).some(cb => cb.checked);

    if (!todosMarcados) {
        alert("Debes marcar al menos un ítem en el checklist.");
        return;
    }

    // Aquí podrías construir un resumen de los datos
    document.getElementById("resumenEntrada").innerHTML = "<p><strong>Checklist completado</strong></p>";
    mostrarPaso(3);
}
</script>
@stop
