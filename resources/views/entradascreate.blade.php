{{-- @extends('adminlte::page')

@section('title', 'Crear Entrada')

@section('content_header')
    <h1>Crear Nueva Entrada</h1>
@stop

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h4>Formulario de Nueva Entrada</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('entradas.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="VIN" class="form-label">VIN</label>
                        <input type="text" class="form-control" id="VIN" name="VIN" maxlength="17">
                    </div>
                    >
                    <div class="mb-3">
                        <label for="Motor" class="form-label">Motor</label>
                        <input type="text" class="form-control" id="Motor" name="Motor" maxlength="20">
                    </div>
                    <div class="mb-3">
                        <label for="Version" class="form-label">Versión</label>
                        <input type="text" class="form-control" id="Version" name="Version" required>
                    </div>
                    <div class="mb-3">
                        <label for="Color" class="form-label">Color</label>
                        <input type="text" class="form-control" id="Color" name="Color" required>
                    </div>
                    <div class="mb-3">
                        <label for="Modelo" class="form-label">Modelo</label>
                        <input type="text" class="form-control" id="Modelo" name="Modelo" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="Almacen_salida" class="form-label">Almacén salida</label>
                        <input type="number" class="form-control" id="Almacen_salida" name="Almacen_salida">
                    </div>
                    <div class="mb-3">
                        <label for="Almacen_entrada" class="form-label">Almacén entrada</label>
                        <input type="number" class="form-control" id="Almacen_entrada" name="Almacen_entrada">
                    </div>
                    <div class="mb-3">
                        <label for="Fecha_entrada" class="form-label">Fecha de Entrada</label>
                        <input type="datetime-local" class="form-control" id="Fecha_entrada" name="Fecha_entrada">
                    </div>
                    <div class="mb-3">
                        <label for="Estado" class="form-label">Estado</label>
                        <select class="form-control" id="Estado" name="Estado">
                        <option value="inspeccion">Inspección</option>
                        <option value="levantamiento">Transito</option>
                        <option value="disponible">Disponible</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="Tipo" class="form-label">Tipo</label>
                        <input type="text" class="form-control" id="Tipo" name="Tipo">
                    </div>
                    <div class="mb-3">
                        <label for="Coordinador_Logistica" class="form-label">Cordinador de logistica</label>
                        <input type="text" class="form-control" id="Coordinador_Logistica" name="Coordinador_Logistica">

                    </div>
                    <button type="submit" class="btn btn-danger">Guardar Entrada</button>
                    <a href="{{ route('admin.entradas') }}" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
    

@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log('Formulario de creación de entradas cargado correctamente!'); </script>
@stop --}}


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Wizard Entrada</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .nav-pills .nav-link.active {
      background-color: #0d6efd;
    }
  </style>
</head>
<body>

<div class="container mt-5 text-center">
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearEntrada">Crear Entrada</button>
</div>

<!-- Modal Wizard Entrada -->
<div class="modal fade" id="modalCrearEntrada" tabindex="-1" aria-labelledby="modalCrearEntradaLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title" id="modalCrearEntradaLabel">Crear Nueva Entrada</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <form action="#" method="POST" id="formEntrada">
        <div class="modal-body">
          <!-- Tabs de pasos -->
          <ul class="nav nav-pills mb-4 justify-content-center">
            <li class="nav-item"><span class="nav-link active" id="tab-1">Entrada</span></li>
            <li class="nav-item"><span class="nav-link" id="tab-2">Inspección</span></li>
            <li class="nav-item"><span class="nav-link" id="tab-3">Resumen</span></li>
          </ul>

          <!-- Paso 1 -->
          <div class="step step-1">
            <div class="row">
              <div class="col-md-6">
                <label>VIN</label><input type="text" name="VIN" class="form-control" required>
                <label>Motor</label><input type="text" name="Motor" class="form-control" required>
                <label>Versión</label><input type="text" name="Version" class="form-control" required>
                <label>Color</label><input type="text" name="Color" class="form-control" required>
              </div>
              <div class="col-md-6">
                <label>Modelo</label><input type="text" name="Modelo" class="form-control" required>
                <label>Tipo</label><input type="text" name="Tipo" class="form-control">
                <label>Almacén Salida</label><input type="number" name="Almacen_salida" class="form-control">
                <label>Almacén Entrada</label><input type="number" name="Almacen_entrada" class="form-control">
                <label>Fecha Entrada</label><input type="datetime-local" name="Fecha_entrada" class="form-control">
              </div>
            </div>
          </div>

          <!-- Paso 2 -->
          <div class="step step-2 d-none">
            <h5>Checklist de Inspección</h5>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="revision_motor" id="check1">
              <label class="form-check-label" for="check1">Revisión de motor</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="revisar_frenos" id="check2">
              <label class="form-check-label" for="check2">Revisar frenos</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="estado_llantas" id="check3">
              <label class="form-check-label" for="check3">Estado de llantas</label>
            </div>
            <label>Estado General</label>
            <select class="form-select" name="Estado">
              <option value="">Selecciona...</option>
              <option value="inspeccion">Inspección</option>
              <option value="levantamiento">Tránsito</option>
              <option value="disponible">Disponible</option>
            </select>
            <label>Coordinador de Logística</label>
            <input type="text" class="form-control" name="Coordinador_Logistica">
          </div>

          <!-- Paso 3 -->
          <div class="step step-3 d-none">
            <h5>Resumen</h5>
            <pre id="resumenDatos" class="bg-light border p-3 rounded" style="white-space: pre-wrap;"></pre>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-outline-primary" id="btnPrev">Anterior</button>
          <button type="button" class="btn btn-primary" id="btnNext">Siguiente</button>
          <button type="submit" class="btn btn-danger d-none" id="btnSubmit">Guardar Entrada</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const steps = document.querySelectorAll('.step');
  const tabs = [document.getElementById('tab-1'), document.getElementById('tab-2'), document.getElementById('tab-3')];
  const btnNext = document.getElementById('btnNext');
  const btnPrev = document.getElementById('btnPrev');
  const btnSubmit = document.getElementById('btnSubmit');
  const resumenDiv = document.getElementById('resumenDatos');
  let currentStep = 0;

  function updateUI() {
    steps.forEach((step, i) => step.classList.toggle('d-none', i !== currentStep));
    tabs.forEach((tab, i) => tab.classList.toggle('active', i === currentStep));
    btnPrev.style.display = currentStep === 0 ? 'none' : 'inline-block';
    btnNext.style.display = currentStep === steps.length - 1 ? 'none' : 'inline-block';
    btnSubmit.classList.toggle('d-none', currentStep !== steps.length - 1);
  }

  function gatherFormData() {
    const form = document.getElementById('formEntrada');
    const data = new FormData(form);
    return [...data.entries()].map(([k, v]) => `${k}: ${v}`).join('\n');
  }

  btnNext.addEventListener('click', () => {
    if (currentStep < steps.length - 1) currentStep++;
    if (currentStep === steps.length - 1) resumenDiv.textContent = gatherFormData();
    updateUI();
  });

  btnPrev.addEventListener('click', () => {
    if (currentStep > 0) currentStep--;
    updateUI();
  });

  updateUI();
});
</script>

</body>
</html>
