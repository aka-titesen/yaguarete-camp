@echo off
setlocal enabledelayedexpansion

REM =============================================================================
REM YAGARUETE CAMP - SCRIPT DE DESPLIEGUE PRINCIPAL (WINDOWS)
REM =============================================================================
REM Descripción: Script principal para desplegar la aplicación Yagaruete Camp
REM Autor: Proyecto Martínez González
REM Versión: 2.0
REM =============================================================================

set "PROJECT_NAME=yagaruete-camp"
set "SCRIPT_DIR=%~dp0"
set "PROJECT_ROOT=%SCRIPT_DIR%..\.."

REM Variables de configuración
set "VERBOSE=false"
set "SKIP_DEPENDENCIES=false"
set "ENVIRONMENT=development"
set "COMMAND="

REM =============================================================================
REM FUNCIONES AUXILIARES
REM =============================================================================

:print_banner
echo.
echo ╔══════════════════════════════════════════════════════════════╗
echo ║                    YAGARUETE CAMP                            ║
echo ║              Script de Despliegue v2.0                      ║
echo ╚══════════════════════════════════════════════════════════════╝
echo.
goto :eof

:print_info
echo [INFO] %~1
goto :eof

:print_success
echo [SUCCESS] %~1
goto :eof

:print_warning
echo [WARNING] %~1
goto :eof

:print_error
echo [ERROR] %~1
goto :eof

:print_step
echo.
echo [STEP] %~1
echo --------------------------------------------------------------
goto :eof

:show_help
echo Uso: %0 [OPCIONES] [COMANDO]
echo.
echo COMANDOS:
echo     start, up       Iniciar la aplicación completa
echo     stop, down      Detener la aplicación
echo     restart         Reiniciar la aplicación
echo     status          Mostrar estado de contenedores
echo     logs            Mostrar logs de la aplicación
echo     clean           Limpiar contenedores y volúmenes
echo     reset           Reset completo (limpia y reconstruye)
echo.
echo OPCIONES:
echo     -v, --verbose   Modo verbose (más información)
echo     -s, --skip-deps Saltar verificación de dependencias
echo     -e, --env ENV   Ambiente (development^|production) [default: development]
echo     -h, --help      Mostrar esta ayuda
echo.
echo EJEMPLOS:
echo     %0 start                    # Iniciar aplicación
echo     %0 start --verbose          # Iniciar con modo verbose
echo     %0 reset --env production   # Reset completo en producción
echo     %0 logs app                 # Ver logs del contenedor app
echo.
goto :eof

:check_dependencies
if "%SKIP_DEPENDENCIES%"=="true" (
    call :print_warning "Saltando verificación de dependencias..."
    goto :eof
)

call :print_step "Verificando dependencias del sistema"

REM Verificar Docker
where docker >nul 2>&1
if errorlevel 1 (
    call :print_error "Docker no está instalado. Por favor instala Docker Desktop para Windows."
    echo Descarga desde: https://docs.docker.com/desktop/windows/
    exit /b 1
)

REM Verificar Docker Compose
where docker-compose >nul 2>&1
if errorlevel 1 (
    call :print_error "Docker Compose no está instalado."
    exit /b 1
)

call :print_success "Todas las dependencias están instaladas"
goto :eof

:check_config_files
call :print_step "Verificando archivos de configuración"

cd /d "%PROJECT_ROOT%"

REM Verificar docker-compose.yml
if not exist "docker-compose.yml" (
    call :print_error "Archivo docker-compose.yml no encontrado"
    exit /b 1
)

REM Verificar .env
if not exist ".env" (
    if exist ".env.docker" (
        call :print_info "Copiando .env.docker a .env..."
        copy ".env.docker" ".env" >nul
    ) else (
        call :print_warning "Archivo .env no encontrado. Creando uno básico..."
        (
            echo # Configuración básica de Yagaruete Camp
            echo DB_DATABASE=bd_yagaruete_camp
            echo DB_USERNAME=root
            echo DB_PASSWORD=root
            echo DB_PORT=3306
            echo.
            echo # Configuración de entorno
            echo CI_ENVIRONMENT=%ENVIRONMENT%
        ) > .env
    )
)

call :print_success "Configuración verificada correctamente"
goto :eof

:start_application
call :print_step "Iniciando aplicación Yagaruete Camp"

cd /d "%PROJECT_ROOT%"

call :print_info "Construyendo e iniciando contenedores..."
if "%VERBOSE%"=="true" (
    docker-compose up -d --build
) else (
    docker-compose up -d --build >nul 2>&1
)

call :print_info "Esperando a que los servicios estén listos..."
timeout /t 10 /nobreak >nul

call :print_info "Verificando estado de contenedores..."
docker-compose ps

call :print_success "Aplicación iniciada correctamente"
call :print_info "Accede a la aplicación en: http://localhost:8080"
call :print_info "PHPMyAdmin disponible en: http://localhost:8081"
goto :eof

:stop_application
call :print_step "Deteniendo aplicación"

cd /d "%PROJECT_ROOT%"

if "%VERBOSE%"=="true" (
    docker-compose down
) else (
    docker-compose down >nul 2>&1
)

call :print_success "Aplicación detenida correctamente"
goto :eof

