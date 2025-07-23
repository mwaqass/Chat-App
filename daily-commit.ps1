# Daily Auto-Commit Script for Chat-App
# Simple script to maintain daily commits

Write-Host "Daily Auto-Commit for Chat-App" -ForegroundColor Green
Write-Host "=============================" -ForegroundColor Green
Write-Host ""

# Change to repository directory
Set-Location "C:\laragon\www\Chat-App"

# Get current timestamp
$timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"

# Add timestamp to README
Add-Content -Path "README.md" -Value "`n<!-- Auto-update: $timestamp -->"

# Git operations
git add .
git commit -m "chore: daily maintenance update - $timestamp"
git push origin master

Write-Host ""
Write-Host "Daily commit completed successfully!" -ForegroundColor Green
Write-Host "Commit message: 'chore: daily maintenance update - $timestamp'" -ForegroundColor Yellow
Write-Host ""

Read-Host "Press Enter to continue"
