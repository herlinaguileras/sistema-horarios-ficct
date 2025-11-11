<?php

/**
 * Generar Excel de prueba con choques de horarios
 * Este script crea un archivo Excel con casos de prueba para validar
 * las validaciones de choque de horarios, aulas y docentes
 */

require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

echo "═══════════════════════════════════════════════════════════\n";
echo "  GENERADOR DE EXCEL DE PRUEBA - CHOQUES DE HORARIOS\n";
echo "═══════════════════════════════════════════════════════════\n\n";

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Encabezados
$headers = ['SIGLA', 'SEMESTRE', 'GRUPO', 'MATERIA', 'DOCENTE',
            'DIA', 'HORA', 'AULA', 'DIA', 'HORA', 'AULA',
            'DIA', 'HORA', 'AULA', 'DIA', 'HORA', 'AULA'];
$sheet->fromArray($headers, null, 'A1');

// Estilo para encabezado
$sheet->getStyle('A1:Q1')->getFont()->setBold(true);
$sheet->getStyle('A1:Q1')->getFill()
    ->setFillType(Fill::FILL_SOLID)
    ->getStartColor()->setARGB('FF4CAF50');

// Casos de prueba
$casos = [];

echo "📝 Creando casos de prueba:\n\n";

// ========================================
// CASO 1: Horarios SIN CONFLICTOS (debe pasar)
// ========================================
echo "1️⃣  CASO VÁLIDO - Sin conflictos\n";
echo "   ✓ MAT101 - Grupo A - Lunes 08:00-10:00 Aula 101\n";
echo "   ✓ MAT101 - Grupo A - Miércoles 08:00-10:00 Aula 101\n\n";

$casos[] = [
    'MAT101', '1', 'A', 'CALCULO I', 'PEREZ GOMEZ JUAN',
    'Lun', '08:00-10:00', '101',
    'Mie', '08:00-10:00', '101',
    '', '', '', '', '', ''
];

// ========================================
// CASO 2: CHOQUE DE AULA (mismo día, misma hora, misma aula)
// ========================================
echo "2️⃣  CHOQUE DE AULA - No debe pasar\n";
echo "   ❌ MAT102 - Grupo B - Lunes 08:00-10:00 Aula 101 (CONFLICTO con caso 1)\n\n";

$casos[] = [
    'MAT102', '1', 'B', 'ALGEBRA LINEAL', 'RODRIGUEZ LOPEZ MARIA',
    'Lun', '08:00-10:00', '101', // MISMO DÍA, MISMA HORA, MISMA AULA que MAT101-A
    '', '', '', '', '', ''
];

// ========================================
// CASO 3: CHOQUE DE DOCENTE (mismo docente, dos clases al mismo tiempo)
// ========================================
echo "3️⃣  CHOQUE DE DOCENTE - No debe pasar\n";
echo "   ❌ FIS100 - Grupo C - Lunes 08:00-10:00 Aula 201 (Mismo docente que caso 1)\n\n";

$casos[] = [
    'FIS100', '1', 'C', 'FISICA I', 'PEREZ GOMEZ JUAN', // MISMO DOCENTE que MAT101-A
    'Lun', '08:00-10:00', '201', // MISMO DÍA Y HORA pero DIFERENTE AULA
    '', '', '', '', '', ''
];

// ========================================
// CASO 4: CHOQUE INTERNO (mismo grupo, dos horarios simultáneos en el Excel)
// ========================================
echo "4️⃣  CHOQUE INTERNO - No debe pasar\n";
echo "   ❌ QUI150 - Grupo D - Martes 14:00-16:00 Aula 301 Y Martes 14:00-16:00 Aula 302\n";
echo "   (Mismo grupo en dos lugares a la vez)\n\n";

$casos[] = [
    'QUI150', '2', 'D', 'QUIMICA GENERAL', 'SANTOS MARTINEZ ANA',
    'Mar', '14:00-16:00', '301',
    'Mar', '14:00-16:00', '302', // MISMO DÍA Y HORA, DIFERENTES AULAS
    '', '', '', '', '', ''
];

// ========================================
// CASO 5: SUPERPOSICIÓN PARCIAL DE HORARIOS
// ========================================
echo "5️⃣  SUPERPOSICIÓN PARCIAL - No debe pasar\n";
echo "   ✓ PRO100 - Grupo E - Miércoles 10:00-12:00 Aula 401\n";
echo "   ❌ PRO101 - Grupo F - Miércoles 11:00-13:00 Aula 401 (Se superpone 1 hora)\n\n";

$casos[] = [
    'PRO100', '3', 'E', 'PROGRAMACION I', 'GARCIA FLORES LUIS',
    'Mie', '10:00-12:00', '401',
    '', '', '', '', '', ''
];

$casos[] = [
    'PRO101', '3', 'F', 'PROGRAMACION II', 'MENDEZ ROJAS CARLOS',
    'Mie', '11:00-13:00', '401', // SE SUPERPONE con PRO100 de 11:00 a 12:00
    '', '', '', '', '', ''
];

