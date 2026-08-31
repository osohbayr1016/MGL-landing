$ErrorActionPreference = "Stop"
$here = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location $here

$phpCandidates = @(
  "php",
  "C:\laragon\bin\php\php-8.3.12-Win32-vs16-x64\php.exe",
  "C:\laragon\bin\php\php-8.2.12-Win32-vs16-x64\php.exe",
  "C:\xampp\php\php.exe",
  "C:\php\php.exe"
)

$php = $null
foreach ($candidate in $phpCandidates) {
  if ($candidate -eq "php") {
    $cmd = Get-Command php -ErrorAction SilentlyContinue
    if ($cmd) { $php = $cmd.Source; break }
  } elseif (Test-Path $candidate) {
    $php = $candidate
    break
  }
}

if (-not $php) {
  Write-Host ""
  Write-Host "PHP not found." -ForegroundColor Red
  Write-Host "Install Laragon (recommended) or XAMPP, then run this script again."
  Write-Host "https://laragon.org/download/"
  Write-Host ""
  exit 1
}

if (-not (Test-Path "const.local.php")) {
  Copy-Item "const.local.php.example" "const.local.php"
  Write-Host "Created cpadmin/const.local.php — edit DB credentials before login."
}

$port = 8075
Write-Host ""
Write-Host "MGL CP Admin — local"
Write-Host "--------------------"
Write-Host "Admin:  http://localhost:$port/"
Write-Host "Promo:  http://localhost:$port/insert/promo"
Write-Host ""
Write-Host "Press Ctrl+C to stop."
Write-Host ""

& $php -S "localhost:$port" "router.php"
