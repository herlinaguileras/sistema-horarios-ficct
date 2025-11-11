<?php

/**
 * Test de la función extraerNivelSemestre
 */

// Simulación de la función
function extraerNivelSemestre($semestreTexto)
{
    $texto = strtolower(trim($semestreTexto));

    if (is_numeric($texto)) {
        return (int) $texto;
    }

    $niveles = [
        'primer' => 1, 'primero' => 1, '1er' => 1, '1°' => 1,
        'segundo' => 2, '2do' => 2, '2°' => 2,
        'tercer' => 3, 'tercero' => 3, '3er' => 3, '3°' => 3,
        'cuarto' => 4, '4to' => 4, '4°' => 4,
        'quinto' => 5, '5to' => 5, '5°' => 5,
        'sexto' => 6, '6to' => 6, '6°' => 6,
        'septimo' => 7, 'séptimo' => 7, '7mo' => 7, '7°' => 7,
        'octavo' => 8, '8vo' => 8, '8°' => 8,
        'noveno' => 9, '9no' => 9, '9°' => 9,
        'decimo' => 10, 'décimo' => 10, '10mo' => 10, '10°' => 10
    ];

    foreach ($niveles as $palabra => $numero) {
        if (strpos($texto, $palabra) !== false) {
            return $numero;
        }
    }

    return 1;
}

echo "============================================\n";
echo "  TEST: Extracción de Nivel Semestre\n";
echo "============================================\n\n";

$tests = [
    '1' => 1,
    '2' => 2,
    '3' => 3,
    'Primer Semestre' => 1,
    'Segundo Semestre' => 2,
    'Tercer Semestre' => 3,
    'Cuarto Semestre' => 4,
    '5to Semestre' => 5,
    'Sexto' => 6,
    '  7  ' => 7,
    'texto raro' => 1 // default
];

$pasados = 0;
$fallidos = 0;

foreach ($tests as $input => $esperado) {
    $resultado = extraerNivelSemestre($input);
    $status = $resultado === $esperado ? '✅' : '❌';

    echo "{$status} '{$input}' => {$resultado}";

    if ($resultado === $esperado) {
        echo " (esperado: {$esperado}) ✓\n";
        $pasados++;
    } else {
        echo " (esperado: {$esperado}, obtenido: {$resultado}) ✗\n";
        $fallidos++;
    }
}

echo "\n";
echo "══════════════════════════════════════════\n";
echo "Pasados: {$pasados}/{" . count($tests) . "}\n";
echo "Fallidos: {$fallidos}\n";
echo "══════════════════════════════════════════\n\n";

if ($fallidos === 0) {
    echo "╔══════════════════════════════════════════╗\n";
    echo "║  ✅ TODOS LOS TESTS PASARON             ║\n";
    echo "╚══════════════════════════════════════════╝\n";
} else {
    echo "╔══════════════════════════════════════════╗\n";
    echo "║  ❌ ALGUNOS TESTS FALLARON              ║\n";
    echo "╚══════════════════════════════════════════╝\n";
}
