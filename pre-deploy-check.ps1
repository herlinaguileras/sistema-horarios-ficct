# ===========================================
# Script de Validación Pre-Deploy (Windows)
# ===========================================
# Este script verifica que el proyecto esté
# listo para ser deployado a producción
#
# Uso: .\pre-deploy-check.ps1
# ===========================================

Write-Host "🚀 Iniciando validación pre-deploy..." -ForegroundColor Cyan
Write-Host ""

$errors = 0
$warnings = 0

function Show-Error {
    param([string]$message)
    Write-Host "❌ ERROR: $message" -ForegroundColor Red
    $script:errors++
}

function Show-Warning {
    param([string]$message)
    Write-Host "⚠️  WARNING: $message" -ForegroundColor Yellow
    $script:warnings++
}

function Show-Success {
    param([string]$message)
    Write-Host "✅ $message" -ForegroundColor Green
}

# Verificar archivos críticos
Write-Host "📋 Verificando archivos requeridos..." -ForegroundColor Cyan
Write-Host ""

if (!(Test-Path "composer.json")) {
    Show-Error "composer.json no encontrado"
} else {
    Show-Success "composer.json encontrado"
}

if (!(Test-Path "package.json")) {
    Show-Error "package.json no encontrado"
} else {
    Show-Success "package.json encontrado"
}

if (!(Test-Path ".env.production.example")) {
    Show-Error ".env.production.example no encontrado"
} else {
    Show-Success ".env.production.example encontrado"
}

if (!(Test-Path "Procfile")) {
    Show-Error "Procfile no encontrado (requerido para Railway)"
} else {
    Show-Success "Procfile encontrado"
}

if (!(Test-Path "nixpacks.toml")) {
    Show-Warning "nixpacks.toml no encontrado (opcional pero recomendado)"
} else {
    Show-Success "nixpacks.toml encontrado"
}

# Verificar configuración de seguridad
Write-Host ""
Write-Host "🔐 Verificando configuración de seguridad..." -ForegroundColor Cyan
Write-Host ""

if (Test-Path ".env") {
    $envContent = Get-Content ".env" -Raw

    if ($envContent -match "APP_DEBUG=true") {
        Show-Warning ".env local tiene APP_DEBUG=true (OK en desarrollo)"
    }

    if ($envContent -match "APP_ENV=production") {
        Show-Error ".env local NO debe usar APP_ENV=production"
    } else {
        Show-Success "APP_ENV configurado correctamente para desarrollo"
    }

    $gitignoreContent = Get-Content ".gitignore" -Raw
    if ($gitignoreContent -match "^\.env$") {
        Show-Success ".env está en .gitignore"
    } else {
        Show-Error ".env NO está en .gitignore - RIESGO DE SEGURIDAD"
    }
} else {
    Show-Warning ".env no encontrado (OK si es primera instalación)"
}

# Verificar dependencias
Write-Host ""
Write-Host "🔍 Verificando dependencias..." -ForegroundColor Cyan
Write-Host ""

if (!(Test-Path "composer.lock")) {
    Show-Error "composer.lock no encontrado - ejecuta 'composer install'"
} else {
    Show-Success "composer.lock encontrado"
}

if (!(Test-Path "package-lock.json")) {
    Show-Warning "package-lock.json no encontrado - ejecuta 'npm install'"
} else {
    Show-Success "package-lock.json encontrado"
}

if (!(Test-Path "vendor")) {
    Show-Error "vendor/ no encontrado - ejecuta 'composer install'"
} else {
    Show-Success "vendor/ encontrado"
}

if (!(Test-Path "node_modules")) {
    Show-Error "node_modules/ no encontrado - ejecuta 'npm install'"
} else {
    Show-Success "node_modules/ encontrado"
}

# Verificar assets compilados
Write-Host ""
Write-Host "📦 Verificando assets compilados..." -ForegroundColor Cyan
Write-Host ""

if (!(Test-Path "public\build")) {
    Show-Error "public\build\ no encontrado - ejecuta 'npm run build'"
} else {
    Show-Success "public\build\ encontrado"

    if (!(Test-Path "public\build\manifest.json")) {
        Show-Error "public\build\manifest.json no encontrado"
    } else {
        Show-Success "manifest.json presente"
    }
}

