@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-sign-in-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Solicitud de Entradas</span>
                    <span class="info-box-number">16</span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-route"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Solicitud de Salidas</span>
                    <span class="info-box-number">11</span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-box"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Vehículos</span>
                    <span class="info-box-number">185</span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box">
                <span class="info-box-icon bg-danger"><i class="fas fa-file-invoice"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Facturas</span>
                    <span class="info-box-number">2</span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box">
                <span class="info-box-icon bg-primary"><i class="fas fa-boxes"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Existencia total</span>
                    <span class="info-box-number">868</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-12">
            <a href="{{ route('almacen') }}" class="text-decoration-none">
                <div class="info-box">
                    <span class="info-box-icon bg-secondary"><i class="fas fa-warehouse"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Gestionar Almacenes</span>
                        <span class="info-box-number">{{ $cantidadAlmacenes ?? '0' }}</span> <!-- Si está vacío, muestra 0 -->
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-chart-line"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Entradas del dia</span>
                    <span class="info-box-number">562</span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-hammer"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Mantenimiento</span>
                    <span class="info-box-number">$1405</span>
                </div>
            </div>
        </div>

    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
@stop