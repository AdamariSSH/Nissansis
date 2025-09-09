 @extends('adminlte::page')

@section('title', 'Editar Entrada')

@section('content_header')
    <h1>Editar Entrada de Vehículo</h1>
@stop

@section('content')
<div class="container-fluid">
    <form method="POST" action="{{ route('entradas.update', ['entrada' => $entrada->No_orden]) }}">

        @csrf
        @method('PUT')

        <!-- Paso 1: Datos Generales -->
        <div class="card card-primary">
            <div class="card-header" style="background-color: #6b6666;">
                <h3 class="card-title">Paso 1: Datos Generales</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Vehículo -->
                    <div class="col-md-4">
                        <x-adminlte-input name="VIN" label="VIN" maxlength="17" readonly value="{{ old('VIN', $entrada->VIN) }}" />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input name="Motor" label="Motor" maxlength="17" requiredvalue="{{ old('Motor', $entrada->Motor ?? '') }}" />

                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input name="Caracteristicas" label="Caracteristicas" required value="{{ old('Caracteristicas', $entrada->Caracteristicas) }}" />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input name="Color" label="Color" required value="{{ old('Color', $entrada->Color) }}" />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input name="Modelo" label="Modelo" required value="{{ old('Modelo', $entrada->Modelo) }}" />
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input name="Kilometraje_entrada" label="Kilometraje" type="number" value="{{ old('Kilometraje_entrada', $entrada->Kilometraje_entrada) ?? 0 }}" />
                    </div>

                    <div class="col-md-4">
                        <x-adminlte-select name="Almacen_entrada" label="Almacén Entrada" required>
                            @foreach ($almacenes as $almacen)
                                <option value="{{ $almacen->Id_Almacen }}" {{ old('Almacen_entrada', $entrada->Almacen_entrada) == $almacen->Id_Almacen ? 'selected' : '' }}>
                                    {{ $almacen->Nombre }}
                                </option>
                            @endforeach
                        </x-adminlte-select>
                    </div>
                    <div class="col-md-4">
                        <x-adminlte-input name="Fecha_entrada" label="Fecha Entrada" type="date" value="{{ old('Fecha_entrada', $entrada->Fecha_entrada ? \Carbon\Carbon::parse($entrada->Fecha_entrada)->format('Y-m-d') : '') }}" />

                    </div>
                    <div class="col-md-4">
                        <x-adminlte-select name="Tipo" label="Tipo Entrada" id="tipo" required>
                            <option value="">Seleccione...</option>
                            <option value="Madrina" {{ old('Tipo', $entrada->Tipo) == 'Madrina' ? 'selected' : '' }}>Madrina</option>
                            <option value="Traspaso" {{ old('Tipo', $entrada->Tipo) == 'Traspaso' ? 'selected' : '' }}>Traspaso</option>
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
                <!-- Aquí carga JS el checklist con valores por defecto -->
            </div>
        </div>

        <input type="hidden" name="Estado" id="estado_vehiculo" value="{{ old('Estado', $entrada->Estado) }}">

        <!-- Paso 3: Confirmación -->
        <div class="card card-success">
            <div class="card-header" style="background-color: #6b6666;">
                <h3 class="card-title">Paso 3: Confirmación</h3>
            </div>
            <div class="card-body">
                <x-adminlte-textarea name="Observaciones" label="Observaciones">{{ old('Observaciones', $entrada->Observaciones) }}</x-adminlte-textarea>
                <x-adminlte-button id="btn-guardar" label="Actualizar Entrada" theme="success" icon="fas fa-save" type="submit" />
                <a href="{{ route('admin.vehiculos') }}" class="btn btn-secondary ml-2">Cancelar</a>
            </div>
        </div>
    </form>
</div>
@endsection

