# Script de instalación para el módulo de importación de horarios

Write-Host "`n" -NoNewline
Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║     INSTALACIÓN: MÓDULO DE IMPORTACIÓN DE HORARIOS          ║" -ForegroundColor Cyan
Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host "`n"

Write-Host "📦 Instalando dependencias..." -ForegroundColor Yellow
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Yellow
Write-Host ""

# Instalar PhpSpreadsheet
Write-Host "  → Instalando PhpOffice/PhpSpreadsheet..." -ForegroundColor Cyan
composer require phpoffice/phpspreadsheet

Write-Host "`n"
Write-Host "✅ Instalación completada!" -ForegroundColor Green
Write-Host "`n"
Write-Host "📋 Próximos pasos:" -ForegroundColor Yellow
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Yellow
Write-Host "  1. Acceder al módulo: /importacion-horarios" -ForegroundColor White
Write-Host "  2. Descargar la plantilla de ejemplo" -ForegroundColor White
Write-Host "  3. Completar los datos en el archivo" -ForegroundColor White
Write-Host "  4. Subir el archivo para importar" -ForegroundColor White
Write-Host "`n"
