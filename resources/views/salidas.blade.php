@extends('adminlte::page')

@section('title', 'Salidas de Almacén')

@section('content_header')
    <h1>Salidas de Almacén</h1>
@stop

@section('content')

    {{-- Mensaje de éxito --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Listado de Salidas</span>
            <button class="btn btn-danger" data-toggle="modal" data-target="#modalCrearSalida">Registrar Salida</button>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>VIN</th>
                        <th>Motor</th>
                        <th>Versión</th>
                        <th>Color</th>
                        <th>Tipo de salida</th>
                        <th>Almacén Salida</th>
                        <th>Almacén Entrada</th>
                        <th>Fecha</th>
                        <th>Modelo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($salidas as $salida)
                    <tr>
                        <td>{{ $salida->id_salida }}</td>
                        <td>{{ $salida->VIN }}</td>
                        <td>{{ $salida->Motor }}</td>
                        <td>{{ $salida->Version }}</td>
                        <td>{{ $salida->Color }}</td>
                        <td>{{ $salida->Tipo_salida }}</td>
                        <td>{{ $salida->Almacen_salida }}</td>
                        <td>{{ $salida->Almacen_entrada }}</td>
                        <td>{{ $salida->Fecha }}</td>
                        <td>{{ $salida->Modelo }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Crear Salida -->
    <div class="modal fade" id="modalCrearSalida" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <form action="{{ route('salidas.store') }}" method="POST">
            @csrf
            <div class="modal-header bg-danger text-white">
              <h5 class="modal-title">Registrar Salida</h5>
              <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
              <div class="form-group">
                  <label>VIN</label>
                  <select name="VIN" class="form-control" required>
                      <option value="">Seleccione VIN</option>
                      @foreach($vehiculos as $vehiculo)
                          <option value="{{ $vehiculo->VIN }}">{{ $vehiculo->VIN }}</option>
                      @endforeach
                  </select>
              </div>

              <div class="form-group">
                  <label>Motor</label>
                  <input type="text" name="Motor" id="motor" class="form-control" readonly>
              </div>

              <div class="form-group">
                  <label>Versión</label>
                  <input type="text" name="Version" id="version" class="form-control" readonly>
              </div>

              <div class="form-group">
                  <label>Color</label>
                  <input type="text" name="Color" id="color" class="form-control" readonly>
              </div>

              <div class="form-group">
                  <label>Modelo</label>
                  <input type="text" name="Modelo" id="modelo" class="form-control" readonly>
              </div>

              <div class="form-group">
                  <label>Tipo de salida</label>
                  <input type="text" name="Tipo_salida" class="form-control" required>
              </div>

              <div class="form-group">
                  <label>Almacén de salida</label>
                  <select name="Almacen_salida" class="form-control" required>
                      <option value="">Seleccione almacén</option>
                      @foreach($almacenes as $almacen)
                          <option value="{{ $almacen->Id_Almacen }}">{{ $almacen->Nombre }}</option>
                      @endforeach
                  </select>
              </div>

              <div class="form-group">
                  <label>Almacén de entrada</label>
                  <select name="Almacen_entrada" class="form-control">
                      <option value="">-- Opcional --</option>
                      @foreach($almacenes as $almacen)
                          <option value="{{ $almacen->Id_Almacen }}">{{ $almacen->Nombre }}</option>
                      @endforeach
                  </select>
              </div>

              <div class="form-group">
                  <label>Fecha</label>
                  <input type="date" name="Fecha" class="form-control" required>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-danger">Guardar Salida</button>
            </div>
          </form>
        </div>
      </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Autocompletar datos del VIN
    $('select[name="VIN"]').on('change', function () {
        const vin = $(this).val();
        if (vin) {
            $.ajax({
                url: `/vehiculo/${vin}`,
                type: 'GET',
                success: function (data) {
                    $('#motor').val(data.Motor || '');
                    $('#version').val(data.Version || '');
                    $('#color').val(data.Color || '');
                    $('#modelo').val(data.Modelo || '');
                }
            });
        } else {
            $('#motor, #version, #color, #modelo').val('');
        }
    });

    // Cerrar alert después de 3 segundos
    setTimeout(function () {
        $('.alert-success').fadeOut('slow');
    }, 3000);
</script>
@stop
