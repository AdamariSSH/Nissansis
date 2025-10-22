
@extends('adminlte::page')

@section('title', 'Crear Salida')

@section('content_header')
    <h1>Crear Salida de Vehículo</h1>
@stop

@section('content')
    <form method="POST" action="{{ route('salidas.store') }}">
        @csrf

        <!-- Paso 0: Buscar vehículo por VIN -->
        <div class="card card-secondary">
            <div class="card-header" style="background-color: #6b6666;">
                <h3 class="card-title">Buscar Vehículo</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        {{--  Se agrega el id="vin" --}}
                        {{-- <x-adminlte-input id="vin" name="VIN" label="VIN" maxlength="17" value="{{ $vehiculo->VIN ?? '' }}" />
                    </div> --}}

                    {{-- <div class="col-md-4 position-relative">
                        <x-adminlte-input id="buscarVin" name="VIN" label="VIN" maxlength="17" />
                        <div id="resultadosVin" class="list-group position-absolute w-100" style="z-index: 1000;"></div>
                    </div> --}}
                    <x-adminlte-input id="buscarVin" name="VIN" label="VIN" maxlength="17" />
<div id="resultadosVin" class="list-group position-absolute w-100" style="z-index: 1000;"></div>

                    <div class="col-md-4">
                        <x-adminlte-button id="btn-cargar" label="Cargar Datos" theme="primary" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Paso 1: Datos del vehículo -->
        <div class="card card-primary">
            <div class="card-header" style="background-color: #6b6666;">
                <h3 class="card-title">Datos del Vehículo</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <x-adminlte-input name="Motor" label="Motor" value="{{ $vehiculo->Motor ?? '' }}" readonly />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input name="Caracteristicas" label="Características" value="{{ $vehiculo->Caracteristicas ?? '' }}" readonly />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input name="Color" label="Color" value="{{ $vehiculo->Color ?? '' }}" readonly />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input name="Modelo" label="Modelo" value="{{ $vehiculo->Modelo ?? '' }}" readonly />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input name="Kilometraje_salida" label="Kilometraje" type="number" value="0" />
                    </div>
                    
                    @isset($almacenes)
                        
                        <div class="col-md-4">
                            <x-adminlte-input name="Almacen_salida_texto" label="Almacén Salida"
                                value="{{ auth()->user()->almacen->Nombre ?? 'No asignado' }}" disabled />
                            <input type="hidden" name="Almacen_salida" value="{{ auth()->user()->almacen_id }}">
                        </div>

                       
                        <div class="col-md-4">
                            <x-adminlte-select name="Almacen_entrada" label="Almacén Entrada" required>
                                @foreach ($almacenes as $almacen)
                                    <option value="{{ $almacen->Id_Almacen }}">{{ $almacen->Nombre }}</option>
                                @endforeach
                            </x-adminlte-select>
                        </div>
                        @endisset

                    
                    <div class="col-md-4">
                        <x-adminlte-input 
                            type="date" 
                            name="Fecha" 
                            label="Fecha Salida"  
                            value="{{ \Carbon\Carbon::now('America/Mexico_City')->format('Y-m-d') }}" 
                            required 
                        />
                    </div>
                    <div class="form-group">
                    <label for="Tipo_salida">Tipo de Salida</label>
                    <select name="Tipo_salida" id="Tipo_salida" class="form-control" required>
                        <option value="" disabled selected>Seleccione...</option>
                        <option value="Venta">Venta</option>
                        <option value="Traspaso">Traspaso</option>
                        <option value="Devolucion">Devolución</option>
                    </select>
                </div>
                </div>
            </div>
        </div>

        <!-- Paso 2: Checklist de salida -->
        <div class="card card-info">
            <div class="card-header" style="background-color: #6b6666;">
                <h3 class="card-title">Checklist de Salida</h3>
            </div>
            <div class="card-body" id="checklist-container">
                <p>No hay checklist previo. Se creará uno nuevo al guardar.</p>
            </div>
        </div>

        <!-- Paso 3: Confirmación -->
        <div class="card card-success">
            <div class="card-header" style="background-color: #6b6666;">
                <h3 class="card-title">Confirmación</h3>
            </div>
            <div class="card-body">
                <x-adminlte-textarea name="Observaciones_salida" label="Observaciones de salida" />
                <x-adminlte-button id="btn-guardar" label="Guardar Salida" theme="success" icon="fas fa-save" type="submit" />
                <a href="{{ route('vehiculos.index') }}" class="btn btn-secondary ml-2">Cancelar</a>
            </div>
        </div>
    </form>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if (session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: '{{ session('success') }}',
        confirmButtonText: 'OK',
        confirmButtonColor: '#3085d6'
    });
