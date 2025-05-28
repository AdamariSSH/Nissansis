@extends('adminlte::page')

@section('title', 'Orden de Movimiento de Vehículo') {{-- Título unificado y descriptivo --}}

@section('content_header')
    {{-- Puedes añadir un header específico si lo deseas, o dejarlo vacío --}}
@stop

@section('content')
<div class="container mt-4" style="font-size: 14px;">

    {{-- Encabezado de la Orden --}}
    <div class="text-center mb-4">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width: 120px;">
        <h4 class="mt-2 mb-1 font-weight-bold text-uppercase">GRUPO GRAN AUTO SONORA</h4>
        <p class="mb-0 font-weight-bold text-uppercase">ORDEN DE MOVIMIENTO DE VEHÍCULO</p> {{-- Título más genérico --}}
    </div>

    {{-- Datos principales de la Entrada/Vehículo --}}
    <table class="table table-bordered mb-4">
        <tr>
            <td><strong>Fecha Actual:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y') }}</td>
            <td><strong>Coordinador de Logística:</strong> {{ $entrada->Coordinador_Logistica }}</td>
            <td><strong>Almacén Salida:</strong> {{ $entrada->Almacen_salida }}</td>
            <td><strong>Almacén Entrada:</strong> {{ $entrada->Almacen_entrada }}</td>
        </tr>
        <tr>
            <td><strong>VIN:</strong> {{ $entrada->VIN }}</td>
            <td><strong>Motor:</strong> {{ $entrada->Motor }}</td>
            <td><strong>Modelo:</strong> {{ $entrada->Modelo }}</td>
            <td><strong>Color:</strong> {{ $entrada->Color }}</td>
        </tr>
        {{-- Puedes añadir más filas aquí si necesitas Version u otros datos --}}
        {{-- <tr><td colspan="4"><strong>Versión:</strong> {{ $entrada->Version }}</td></tr> --}}
    </table>

    {{-- Cuadro del VIN y QR --}}
    <div class="row mt-3 mb-4 align-items-center">
        <div class="col-md-8">
            <h5 class="mb-3">Información de la Unidad</h5>
            <div class="vin-box" style="border: 1px solid #000; padding: 5px; display: inline-block; margin: 10px 0;">
                <strong style="font-size: 1.5em;">VIN: {{ $entrada->VIN }}</strong>
            </div>

            <h5 class="mt-3">CARACTERÍSTICAS</h5>
            <p><strong>Modelo:</strong> {{ $entrada->Modelo }}</p>

            <h5>COLOR</h5>
            <p><strong>Color:</strong> {{ $entrada->Color }}</p>

            <h5>DATOS DE LA UNIDAD</h5>
            <p><strong>VERSIÓN:</strong> {{ $entrada->Version ?? 'N/A' }}</p> {{-- Asegúrate de que $entrada->Version exista --}}
            <p><strong>MOTOR:</strong> {{ $entrada->Motor }}</p>
        </div>

        <div class="col-md-4 text-right">
            @if(!empty($qrBase64))
                <img
                    src="data:image/svg+xml;base64,{{ $qrBase64 }}"
                    alt="QR Code"
                    style="width: 180px; height: 180px; border: 1px solid #ccc;"
                />
            @else
                <p>No se pudo generar el QR.</p>
            @endif
        </div>
    </div>

    {{-- CHECK LIST --}}
    <h5 class="mt-4">CHECK LIST DE LA UNIDAD</h5>
    <table class="table table-bordered table-checklist text-center">
        <thead class="thead-light">
            <tr>
                <th>Ítem</th>
                <th>Trasladista</th>
                <th>Almacén Salida</th>
                <th>Almacén Entrada</th>
                <th>Observaciones</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>Documentos y accesorios originales</td><td>✔️</td><td>✔️</td><td>❌</td><td></td></tr>
            <tr><td>Carrocería</td><td>✔️</td><td>✔️</td><td>✔️</td><td></td></tr>
            <tr><td>Interior</td><td>✔️</td><td>✔️</td><td>✔️</td><td></td></tr>
            <tr><td>Revisión o Previa</td><td>✔️</td><td>✔️</td><td>✔️</td><td></td></tr>
            <tr><td>Seguro</td><td>✔️</td><td>✔️</td><td>✔️</td><td></td></tr>
            <tr><td>NFC/GPS</td><td>✔️</td><td>✔️</td><td>✔️</td><td></td></tr>
            <tr>
                <td>Kilometraje</td>
                <td colspan="4" class="text-left pl-4">
                    Inicial: _______ km &nbsp;&nbsp;&nbsp;&nbsp; Final: _______ km
                </td>
            </tr>
        </tbody>
    </table>

    {{-- Firmas --}}
    <div class="row mt-5"> {{-- Margen superior para separar del checklist --}}
        <div class="col-4 text-center">
            <p><strong>ALMACÉN DE SALIDA</strong></p>
            <p>Entrego la unidad en las condiciones seleccionadas anteriormente.</p>
            <div style="height: 50px;"></div> {{-- Espacio para firma --}}
            <hr style="border-top: 1px solid #000; width: 80%;">
        </div>

        <div class="col-4 text-center">
            <p><strong>COORDINADOR DE ENTREGAS / TRASLADISTA</strong></p>
            <p>Recibo la unidad en las condiciones seleccionadas anteriormente.</p>
            <div style="height: 50px;"></div> {{-- Espacio para firma --}}
            <hr style="border-top: 1px solid #000; width: 80%;">
        </div>

        <div class="col-4 text-center">
            <p><strong>ALMACÉN DE ENTRADA</strong></p>
            <p>Recibo la unidad en las condiciones seleccionadas anteriormente.</p>
            <div style="height: 50px;"></div> {{-- Espacio para firma --}}
            <hr style="border-top: 1px solid #000; width: 80%;">
        </div>
    </div>

    <div class="text-right mt-3">
        <small>Generado el: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</small>
    </div>

    {{-- Botones de acción (no se imprimen) --}}
    <div class="d-print-none mt-4 text-center">
        <button onclick="window.print()" class="btn btn-success mx-2">Imprimir</button>
        <a href="{{ route('admin.entradas') }}" class="btn btn-secondary mx-2">Regresar</a>
    </div>

