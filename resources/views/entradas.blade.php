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
                    <!-- Botón para abrir modal Crear Entrada -->
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
                            <th>Almacen Salida</th>
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
                                <td>{{ $entrada->Tipo}}</td>
                                <td>
                                    <a href="{{ route('entradas.edit', $entrada->No_orden) }}" class="btn btn-warning btn-sm" title="Editar"><i class="fas fa-edit"></i></a>
                                    <button class="btn btn-danger btn-sm ml-1" title="Eliminar"><i class="fas fa-trash"></i></button>
                                    <a href="{{ route('entradasimprimir', ['No_orden' => $entrada->No_orden]) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop



<!-- Modal Wizard para Crear Entrada -->
<div class="modal fade" id="modalCrearEntrada" tabindex="-1" role="dialog" aria-labelledby="modalCrearEntradaLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header bg-secondary text-white">
        <h5 class="modal-title" id="modalCrearEntradaLabel">Crear Nueva Entrada</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form id="formCrearEntrada" action="{{ route('entradas.store') }}" method="POST">
        @csrf
        <div class="modal-body">

          <!-- Paso 1 -->
          <div id="paso1">
            <h5 class="text-primary">Paso 1: Datos del Vehículo</h5>
            <div class="row">
              <div class="col-md-6">
                <label for="VIN">VIN</label>
                <input type="text" id="VIN" name="VIN" class="form-control" minlength="17" maxlength="17" required>

                <label for="Motor" class="mt-2">Motor</label>
                <input type="text" id="Motor" name="Motor" class="form-control" minlength="17" maxlength="17" required>

                <label for="Version" class="mt-2">Versión</label>
                <input type="text" id="Version" name="Version" class="form-control" required>

                <label for="Color" class="mt-2">Color</label>
                <input type="text" id="Color" name="Color" class="form-control" required>
              </div>

              <div class="col-md-6">
                <label for="Modelo">Modelo</label>
                <input type="text" id="Modelo" name="Modelo" class="form-control" required>

                <label for="Tipo" class="mt-2">Tipo</label>
                <input type="text" id="Tipo" name="Tipo" class="form-control">

                <label for="Almacen_salida" class="mt-2">Almacén Salida</label>
                <input type="number" id="Almacen_salida" name="Almacen_salida" class="form-control">

                <label for="Almacen_entrada" class="mt-2">Almacén Entrada</label>
                <input type="number" id="Almacen_entrada" name="Almacen_entrada" class="form-control">

                <label for="Fecha_entrada" class="mt-2">Fecha Entrada</label>
                <input type="datetime-local" id="Fecha_entrada" name="Fecha_entrada" class="form-control">

                <label for="Estado" class="mt-2">Estado</label>
                <select id="Estado" name="Estado" class="form-control">
                  <option value="inspeccion">Inspección</option>
                  <option value="levantamiento">Tránsito</option>
                  <option value="disponible">Disponible</option>
                </select>

                <label for="Coordinador_Logistica" class="mt-2">Coordinador Logística</label>
                <input type="text" id="Coordinador_Logistica" name="Coordinador_Logistica" class="form-control">
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
          <div id="paso3" style="display:none;">
            <h5 class="text-success">Paso 3: Resumen de Entrada</h5>
            <p>Revisa los datos capturados antes de guardar la entrada.</p>
            <div id="resumenEntrada"></div>

            <button type="button" class="btn btn-secondary" onclick="mostrarPaso(2)">Atrás</button>
            <button type="button" class="btn btn-success float-right" onclick="enviarFormulario()">Guardar Entrada</button>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function mostrarPaso(n) {
  // Mostrar solo el paso seleccionado
  document.getElementById('paso1').style.display = n === 1 ? 'block' : 'none';
  document.getElementById('paso2').style.display = n === 2 ? 'block' : 'none';
  document.getElementById('paso3').style.display = n === 3 ? 'block' : 'none';
}

function validarChecklist() {
  const checklist = document.querySelectorAll('.checklist');
  let alMenosUnCheck = false;

  checklist.forEach(cb => {
    if (cb.checked) alMenosUnCheck = true;
  });

  if (!alMenosUnCheck) {
    alert('Debes marcar al menos un ítem del checklist para continuar.');
    return;
  }

  // Construir resumen simple para paso 3 (puedes personalizar)
  document.getElementById('resumenEntrada').innerHTML = '<p><strong>Checklist válido. Listo para guardar.</strong></p>';

  mostrarPaso(3);
}

function enviarFormulario() {
  document.getElementById('formCrearEntrada').submit();
}
</script>




@section('css')
<style>
    /* Tu estilo para alertas, etc. */
    .alert-box {
        position: fixed;
        top: 30%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: #fff3cd;
        border: 1px solid #ffeeba;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        padding: 20px;
        border-radius: 10px;
        z-index: 9999;
        width: 350px;
        display: none;
        text-align: center;
        font-family: Arial, sans-serif;
    }

    .alert-box.show {
        display: block;
    }

    .alert-icon {
        font-size: 30px;
        margin-bottom: 10px;
    }

    .alert-content strong {
        font-size: 18px;
        display: block;
        margin-bottom: 5px;
    }

    .alert-actions {
        margin-top: 15px;
    }

    .alert-actions button {
        margin: 5px;
        padding: 8px 16px;
        border: none;
        background-color: #ffc107;
        color: #000;
        border-radius: 5px;
        cursor: pointer;
    }

    .alert-actions button:hover {
        background-color: #e0a800;
    }
</style>
@stop

@section('js')
<script>
    function closeAlert() {
        document.getElementById('customAlert').classList.remove('show');
    }
</script>
@stop