# Verificar estructura de base de datos
Write-Host ""
Write-Host "🗄️ Verificando estructura de base de datos..." -ForegroundColor Cyan
Write-Host ""

$migrations = Get-ChildItem -Path "database\migrations" -Filter "*.php" -ErrorAction SilentlyContinue
if ($migrations.Count -eq 0) {
    Show-Error "No se encontraron migraciones"
} else {
    Show-Success "$($migrations.Count) migraciones encontradas"
}

if (!(Test-Path "database\seeders\RoleSeeder.php")) {
    Show-Warning "RoleSeeder.php no encontrado"
} else {
    Show-Success "RoleSeeder.php encontrado"
}

# Verificar Git
Write-Host ""
Write-Host "📝 Verificando configuración de Git..." -ForegroundColor Cyan
Write-Host ""

if (!(Test-Path ".git")) {
    Show-Error "No es un repositorio Git - ejecuta 'git init'"
} else {
    Show-Success "Repositorio Git inicializado"

    $gitStatus = git status --porcelain
    if ($gitStatus) {
        Show-Warning "Hay cambios sin commit"
        Write-Host "   Ejecuta: git add . && git commit -m 'Preparar para deploy'" -ForegroundColor Gray
    } else {
        Show-Success "No hay cambios pendientes"
    }

    $gitRemote = git remote
    if ($gitRemote -contains "origin") {
        Show-Success "Remote 'origin' configurado"
    } else {
        Show-Warning "No hay remote 'origin' configurado"
        Write-Host "   Ejecuta: git remote add origin <URL>" -ForegroundColor Gray
    }
}

# Verificar permisos de carpetas
Write-Host ""
Write-Host "🔧 Verificando carpetas..." -ForegroundColor Cyan
Write-Host ""

if (Test-Path "storage") {
    Show-Success "storage/ existe"
} else {
    Show-Error "storage/ no encontrado"
}

if (Test-Path "bootstrap\cache") {
    Show-Success "bootstrap\cache\ existe"
} else {
    Show-Error "bootstrap\cache\ no encontrado"
}

# Verificar tests
Write-Host ""
Write-Host "🧪 Verificando tests..." -ForegroundColor Cyan
Write-Host ""

$tests = Get-ChildItem -Path "tests" -Filter "*Test.php" -Recurse -ErrorAction SilentlyContinue
if ($tests.Count -eq 0) {
    Show-Warning "No se encontraron tests (recomendado agregar)"
} else {
    Show-Success "$($tests.Count) archivos de test encontrados"
}

# Verificar documentación
Write-Host ""
Write-Host "📄 Verificando documentación..." -ForegroundColor Cyan
Write-Host ""

if (!(Test-Path "README.md")) {
    Show-Warning "README.md no encontrado"
} else {
    $readmeContent = Get-Content "README.md" -Raw
    if ($readmeContent -match "About Laravel") {
        Show-Warning "README.md parece ser el de Laravel por defecto"
    } else {
        Show-Success "README.md personalizado encontrado"
    }
}

if (!(Test-Path "DEPLOYMENT.md")) {
    Show-Warning "DEPLOYMENT.md no encontrado (recomendado)"
} else {
    Show-Success "DEPLOYMENT.md encontrado"
}

# Resumen
Write-Host ""
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "📊 RESUMEN DE VALIDACIÓN" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host ""

if ($errors -eq 0 -and $warnings -eq 0) {
    Write-Host "✨ ¡PERFECTO! El proyecto está listo para deploy" -ForegroundColor Green
    Write-Host ""
    Write-Host "Próximos pasos:"
    Write-Host "1. Ejecuta: git push origin main"
    Write-Host "2. Ve a Railway y crea un nuevo proyecto"
    Write-Host "3. Conecta tu repositorio de GitHub"
    Write-Host "4. Sigue la guía en DEPLOYMENT.md"
    exit 0
} elseif ($errors -eq 0) {
    Write-Host "⚠️  Hay $warnings advertencias (no críticas)" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "Puedes continuar con el deploy, pero considera revisar las advertencias."
    exit 0
} else {
    Write-Host "❌ Hay $errors errores críticos y $warnings advertencias" -ForegroundColor Red
    Write-Host ""
    Write-Host "DEBES corregir los errores antes de deployar."
    Write-Host "Revisa el output arriba para más detalles."
    exit 1
}
