@echo off
REM Script para generar archivo .env seguro en Windows
REM Uso: scripts\setup\generate-env.bat

echo 🔐 Generando archivo .env seguro...

REM Copiar template
copy .env.example .env > nul

REM Generar passwords aleatorios (simplificado para Windows)
set DB_ROOT_PASS=Root_%RANDOM%%RANDOM%_Pass
set DB_APP_PASS=App_%RANDOM%%RANDOM%_Pass
set APP_SECRET=%RANDOM%%RANDOM%%RANDOM%%RANDOM%
set JWT_SECRET=%RANDOM%%RANDOM%%RANDOM%%RANDOM%

REM Reemplazar valores en .env usando PowerShell
powershell -Command "(Get-Content .env) -replace 'CHANGE_ME_STRONG_PASSWORD', '%DB_ROOT_PASS%' | Set-Content .env"
powershell -Command "(Get-Content .env) -replace 'CHANGE_ME_APP_PASSWORD', '%DB_APP_PASS%' | Set-Content .env"
powershell -Command "(Get-Content .env) -replace 'CHANGE_ME_ROOT_PASSWORD', '%DB_ROOT_PASS%' | Set-Content .env"
powershell -Command "(Get-Content .env) -replace 'CHANGE_ME_32_CHAR_SECRET_KEY', '%APP_SECRET%' | Set-Content .env"
powershell -Command "(Get-Content .env) -replace 'CHANGE_ME_JWT_SECRET_KEY', '%JWT_SECRET%' | Set-Content .env"

echo ✅ Archivo .env generado con passwords seguros
echo 📋 Passwords generados:
echo    - DB Root: %DB_ROOT_PASS%
echo    - DB App: %DB_APP_PASS%
echo.
echo ⚠️  IMPORTANTE:
echo    - Guarda estos passwords en un lugar seguro
echo    - NO los compartas en chat/email
echo    - El archivo .env NO se subirá a git
echo.
echo 🚀 Ahora puedes ejecutar: docker-compose up -d

pause
