@extends('adminlte::page')

@section('title', 'Editar Vehículo')

@section('content_header')
    <h1>Editar Vehículo</h1>
@stop

@section('content')
<div class="container-fluid">
    <form method="POST" action="{{ route('vehiculos.update', ['vin' => $vehiculo->VIN]) }}">
        @csrf
        @method('PUT')

        <!-- Paso 1: Datos Generales -->
        <div class="card card-primary">
            <div class="card-header" style="background-color: #6b6666;">
                <h3 class="card-title">Paso 1: Datos Generales</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- VIN -->
                    <div class="col-md-4">
                        <x-adminlte-input name="VIN" label="VIN" readonly value="{{ $vehiculo->VIN }}" />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input name="Motor" label="Motor" value="{{ old('Motor', $vehiculo->Motor) }}" />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input name="Caracteristicas" label="Características" value="{{ old('Caracteristicas', $vehiculo->Caracteristicas) }}" />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input name="Color" label="Color" value="{{ old('Color', $vehiculo->Color) }}" />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input name="Modelo" label="Modelo" value="{{ old('Modelo', $vehiculo->Modelo) }}" />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input type="date" name="Proximo_mantenimiento" label="Próximo Mantenimiento" 
                            value="{{ old('Proximo_mantenimiento', $vehiculo->Proximo_mantenimiento) }}" />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-select name="estatus" label="Estatus">
                            <option value="En almacén" {{ $vehiculo->estatus == 'En almacén' ? 'selected' : '' }}>En almacén</option>
                            <option value="En tránsito" {{ $vehiculo->estatus == 'En tránsito' ? 'selected' : '' }}>En tránsito</option>
                            <option value="Pendiente salida" {{ $vehiculo->estatus == 'Pendiente salida' ? 'selected' : '' }}>Pendiente salida</option>
                            <option value="Vendido" {{ $vehiculo->estatus == 'Vendido' ? 'selected' : '' }}>Vendido</option>
                            <option value="Rechazado" {{ $vehiculo->estatus == 'Rechazado' ? 'selected' : '' }}>Rechazado</option>
                        </x-adminlte-select>
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input name="Estado" label="Estado" value="{{ old('Estado', $vehiculo->Estado) }}" />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input name="Coordinador_Logistica" label="Coordinador Logística" value="{{ old('Coordinador_Logistica', $vehiculo->Coordinador_Logistica) }}" />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-select name="Almacen_actual" label="Almacén Actual">
                            <option value="">-- Selecciona un almacén --</option>
                            @foreach ($almacenes as $almacen)
                                <option value="{{ $almacen->Id_Almacen }}" {{ $vehiculo->Almacen_actual == $almacen->Id_Almacen ? 'selected' : '' }}>
                                    {{ $almacen->Nombre }}
                                </option>
                            @endforeach
                        </x-adminlte-select>
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-select name="tipo" label="Tipo">
                            <option value="Madrina" {{ $vehiculo->tipo == 'Madrina' ? 'selected' : '' }}>Madrina</option>
                            <option value="Traspaso" {{ $vehiculo->tipo == 'Traspaso' ? 'selected' : '' }}>Traspaso</option>
                            <option value="Devolucion" {{ $vehiculo->tipo == 'Devolucion' ? 'selected' : '' }}>Devolución</option>
                            <option value="Otro" {{ $vehiculo->tipo == 'Otro' ? 'selected' : '' }}>Otro</option>
                        </x-adminlte-select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Paso 2: Confirmación -->
        <div class="card card-success">
            <div class="card-header" style="background-color: #6b6666;">
                <h3 class="card-title">Paso 2: Confirmación</h3>
            </div>
            <div class="card-body">
                <x-adminlte-textarea name="Observaciones" label="Observaciones">{{ old('Observaciones', $vehiculo->Observaciones) }}</x-adminlte-textarea>
                <x-adminlte-button label="Actualizar Vehículo" theme="success" icon="fas fa-save" type="submit" />
                <a href="{{ route('vehiculos.index') }}" class="btn btn-secondary ml-2">Cancelar</a>
            </div>
        </div>
    </form>

    <!-- Paso 3: Histórico -->
    <div class="card card-info mt-4">
        <div class="card-header" style="background-color: #6b6666;">
            <h3 class="card-title">Paso 3: Histórico de Movimientos</h3>
        </div>
        <div class="card-body">
            <h5>📥 Entradas</h5>
            <table class="table table-bordered table-sm">
                <thead class="table-dark">
                    <tr>
                        <th>No. Orden</th>
                        <th>Fecha Entrada</th>
                        <th>Almacén</th>
                        <th>Tipo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entradas as $entrada)
                        <tr>
                            <td>{{ $entrada->No_orden }}</td>
                            <td>{{ $entrada->Fecha_entrada }}</td>
                            <td>{{ optional($entrada->almacenEntrada)->Nombre }}</td>
                            <td>{{ $entrada->Tipo }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center">Sin entradas registradas</td></tr>
                    @endforelse
                </tbody>
            </table>

            <h5 class="mt-4">📤 Salidas</h5>
            <table class="table table-bordered table-sm">
                <thead class="table-dark">
                    <tr>
                        <th>No. Salida</th>
                        <th>Fecha Salida</th>
                        <th>Destino</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salidas as $salida)
                        <tr>
                            <td>{{ $salida->id }}</td>
                            <td>{{ $salida->Fecha_salida }}</td>
                            <td>{{ optional($salida->almacenSalida)->Nombre }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center">Sin salidas registradas</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
