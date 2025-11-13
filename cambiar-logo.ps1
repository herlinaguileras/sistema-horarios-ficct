# Script para Cambiar Logo
# Uso: .\cambiar-logo.ps1 "ruta/a/tu/imagen.png"

param(
    [Parameter(Mandatory=$false)]
    [string]$ImagenOrigen,

    [Parameter(Mandatory=$false)]
    [string]$NombreDestino = "logo.png"
)

# Colores para output
function Write-Success { Write-Host $args -ForegroundColor Green }
function Write-Info { Write-Host $args -ForegroundColor Cyan }
function Write-Warning { Write-Host $args -ForegroundColor Yellow }
function Write-Error { Write-Host $args -ForegroundColor Red }

# Banner
Write-Host "`n============================================" -ForegroundColor Magenta
Write-Host "    CAMBIO DE LOGO - SISTEMA FICCT" -ForegroundColor Magenta
Write-Host "============================================`n" -ForegroundColor Magenta

# Verificar si se proporciono imagen
if ([string]::IsNullOrEmpty($ImagenOrigen)) {
    Write-Info "No se proporciono imagen. Mostrando ubicacion del logo...`n"

    $directorioLogo = Join-Path $PSScriptRoot "public\images"
    $archivoLogo = Join-Path $directorioLogo "logo.png"

    Write-Info "Ubicacion donde debes colocar tu logo:"
    Write-Host "   $archivoLogo`n" -ForegroundColor White

    Write-Info "Pasos para cambiar el logo manualmente:"
    Write-Host "   1. Prepara tu imagen (PNG, JPG, SVG)" -ForegroundColor White
    Write-Host "   2. Renombrala a: logo.png" -ForegroundColor White
    Write-Host "   3. Copiala a: $directorioLogo" -ForegroundColor White
    Write-Host "   4. Ejecuta: php artisan view:clear`n" -ForegroundColor White

    Write-Info "Uso con parametro:"
    Write-Host "   .\cambiar-logo.ps1 `"C:\ruta\a\tu\imagen.png`"`n" -ForegroundColor Yellow

    # Abrir carpeta de destino
    $respuesta = Read-Host "Deseas abrir la carpeta de destino? (S/N)"
    if ($respuesta -eq "S" -or $respuesta -eq "s") {
        explorer $directorioLogo
        Write-Success "Carpeta abierta en el explorador"
    }

    exit
}

# Verificar que el archivo origen existe
if (-not (Test-Path $ImagenOrigen)) {
    Write-Error "Error: No se encontro el archivo: $ImagenOrigen"
    exit 1
}

# Obtener informacion del archivo
$archivo = Get-Item $ImagenOrigen
$extension = $archivo.Extension.ToLower()
$extensionesValidas = @('.png', '.jpg', '.jpeg', '.svg', '.webp', '.gif')

# Validar extension
if ($extension -notin $extensionesValidas) {
    Write-Warning "Advertencia: Extension '$extension' podria no ser compatible"
    Write-Info "   Extensiones recomendadas: .png, .jpg, .svg"
    $continuar = Read-Host "Deseas continuar de todos modos? (S/N)"
    if ($continuar -ne "S" -and $continuar -ne "s") {
        Write-Info "Operacion cancelada"
        exit 0
    }
}

# Verificar tamano
$tamanoMB = [math]::Round($archivo.Length / 1MB, 2)
if ($tamanoMB -gt 1) {
    Write-Warning "El archivo es grande ($tamanoMB MB). Se recomienda menor a 1MB"
}

# Directorio de destino
$directorioDestino = Join-Path $PSScriptRoot "public\images"

# Crear directorio si no existe
if (-not (Test-Path $directorioDestino)) {
    Write-Info "Creando directorio: $directorioDestino"
    New-Item -ItemType Directory -Path $directorioDestino -Force | Out-Null
}

# Determinar nombre final
if ($NombreDestino -notmatch '\.[a-z]+$') {
    $NombreDestino += $extension
}

$archivoDestino = Join-Path $directorioDestino $NombreDestino

# Backup del logo anterior si existe
if (Test-Path $archivoDestino) {
    $timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
    $backupPath = Join-Path $directorioDestino "logo_backup_$timestamp$extension"
    Write-Info "Respaldando logo anterior: $backupPath"
    Copy-Item $archivoDestino $backupPath
}

# Copiar el nuevo logo
Write-Info "Copiando nueva imagen..."
try {
    Copy-Item $ImagenOrigen $archivoDestino -Force
    Write-Success "`nLogo copiado exitosamente!"
    Write-Info "   Origen: $ImagenOrigen"
    Write-Info "   Destino: $archivoDestino"
} catch {
    Write-Error "Error al copiar el archivo: $_"
    exit 1
}

# Actualizar application-logo.blade.php si el nombre es diferente
if ($NombreDestino -ne "logo.png") {
    $componentPath = Join-Path $PSScriptRoot "resources\views\components\application-logo.blade.php"
    if (Test-Path $componentPath) {
        Write-Info "`nActualizando componente de logo..."
        $content = Get-Content $componentPath -Raw
        $newContent = $content -replace "images/logo\.(png|jpg|jpeg|svg|webp)", "images/$NombreDestino"
        Set-Content $componentPath $newContent
        Write-Success "Componente actualizado"
    }
}

# Limpiar cache de Laravel
Write-Info "`nLimpiando cache de vistas..."
try {
    $phpPath = Get-Command php -ErrorAction SilentlyContinue
    if ($phpPath) {
        & php artisan view:clear 2>$null
        Write-Success "Cache limpiado"
    } else {
        Write-Warning "PHP no encontrado en PATH. Ejecuta manualmente:"
        Write-Host "   php artisan view:clear" -ForegroundColor Yellow
    }
} catch {
    Write-Warning "No se pudo limpiar el cache automaticamente"
    Write-Host "   Ejecuta: php artisan view:clear" -ForegroundColor Yellow
}

# Resumen final
Write-Host "`n============================================" -ForegroundColor Green
Write-Host "          PROCESO COMPLETADO" -ForegroundColor Green
Write-Host "============================================`n" -ForegroundColor Green

Write-Info "Informacion del logo:"
Write-Host "   Archivo: $NombreDestino" -ForegroundColor White
Write-Host "   Tamanio: $tamanoMB MB" -ForegroundColor White
Write-Host "   Formato: $extension" -ForegroundColor White

Write-Info "`nProximos pasos:"
Write-Host "   1. Abre tu aplicacion en el navegador" -ForegroundColor White
Write-Host "   2. Presiona Ctrl+F5 para forzar recarga" -ForegroundColor White
Write-Host "   3. Verifica que el logo se muestra correctamente`n" -ForegroundColor White

Write-Info "Tip: Si el logo se ve muy grande o pequeno, ajusta el tamano en:"
Write-Host "   resources/views/layouts/navigation.blade.php (linea ~7)`n" -ForegroundColor Yellow

# Opcion para abrir navegador
$respuesta = Read-Host "Deseas abrir la aplicacion en el navegador? (S/N)"
if ($respuesta -eq "S" -or $respuesta -eq "s") {
    Start-Process "http://localhost/materia"
    Write-Success "Navegador abierto"
}

Write-Host "`nDisfruta tu nuevo logo!`n" -ForegroundColor Magenta
