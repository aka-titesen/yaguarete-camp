@echo off
setlocal enabledelayedexpansion

REM =============================================================================
REM YAGARUETE CAMP - SCRIPT DE INICIO RÁPIDO (WINDOWS)
REM =============================================================================
REM Descripción: Script simple para desarrolladores - Solo requiere Docker
REM Uso: deploy.bat [start|stop|restart|logs|reset]
REM =============================================================================

set "SCRIPT_DIR=%~dp0"
set "PROJECT_ROOT=%SCRIPT_DIR%..\.."
set "COMMAND=%~1"

REM =============================================================================
REM FUNCIONES
REM =============================================================================

:print_banner
echo.
echo ╔═══════════════════════════════════════════════════════════════╗
echo ║                    🦎 YAGARUETE CAMP                         ║
echo ║              Inicio Rápido para Desarrolladores              ║
echo ╚═══════════════════════════════════════════════════════════════╝
echo.
echo 📋 Requisitos: Solo Docker Desktop instalado
echo 🚀 Uso: %0 [start^|stop^|restart^|logs^|reset]
echo.
goto :eof

:print_info
echo [INFO] %~1
goto :eof

:print_success
echo [✅] %~1
goto :eof

:print_error
echo [❌] %~1
goto :eof

:check_docker
where docker >nul 2>&1
if errorlevel 1 (
    call :print_error "Docker no está instalado"
    echo.
    echo 📥 Instala Docker Desktop desde: https://docs.docker.com/desktop/windows/
    echo    Después reinicia y ejecuta este script nuevamente
    pause
    exit /b 1
)

docker info >nul 2>&1
if errorlevel 1 (
    call :print_error "Docker no está corriendo"
    echo.
    echo 🔄 Inicia Docker Desktop e intenta nuevamente
    pause
    exit /b 1
)

call :print_success "Docker está listo"
goto :eof

:create_env_if_missing
if not exist ".env" (
    call :print_info "Creando archivo .env básico..."
    (
        echo # Yagaruete Camp - Configuración Docker para Desarrollo
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
    ) > .env
    call :print_success "Archivo .env creado"
)
goto :eof

:start_application
call :print_info "🚀 Iniciando Yagaruete Camp..."
echo.

call :print_info "Construyendo contenedores..."
docker-compose up -d --build

call :print_info "Esperando a que los servicios estén listos..."
timeout /t 15 /nobreak >nul

call :print_info "Ejecutando migraciones..."
docker-compose exec -T app php spark migrate

call :print_info "Ejecutando seeders..."
docker-compose exec -T app php spark db:seed

echo.
call :print_success "¡Aplicación lista!"
echo.
echo 🌐 Accede a tu aplicación en: http://localhost:8080
echo 🗄️  PHPMyAdmin en: http://localhost:8081
echo.
goto :eof

:stop_application
call :print_info "🛑 Deteniendo aplicación..."
docker-compose down
call :print_success "Aplicación detenida"
goto :eof

:restart_application
call :print_info "🔄 Reiniciando aplicación..."
docker-compose restart
call :print_success "Aplicación reiniciada"
goto :eof

:show_logs
call :print_info "📋 Mostrando logs..."
docker-compose logs -f
goto :eof

:reset_application
call :print_info "🗑️  Reset completo de la aplicación..."
echo.
echo ⚠️  Esto eliminará TODOS los datos y contenedores
set /p confirm="¿Continuar? (y/N): "

if /i "%confirm%"=="y" (
    docker-compose down -v --remove-orphans
    docker system prune -f
    call :print_success "Reset completado. Ejecuta 'deploy.bat start' para iniciar nuevamente"
) else (
    call :print_info "Operación cancelada"
)
goto :eof

REM =============================================================================
REM EJECUCIÓN PRINCIPAL
REM =============================================================================

call :print_banner

cd /d "%PROJECT_ROOT%"

REM Verificar Docker
call :check_docker
if errorlevel 1 exit /b 1

REM Crear .env si no existe
call :create_env_if_missing

REM Si no se especifica comando, usar start
if "%COMMAND%"=="" set "COMMAND=start"

REM Ejecutar comando
if /i "%COMMAND%"=="start" call :start_application
if /i "%COMMAND%"=="stop" call :stop_application
if /i "%COMMAND%"=="restart" call :restart_application
if /i "%COMMAND%"=="logs" call :show_logs
if /i "%COMMAND%"=="reset" call :reset_application

if /i not "%COMMAND%"=="start" if /i not "%COMMAND%"=="stop" if /i not "%COMMAND%"=="restart" if /i not "%COMMAND%"=="logs" if /i not "%COMMAND%"=="reset" (
    call :print_error "Comando desconocido: %COMMAND%"
    echo.
    echo 💡 Comandos disponibles:
    echo    start   - Iniciar aplicación
    echo    stop    - Detener aplicación
    echo    restart - Reiniciar aplicación
    echo    logs    - Ver logs
    echo    reset   - Reset completo
)