// ========================================
// CASO 6: HORARIOS VÁLIDOS CON MÚLTIPLES SESIONES
// ========================================
echo "6️⃣  CASO VÁLIDO - Múltiples sesiones sin conflicto\n";
echo "   ✓ EST200 - Grupo G - Lunes 18:00-20:00, Jueves 18:00-20:00, Viernes 16:00-18:00\n\n";

$casos[] = [
    'EST200', '4', 'G', 'ESTADISTICA', 'TORRES VEGA SOFIA',
    'Lun', '18:00-20:00', '501',
    'Jue', '18:00-20:00', '501',
    'Vie', '16:00-18:00', '502',
    '', '', ''
];

// ========================================
// CASO 7: CHOQUE DE GRUPO (grupo ya tiene horario en ese momento)
// ========================================
echo "7️⃣  CASO VÁLIDO CON ACTUALIZACIÓN\n";
echo "   ✓ EST200 - Grupo G - Nueva distribución (reemplazará la anterior)\n";
echo "   Martes 14:00-16:00, Jueves 14:00-16:00\n\n";

$casos[] = [
    'EST200', '4', 'G', 'ESTADISTICA', 'TORRES VEGA SOFIA',
    'Mar', '14:00-16:00', '503',
    'Jue', '14:00-16:00', '503',
    '', '', '', '', '', ''
];

// ========================================
// CASO 8: DIFERENTES AULAS, MISMO HORARIO (debe pasar)
// ========================================
echo "8️⃣  CASO VÁLIDO - Diferentes aulas\n";
echo "   ✓ ING100 - Grupo H - Viernes 08:00-10:00 Aula 601\n";
echo "   ✓ ING101 - Grupo I - Viernes 08:00-10:00 Aula 602 (Diferente aula)\n\n";

$casos[] = [
    'ING100', '5', 'H', 'INGLES I', 'RAMIREZ CRUZ PATRICIA',
    'Vie', '08:00-10:00', '601',
    '', '', '', '', '', ''
];

$casos[] = [
    'ING101', '5', 'I', 'INGLES II', 'CASTRO DIAZ ROBERTO',
    'Vie', '08:00-10:00', '602', // DIFERENTE AULA, debe pasar
    '', '', '', '', '', ''
];

// Escribir casos al Excel
$sheet->fromArray($casos, null, 'A2');

// Auto-ajustar columnas
foreach (range('A', 'Q') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Colorear filas con problemas
$sheet->getStyle('A3:Q3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFCCCC'); // Caso 2 - Rojo claro
$sheet->getStyle('A4:Q4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFCCCC'); // Caso 3 - Rojo claro
$sheet->getStyle('A5:Q5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFCCCC'); // Caso 4 - Rojo claro
$sheet->getStyle('A7:Q7')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFCCCC'); // Caso 6 - Rojo claro

$sheet->getStyle('A2:Q2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFC8E6C9'); // Caso 1 - Verde claro
$sheet->getStyle('A6:Q6')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFC8E6C9'); // Caso 5 - Verde claro
$sheet->getStyle('A8:Q8')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFC8E6C9'); // Caso 7 - Verde claro
$sheet->getStyle('A9:Q9')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFC8E6C9'); // Caso 8 - Verde claro
$sheet->getStyle('A10:Q11')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFC8E6C9'); // Casos 9-10 - Verde claro

// Guardar archivo
$filename = 'storage/app/excel_prueba_choques_horarios.xlsx';
$writer = new Xlsx($spreadsheet);
$writer->save($filename);

echo "═══════════════════════════════════════════════════════════\n";
echo "  ✅ ARCHIVO GENERADO EXITOSAMENTE\n";
echo "═══════════════════════════════════════════════════════════\n\n";
echo "📁 Ubicación: {$filename}\n";
echo "📊 Total de casos: " . count($casos) . "\n\n";

echo "📋 RESUMEN DE CASOS:\n";
echo "   ✓ Casos válidos (deben pasar): 5\n";
echo "   ❌ Casos con conflictos (deben fallar): 4\n\n";

echo "💡 PRÓXIMOS PASOS:\n";
echo "   1. Ve a: http://127.0.0.1:8000/horarios/import\n";
echo "   2. Sube el archivo: {$filename}\n";
echo "   3. Verifica que se detecten los 4 conflictos\n";
echo "   4. Revisa el reporte de importación\n\n";

echo "🔍 CONFLICTOS ESPERADOS:\n";
echo "   • Línea 3: Choque de aula (Aula 101 ocupada)\n";
echo "   • Línea 4: Choque de docente (PEREZ GOMEZ JUAN ocupado)\n";
echo "   • Línea 5: Choque interno (Grupo D en dos lugares)\n";
echo "   • Línea 7: Superposición parcial (Aula 401, 11:00-12:00)\n\n";

echo "═══════════════════════════════════════════════════════════\n";
