@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="card shadow-sm mb-4">
                <div class="card-body bg-gradient-primary text-white">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="mb-1">
                                <i class="fas fa-file-upload me-2"></i>
                                Importar Horarios
                            </h3>
                            <p class="mb-0 opacity-75">Carga masiva de horarios desde archivo Excel</p>
                        </div>
                        <div>
                            <a href="{{ route('horarios.plantilla') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-download me-1"></i>
                                Descargar Plantilla
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Error:</strong> {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <!-- Instrucciones -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle text-info me-2"></i>
                        Formato del Archivo
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Columnas Requeridas:</h6>
                            <ol class="list-group list-group-numbered mb-3">
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold">SIGLA</div>
                                        Código de la materia (ej: MAT101)
                                    </div>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold">SEMESTRE</div>
                                        Número del semestre (ej: 1)
                                    </div>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold">GRUPO</div>
                                        Nombre del grupo (ej: F1, A, B)
                                    </div>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold">MATERIA</div>
                                        Nombre completo de la materia
                                    </div>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold">DOCENTE</div>
                                        Nombre completo del docente
                                    </div>
                                </li>
                            </ol>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-success mb-3">Horarios (repetir):</h6>
                            <div class="alert alert-success">
                                <p class="mb-2">Después de las 5 columnas base, repite estas 3:</p>
                                <ul class="mb-0">
                                    <li><strong>DIA:</strong> Lun, Mar, Mie, Jue, Vie</li>
                                    <li><strong>HORA:</strong> Formato HH:MM-HH:MM (ej: 18:15-20:30)</li>
                                    <li><strong>AULA:</strong> Número o nombre del aula</li>
                                </ul>
                            </div>
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-lightbulb me-1"></i>
                                <strong>Ejemplo:</strong><br>
                                <code class="text-dark">MAT101 | 1 | F1 | CALCULO I | PEREZ | Mar | 18:15-20:30 | 14 | Jue | 18:15-20:30 | 14</code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulario de carga -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-upload text-success me-2"></i>
                        Cargar Archivo
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('horarios.import.process') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label for="archivo" class="form-label fw-bold">
                                Seleccionar archivo Excel (.xlsx, .xls, .csv)
                            </label>
                            <input type="file"
                                   class="form-control form-control-lg @error('archivo') is-invalid @enderror"
                                   id="archivo"
                                   name="archivo"
                                   accept=".xlsx,.xls,.csv"
                                   required>
                            @error('archivo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Tamaño máximo: 10 MB
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-exclamation-triangle fa-2x me-3 mt-1"></i>
                                <div>
                                    <h6 class="alert-heading mb-2">Advertencias Importantes:</h6>
                                    <ul class="mb-0">
                                        <li>Los <strong>horarios anteriores del grupo se eliminarán</strong></li>
                                        <li>Se crearán <strong>automáticamente</strong> docentes, materias y aulas que no existan</li>
                                        <li>Asegúrate de que el archivo siga el formato de la plantilla</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('horarios.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-cloud-upload-alt me-2"></i>
                                Importar Horarios
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Características -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-star text-warning me-2"></i>
                        Funcionalidades
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-magic text-primary fa-2x me-3"></i>
                                <div>
                                    <h6 class="mb-1">Auto-Creación</h6>
                                    <small class="text-muted">Crea automáticamente docentes, materias y aulas que no existan</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-sync-alt text-success fa-2x me-3"></i>
                                <div>
                                    <h6 class="mb-1">Actualización</h6>
                                    <small class="text-muted">Actualiza datos existentes automáticamente</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-check-double text-info fa-2x me-3"></i>
                                <div>
                                    <h6 class="mb-1">Validación</h6>
                                    <small class="text-muted">Valida formato de datos antes de importar</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>
@endsection
