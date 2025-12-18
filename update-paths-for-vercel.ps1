# Script para actualizar rutas de /phpweb/ a / para deployment en Vercel
# Uso: .\update-paths-for-vercel.ps1

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Actualizador de Rutas para Vercel" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Obtener la ruta actual
$projectPath = Get-Location
Write-Host "Directorio del proyecto: $projectPath" -ForegroundColor Yellow
Write-Host ""

# Crear carpeta de backup
$backupFolder = Join-Path $projectPath "backup_before_vercel"
if (-not (Test-Path $backupFolder)) {
    New-Item -ItemType Directory -Path $backupFolder | Out-Null
    Write-Host "✓ Carpeta de backup creada: $backupFolder" -ForegroundColor Green
} else {
    Write-Host "⚠ La carpeta de backup ya existe" -ForegroundColor Yellow
}

# Buscar todos los archivos PHP
$phpFiles = Get-ChildItem -Path $projectPath -Filter "*.php" -Recurse -File | 
    Where-Object { $_.FullName -notlike "*backup_*" -and $_.FullName -notlike "*vendor*" -and $_.FullName -notlike "*node_modules*" }

Write-Host "Archivos PHP encontrados: $($phpFiles.Count)" -ForegroundColor Cyan
Write-Host ""

# Contadores
$filesModified = 0
$totalReplacements = 0

# Preguntar confirmación
Write-Host "Este script va a:" -ForegroundColor Yellow
Write-Host "  1. Hacer backup de todos los archivos PHP" -ForegroundColor White
Write-Host "  2. Reemplazar '/phpweb/' por '/' en todos los archivos" -ForegroundColor White
Write-Host ""
$confirmation = Read-Host "¿Deseas continuar? (S/N)"

if ($confirmation -ne 'S' -and $confirmation -ne 's') {
    Write-Host "Operación cancelada." -ForegroundColor Red
    exit
}

Write-Host ""
Write-Host "Procesando archivos..." -ForegroundColor Cyan
Write-Host ""

foreach ($file in $phpFiles) {
    try {
        # Leer contenido
        $content = Get-Content -Path $file.FullName -Raw -Encoding UTF8
        
        # Contar ocurrencias
        $matches = ([regex]::Matches($content, '/phpweb/')).Count
        
        if ($matches -gt 0) {
            # Hacer backup
            $relativePath = $file.FullName.Substring($projectPath.Path.Length + 1)
            $backupPath = Join-Path $backupFolder $relativePath
            $backupDir = Split-Path $backupPath -Parent
            
            if (-not (Test-Path $backupDir)) {
                New-Item -ItemType Directory -Path $backupDir -Force | Out-Null
            }
            
            Copy-Item -Path $file.FullName -Destination $backupPath -Force
            
            # Reemplazar contenido
            $newContent = $content -replace '/phpweb/', '/'
            
            # Guardar archivo modificado
            Set-Content -Path $file.FullName -Value $newContent -Encoding UTF8 -NoNewline
            
            # Actualizar contadores
            $filesModified++
            $totalReplacements += $matches
            
            # Mostrar progreso
            Write-Host "✓ $relativePath" -ForegroundColor Green
            Write-Host "  → $matches reemplazos realizados" -ForegroundColor Gray
        }
    }
    catch {
        Write-Host "✗ Error procesando: $($file.FullName)" -ForegroundColor Red
        Write-Host "  Error: $($_.Exception.Message)" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Resumen" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Archivos modificados: $filesModified" -ForegroundColor Green
Write-Host "Total de reemplazos: $totalReplacements" -ForegroundColor Green
Write-Host "Backup guardado en: $backupFolder" -ForegroundColor Yellow
Write-Host ""

if ($filesModified -gt 0) {
    Write-Host "⚠ IMPORTANTE: Revisa los cambios antes de hacer commit" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "Próximos pasos:" -ForegroundColor Cyan
    Write-Host "  1. Revisa los archivos modificados" -ForegroundColor White
    Write-Host "  2. Actualiza oauth/config.php con la URL de producción" -ForegroundColor White
    Write-Host "  3. Prueba localmente con: php -S localhost:8000" -ForegroundColor White
    Write-Host "  4. Si todo funciona, haz commit y push a GitHub" -ForegroundColor White
    Write-Host "  5. Despliega en Vercel" -ForegroundColor White
    Write-Host ""
    Write-Host "Para revertir los cambios, copia los archivos desde:" -ForegroundColor Yellow
    Write-Host "  $backupFolder" -ForegroundColor Gray
} else {
    Write-Host "No se encontraron archivos que modificar." -ForegroundColor Yellow
}

Write-Host ""
Write-Host "Presiona cualquier tecla para salir..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
