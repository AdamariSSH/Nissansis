@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Sistema Almacenes</h1>
@stop

@section('content')
    <div class="row">
        {{-- Entradas hoy --}}
        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-sign-in-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Entradas de Hoy</span>
                    <span class="info-box-number">{{ $entradasHoy }}</span>
                </div>
            </div>
        </div>

        {{-- Salidas hoy --}}
        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-route"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Salidas de Hoy</span>
                    <span class="info-box-number">{{ $salidasHoy }}</span>
                </div>
            </div>
        </div>

        {{-- Entradas pendientes --}}
        <div class="col-md-3 col-sm-6 col-12">
            <a href="{{ route('admin.entradas', ['estatus' => 'pendiente']) }}" class="text-decoration-none">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-sign-in-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Entradas Pendientes</span>
                        <span class="info-box-number">{{ $entradasPendientes }}</span>
                    </div>
                </div>
            </a>
        </div>

        {{-- Salidas pendientes --}}
        <div class="col-md-3 col-sm-6 col-12">
            <a href="{{ route('salidas.index', ['estatus' => 'pendiente']) }}" class="text-decoration-none">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fas fa-truck-loading"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Salidas Pendientes</span>
                        <span class="info-box-number">{{ $salidasPendientes }}</span>
                    </div>
                </div>
            </a>
        </div>

        {{-- Total vehículos --}}
        <div class="col-md-3 col-sm-6 col-12">
            <a href="{{ route('admin.vehiculos') }}" class="text-decoration-none">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-car"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Vehículos Registrados</span>
                        <span class="info-box-number">{{ $cantidadVehiculos }}</span>
                    </div>
                </div>
            </a>
        </div>

        {{-- Total almacenes --}}
        <div class="col-md-3 col-sm-6 col-12">
            <a href="{{ route('almacen') }}" class="text-decoration-none">
                <div class="info-box">
                    <span class="info-box-icon bg-secondary"><i class="fas fa-warehouse"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Almacenes</span>
                        <span class="info-box-number">{{ $cantidadAlmacenes }}</span>
                    </div>
                </div>
            </a>
        </div>

        {{-- Vehículos en mantenimiento --}}
        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-hammer"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Mantenimiento</span>
                    <span class="info-box-number">{{ $vehiculosMantenimiento }}</span>
                </div>
            </div>
        </div>
        {{-- Vehículos en mantenimiento --}}
<div class="col-md-3 col-sm-6 col-12">
    <div class="info-box">
        <span class="info-box-icon bg-success"><i class="fas fa-hammer"></i></span>
        <div class="info-box-content">
            <span class="info-box-text">Mantenimiento</span>
            <span class="info-box-number">{{ $vehiculosMantenimiento }}</span>
        </div>
    </div>
</div>

        {{-- Stock actual --}}
        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box">
                <span class="info-box-icon bg-primary"><i class="fas fa-boxes"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Stock Actual</span>
                    <span class="info-box-number">{{ $stockActual }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Gráficas --}}
    <div class="row mt-4">
        {{-- Gráfico Entradas vs Salidas --}}
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">Entradas vs Salidas (Mensual)</h3>
                </div>
                <div class="card-body">
                    <canvas id="vehiculosChart" height="120"></canvas>
                </div>
            </div>
        </div>

        {{-- Gráfico Distribución por Almacén --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h3 class="card-title">Vehículos por Almacén</h3>
                </div>
                <div class="card-body">
                    <canvas id="almacenesChart" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>
@stop


@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // ====== Gráfico de Barras Entradas vs Salidas ======
        const ctx = document.getElementById('vehiculosChart').getContext('2d');
        const meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

        const entradas = @json(array_values($entradasMes->toArray()));
        const salidas = @json(array_values($salidasMes->toArray()));
        const stock = new Array(entradas.length).fill({{ $stockActual }});

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: meses.slice(0, entradas.length),
                datasets: [
                    {
                        label: 'Entradas',
                        data: entradas,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        borderRadius: 5
                    },
                    {
                        label: 'Salidas',
                        data: salidas,
                        backgroundColor: 'rgba(75, 192, 192, 0.7)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 2,
                        borderRadius: 5
                    },
                    {
                        label: 'Stock',
                        data: stock,
                        backgroundColor: 'rgba(255, 159, 64, 0.7)',
                        borderColor: 'rgba(255, 159, 64, 1)',
                        borderWidth: 2,
                        borderRadius: 5
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    title: {
                        display: true,
                        text: 'Entradas, Salidas y Stock de Vehículos'
                    }
                }
            }
        });

        // ====== Gráfico de Pastel Vehículos por Almacén ======
        const ctx2 = document.getElementById('almacenesChart').getContext('2d');
        const almacenesLabels = @json(array_keys($vehiculosPorAlmacen->toArray()));
        const almacenesData = @json(array_values($vehiculosPorAlmacen->toArray()));

        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: almacenesLabels,
                datasets: [{
                    label: 'Vehículos',
                    data: almacenesData,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(153, 102, 255, 0.7)'
                    ],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    title: {
                        display: true,
                        text: 'Distribución de Vehículos por Almacén'
                    }
                }
            }
        });
    </script>
@stop
