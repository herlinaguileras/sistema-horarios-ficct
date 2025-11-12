@props(['action'])

@php
    $config = [
        'CREATE' => ['color' => 'green', 'icon' => 'fa-plus-circle', 'text' => 'CREAR'],
        'UPDATE' => ['color' => 'blue', 'icon' => 'fa-edit', 'text' => 'ACTUALIZAR'],
        'DELETE' => ['color' => 'red', 'icon' => 'fa-trash-alt', 'text' => 'ELIMINAR'],
        'LOGIN' => ['color' => 'purple', 'icon' => 'fa-sign-in-alt', 'text' => 'LOGIN'],
        'LOGOUT' => ['color' => 'orange', 'icon' => 'fa-sign-out-alt', 'text' => 'LOGOUT'],
        'IMPORT' => ['color' => 'yellow', 'icon' => 'fa-file-import', 'text' => 'IMPORTAR'],
        'EXPORT' => ['color' => 'indigo', 'icon' => 'fa-file-export', 'text' => 'EXPORTAR'],
    ];

    // Buscar coincidencia parcial
    $badge = null;
    foreach ($config as $key => $value) {
        if (str_contains($action, $key)) {
            $badge = $value;
            break;
        }
    }

    // Valor por defecto si no hay coincidencia
    if (!$badge) {
        $badge = ['color' => 'gray', 'icon' => 'fa-info-circle', 'text' => $action];
    } else {
        $badge['text'] = $action; // Usar el texto original
    }
@endphp

<span {{ $attributes->merge(['class' => "px-3 py-1 inline-flex text-xs font-semibold rounded-full items-center gap-1 bg-{$badge['color']}-100 text-{$badge['color']}-800"]) }}>
    <i class="fas {{ $badge['icon'] }}"></i>
    {{ $badge['text'] }}
</span>