</script>
@endif

@if (session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: '¡Oops!',
        text: '{{ session('error') }}',
        confirmButtonText: 'OK',
        confirmButtonColor: '#d33'
    });
</script>
@endif

</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const btnCargar = document.getElementById('btn-cargar');
    const checklistContainer = document.getElementById("checklist-container");
    const input = document.getElementById('buscarVin');
    const resultados = document.getElementById('resultadosVin');

    // 🔍 AUTOCOMPLETAR VIN
    input.addEventListener('keyup', function() {
        const query = this.value.trim();

        if (query.length < 3) {
            resultados.innerHTML = '';
            return;
        }

        fetch(`/buscar-vin?query=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                resultados.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(v => {
                        const item = document.createElement('a');
                        item.href = "#";
                        item.classList.add('list-group-item', 'list-group-item-action');
                        item.textContent = `${v.VIN} - ${v.Modelo} (${v.Color})`;

                        item.addEventListener('click', function(e) {
                            e.preventDefault();
                            input.value = v.VIN;
                            resultados.innerHTML = '';
                        });

                        resultados.appendChild(item);
                    });
                } else {
                    resultados.innerHTML = '<div class="list-group-item text-muted">Sin resultados</div>';
                }
            })
            .catch(err => console.error(err));
    });

    // Ocultar sugerencias al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!input.contains(e.target)) {
            resultados.innerHTML = '';
        }
    });

    // 🚗 BOTÓN CARGAR DATOS
    btnCargar.addEventListener('click', function (e) {
        e.preventDefault();

        let vin = document.getElementById('buscarVin').value; // ✅ id corregido
        if (!vin) {
            alert("Ingrese un VIN");
            return;
        }

        fetch(`/admin/salidas/vehiculo/${vin}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }

                // 🚗 Rellenar campos del vehículo
                document.querySelector("input[name='Motor']").value = data.vehiculo.Motor || '';
                document.querySelector("input[name='Caracteristicas']").value = data.vehiculo.Caracteristicas || '';
                document.querySelector("input[name='Color']").value = data.vehiculo.Color || '';
                document.querySelector("input[name='Modelo']").value = data.vehiculo.Modelo || '';

                // 📝 Checklist info dinámico
                if (data.checklist) {
                    checklistContainer.innerHTML = renderChecklist(data.checklist);
                } else {
                    checklistContainer.innerHTML = `<p>No hay checklist previo. Se creará uno nuevo al guardar.</p>`;
                }
            })
            .catch(error => console.error("Error:", error));
    });

    // ✅ FUNCIÓN: renderizar checklist
    function renderChecklist(c) {
        return `
            <input type="hidden" name="documentos_completos" value="0">
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="documentos_completos" value="1" ${c.documentos_completos ? 'checked' : ''}>
                <label class="form-check-label">Documentos Completos</label>
            </div>

            <input type="hidden" name="accesorios_completos" value="0">
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="accesorios_completos" value="1" ${c.accesorios_completos ? 'checked' : ''}>
                <label class="form-check-label">Accesorios Completos</label>
            </div>

            <div class="form-group">
                <label>Estado Exterior</label>
                <select class="form-control" name="estado_exterior">
                    <option ${c.estado_exterior === 'Excelente' ? 'selected' : ''}>Excelente</option>
                    <option ${c.estado_exterior === 'Bueno' ? 'selected' : ''}>Bueno</option>
                    <option ${c.estado_exterior === 'Regular' ? 'selected' : ''}>Regular</option>
                    <option ${c.estado_exterior === 'Malo' ? 'selected' : ''}>Malo</option>
                </select>
            </div>

            <div class="form-group">
                <label>Estado Interior</label>
                <select class="form-control" name="estado_interior">
                    <option ${c.estado_interior === 'Excelente' ? 'selected' : ''}>Excelente</option>
                    <option ${c.estado_interior === 'Bueno' ? 'selected' : ''}>Bueno</option>
                    <option ${c.estado_interior === 'Regular' ? 'selected' : ''}>Regular</option>
                    <option ${c.estado_interior === 'Malo' ? 'selected' : ''}>Malo</option>
                </select>
            </div>

            <input type="hidden" name="pdi_realizada" value="0">
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="pdi_realizada" value="1" ${c.pdi_realizada ? 'checked' : ''}>
                <label class="form-check-label">PDI Realizada</label>
            </div>

            <input type="hidden" name="seguro_vigente" value="0">
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="seguro_vigente" value="1" ${c.seguro_vigente ? 'checked' : ''}>
                <label class="form-check-label">Seguro Vigente</label>
            </div>

            <input type="hidden" name="nfc_instalado" value="0">
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="nfc_instalado" value="1" ${c.nfc_instalado ? 'checked' : ''}>
                <label class="form-check-label">NFC Instalado</label>
            </div>

            <input type="hidden" name="gps_instalado" value="0">
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="gps_instalado" value="1" ${c.gps_instalado ? 'checked' : ''}>
                <label class="form-check-label">GPS Instalado</label>
            </div>

            <input type="hidden" name="folder_viajero" value="0">
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="folder_viajero" value="1" ${c.folder_viajero ? 'checked' : ''}>
                <label class="form-check-label">Folder Viajero</label>
            </div>

            <div class="form-group">
                <label>Observaciones</label>
                <textarea class="form-control" name="observaciones_checklist">${c.observaciones_checklist || ''}</textarea>
            </div>

            <div class="form-group">
                <label>Recibido por</label>
                <input type="text" class="form-control" name="recibido_por" value="${c.recibido_por || ''}">
            </div>

            <div class="form-group">
                <label>Fecha Revisión</label>
                <input type="date" class="form-control" name="fecha_revision" value="${c.fecha_revision || ''}">
            </div>
        `;
    }
});
</script>
@stop


