param(
    [string]$HostName = "127.0.0.1",
    [int]$Port = 5001
)

$root = Split-Path -Parent $MyInvocation.MyCommand.Path
$venvPython = Join-Path $root ".venv\Scripts\python.exe"

if (-not (Test-Path $venvPython)) {
    Write-Error "Python venv not found. Run: python -m venv .venv and install requirements first."
    exit 1
}

$env:BRAIN_TUMOR_SERVICE_HOST = $HostName
$env:BRAIN_TUMOR_SERVICE_PORT = "$Port"

& $venvPython (Join-Path $root "prediction_server.py")
