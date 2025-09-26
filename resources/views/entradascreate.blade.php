@extends('adminlte::page')

@section('title', 'Crear Entrada')

@section('content_header')
    <h1>Crear Entrada de Vehículo</h1>
@stop

@section('content')
<div class="container-fluid">
    <form method="POST" action="{{ route('entradas.store') }}">
        @csrf

        <!-- Paso 1: Datos Generales -->
        <div class="card card-primary">
            <div class="card-header" style="background-color: #6b6666;">
                <h3 class="card-title">Paso 1: Datos Generales</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Vehículo -->
                    <div class="col-md-4">
                        <x-adminlte-input name="VIN" label="VIN" maxlength="17" required />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input name="Motor" label="Motor"  maxlength="17" required/>
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input name="Caracteristicas" label="Caracteristicas" required />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input name="Color" label="Color" required />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input name="Modelo" label="Modelo" required />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input name="Kilometraje_entrada" label="Kilometraje" type="number" value="0" />
                    </div>
                     {{-- <div class="col-md-4">
                        <x-adminlte-input name="Coordinador_Logistica" label="Coordinador de Logística" required />
                    </div> --}}
                    

                    <!-- Almacén y tipo -->
                    {{-- <div class="col-md-4">
                        <x-adminlte-select name="Almacen_entrada" label="Almacén Entrada" required>
                            @foreach ($almacenes as $almacen)
                                <option value="{{ $almacen->Id_Almacen }}">{{ $almacen->Nombre }}</option>
                            @endforeach
                        </x-adminlte-select>
                    </div> --}}
                    <div class="col-md-4">
                        @if(auth()->user()->isAdmin())
                            {{-- Admin: puede elegir cualquier almacén --}}
                            <x-adminlte-select name="Almacen_entrada" label="Almacén Entrada" required>
                                @foreach ($almacenes as $almacen)
                                    <option value="{{ $almacen->Id_Almacen }}">{{ $almacen->Nombre }}</option>
                                @endforeach
                            </x-adminlte-select>
                        @else
                            {{-- Usuario normal: almacén fijo en hidden --}}
                            <input type="hidden" name="Almacen_entrada" value="{{ auth()->user()->almacen_id }}">
                            <x-adminlte-input name="Almacen_nombre" label="Almacén Entrada" value="{{ auth()->user()->almacen->Nombre }}" disabled />
                        @endif
                    </div>

                    <div class="col-md-4">
                        {{-- <x-adminlte-input name="Fecha_entrada" label="Fecha Entrada" type="date" /> --}}
                        <x-adminlte-input 
                            type="date" 
                            name="Fecha" 
                            label="Fecha Salida"  
                            value="{{ \Carbon\Carbon::now('America/Mexico_City')->format('Y-m-d') }}" 
                            required 
                        />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-select name="Tipo" label="Tipo Entrada" id="tipo" required>
                            <option value="">Seleccione...</option>
                            <option value="Madrina">Madrina</option>
                            <option value="Traspaso">Traspaso</option>
                        </x-adminlte-select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Paso 2: Checklist -->
        <div class="card card-info">
            <div class="card-header" style="background-color: #6b6666;">
                <h3 class="card-title">Paso 2: Check List</h3>
            </div>
            <div class="card-body" id="checklist-container">
                <!-- Se carga dinámicamente -->
            </div>
        </div>

        {{-- //campos ocultos  --}}
        <input type="hidden" name="Estado" id="estado_vehiculo" value="mantenimiento">



        <!-- Paso 3: Confirmación -->
        <div class="card card-success">
            <div class="card-header" style="background-color: #6b6666;">
                <h3 class="card-title">Paso 3: Confirmación</h3>
            </div>
            <div class="card-body">
                <x-adminlte-textarea name="Observaciones" label="Observaciones" />
                {{-- <x-adminlte-button label="Guardar Entrada" theme="success" icon="fas fa-save" type="submit" /> --}}
                <x-adminlte-button id="btn-guardar" label="Guardar Entrada" theme="success" icon="fas fa-save" type="submit" />


                <a href="{{ route('admin.vehiculos') }}" class="btn btn-secondary ml-2">Cancelar</a>
            </div>
        </div>
    </form>
</div>
@endsection


 @section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const botonGuardar = document.getElementById('btn-guardar');
    if (botonGuardar) {
        botonGuardar.disabled = true; // Deshabilitar al cargar
    }

    const tipoSelect = document.getElementById('tipo');
    tipoSelect.addEventListener('change', function () {
        const tipo = this.value;
        const container = document.getElementById('checklist-container');
        container.innerHTML = '';

        if (!tipo) {
            if (botonGuardar) botonGuardar.disabled = true; // Deshabilitar si no hay tipo
            return;
        }

        // Habilitar botón guardar al seleccionar tipo, sin importar checkboxes
        if (botonGuardar) botonGuardar.disabled = false;

        fetch(`{{ url('/checklist') }}/${tipo}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    container.innerHTML = `<p class="text-danger">No hay checklist disponible para este tipo.</p>`;
                    return;
                }

                let html = `
                    <input type="hidden" name="documentos_completos" value="0">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="documentos_completos" value="1" ${data.documentos_completos ? 'checked' : ''}>
                        <label class="form-check-label">Documentos Completos</label>
                    </div>

                    <input type="hidden" name="accesorios_completos" value="0">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="accesorios_completos" value="1" ${data.accesorios_completos ? 'checked' : ''}>
                        <label class="form-check-label">Accesorios Completos</label>
                    </div>

                    <div class="form-group">
                        <label>Estado Exterior</label>
                        <select class="form-control" name="estado_exterior">
                            <option ${data.estado_exterior == 'Excelente' ? 'selected' : ''}>Excelente</option>
                            <option ${data.estado_exterior == 'Bueno' ? 'selected' : ''}>Bueno</option>
                            <option ${data.estado_exterior == 'Regular' ? 'selected' : ''}>Regular</option>
                            <option ${data.estado_exterior == 'Malo' ? 'selected' : ''}>Malo</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Estado Interior</label>
                        <select class="form-control" name="estado_interior">
                            <option ${data.estado_interior == 'Excelente' ? 'selected' : ''}>Excelente</option>
                            <option ${data.estado_interior == 'Bueno' ? 'selected' : ''}>Bueno</option>
                            <option ${data.estado_interior == 'Regular' ? 'selected' : ''}>Regular</option>
                            <option ${data.estado_interior == 'Malo' ? 'selected' : ''}>Malo</option>
                        </select>
                    </div>

                    <input type="hidden" name="pdi_realizada" value="0">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="pdi_realizada" value="1" ${data.pdi_realizada ? 'checked' : ''}>
                        <label class="form-check-label">PDI Realizada</label>
                    </div>

                    <input type="hidden" name="seguro_vigente" value="0">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="seguro_vigente" value="1" ${data.seguro_vigente ? 'checked' : ''}>
                        <label class="form-check-label">Seguro Vigente</label>
                    </div>

                    <input type="hidden" name="nfc_instalado" value="0">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="nfc_instalado" value="1" ${data.nfc_instalado ? 'checked' : ''}>
                        <label class="form-check-label">NFC Instalado</label>
                    </div>

                    <input type="hidden" name="gps_instalado" value="0">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="gps_instalado" value="1" ${data.gps_instalado ? 'checked' : ''}>
                        <label class="form-check-label">GPS Instalado</label>
                    </div>

                    <input type="hidden" name="folder_viajero" value="0">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="folder_viajero" value="1" ${data.folder_viajero ? 'checked' : ''}>
                        <label class="form-check-label">Folder Viajero</label>
                    </div>

                    <div class="form-group">
                        <label>Observaciones</label>
                        <textarea class="form-control" name="observaciones_checklist">${data.observaciones || ''}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Recibido por</label>
                        <input type="text" class="form-control" name="recibido_por" value="${data.recibido_por || ''}">
                    </div>

                    <div class="form-group">
                        <label>Fecha Revisión</label>
                        <input type="date" class="form-control" name="fecha_revision" value="${data.fecha_revision || ''}">
                    </div>
                `;

                container.innerHTML = html;

                document.querySelectorAll('.form-check-input[type="checkbox"]').forEach(cb => {
                    cb.classList.add('checklist-item');
                    cb.addEventListener('change', () => {
                        verificarChecklist();
                        actualizarObservacionesChecklist();
                    });
                });

                verificarChecklist();
                actualizarObservacionesChecklist();
            })
            .catch(error => {
                console.error(error);
                container.innerHTML = `<p class="text-danger">Error al cargar el checklist.</p>`;
            });
    });

    function verificarChecklist() {
        const checkboxes = document.querySelectorAll('.checklist-item');
        // Ya no bloqueamos el botón guardar aquí, solo actualizamos estado
        const todosMarcados = Array.from(checkboxes).every(cb => cb.checked);

        const estadoVehiculoInput = document.getElementById('estado_vehiculo');
        if (estadoVehiculoInput) {
            estadoVehiculoInput.value = todosMarcados ? 'disponible' : 'mantenimiento';
            console.log('Estado asignado:', estadoVehiculoInput.value);
        }
    }

    function actualizarObservacionesChecklist() {
        const observaciones = [];

        const razones = {
            documentos_completos: 'Documentos incompletos',
            accesorios_completos: 'Faltan accesorios',
            pdi_realizada: 'PDI no realizada',
            seguro_vigente: 'Seguro no vigente',
            nfc_instalado: 'NFC no instalado',
            gps_instalado: 'GPS no instalado',
            folder_viajero: 'Falta folder viajero',
        };

        Object.keys(razones).forEach(nombre => {
            // Busca checkbox con valor=1 marcado
            const cb = document.querySelector(`input[name="${nombre}"][value="1"]`);
            if (!cb || !cb.checked) {
                observaciones.push(`- ${razones[nombre]}`);
            }
        });

        const textarea = document.querySelector('textarea[name="observaciones_checklist"]');
        if (textarea) {
            textarea.value = observaciones.join('\n');
        }
    }

    // Antes de enviar formulario, actualiza estado vehículo
    const formulario = document.querySelector('form');
    if (formulario) {
        formulario.addEventListener('submit', function () {
            verificarChecklist();
        });
    }
});
</script>
@endsection
