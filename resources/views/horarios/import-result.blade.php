@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header de resultado -->
            <div class="card shadow-sm mb-4">
                <div class="card-body {{ $estadisticas['errores'] > 0 ? 'bg-warning' : 'bg-success' }} text-white">
                    <div class="text-center">
                        <i class="fas {{ $estadisticas['errores'] > 0 ? 'fa-exclamation-triangle' : 'fa-check-circle' }} fa-3x mb-3"></i>
                        <h3 class="mb-2">
                            {{ $estadisticas['errores'] > 0 ? 'Importación Completada con Advertencias' : 'Importación Exitosa' }}
                        </h3>
                        <p class="mb-0">
                            {{ $estadisticas['exitosas'] }} de {{ $estadisticas['total'] }} filas procesadas correctamente
                        </p>
                    </div>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center">
                            <i class="fas fa-check-circle text-success fa-3x mb-2"></i>
                            <h3 class="mb-0">{{ $estadisticas['exitosas'] }}</h3>
                            <small class="text-muted">Filas Exitosas</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center">
                            <i class="fas fa-times-circle text-danger fa-3x mb-2"></i>
                            <h3 class="mb-0">{{ $estadisticas['errores'] }}</h3>
                            <small class="text-muted">Con Errores</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center">
                            <i class="fas fa-clock text-primary fa-3x mb-2"></i>
                            <h3 class="mb-0">{{ $estadisticas['horarios_creados'] }}</h3>
                            <small class="text-muted">Horarios Creados</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center">
                            <i class="fas fa-users text-info fa-3x mb-2"></i>
                            <h3 class="mb-0">{{ $estadisticas['grupos_creados'] }}</h3>
                            <small class="text-muted">Grupos Creados</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resumen de creaciones -->
            @if($estadisticas['docentes_creados'] > 0 || $estadisticas['materias_creadas'] > 0 || $estadisticas['aulas_creadas'] > 0)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-plus-circle text-success me-2"></i>
                        Registros Creados Automáticamente
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        @if($estadisticas['docentes_creados'] > 0)
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <i class="fas fa-chalkboard-teacher text-primary fa-2x mb-2"></i>
                                <h4 class="mb-0">{{ $estadisticas['docentes_creados'] }}</h4>
                                <small class="text-muted">Docentes Nuevos</small>
                            </div>
                        </div>
                        @endif
                        @if($estadisticas['materias_creadas'] > 0)
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <i class="fas fa-book text-success fa-2x mb-2"></i>
                                <h4 class="mb-0">{{ $estadisticas['materias_creadas'] }}</h4>
                                <small class="text-muted">Materias Nuevas</small>
                            </div>
                        </div>
                        @endif
                        @if($estadisticas['aulas_creadas'] > 0)
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <i class="fas fa-door-open text-info fa-2x mb-2"></i>
                                <h4 class="mb-0">{{ $estadisticas['aulas_creadas'] }}</h4>
                                <small class="text-muted">Aulas Nuevas</small>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Detalle de procesamiento -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-list-alt me-2"></i>
                        Detalle del Procesamiento
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-hover mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th width="80">Línea</th>
                                    <th width="80">Estado</th>
                                    <th>Mensaje</th>
                                    <th width="100">Detalles</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($estadisticas['detalles'] as $detalle)
                                <tr class="{{ $detalle['exito'] ? '' : 'table-danger' }}">
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $detalle['linea'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($detalle['exito'])
                                            <i class="fas fa-check-circle text-success fa-lg"></i>
                                        @else
                                            <i class="fas fa-times-circle text-danger fa-lg"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $detalle['mensaje'] }}</strong>
                                        
                                        @if(!empty($detalle['errores_validacion']))
                                            <div class="mt-2 alert alert-danger py-2 mb-2">
                                                <strong><i class="fas fa-exclamation-triangle me-1"></i> Errores de Validación:</strong>
                                                @foreach($detalle['errores_validacion'] as $error)
                                                <div class="small mt-1">
                                                    {{ $error }}
                                                </div>
                                                @endforeach
                                            </div>
                                        @endif
                                        
                                        @if(!empty($detalle['advertencias']))
                                            <div class="mt-2">
                                                @foreach($detalle['advertencias'] as $advertencia)
                                                <div class="text-muted small">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    {{ $advertencia }}
                                                </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(!empty($detalle['errores_validacion']))
                                        <span class="badge bg-danger">
                                            {{ count($detalle['errores_validacion']) }} conflicto(s)
                                        </span>
                                        @endif
                                        @if(!empty($detalle['advertencias']))
                                        <span class="badge bg-info">
                                            {{ count($detalle['advertencias']) }} advertencia(s)
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="d-flex gap-2 justify-content-center mt-4">
                <a href="{{ route('horarios.import') }}" class="btn btn-primary">
                    <i class="fas fa-file-upload me-2"></i>
                    Importar Otro Archivo
                </a>
                <a href="{{ route('horarios.index') }}" class="btn btn-secondary">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Ver Horarios
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.sticky-top {
    position: sticky;
    top: 0;
    z-index: 10;
}
</style>
@endsection
