@echo off
title Service WhatsApp AFT Import-Export
color 0A
echo ========================================================
echo   DEMARRAGE DU SERVICE WHATSAPP GRATUIT - AFT IMPORT-EXPORT
echo ========================================================
echo.
cd /d "%~dp0whatsapp-service"
node server.js
pause