</div>
@stop

@section('css')
<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 14px;
        color: #333;
    }

    .container {
        max-width: 900px; /* Ancho máximo para el contenido principal */
        margin: auto;
    }

    .table {
        border-collapse: collapse;
        width: 100%;
        margin-bottom: 15px;
    }

    .table th, .table td {
        border: 1px solid #000;
        padding: 8px;
        text-align: left; /* Alineación de texto por defecto en tablas */
        vertical-align: top;
    }

    .table th {
        background-color: #f2f2f2;
        font-weight: bold;
        text-align: center; /* Encabezados de tabla centrados */
    }

    /* Estilos específicos para la tabla de checklist */
    .table-checklist th,
    .table-checklist td {
        text-align: center;
        vertical-align: middle;
    }

    .check-icon {
        font-size: 1.2em; /* Iconos de check más grandes */
        font-weight: bold;
    }

    .vin-box {
        background-color: #f8f9fa;
        border-radius: 5px; /* Bordes ligeramente redondeados */
    }

    /* Estilos para la impresión */
    @media print {
        body {
            margin: 0.5cm; /* Márgenes para impresión */
            font-size: 12px; /* Tamaño de fuente más pequeño para impresión */
            -webkit-print-color-adjust: exact; /* Para imprimir colores de fondo */
            print-color-adjust: exact;
        }

        .d-print-none {
            display: none !important; /* Oculta elementos que no quieres imprimir */
        }

        .btn, a { /* Asegura que los botones y enlaces no se impriman */
            display: none !important;
        }

        .table {
            page-break-inside: avoid; /* Evita que las tablas se dividan en varias páginas */
        }

        .table th, .table td {
            border: 1px solid #666 !important; /* Bordes más oscuros para impresión */
        }

        .vin-box {
            background-color: #e9ecef !important; /* Color de fondo claro para el VIN en impresión */
            border: 1px solid #000 !important;
        }

        h1, h2, h3, h4, h5, h6 {
            page-break-after: avoid; /* Evita saltos de página después de los títulos */
        }
    }
</style>
@stop

@section('js')
    <script>
        console.log('Vista de impresión de entrada cargada correctamente!');
    </script>
@stop