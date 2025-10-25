#!/usr/bin/env bash

# ===========================================
# Script de Validación Pre-Deploy
# ===========================================
# Este script verifica que el proyecto esté
# listo para ser deployado a producción
#
# Uso: bash pre-deploy-check.sh
# ===========================================

echo "🚀 Iniciando validación pre-deploy..."
echo ""

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

ERRORS=0
WARNINGS=0

# Función para mostrar error
error() {
    echo -e "${RED}❌ ERROR: $1${NC}"
    ((ERRORS++))
}

# Función para mostrar advertencia
warning() {
    echo -e "${YELLOW}⚠️  WARNING: $1${NC}"
    ((WARNINGS++))
}

# Función para mostrar éxito
success() {
    echo -e "${GREEN}✅ $1${NC}"
}

echo "📋 Verificando archivos requeridos..."
echo ""

# Verificar archivos críticos
if [ ! -f "composer.json" ]; then
    error "composer.json no encontrado"
else
    success "composer.json encontrado"
fi

if [ ! -f "package.json" ]; then
    error "package.json no encontrado"
else
    success "package.json encontrado"
fi

if [ ! -f ".env.production.example" ]; then
    error ".env.production.example no encontrado"
else
    success ".env.production.example encontrado"
fi

if [ ! -f "Procfile" ]; then
    error "Procfile no encontrado (requerido para Railway)"
else
    success "Procfile encontrado"
fi

if [ ! -f "nixpacks.toml" ]; then
    warning "nixpacks.toml no encontrado (opcional pero recomendado)"
else
    success "nixpacks.toml encontrado"
fi

echo ""
echo "🔐 Verificando configuración de seguridad..."
echo ""

# Verificar .env local
if [ -f ".env" ]; then
    # Verificar APP_DEBUG
    if grep -q "APP_DEBUG=true" .env; then
        warning ".env local tiene APP_DEBUG=true (OK en desarrollo)"
    fi

    # Verificar APP_ENV
    if grep -q "APP_ENV=production" .env; then
        error ".env local NO debe usar APP_ENV=production"
    else
        success "APP_ENV configurado correctamente para desarrollo"
    fi

    # Verificar que .env está en .gitignore
    if grep -q "^\.env$" .gitignore; then
        success ".env está en .gitignore"
    else
        error ".env NO está en .gitignore - RIESGO DE SEGURIDAD"
    fi
else
    warning ".env no encontrado (OK si es primera instalación)"
fi

echo ""
echo "🔍 Verificando dependencias..."
echo ""

# Verificar composer.lock
if [ ! -f "composer.lock" ]; then
    error "composer.lock no encontrado - ejecuta 'composer install'"
else
    success "composer.lock encontrado"
fi

# Verificar package-lock.json
if [ ! -f "package-lock.json" ]; then
    warning "package-lock.json no encontrado - ejecuta 'npm install'"
else
    success "package-lock.json encontrado"
fi

# Verificar vendor
if [ ! -d "vendor" ]; then
    error "vendor/ no encontrado - ejecuta 'composer install'"
else
    success "vendor/ encontrado"
fi

# Verificar node_modules
if [ ! -d "node_modules" ]; then
    error "node_modules/ no encontrado - ejecuta 'npm install'"
else
    success "node_modules/ encontrado"
fi

echo ""
echo "📦 Verificando assets compilados..."
echo ""

# Verificar build de Vite
if [ ! -d "public/build" ]; then
    error "public/build/ no encontrado - ejecuta 'npm run build'"
else
    success "public/build/ encontrado"

    if [ ! -f "public/build/manifest.json" ]; then
        error "public/build/manifest.json no encontrado"
    else
        success "manifest.json presente"
    fi
fi

echo ""
echo "🗄️ Verificando estructura de base de datos..."
echo ""

# Verificar migraciones
MIGRATION_COUNT=$(find database/migrations -name "*.php" | wc -l)
if [ $MIGRATION_COUNT -eq 0 ]; then
    error "No se encontraron migraciones"
