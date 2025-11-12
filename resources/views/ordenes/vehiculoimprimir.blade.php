@extends('adminlte::page')

@section('title', 'Ficha del Vehículo')

@section('content_header')
@stop

@section('content')
<div class="container mt-4" style="font-size: 14px;">

    {{-- ENCABEZADO--}}
    <div class="text-center mb-4">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width: 120px;">
        <h4 class="mt-2 font-weight-bold text-uppercase">FICHA DE VEHÍCULO</h4>
        <p>Documento de Control Interno | Fecha de impresión: {{ now()->format('d/m/Y H:i') }}</p>
    </div>
    <hr class="mb-4"> {{-- Línea divisoria formal --}}

    {{-- BOTONES (Ocultos en impresión) --}}
    <div class="text-right mb-3 no-print"> 
        <button class="btn btn-danger" onclick="window.print()">
            <i class="fas fa-print"></i> Imprimir
        </button>
        <a href="{{ route('vehiculos.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Regresar
        </a>
    </div>

    {{-- BLOQUE 1: DATOS GENERALES Y OPERATIVOS (Estructura de dos columnas) --}}
    <h5 class="font-weight-bold mb-2 p-1 bg-light border-bottom">1. DATOS IDENTIFICATIVOS Y OPERATIVOS</h5>
    
    <table class="table table-sm table-bordered">
        <thead>
            <tr>
                <th colspan="2" class="text-center font-weight-bold" style="width: 50%; background-color: #f0f0f0;">IDENTIFICACIÓN</th>
                <th colspan="2" class="text-center font-weight-bold" style="width: 50%; background-color: #f0f0f0;">ESTATUS Y UBICACIÓN</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th style="width: 15%;">VIN</th><td style="width: 35%;">{{ $vehiculo->VIN }}</td>
                <th style="width: 15%;">Estatus Actual</th><td style="width: 35%;" class="font-weight-bold">{{ strtoupper($vehiculo->estatus) }}</td>
            </tr>
            <tr>
                <th>Motor</th><td>{{ $vehiculo->Motor }}</td>
                <th>Almacén Actual</th><td>{{ $vehiculo->almacen->Nombre ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Modelo</th><td>{{ $vehiculo->Modelo }}</td>
                <th>Próximo Mantenimiento</th><td>{{ $vehiculo->Proximo_mantenimiento ?? 'No asignado' }}</td>
            </tr>
            <tr>
                <th>Color</th><td>{{ $vehiculo->Color }}</td>
                <th>Características</th><td>{{ $vehiculo->Caracteristicas }}</td>
            </tr>
        </tbody>
    </table>

    {{-- BLOQUE 2: ÚLTIMO MOVIMIENTO (Tabla específica más limpia) --}}
    <h5 class="font-weight-bold mt-4 mb-2 p-1 bg-light border-bottom">2. ÚLTIMO MOVIMIENTO REGISTRADO</h5>
    
    <table class="table table-sm table-bordered">
        @if($tipoMovimiento === 'entrada')
            <tr>
                <th style="width: 15%;">Tipo de Movimiento</th><td style="width: 35%;">{{ strtoupper($ultimoMovimiento->Tipo) }}</td>
                <th style="width: 15%;">Fecha de Registro</th><td style="width: 35%;">{{ $ultimoMovimiento->updated_at?->format('Y-m-d H:i') }}</td>
            </tr>
            <tr>
                <th>No. Orden</th><td>{{ $ultimoMovimiento->No_orden }}</td>
                <th>Almacén Origen/Destino</th><td>{{ $ultimoMovimiento->almacenEntrada->Nombre ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th colspan="1">Observaciones</th><td colspan="3">{{ $ultimoMovimiento->Estado ?? 'Sin observaciones' }}</td>
            </tr>
        @else {{-- Si es salida --}}
            <tr>
                <th style="width: 15%;">Tipo de Movimiento</th><td style="width: 35%;">{{ strtoupper($ultimoMovimiento->Tipo_salida) }}</td>
                <th style="width: 15%;">Fecha de Registro</th><td style="width: 35%;">{{ $ultimoMovimiento->updated_at?->format('Y-m-d H:i') }}</td>
            </tr>
            <tr>
                <th>No. Orden</th><td>{{ $ultimoMovimiento->No_orden_salida }}</td>
                <th>Almacén Origen/Destino</th><td>{{ $ultimoMovimiento->almacenSalida->Nombre ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th colspan="1">Observaciones</th><td colspan="3">{{ $ultimoMovimiento->Observaciones ?? 'Sin observaciones' }}</td>
            </tr>
        @endif
    </table>

    {{-- BLOQUE 3: CHECKLIST DINÁMICO (Muestra el último: Entrada o Salida) --}}
    @if($checklist)
        <h5 class="font-weight-bold mt-4 mb-2 p-1 bg-light border-bottom">3. ÚLTIMO CHECKLIST DE {{ strtoupper($tipoMovimiento) }}</h5>
        
        <table class="table table-sm table-bordered text-center">
            <thead style="background-color: #f0f0f0;">
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
                    @if($tipoMovimiento === 'salida')
                        {{-- PROPIEDADES DEL CHECKLIST DE SALIDA (Asumiendo sufijo _salida) --}}
                        <td>{{ $checklist_salidas->documentos_completos ? '✔' : '✖' }}</td>
                        <td>{{ $checklist_salidas->accesorios_completos ? '✔' : '✖' }}</td>
                        <td>{{ $checklist_salidas->pdi_realizada ? '✔' : '✖' }}</td>
                        <td>{{ $checklist_salidas->seguro_vigente ? '✔' : '✖' }}</td>
                        <td>{{ $checklist_salidas->nfc_instalado ? '✔' : '✖' }}</td>
                        <td>{{ $checklist_salidas->gps_instalado ? '✔' : '✖' }}</td>
                        <td>{{ $checklist_salidas->folder_viajero ? '✔' : '✖' }}</td>
                        <td>{{ $checklist_salidas->fecha_revision ?? 'N/A' }}</td>
                        <td>{{ $checklist_salidas->revisado ?? 'N/A' }}</td>
                        <td>{{ $checklist_salidas->observaciones ?? 'Sin observaciones' }}</td>
                    @else
                        {{-- PROPIEDADES DEL CHECKLIST DE ENTRADA (Sin sufijo) --}}
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
                    @endif
                </tr>
            </tbody>
        </table>
    @endif

    {{-- FOOTER --}}
    <div class="text-center mt-5">
        <small class="text-muted">Documento generado automáticamente — Sistema Nissan SIS</small>
    </div>
</div>

@push('css')
<style>
    /* Oculta elementos no deseados en la impresión */
    @media print {
        .no-print {
            display: none !important;
        }
        /* Fuerza fondos de color claro para encabezados en impresión */
        .bg-light, .table thead th {
            -webkit-print-color-adjust: exact;
            background-color: #f0f0f0 !important;
        }
        body {
            background-color: #fff !important;
        }
    }
</style>
@endpush
@stop