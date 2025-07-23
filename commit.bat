@echo off
echo Quick Auto-Commit for Chat-App
echo ==============================
echo.

cd /d "C:\laragon\www\Chat-App"

REM Add timestamp to README
echo. >> README.md
echo <!-- Auto-update: %date% %time% --> >> README.md

REM Git operations
git add .
git commit -m "chore: daily maintenance update - %date% %time%"
git push origin master

echo.
echo Daily commit completed successfully!
echo Commit message: "chore: daily maintenance update - %date% %time%"
echo.
pause