@section('js')
<script>
    const checklist = {!! json_encode($checklist ?? null, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!};

    // Variable checklist disponible desde Blade
    // Ya declarada fuera en el template para evitar problemas con el JSON

    document.addEventListener('DOMContentLoaded', function () {
        const botonGuardar = document.getElementById('btn-guardar');
        if (botonGuardar) {
            botonGuardar.disabled = false;
        }

        const tipoSelect = document.getElementById('tipo');
        const checklistContainer = document.getElementById('checklist-container');

        function getValue(a, b) {
            return (a !== undefined && a !== null) ? a : b;
        }

        function escapeHtml(text) {
            if (!text) return '';
            return text
                .toString()
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#39;")
                .replace(/\n/g, "\\n")
                .replace(/\r/g, "");
        }

        function cargarChecklist(tipo) {
            checklistContainer.innerHTML = '';

            if (!tipo) {
                if (botonGuardar) botonGuardar.disabled = true;
                return;
            }

            fetch(`{{ url('/checklist') }}/${tipo}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        checklistContainer.innerHTML = `<p class="text-danger">No hay checklist disponible para este tipo.</p>`;
                        return;
                    }

                    const html = `
                        <input type="hidden" name="documentos_completos" value="0">
                        <div class="form-check mb-2">
                            <input class="form-check-input checklist-item" type="checkbox" name="documentos_completos" value="1" ${ getValue(checklist?.documentos_completos, data.documentos_completos) ? 'checked' : '' }>
                            <label class="form-check-label">Documentos Completos</label>
                        </div>

                        <input type="hidden" name="accesorios_completos" value="0">
                        <div class="form-check mb-2">
                            <input class="form-check-input checklist-item" type="checkbox" name="accesorios_completos" value="1" ${ getValue(checklist?.accesorios_completos, data.accesorios_completos) ? 'checked' : '' }>
                            <label class="form-check-label">Accesorios Completos</label>
                        </div>

                        <div class="form-group">
                            <label>Estado Exterior</label>
                            <select class="form-control" name="estado_exterior">
                                <option ${ getValue(checklist?.estado_exterior, data.estado_exterior) == 'Excelente' ? 'selected' : '' }>Excelente</option>
                                <option ${ getValue(checklist?.estado_exterior, data.estado_exterior) == 'Bueno' ? 'selected' : '' }>Bueno</option>
                                <option ${ getValue(checklist?.estado_exterior, data.estado_exterior) == 'Regular' ? 'selected' : '' }>Regular</option>
                                <option ${ getValue(checklist?.estado_exterior, data.estado_exterior) == 'Malo' ? 'selected' : '' }>Malo</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Estado Interior</label>
                            <select class="form-control" name="estado_interior">
                                <option ${ getValue(checklist?.estado_interior, data.estado_interior) == 'Excelente' ? 'selected' : '' }>Excelente</option>
                                <option ${ getValue(checklist?.estado_interior, data.estado_interior) == 'Bueno' ? 'selected' : '' }>Bueno</option>
                                <option ${ getValue(checklist?.estado_interior, data.estado_interior) == 'Regular' ? 'selected' : '' }>Regular</option>
                                <option ${ getValue(checklist?.estado_interior, data.estado_interior) == 'Malo' ? 'selected' : '' }>Malo</option>
                            </select>
                        </div>

                        <input type="hidden" name="pdi_realizada" value="0">
                        <div class="form-check mb-2">
                            <input class="form-check-input checklist-item" type="checkbox" name="pdi_realizada" value="1" ${ getValue(checklist?.pdi_realizada, data.pdi_realizada) ? 'checked' : '' }>
                            <label class="form-check-label">PDI Realizada</label>
                        </div>

                        <input type="hidden" name="seguro_vigente" value="0">
                        <div class="form-check mb-2">
                            <input class="form-check-input checklist-item" type="checkbox" name="seguro_vigente" value="1" ${ getValue(checklist?.seguro_vigente, data.seguro_vigente) ? 'checked' : '' }>
                            <label class="form-check-label">Seguro Vigente</label>
                        </div>

                        <input type="hidden" name="nfc_instalado" value="0">
                        <div class="form-check mb-2">
                            <input class="form-check-input checklist-item" type="checkbox" name="nfc_instalado" value="1" ${ getValue(checklist?.nfc_instalado, data.nfc_instalado) ? 'checked' : '' }>
                            <label class="form-check-label">NFC Instalado</label>
                        </div>

                        <input type="hidden" name="gps_instalado" value="0">
                        <div class="form-check mb-2">
                            <input class="form-check-input checklist-item" type="checkbox" name="gps_instalado" value="1" ${ getValue(checklist?.gps_instalado, data.gps_instalado) ? 'checked' : '' }>
                            <label class="form-check-label">GPS Instalado</label>
                        </div>

                        <input type="hidden" name="folder_viajero" value="0">
                        <div class="form-check mb-2">
                            <input class="form-check-input checklist-item" type="checkbox" name="folder_viajero" value="1" ${ getValue(checklist?.folder_viajero, data.folder_viajero) ? 'checked' : '' }>
                            <label class="form-check-label">Folder Viajero</label>
                        </div>

                        <div class="form-group">
                            <label>Observaciones</label>
                            <textarea class="form-control" name="observaciones_checklist">${escapeHtml(getValue(checklist?.observaciones_checklist, data.observaciones))}</textarea>
                        </div>

                        <div class="form-group">
                            <label>Recibido por</label>
                            <input type="text" class="form-control" name="recibido_por" value="${escapeHtml(getValue(checklist?.recibido_por, data.recibido_por))}">
                        </div>

                        <div class="form-group">
                            <label>Fecha Revisión</label>
                            <input type="date" class="form-control" name="fecha_revision" value="${escapeHtml(getValue(checklist?.fecha_revision, data.fecha_revision))}">
                        </div>
                    `;

                    checklistContainer.innerHTML = html;

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
                    checklistContainer.innerHTML = `<p class="text-danger">Error al cargar el checklist.</p>`;
                });
        }

        tipoSelect.addEventListener('change', () => {
            cargarChecklist(tipoSelect.value);
        });

        if (tipoSelect.value) {
            cargarChecklist(tipoSelect.value);
        }

        function verificarChecklist() {
            const checkboxes = document.querySelectorAll('.checklist-item');
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

        const formulario = document.querySelector('form');
        if (formulario) {
            formulario.addEventListener('submit', function () {
                verificarChecklist();
            });
        }
    });
</script>

@endsection 


