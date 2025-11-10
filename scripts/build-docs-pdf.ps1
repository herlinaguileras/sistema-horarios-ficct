<#
.SYNOPSIS
  Genera un PDF con la documentación del proyecto usando PlantUML, Pandoc y wkhtmltopdf.

.DESCRIPTION
  Este script realizará los pasos necesarios para:
    - Renderizar los archivos .puml en docs/diagrams a PNG (usa Docker con plantuml image por defecto)
    - Crear una copia de los archivos Markdown en docs/build/md reemplazando .puml por .png
    - Generar un HTML con Pandoc y convertirlo a PDF usando wkhtmltopdf (o fallback a xelatex si no existe)

  Ejecutar desde la raíz del repositorio en PowerShell (Windows).

.NOTES
  Requisitos: Docker (opcional si tiene plantuml.jar), Pandoc, wkhtmltopdf (recomendado) o TeX (xelatex).
#>

Set-StrictMode -Version Latest

function Test-CommandExists([string]$name){
    return $null -ne (Get-Command $name -ErrorAction SilentlyContinue)
}

Write-Host "== Generador de documentación (PDF) =="

if (-not (Test-CommandExists docker)) {
    Write-Host "Aviso: 'docker' no está disponible en PATH. Se intentará usar plantuml.jar si está presente." -ForegroundColor Yellow
}

if (-not (Test-CommandExists pandoc)) {
    Write-Host "Error: pandoc no está instalado o no está en PATH. Instala Pandoc antes de continuar." -ForegroundColor Red
    exit 1
}

$haveWk = Test-CommandExists wkhtmltopdf
if (-not $haveWk) { Write-Host "Aviso: 'wkhtmltopdf' no encontrado. Se usará xelatex si está disponible." -ForegroundColor Yellow }

# 1) Render PlantUML -> PNG
Write-Host "1) Renderizando PlantUML a PNG..."
try {
    if (Test-CommandExists docker) {
        docker run --rm -v "${PWD}:/workspace" -w /workspace plantuml/plantuml:latest -tpng docs/diagrams
        if ($LASTEXITCODE -ne 0) { throw "PlantUML (docker) falló con código $LASTEXITCODE" }
    } else {
        # Intenta usar plantuml.jar si existe en ruta actual
        if (Test-Path "plantuml.jar") {
            java -jar plantuml.jar -tpng docs/diagrams
            if ($LASTEXITCODE -ne 0) { throw "PlantUML (jar) falló con código $LASTEXITCODE" }
        } else {
            Write-Host "No se puede renderizar .puml: ni Docker ni plantuml.jar disponibles." -ForegroundColor Red
            throw "No PlantUML renderer"
        }
    }
    Write-Host "PlantUML renderizado correctamente." -ForegroundColor Green
} catch {
    Write-Host "Error al renderizar PlantUML: $_" -ForegroundColor Red
    exit 1
}

# 2) Preparar carpeta docs/build/md y copiar MD reemplazando .puml -> .png
Write-Host "2) Preparando copia de Markdown en docs/build/md y reemplazando referencias .puml -> .png"
Remove-Item -Recurse -Force docs\build -ErrorAction SilentlyContinue
New-Item -ItemType Directory -Path docs\build\md -Force | Out-Null

Get-ChildItem -Path docs -Recurse -Filter *.md | ForEach-Object {
    $full = $_.FullName
    $rel = $full.Substring((Get-Location).Path.Length + 1)
    $target = Join-Path "docs\build\md" ($rel -replace '^docs[\\/]', '')
    $tDir = Split-Path $target
    if(!(Test-Path $tDir)){ New-Item -ItemType Directory -Path $tDir | Out-Null }
    (Get-Content $full -Raw) -replace '\.puml', '.png' | Set-Content -Path $target -Encoding UTF8
}

# 3) Orden de los documentos
if (-not (Test-Path docs\build\ordered.txt)) {
    Write-Host "docs/build/ordered.txt no existe. Generando lista ordenada automáticamente (puedes editarla)."
    Get-ChildItem -Path docs -Recurse -Filter *.md | Sort-Object FullName | ForEach-Object {
        $rel = $_.FullName.Substring((Get-Location).Path.Length + 1) -replace '\\','/'
        $rel
    } | Set-Content docs\build\ordered.txt -Encoding UTF8
}

Write-Host "Archivo ordered.txt listo en docs/build/ordered.txt (edítalo si necesitas otro orden)."

# 4) Construir lista de archivos para Pandoc
$files = Get-Content docs\build\ordered.txt | ForEach-Object { $_ -replace '^docs/', 'docs/build/md/' }

Write-Host "Archivos a incluir en el PDF:" -ForegroundColor Cyan
$files | ForEach-Object { Write-Host " - $_" }

# 5) Generar HTML con Pandoc
Write-Host "5) Generando HTML con Pandoc..."
pandoc @($files) -s --toc --css=docs/style.css -o docs\build\documentation.html --metadata title="Sistema de Horarios FICCT"
if ($LASTEXITCODE -ne 0) { Write-Host "Pandoc falló." -ForegroundColor Red; exit 1 }

# 6) Convertir HTML a PDF
if ($haveWk) {
    Write-Host "6) Generando PDF con wkhtmltopdf..."
    wkhtmltopdf docs\build\documentation.html docs\build\documentation.pdf
    if ($LASTEXITCODE -ne 0) { Write-Host "wkhtmltopdf falló." -ForegroundColor Red; exit 1 }
} else {
    Write-Host "wkhtmltopdf no disponible. Intentando Pandoc -> xelatex..."
    if (Test-CommandExists xelatex) {
        pandoc @($files) -s --toc --pdf-engine=xelatex -V geometry:margin=1in -o docs\build\documentation.pdf --metadata title="Sistema de Horarios FICCT"
        if ($LASTEXITCODE -ne 0) { Write-Host "Pandoc->xelatex falló." -ForegroundColor Red; exit 1 }
    } else {
        Write-Host "No hay motor PDF disponible (wkhtmltopdf ni xelatex). Instala uno y reintenta." -ForegroundColor Red
        exit 1
    }
}

Write-Host "PDF generado: docs/build/documentation.pdf" -ForegroundColor Green

Write-Host "Proceso finalizado." -ForegroundColor Green
