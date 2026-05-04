@echo off

:: Sprawdzanie uprawnień administratora (wymagane do net stop i tailscale reset)
net session >nul 2>&1
if %errorLevel% neq 0 (
    powershell -Command "Start-Process '%~0' -Verb RunAs"
    exit /b
)

echo Zatrzymywanie procesów i czyszczenie konfiguracji...

:: Kill App terminals
taskkill /FI "WINDOWTITLE eq Laravel*" /T /F
taskkill /FI "WINDOWTITLE eq Vite*" /T /F
taskkill /FI "WINDOWTITLE eq TS HTTP*" /T /F
taskkill /FI "WINDOWTITLE eq TS HTTPS*" /T /F

:: Reset Tailscale
echo Resetowanie Tailscale...
    tailscale serve reset

:: Kill MySQL service
echo Zatrzymywanie bazy danych...
    net stop MySQL95

echo Gotowe! Wszystkie procesy zostały zamknięte.
pause