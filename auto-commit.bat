@echo off
echo Auto-Commit Script for Chat-App Repository
echo ===========================================
echo.

REM Check if PowerShell is available
powershell -Command "Get-Host" >nul 2>&1
if %errorlevel% neq 0 (
    echo Error: PowerShell is not available
    pause
    exit /b 1
)

REM Run the auto-commit script
powershell -ExecutionPolicy Bypass -File "%~dp0auto-commit.ps1" %*

echo.
echo Script execution completed.
pause
