@extends('adminlte::page')

@section('title', 'Orden de Movimiento de Vehículo')

@section('content_header')
@stop

@section('content')
<div class="container mt-4" style="font-size: 14px;">

    {{-- ENCABEZADO --}}
    <div class="text-center mb-3">
        <img src="{{ asset('images/logo.png') }}" alt="Logo Nissan" style="width: 100px;">
        <h4 class="mt-2 mb-0 font-weight-bold text-uppercase">GRUPO GRAN AUTO SONORA</h4>
        <p class="mb-0 font-weight-bold text-uppercase">ORDEN DE MOVIMIENTO DE VEHÍCULO</p>
    </div>

   {{-- SECCIÓN PRINCIPAL: DETALLES (izquierda) y VIN (derecha) --}}
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
                <td><strong>Color:</strong></td>
                <td>{{ optional($entrada->vehiculo)->Color ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Tipo de Movimiento:</strong></td>
                <td>Entrada a Almacén</td>
            </tr>
            <tr>
                <td><strong>Coordinador Logística:</strong></td>
                <td>{{ $entrada->Coordinador_Logistica ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Almacén Origen:</strong></td>
                <td>{{ $entrada->almacen_origen ?? 'N/A' }}</td>
            </tr>
            <tr style="background-color: #f2f0fa;">
                <td><strong>Almacén Destino:</strong></td>
                <td>{{ $entrada->almacen_destino ?? 'N/A' }}</td>
            </tr>
             <tr>
                <td><strong>Modelo :</strong></td>
                <td>{{ optional($entrada->vehiculo)->Modelo ?? 'N/A' }}</td>
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
                        {{ $entrada->VIN }}
                    </div>
                    @if(!empty($qrBase64))
                        <img src="data:image/svg+xml;base64,{{ $qrBase64 }}" 
                             alt="QR Code" style="width: 300px; margin-top: 0px;">
                    @endif
                </td>
            </tr>
            {{-- <tr>
                <td class="text-left">
                    <strong>Almacén Actual:</strong> {{ optional($entrada->almacenEntrada)->Nombre ?? 'N/A' }}<br>
                </td>
            </tr> --}}
        </table>
    </div>
</div>


    {{-- CHECKLIST --}}
    @if($checklist)
    <h5 class="mt-4 mb-2 font-weight-bold">Checklist de Entrada</h5>
    <table class="table table-bordered text-center table-checklist">
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
                <td></td>
                <td>{{ $checklist->estado_exterior ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Estado Interior</td>
                <td></td>
                <td>{{ $checklist->estado_interior ?? 'N/A' }}</td>
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
                <td colspan="2">{{ $checklist->observaciones ?? 'Checklist en orden' }}</td>
            </tr>
        </tbody>
    </table>
    @endif

    {{-- FIRMAS --}}
    <div class="row mt-5 text-center">
        <div class="col-4">
            <strong>ALMACÉN DE ENTRADA</strong>
            <div style="height: 60px;"></div>
            <hr style="border-top: 1px solid #000; width: 80%;">
        </div>
        <div class="col-4">
            <strong>COORDINADOR / TRASLADISTA</strong>
            <div style="height: 60px;"></div>
            <hr style="border-top: 1px solid #000; width: 80%;">
        </div>
        <div class="col-4">
            <strong>ALMACÉN DE SALIDA</strong>
            <div style="height: 60px;"></div>
            <hr style="border-top: 1px solid #000; width: 80%;">
        </div>
    </div>

    {{-- PIE --}}
    <div class="text-right mt-3">
        <small>Generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</small>
    </div>

    <div class="d-print-none text-center mt-4">
        <button onclick="imprimirYRegresar()" class="btn btn-success mx-2">Imprimir</button>
        <a href="{{ route('entradas.index') }}" class="btn btn-secondary mx-2">Regresar</a>
    </div>
</div>
@stop

@section('css')
<style>
.table-checklist th {
    background-color: #f2f2f2;
}
.table td, .table th {
    vertical-align: middle !important;
}
@media print {
    .d-print-none { display: none !important; }
    body { margin: 0.8cm; font-size: 12px; }
    .table { page-break-inside: avoid; }
}
</style>
@stop

@section('js')
<script>
function imprimirYRegresar() {
    window.print();
    setTimeout(() => {
        window.location.href = "{{ route('entradas.index') }}";
    }, 800);
}
</script>
@stop
