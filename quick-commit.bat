@echo off
echo Quick Daily Commit for Chat-App
echo ===============================
echo.

cd /d "C:\laragon\www\Chat-App"

echo Adding timestamp to README...
echo. >> README.md
echo ^<!-- Auto-update: %date% %time% --^> >> README.md

echo.
echo Staging changes...
git add .

echo.
echo Committing changes...
git commit -m "chore: daily maintenance update - %date% %time%"

echo.
echo Pushing to remote...
git push origin master

echo.
echo ===============================
echo Daily commit completed successfully!
echo Commit: "chore: daily maintenance update - %date% %time%"
echo ===============================
echo.
pause
