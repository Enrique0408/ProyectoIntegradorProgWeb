# init-db.ps1 - Automatiza la ejecución de backend\setup_database.php desde PowerShell
# Uso: Ejecutar desde la raíz del proyecto
#   PS> .\init-db.ps1

$projectRoot = Split-Path -Parent $MyInvocation.MyCommand.Definition
Write-Host "Proyecto: $projectRoot" -ForegroundColor Cyan

# Intentar localizar PHP de XAMPP
$xamppPhp = 'C:\xampp\php\php.exe'
$phpCmd = 'php'
if (Test-Path $xamppPhp) {
    $phpCmd = $xamppPhp
    Write-Host "Usando PHP de XAMPP: $phpCmd" -ForegroundColor Green
} else {
    # comprobar php en PATH
    try {
        $ver = & php -v 2>$null
        if ($LASTEXITCODE -eq 0) {
            Write-Host "Usando php desde PATH" -ForegroundColor Green
            $phpCmd = 'php'
        } else {
            Write-Host "No se encontró PHP en PATH ni en C:\xampp\php\php.exe" -ForegroundColor Yellow
            Write-Host "Si usas XAMPP, asegúrate de que C:\\xampp\\php\\php.exe existe o añade PHP a tu PATH." -ForegroundColor Yellow
        }
    } catch {
        Write-Host "No se encontró PHP en PATH." -ForegroundColor Yellow
    }
}

if (-not (Get-Command $phpCmd -ErrorAction SilentlyContinue) -and -not (Test-Path $phpCmd)) {
    Write-Host "No hay un ejecutable de PHP disponible. Instala PHP o ajusta la variable phpCmd en este script." -ForegroundColor Red
    exit 1
}

# Ejecutar setup
$setup = Join-Path $projectRoot 'backend\setup_database.php'
if (-not (Test-Path $setup)) {
    Write-Host "No se encontró $setup" -ForegroundColor Red
    exit 1
}

Write-Host "Ejecutando: $phpCmd $setup" -ForegroundColor Cyan
& $phpCmd $setup
$code = $LASTEXITCODE
if ($code -ne 0) {
    Write-Host "El script terminó con código de salida: $code" -ForegroundColor Red
    exit $code
}
Write-Host "Terminado." -ForegroundColor Green
