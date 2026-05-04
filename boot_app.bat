@echo off

:: MYSQL service
net session >nul 2>&1
if %errorLevel% neq 0 (
    powershell -Command "Start-Process '%~0' -Verb RunAs"
    exit /b
)

net start MySQL95

:: ROOT
set "ROOT=E:\QuizApp"

:: Laravel 
start "Laravel" cmd /k "cd /d %ROOT%\quizApp && php artisan serve --host=0.0.0.0"

:: Vite
start "Vite" cmd /k "cd /d %ROOT%\quizApp && npm run dev -- --host"

:: Tailscale
start "TS HTTP" cmd /k "cd /d %ROOT%\quizApp && tailscale serve 8000"
start "TS HTTPS" cmd /k "cd /d %ROOT% && tailscale serve --https 5173 5173"

:: Database client
start "MySQL Client" cmd /k mysql -u admin -p"