{{-- 
@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnCargar = document.getElementById('btn-cargar');
    const checklistContainer = document.getElementById("checklist-container");

    function renderChecklist(c) {
        return `
            <input type="hidden" name="documentos_completos" value="0">
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="documentos_completos" value="1" ${c.documentos_completos ? 'checked' : ''}>
                <label class="form-check-label">Documentos Completos</label>
            </div>

            <input type="hidden" name="accesorios_completos" value="0">
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="accesorios_completos" value="1" ${c.accesorios_completos ? 'checked' : ''}>
                <label class="form-check-label">Accesorios Completos</label>
            </div>

            <div class="form-group">
                <label>Estado Exterior</label>
                <select class="form-control" name="estado_exterior">
                    <option ${c.estado_exterior === 'Excelente' ? 'selected' : ''}>Excelente</option>
                    <option ${c.estado_exterior === 'Bueno' ? 'selected' : ''}>Bueno</option>
                    <option ${c.estado_exterior === 'Regular' ? 'selected' : ''}>Regular</option>
                    <option ${c.estado_exterior === 'Malo' ? 'selected' : ''}>Malo</option>
                </select>
            </div>

            <div class="form-group">
                <label>Estado Interior</label>
                <select class="form-control" name="estado_interior">
                    <option ${c.estado_interior === 'Excelente' ? 'selected' : ''}>Excelente</option>
                    <option ${c.estado_interior === 'Bueno' ? 'selected' : ''}>Bueno</option>
                    <option ${c.estado_interior === 'Regular' ? 'selected' : ''}>Regular</option>
                    <option ${c.estado_interior === 'Malo' ? 'selected' : ''}>Malo</option>
                </select>
            </div>

            <input type="hidden" name="pdi_realizada" value="0">
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="pdi_realizada" value="1" ${c.pdi_realizada ? 'checked' : ''}>
                <label class="form-check-label">PDI Realizada</label>
            </div>

            <input type="hidden" name="seguro_vigente" value="0">
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="seguro_vigente" value="1" ${c.seguro_vigente ? 'checked' : ''}>
                <label class="form-check-label">Seguro Vigente</label>
            </div>

            <input type="hidden" name="nfc_instalado" value="0">
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="nfc_instalado" value="1" ${c.nfc_instalado ? 'checked' : ''}>
                <label class="form-check-label">NFC Instalado</label>
            </div>

            <input type="hidden" name="gps_instalado" value="0">
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="gps_instalado" value="1" ${c.gps_instalado ? 'checked' : ''}>
                <label class="form-check-label">GPS Instalado</label>
            </div>

            <input type="hidden" name="folder_viajero" value="0">
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="folder_viajero" value="1" ${c.folder_viajero ? 'checked' : ''}>
                <label class="form-check-label">Folder Viajero</label>
            </div>

            <div class="form-group">
                <label>Observaciones</label>
                <textarea class="form-control" name="observaciones_checklist">${c.observaciones_checklist || ''}</textarea>
            </div>

            <div class="form-group">
                <label>Recibido por</label>
                <input type="text" class="form-control" name="recibido_por" value="${c.recibido_por || ''}">
            </div>

            <div class="form-group">
                <label>Fecha Revisión</label>
                <input type="date" class="form-control" name="fecha_revision" value="${c.fecha_revision || ''}">
            </div>
        `;
    }

    btnCargar.addEventListener('click', function (e) {
        e.preventDefault();

        let vin = document.getElementById('vin').value;
        if (!vin) {
            alert("Ingrese un VIN");
            return;
        }

        fetch(`/admin/salidas/vehiculo/${vin}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }

                // 🚗 Rellenar campos del vehículo
                document.querySelector("input[name='Motor']").value = data.vehiculo.Motor || '';
                document.querySelector("input[name='Caracteristicas']").value = data.vehiculo.Caracteristicas || '';
                document.querySelector("input[name='Color']").value = data.vehiculo.Color || '';
                document.querySelector("input[name='Modelo']").value = data.vehiculo.Modelo || '';

                // 📝 Checklist info dinámico
                if (data.checklist) {
                    checklistContainer.innerHTML = renderChecklist(data.checklist);
                } else {
                    checklistContainer.innerHTML = `<p>No hay checklist previo. Se creará uno nuevo al guardar.</p>`;
                }
            })
            .catch(error => console.error("Error:", error));
    });
});
</script>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnCargar = document.getElementById('btn-cargar');
    const checklistContainer = document.getElementById("checklist-container");
    const input = document.getElementById('buscarVin');
    const resultados = document.getElementById('resultadosVin');

    // 🔍 AUTOCOMPLETAR VIN
    input.addEventListener('keyup', function() {
        const query = this.value.trim();
        if (query.length < 3) {
            resultados.innerHTML = '';
            return;
        }

        fetch(`/buscar-vin?query=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                resultados.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(v => {
                        const item = document.createElement('a');
                        item.href = "#";
                        item.classList.add('list-group-item', 'list-group-item-action');
                        item.textContent = `${v.VIN} - ${v.Modelo} (${v.Color})`;

                        item.addEventListener('click', function(e) {
                            e.preventDefault();
                            input.value = v.VIN;
                            resultados.innerHTML = '';
                        });

                        resultados.appendChild(item);
                    });
                } else {
                    resultados.innerHTML = '<div class="list-group-item text-muted">Sin resultados</div>';
                }
            })
            .catch(err => console.error(err));
    });

    // Ocultar sugerencias al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!input.contains(e.target)) {
            resultados.innerHTML = '';
        }
    });

    // 🚗 BOTÓN CARGAR DATOS
    btnCargar.addEventListener('click', function (e) {
        e.preventDefault();

        let vin = document.getElementById('buscarVin').value; // ✅ corregido
        if (!vin) {
            alert("Ingrese un VIN");
            return;
        }

        fetch(`/admin/salidas/vehiculo/${vin}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }

                // Rellenar datos
                document.querySelector("input[name='Motor']").value = data.vehiculo.Motor || '';
                document.querySelector("input[name='Caracteristicas']").value = data.vehiculo.Caracteristicas || '';
                document.querySelector("input[name='Color']").value = data.vehiculo.Color || '';
                document.querySelector("input[name='Modelo']").value = data.vehiculo.Modelo || '';

                // Checklist dinámico
                if (data.checklist) {
                    checklistContainer.innerHTML = renderChecklist(data.checklist);
                } else {
                    checklistContainer.innerHTML = `<p>No hay checklist previo. Se creará uno nuevo al guardar.</p>`;
                }
            })
            .catch(error => console.error("Error:", error));
    });

    function renderChecklist(c) {
        return `
            <input type="hidden" name="documentos_completos" value="0">
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="documentos_completos" value="1" ${c.documentos_completos ? 'checked' : ''}>
                <label class="form-check-label">Documentos Completos</label>
            </div>
            ...
        `;
    }
});
</script>
@stop --}}
