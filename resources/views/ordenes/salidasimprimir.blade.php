@extends('adminlte::page')

@section('title', 'Orden de Movimiento de Salida de Vehículo')

@section('content_header')
@stop

@section('content')
<div class="container mt-4" style="font-size: 14px;">

    {{-- ENCABEZADO --}}
    <div class="text-center mb-4">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width: 120px;">
        <h4 class="mt-2 mb-1 font-weight-bold text-uppercase">GRUPO GRAN AUTO SONORA</h4>
        <p class="mb-0 font-weight-bold text-uppercase">ORDEN DE MOVIMIENTO DE SALIDA DE VEHÍCULO</p>
    </div>

    {{-- BLOQUE PRINCIPAL IZQ-DETALLES / DER-VIN --}}
    <div style="display: flex; justify-content: space-between; gap: 10px;">

        {{-- COLUMNA IZQUIERDA --}}
        <div style="width: 68%;">
            <table class="table table-bordered" style="border: 1px solid #000;">
                <tr class="table-secondary text-center font-weight-bold">
                    <td colspan="2">DETALLES DEL MOVIMIENTO Y VEHÍCULO</td>
                </tr>
                <tr>
                    <td><strong>Fecha Actual:</strong></td>
                    <td>{{ \Carbon\Carbon::now()->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td><strong>Coordinador de Logística:</strong></td>
                    <td>{{ $salida->Coordinador_Logistica ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Almacén de Salida:</strong></td>
                    <td>{{ optional($salida->almacenSalida)->Nombre ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Tipo de Movimiento:</strong></td>
                    <td>Salida de Almacén</td>
                </tr>
                <tr>
                    <td><strong>Color:</strong></td>
                    <td>{{ optional($salida->vehiculo)->Color ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Modelo:</strong></td>
                    <td>{{ optional($salida->vehiculo)->Modelo ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>

        {{-- COLUMNA DERECHA --}}
        <div style="width: 30%; text-align: center;">
            <table class="table table-bordered" style="border: 1px solid #000;">
                <tr class="table-secondary text-center font-weight-bold">
                    <td>NÚMERO DE IDENTIFICACIÓN VEHICULAR (VIN)</td>
                </tr>
                <tr>
                    <td>
                        <div style="font-size: 1.2em; font-weight: bold; color: #003399;">
                            {{ $salida->VIN }}
                        </div>
                        @if(!empty($qrBase64))
                            <img src="data:image/svg+xml;base64,{{ $qrBase64 }}" 
                                 alt="QR Code" style="width: 100px; margin-top: 10px;">
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- CHECKLIST --}}
    @if($checklist)
        <h5 class="mt-4">CHECKLIST DE SALIDA 
            <small class="text-muted">({{ $tipoChecklist }})</small>
        </h5>
        <table class="table table-bordered table-checklist text-center">
            <thead class="thead-light">
                <tr>
                    <th>Ítem</th>
                    <th>Estado</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Documentos Completos</td>
                    <td>{{ (int)$checklist->documentos_completos === 1 ? '✔️' : '❌' }}</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Accesorios Completos</td>
                    <td>{{ (int)$checklist->accesorios_completos === 1 ? '✔️' : '❌' }}</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Estado Exterior</td>
                    <td colspan="2">{{ $checklist->estado_exterior ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Estado Interior</td>
                    <td colspan="2">{{ $checklist->estado_interior ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>PDI Realizada</td>
                    <td>{{ (int)$checklist->pdi_realizada === 1 ? '✔️' : '❌' }}</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Seguro Vigente</td>
                    <td>{{ (int)$checklist->seguro_vigente === 1 ? '✔️' : '❌' }}</td>
                    <td></td>
                </tr>
                <tr>
                    <td>NFC Instalado</td>
                    <td>{{ (int)$checklist->nfc_instalado === 1 ? '✔️' : '❌' }}</td>
                    <td></td>
                </tr>
                <tr>
                    <td>GPS Instalado</td>
                    <td>{{ (int)$checklist->gps_instalado === 1 ? '✔️' : '❌' }}</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Folder Viajero</td>
                    <td>{{ (int)$checklist->folder_viajero === 1 ? '✔️' : '❌' }}</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Recibido Por</td>
                    <td colspan="2">{{ $checklist->recibido_por ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Fecha Revisión</td>
                    <td colspan="2">{{ $checklist->fecha_revision ? \Carbon\Carbon::parse($checklist->fecha_revision)->format('d/m/Y') : 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Observaciones</td>
                    <td colspan="2">{{ $checklist->observaciones ?? '' }}</td>
                </tr>
            </tbody>
        </table>
    @else
        <div class="alert alert-warning mt-4">
            No se encontró checklist de salida para esta orden.
        </div>
    @endif

    {{-- FIRMAS --}}
    <div class="row mt-5">
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
            <p><strong>ALMACÉN DESTINO</strong></p>
            <p>Recibo la unidad en las condiciones seleccionadas anteriormente.</p>
            <div style="height: 50px;"></div>
            <hr style="border-top: 1px solid #000; width: 80%;">
        </div>
    </div>

    <div class="text-right mt-3">
        <small>Generado el: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</small>
    </div>

    {{-- BOTONES --}}
    <div class="d-print-none mt-4 text-center">
        <button onclick="imprimirYRegresar()" class="btn btn-success mx-2">Imprimir</button>
        <a href="{{ route('salidas.index') }}" target="_self" class="btn btn-secondary mx-2">Regresar</a>
    </div>
</div>
@stop

@section('css')
<style>
    .table-checklist th {
        background: #f8f9fa;
    }
    @media print {
        .d-print-none { display: none !important; }
        body { margin: 0.5cm; font-size: 12px; }
        .table { page-break-inside: avoid; }
    }
</style>
@stop

@section('js')
<script>
function imprimirYRegresar() {
    window.print();
    setTimeout(() => {
        window.location.href = "{{ route('salidas.index') }}";
    }, 800);
}
</script>
@stop
