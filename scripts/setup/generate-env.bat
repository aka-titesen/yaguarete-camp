@echo off

REM =============================================================================
REM YAGARUETE CAMP - GENERADOR .ENV SIMPLE
REM =============================================================================
REM Descripción: Genera archivo .env básico para desarrollo
REM Uso: generate-env.bat
REM =============================================================================

echo � Generando archivo .env para desarrollo...
echo.

if exist ".env" (
    echo ⚠️  El archivo .env ya existe
    set /p overwrite="¿Sobrescribir? (y/N): "
    if /i not "%overwrite%"=="y" (
        echo Operación cancelada
        pause
        exit /b 0
    )
)

REM Crear archivo .env básico
(
    echo # =============================================================================
    echo # YAGARUETE CAMP - CONFIGURACIÓN DE DESARROLLO
    echo # =============================================================================
    echo # IMPORTANTE: Este archivo NO se sube a git
    echo # Para producción, crear un .env separado con passwords seguros
    echo # =============================================================================
    echo.
    echo # Entorno
    echo CI_ENVIRONMENT=development
    echo.
    echo # Base de datos ^(Docker - Solo desarrollo^)
    echo DB_DATABASE=bd_yagaruete_camp
    echo DB_USERNAME=root
    echo DB_PASSWORD=dev_password_123
    echo DB_HOSTNAME=db
    echo DB_PORT=3306
    echo.
    echo # URLs
    echo APP_URL=http://localhost:8080
    echo.
    echo # Redis
    echo REDIS_HOST=redis
    echo REDIS_PORT=6379
    echo.
    echo # Email ^(MailHog para desarrollo^)
    echo MAIL_HOST=mailhog
    echo MAIL_PORT=1025
    echo MAIL_USERNAME=
    echo MAIL_PASSWORD=
    echo.
    echo # Claves de aplicación ^(generar nuevas para producción^)
    echo APP_KEY=dev_key_32_characters_long_abc123
    echo JWT_SECRET=dev_jwt_secret_key_for_tokens_xyz789
    echo.
    echo # Debug ^(solo desarrollo^)
    echo CI_DEBUG=true
    echo.
    echo # =============================================================================
    echo # PARA PRODUCCIÓN: Copiar a .env.production.example
    echo # =============================================================================
) > .env

echo ✅ Archivo .env creado correctamente
echo.
echo 📋 Configuración básica lista para desarrollo
echo 🚀 Ejecuta: deploy.bat start
echo.
pause
