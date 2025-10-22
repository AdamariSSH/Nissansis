@extends('adminlte::page')

@section('title', 'Ficha del Vehículo')

@section('content_header')
@stop

@section('content')
<div class="container mt-4" style="font-size: 14px;">

    {{-- ENCABEZADO --}}
    <div class="text-center mb-4">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width: 120px;">
        <h4 class="mt-2">Ficha del Vehículo</h4>
        <p>Fecha de impresión: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    {{-- BOTÓN IMPRIMIR --}}
    <div class="text-right mb-3">
        <button class="btn btn-danger" onclick="window.print()">
            <i class="fas fa-print"></i> Imprimir
        </button>
        <a href="{{ route('admin.vehiculos') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Regresar
        </a>
    </div>

    {{-- DATOS GENERALES --}}
    <h5 class="font-weight-bold mb-2">Datos del Vehículo</h5>
    <table class="table table-bordered">
        <tr><th>VIN</th><td>{{ $vehiculo->VIN }}</td></tr>
        <tr><th>Motor</th><td>{{ $vehiculo->Motor }}</td></tr>
        <tr><th>Modelo</th><td>{{ $vehiculo->Modelo }}</td></tr>
        <tr><th>Color</th><td>{{ $vehiculo->Color }}</td></tr>
        <tr><th>Características</th><td>{{ $vehiculo->Caracteristicas }}</td></tr>
        <tr><th>Estatus</th><td>{{ $vehiculo->estatus }}</td></tr>
        <tr><th>Almacén Actual</th><td>{{ $vehiculo->almacen->Nombre ?? 'N/A' }}</td></tr>
        <tr><th>Próximo Mantenimiento</th><td>{{ $vehiculo->Proximo_mantenimiento ?? 'No asignado' }}</td></tr>
    </table>

    {{-- ÚLTIMO MOVIMIENTO --}}
    <h5 class="font-weight-bold mt-4 mb-2">
        Último Movimiento ({{ strtoupper($tipoMovimiento) }})
    </h5>
    <table class="table table-bordered">
        @if($tipoMovimiento === 'entrada')
                <tr><th>No. Orden Entrada</th><td>{{ $ultimoMovimiento->No_orden }}</td></tr>
                <tr><th>Tipo</th><td>{{ $ultimoMovimiento->Tipo }}</td></tr>
                <tr><th>Fecha Entrada</th><td>{{ $ultimoMovimiento->created_at?->format('Y-m-d H:i') }}</td></tr> 
                <tr><th>Almacén</th><td>{{ $ultimoMovimiento->almacenEntrada->Nombre ?? 'N/A' }}</td></tr>
                <tr><th>Observaciones</th><td>{{ $ultimoMovimiento->Estado ?? 'Sin observaciones' }}</td></tr>
        @else
            <tr><th>No. Orden Salida</th><td>{{ $ultimoMovimiento->No_orden_salida }}</td></tr>
            <tr><th>Tipo de Salida</th><td>{{ $ultimoMovimiento->Tipo_salida }}</td></tr>
            <tr><th>Fecha Salida</th><td>{{ $ultimoMovimiento->Fecha }}</td></tr>
            <tr><th>Almacén</th><td>{{ $ultimoMovimiento->almacenSalida->Nombre ?? 'N/A' }}</td></tr>
            <tr><th>Modelo</th><td>{{ $ultimoMovimiento->Modelo }}</td></tr>
        @endif
    </table>

    {{-- CHECKLIST --}}
    @if($checklist)
        <h5 class="font-weight-bold mt-4 mb-2">
            Checklist de {{ ucfirst($tipoMovimiento) }}
        </h5>
        <table class="table table-bordered text-center">
            <thead class="thead-light">
                <tr>
                    <th>Documentos</th>
                    <th>Accesorios</th>
                    <th>PDI</th>
                    <th>Seguro</th>
                    <th>NFC</th>
                    <th>GPS</th>
                    <th>Folder</th>
                    <th>Revisión</th>
                    <th>Revisado Por</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $checklist->documentos_completos ? '✔' : '✖' }}</td>
                    <td>{{ $checklist->accesorios_completos ? '✔' : '✖' }}</td>
                    <td>{{ $checklist->pdi_realizada ? '✔' : '✖' }}</td>
                    <td>{{ $checklist->seguro_vigente ? '✔' : '✖' }}</td>
                    <td>{{ $checklist->nfc_instalado ? '✔' : '✖' }}</td>
                    <td>{{ $checklist->gps_instalado ? '✔' : '✖' }}</td>
                    <td>{{ $checklist->folder_viajero ? '✔' : '✖' }}</td>
                    <td>{{ $checklist->fecha_revision ?? 'N/A' }}</td>
                    <td>{{ $checklist->recibido_por ?? 'N/A' }}</td>
                    <td>{{ $checklist->observaciones ?? 'Sin observaciones' }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    <div class="text-center mt-5">
        <small>Documento generado automáticamente — Sistema Nissan SIS</small>
    </div>
</div>
@stop
