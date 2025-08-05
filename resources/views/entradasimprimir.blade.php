@extends('adminlte::page')

@section('title', 'Orden de Movimiento de Vehículo')

@section('content_header')
@stop

@section('content')
<div class="container mt-4" style="font-size: 14px;">

    {{-- ENCABEZADO --}}
    <div class="text-center mb-4">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width: 120px;">
        <h4 class="mt-2 mb-1 font-weight-bold text-uppercase">GRUPO GRAN AUTO SONORA</h4>
        <p class="mb-0 font-weight-bold text-uppercase">ORDEN DE MOVIMIENTO DE VEHÍCULO</p>
    </div>

    {{-- DATOS PRINCIPALES --}}
    <table class="table table-bordered mb-4">
        <tr>
            <td><strong>Fecha Actual:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y') }}</td>
            <td><strong>Coordinador de Logística:</strong> {{ $entrada->Coordinador_Logistica ?? 'N/A' }}</td>
            <td><strong>Almacén Entrada:</strong> {{ optional($entrada->almacenEntrada)->Nombre ?? 'N/A' }}</td>
            {{-- Si tienes almacenSalida, agregarlo igual --}}
        </tr>
        <tr>
            <td><strong>VIN:</strong> {{ $entrada->VIN }}</td>
            <td><strong>Modelo:</strong> {{ optional($entrada->vehiculo)->Modelo ?? 'N/A' }}</td>
            <td><strong>Color:</strong> {{ optional($entrada->vehiculo)->Color ?? 'N/A' }}</td>
        </tr>
    </table>

    {{-- VIN Y QR --}}
    <div class="row mt-3 mb-4 align-items-center">
        <div class="col-md-8">
            <h5 class="mb-3">Información de la Unidad</h5>
            <div class="vin-box" style="border: 1px solid #000; padding: 5px; display: inline-block; margin: 10px 0;">
                <strong style="font-size: 1.5em;">VIN: {{ $entrada->VIN }}</strong>
            </div>
            <h5 class="mt-3">Características</h5>
            <p><strong>Modelo:</strong> {{ optional($entrada->vehiculo)->Modelo ?? 'N/A' }}</p>
            <h5>Color</h5>
            <p><strong>Color:</strong> {{ optional($entrada->vehiculo)->Color ?? 'N/A' }}</p>
        </div>

        <div class="col-md-4 text-right">
            @if(!empty($qrBase64))
                <img src="data:image/svg+xml;base64,{{ $qrBase64 }}" alt="QR Code"
                     style="width: 180px; height: 180px; border: 1px solid #ccc;" />
            @else
                <p>No se pudo generar el QR.</p>
            @endif
        </div>
    </div>

    {{-- CHECKLIST --}}
    <h5 class="mt-4">CHECK LIST DE LA UNIDAD</h5>
    <table class="table table-bordered table-checklist text-center">
        <thead class="thead-light">
            <tr>
                <th>Ítem</th>
                <th>Estado</th>
                <th>Observaciones</th>
            </tr>
        </thead>
        <tbody>
            @php $c = $entrada->checklist; @endphp

            <tr>
                <td>Documentos Completos</td>
                <td>{{ ($c && $c->documentos_completos) ? '✔️' : '❌' }}</td>
                <td></td>
            </tr>
            <tr>
                <td>Accesorios Completos</td>
                <td>{{ ($c && $c->accesorios_completos) ? '✔️' : '❌' }}</td>
                <td></td>
            </tr>
            <tr>
                <td>Estado Exterior</td>
                <td colspan="2">{{ $c->estado_exterior ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Estado Interior</td>
                <td colspan="2">{{ $c->estado_interior ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>PDI Realizada</td>
                <td>{{ ($c && $c->pdi_realizada) ? '✔️' : '❌' }}</td>
                <td></td>
            </tr>
            <tr>
                <td>Seguro Vigente</td>
                <td>{{ ($c && $c->seguro_vigente) ? '✔️' : '❌' }}</td>
                <td></td>
            </tr>
            <tr>
                <td>NFC Instalado</td>
                <td>{{ ($c && $c->nfc_instalado) ? '✔️' : '❌' }}</td>
                <td></td>
            </tr>
            <tr>
                <td>GPS Instalado</td>
                <td>{{ ($c && $c->gps_instalado) ? '✔️' : '❌' }}</td>
                <td></td>
            </tr>
            <tr>
                <td>Folder Viajero</td>
                <td>{{ ($c && $c->folder_viajero) ? '✔️' : '❌' }}</td>
                <td></td>
            </tr>
            <tr>
                <td>Recibido Por</td>
                <td colspan="2">{{ $c->recibido_por ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Fecha Revisión</td>
                <td colspan="2">{{ $c && $c->fecha_revision ? \Carbon\Carbon::parse($c->fecha_revision)->format('d/m/Y') : 'N/A' }}</td>
            </tr>
            <tr>
                <td>Observaciones</td>
                <td colspan="2">{{ $c->observaciones ?? '' }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Firmas --}}
    <div class="row mt-5">
        <div class="col-4 text-center">
            <p><strong>ALMACÉN DE ENTRADA</strong></p>
            <p>Entrego la unidad en las condiciones seleccionadas anteriormente.</p>
            <div style="height: 50px;"></div>
            <hr style="border-top: 1px solid #000; width: 80%;">
        </div>

        <div class="col-4 text-center">
            <p><strong>COORDINADOR DE ENTREGAS / TRASLADISTA</strong></p>
            <p>Recibo la unidad en las condiciones seleccionadas anteriormente.</p>
            <div style="height: 50px;"></div>
            <hr style="border-top: 1px solid #000; width: 80%;">
        </div>

        <div class="col-4 text-center">
            <p><strong>ALMACÉN DE SALIDA</strong></p>
            <p>Recibo la unidad en las condiciones seleccionadas anteriormente.</p>
            <div style="height: 50px;"></div>
            <hr style="border-top: 1px solid #000; width: 80%;">
        </div>
    </div>

    <div class="text-right mt-3">
        <small>Generado el: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</small>
    </div>

    <div class="d-print-none mt-4 text-center">
        <button onclick="window.print()" class="btn btn-success mx-2">Imprimir</button>
        <a href="{{ route('admin.entradas') }}" class="btn btn-secondary mx-2">Regresar</a>
    </div>
</div>
@stop

@section('css')
<style>
/* Puedes agregar aquí el mismo CSS que ya tienes para impresión */
</style>
@stop