else
    success "$MIGRATION_COUNT migraciones encontradas"
fi

# Verificar seeders críticos
if [ ! -f "database/seeders/RoleSeeder.php" ]; then
    warning "RoleSeeder.php no encontrado"
else
    success "RoleSeeder.php encontrado"
fi

echo ""
echo "📝 Verificando configuración de Git..."
echo ""

# Verificar que estamos en un repo git
if [ ! -d ".git" ]; then
    error "No es un repositorio Git - ejecuta 'git init'"
else
    success "Repositorio Git inicializado"

    # Verificar cambios sin commit
    if [ -n "$(git status --porcelain)" ]; then
        warning "Hay cambios sin commit"
        echo "   Ejecuta: git add . && git commit -m 'Preparar para deploy'"
    else
        success "No hay cambios pendientes"
    fi

    # Verificar remote
    if git remote | grep -q "origin"; then
        success "Remote 'origin' configurado"
    else
        warning "No hay remote 'origin' configurado"
        echo "   Ejecuta: git remote add origin <URL>"
    fi
fi

echo ""
echo "🔧 Verificando permisos de carpetas..."
echo ""

# Verificar storage
if [ -d "storage" ]; then
    if [ -w "storage" ]; then
        success "storage/ es escribible"
    else
        error "storage/ NO es escribible - ejecuta 'chmod -R 775 storage'"
    fi
else
    error "storage/ no encontrado"
fi

# Verificar bootstrap/cache
if [ -d "bootstrap/cache" ]; then
    if [ -w "bootstrap/cache" ]; then
        success "bootstrap/cache/ es escribible"
    else
        error "bootstrap/cache/ NO es escribible - ejecuta 'chmod -R 775 bootstrap/cache'"
    fi
else
    error "bootstrap/cache/ no encontrado"
fi

echo ""
echo "🧪 Verificando tests..."
echo ""

# Verificar tests
TEST_COUNT=$(find tests -name "*Test.php" | wc -l)
if [ $TEST_COUNT -eq 0 ]; then
    warning "No se encontraron tests (recomendado agregar)"
else
    success "$TEST_COUNT archivos de test encontrados"
fi

echo ""
echo "📄 Verificando documentación..."
echo ""

# Verificar README
if [ ! -f "README.md" ]; then
    warning "README.md no encontrado"
else
    # Verificar que no sea el README por defecto de Laravel
    if grep -q "About Laravel" README.md; then
        warning "README.md parece ser el de Laravel por defecto"
    else
        success "README.md personalizado encontrado"
    fi
fi

# Verificar DEPLOYMENT.md
if [ ! -f "DEPLOYMENT.md" ]; then
    warning "DEPLOYMENT.md no encontrado (recomendado)"
else
    success "DEPLOYMENT.md encontrado"
fi

echo ""
echo "========================================="
echo "📊 RESUMEN DE VALIDACIÓN"
echo "========================================="
echo ""

if [ $ERRORS -eq 0 ] && [ $WARNINGS -eq 0 ]; then
    echo -e "${GREEN}✨ ¡PERFECTO! El proyecto está listo para deploy${NC}"
    echo ""
    echo "Próximos pasos:"
    echo "1. Ejecuta: git push origin main"
    echo "2. Ve a Railway y crea un nuevo proyecto"
    echo "3. Conecta tu repositorio de GitHub"
    echo "4. Sigue la guía en DEPLOYMENT.md"
    exit 0
elif [ $ERRORS -eq 0 ]; then
    echo -e "${YELLOW}⚠️  Hay $WARNINGS advertencias (no críticas)${NC}"
    echo ""
    echo "Puedes continuar con el deploy, pero considera revisar las advertencias."
    exit 0
else
    echo -e "${RED}❌ Hay $ERRORS errores críticos y $WARNINGS advertencias${NC}"
    echo ""
    echo "DEBES corregir los errores antes de deployar."
    echo "Revisa el output arriba para más detalles."
    exit 1
fi