:restart_application
call :print_step "Reiniciando aplicación"
call :stop_application
timeout /t 2 /nobreak >nul
call :start_application
goto :eof

:show_status
call :print_step "Estado de contenedores"

cd /d "%PROJECT_ROOT%"
docker-compose ps

echo.
echo URLs de acceso:
echo - Aplicación: http://localhost:8080
echo - PHPMyAdmin: http://localhost:8081
echo - MailHog: http://localhost:8025
goto :eof

:show_logs
cd /d "%PROJECT_ROOT%"

if not "%LOG_SERVICE%"=="" (
    call :print_info "Mostrando logs del servicio: %LOG_SERVICE%"
    docker-compose logs -f %LOG_SERVICE%
) else (
    call :print_info "Mostrando logs de todos los servicios"
    docker-compose logs -f
)
goto :eof

:clean_environment
call :print_step "Limpiando entorno"

cd /d "%PROJECT_ROOT%"

call :print_warning "Esta acción eliminará todos los contenedores y volúmenes"
set /p confirm="¿Continuar? (y/N): "

if /i "%confirm%"=="y" (
    docker-compose down -v --remove-orphans
    docker system prune -f
    call :print_success "Entorno limpiado correctamente"
) else (
    call :print_info "Operación cancelada"
)
goto :eof

:reset_environment
call :print_step "Reset completo del ambiente"

cd /d "%PROJECT_ROOT%"

call :print_warning "Esta acción eliminará TODOS los datos y reconstruirá desde cero"
set /p confirm="¿Continuar? (y/N): "

if /i "%confirm%"=="y" (
    REM Detener y limpiar
    docker-compose down -v --remove-orphans
    docker system prune -f
    
    REM Reconstruir y iniciar
    docker-compose up -d --build --force-recreate
    
    REM Ejecutar script de inicialización de BD si existe
    if exist "scripts\setup\init-database.bat" (
        call :print_info "Ejecutando inicialización de base de datos..."
        call scripts\setup\init-database.bat
    )
    
    call :print_success "Reset completo finalizado"
    call :show_status
) else (
    call :print_info "Operación cancelada"
)
goto :eof

REM =============================================================================
REM PROCESAMIENTO DE ARGUMENTOS
REM =============================================================================

:parse_args
if "%~1"=="" goto :end_parse

if "%~1"=="-v" (
    set "VERBOSE=true"
    shift & goto :parse_args
)
if "%~1"=="--verbose" (
    set "VERBOSE=true"
    shift & goto :parse_args
)
if "%~1"=="-s" (
    set "SKIP_DEPENDENCIES=true"
    shift & goto :parse_args
)
if "%~1"=="--skip-deps" (
    set "SKIP_DEPENDENCIES=true"
    shift & goto :parse_args
)
if "%~1"=="-e" (
    set "ENVIRONMENT=%~2"
    shift & shift & goto :parse_args
)
if "%~1"=="--env" (
    set "ENVIRONMENT=%~2"
    shift & shift & goto :parse_args
)
if "%~1"=="-h" (
    call :show_help
    exit /b 0
)
if "%~1"=="--help" (
    call :show_help
    exit /b 0
)
if "%~1"=="start" (
    set "COMMAND=start"
    shift & goto :parse_args
)
if "%~1"=="up" (
    set "COMMAND=start"
    shift & goto :parse_args
)
if "%~1"=="stop" (
    set "COMMAND=stop"
    shift & goto :parse_args
)
if "%~1"=="down" (
    set "COMMAND=stop"
    shift & goto :parse_args
)
if "%~1"=="restart" (
    set "COMMAND=restart"
    shift & goto :parse_args
)
if "%~1"=="status" (
    set "COMMAND=status"
    shift & goto :parse_args
)
if "%~1"=="logs" (
    set "COMMAND=logs"
    set "LOG_SERVICE=%~2"
    shift
    if not "%~2"=="" if not "%~2:~0,1%"=="-" shift
    goto :parse_args
)
if "%~1"=="clean" (
    set "COMMAND=clean"
    shift & goto :parse_args
)
if "%~1"=="reset" (
    set "COMMAND=reset"
    shift & goto :parse_args
)

call :print_error "Opción desconocida: %~1"
call :show_help
exit /b 1

:end_parse

REM =============================================================================
REM EJECUCIÓN PRINCIPAL
REM =============================================================================

call :print_banner

REM Si no se especifica comando, usar start por defecto
if "%COMMAND%"=="" set "COMMAND=start"

REM Verificar dependencias para la mayoría de comandos
if not "%COMMAND%"=="status" if not "%COMMAND%"=="logs" (
    call :check_dependencies
    if errorlevel 1 exit /b 1
    call :check_config_files
    if errorlevel 1 exit /b 1
)

REM Ejecutar comando
if "%COMMAND%"=="start" call :start_application
if "%COMMAND%"=="stop" call :stop_application
if "%COMMAND%"=="restart" call :restart_application
if "%COMMAND%"=="status" call :show_status
if "%COMMAND%"=="logs" call :show_logs
if "%COMMAND%"=="clean" call :clean_environment
if "%COMMAND%"=="reset" call :reset_environment

REM Procesar argumentos
call :parse_args %*
