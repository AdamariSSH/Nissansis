@extends('adminlte::page')

@section('title', 'Pase de entrada')

@section('content')
<div class="container mt-4" style="font-size: 14px;">
    <div class="text-center mb-2">
        <h4>BITACORA UNIDADES - {{ $vehiculo->Almacen_salida }}</h4>
    </div>
    
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h4>GRUPO GRANAUTO</h4>
            <h5>SONORA</h5>
        </div>
        <div class="text-right">
            <p><strong>FECHA |</strong> {{ \Carbon\Carbon::now()->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</p>
        </div>
    </div>

    {{-- Datos del vehículo --}}
    <div class="row mt-3 mb-4">
        <div class="col-md-8">
            <p><strong>COORDINADOR DE LOGÍSTICA:</strong> {{ $vehiculo->Coordinador_Logistica }}</p>
            <p><strong>ALMACÉN DE SALIDA:</strong> {{ $vehiculo->Almacen_salida }}</p>
            <p><strong>ALMACÉN DE ENTRADA:</strong> {{ $vehiculo->Almacen_entrada }}</p>
            
            <div class="vin-box" style="border: 1px solid #000; padding: 5px; display: inline-block; margin: 10px 0;">
                <strong style="font-size: 1.5em;">{{ $vehiculo->VIN }}</strong>
            </div>
            
            <h5 class="mt-3">CARACTERÍSTICAS</h5>
            <p><strong>{{ $vehiculo->Modelo }}</strong></p>
            
            <h5>COLOR</h5>
            <p><strong>{{ $vehiculo->Color }}</strong></p>
            
            <h5>DATOS DE LA UNIDAD</h5>
            <p><strong>MODELO</strong><br>{{ explode(' ', $vehiculo->Modelo)[0] }}</p>
            <p><strong>MOTOR</strong><br>{{ $vehiculo->Motor }}</p>
        </div>
        
        <div class="col-md-4 text-right">
            @if(!empty($qrBase64))
                <img 
                    src="data:image/svg+xml;base64,{{ $qrBase64 }}" 
                    alt="QR" 
                    style="width: 150px; height: 150px;"
                />
            @endif
        </div>
    </div>

{{-- CHECK LIST --}}
<h5 class="mt-4">CHECK LIST</h5>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ITEM</th>
            <th>TRASLADISTA</th>
            <th>ALMACÉN DE SALIDA</th>
            <th>ALMACÉN DE ENTRADA</th>
            <th>OBSERVACIONES</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Documentos y accesorios originales</td>
            <td>✔️</td><td>✔️</td><td>❌</td><td></td>
        </tr>
        <tr>
            <td>Carrocería</td>
            <td>✔️</td><td>✔️</td><td>✔️</td><td></td>
        </tr>
        <tr>
            <td>Interior</td>
            <td>✔️</td><td>✔️</td><td>✔️</td><td></td>
        </tr>
        <tr>
            <td>Revisión o previa</td>
            <td>✔️</td><td>✔️</td><td>✔️</td><td></td>
        </tr>
        <tr>
            <td>Seguro</td>
            <td>✔️</td><td>✔️</td><td>✔️</td><td></td>
        </tr>
        <tr>
            <td>NFC instalado / GPS</td>
            <td>✔️</td><td>✔️</td><td>✔️</td><td></td>
        </tr>
    </tbody>
</table>


    {{-- Kilometraje --}}
    <h5 class="mt-3">KILOMETRAJE:</h5>
    <table class="table table-bordered" style="width: 50%;">
        <tr>
            <td><strong>INICIAL</strong></td>
            <td><strong>FINAL</strong></td>
        </tr>
        <tr>
            <td>_________</td>
            <td>_________</td>
        </tr>
    </table>

    {{-- Firmas --}}
    <div class="row mt-4">
        <div class="col-4 text-center">
            <p><strong>ALMACÉN DE SALIDA</strong></p>
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
            <p><strong>ALMACÉN DE ENTRADA</strong></p>
            <p>Recibo la unidad en las condiciones seleccionadas anteriormente.</p>
            <div style="height: 50px;"></div>
            <hr style="border-top: 1px solid #000; width: 80%;">
        </div>
    </div>

    <div class="text-right mt-3">
        <small>{{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</small>
    </div>

    {{-- Botones --}}
    <div class="d-print-none mt-3">
        <button onclick="window.print()" class="btn btn-success">Imprimir</button>
        <a href="{{ route('admin.vehiculos') }}" class="btn btn-secondary">Regresar</a>
    </div>
</div>
@stop

@section('css')
<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 14px;
    }

    .table {
        border-collapse: collapse;
        width: 100%;
        margin-bottom: 10px;
    }

    .table th, .table td {
        border: 1px solid #000;
        padding: 8px;
        text-align: center;
    }

    .table th {
        background-color: #f2f2f2;
        font-weight: bold;
    }

    .vin-box {
        background-color: #f8f9fa;
    }

    @media print {
        .d-print-none {
            display: none !important;
        }

        body {
            margin: 0.5cm;
            font-size: 12px;
        }

        .btn, a {
            display: none !important;
        }
        
        .table {
            page-break-inside: avoid;
        }
    }
</style>
@stop