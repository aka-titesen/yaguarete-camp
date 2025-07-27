#!/bin/bash

# ===========================================================create_env_if_missing() {
    if [[ ! -f ".env" ]]; then
        print_info "Creando archivo .env básico..."
        cat > .env << EOF
# Yagaruete Camp - Configuración Docker
CI_ENVIRONMENT=development

# Base de datos (Docker)
DB_DATABASE=bd_yagaruete_camp
DB_USERNAME=root
DB_PASSWORD=root
DB_HOSTNAME=db
DB_PORT=3306

# URLs
APP_URL=http://localhost:8080

# Redis
REDIS_HOST=redis
REDIS_PORT=6379

# Email (MailHog para desarrollo)
MAIL_HOST=mailhog
MAIL_PORT=1025
EOF
        print_success "Archivo .env creado"
    fi
}# YAGARUETE CAMP - INICIO RÁPIDO PARA DESARROLLADORES
# =============================================================================
# Descripción: Script simple para desarrolladores - Solo requiere Docker
# Uso: ./deploy.sh [start|stop|restart|logs|reset]
# =============================================================================

set -e

# Configuración de colores
readonly GREEN='\033[0;32m'
readonly RED='\033[0;31m'
readonly BLUE='\033[0;34m'
readonly NC='\033[0m'

# Configuración del proyecto
readonly SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
readonly PROJECT_ROOT="$(cd "${SCRIPT_DIR}/../.." && pwd)"
readonly COMMAND="${1:-start}"

# =============================================================================
# FUNCIONES
# =============================================================================

print_banner() {
    echo -e "${BLUE}"
    echo "╔═══════════════════════════════════════════════════════════════╗"
    echo "║                    🦎 YAGARUETE CAMP                         ║"
    echo "║              Inicio Rápido para Desarrolladores              ║"
    echo "╚═══════════════════════════════════════════════════════════════╝"
    echo -e "${NC}"
    echo "📋 Requisitos: Solo Docker y Docker Compose instalados"
    echo "🚀 Uso: $0 [start|stop|restart|logs|reset]"
    echo
}

print_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[✅]${NC} $1"
}

print_error() {
    echo -e "${RED}[❌]${NC} $1"
}

check_docker() {
    if ! command -v docker &> /dev/null; then
        print_error "Docker no está instalado"
        echo
        echo "📥 Instala Docker desde: https://docs.docker.com/get-docker/"
        echo "   Después reinicia y ejecuta este script nuevamente"
        exit 1
    fi

    if ! docker info &> /dev/null; then
        print_error "Docker no está corriendo"
        echo
        echo "🔄 Inicia Docker e intenta nuevamente"
        exit 1
    fi

    if ! command -v docker-compose &> /dev/null; then
        print_error "Docker Compose no está instalado"
        echo
        echo "📥 Instala Docker Compose desde: https://docs.docker.com/compose/install/"
        exit 1
    fi

    print_success "Docker está listo"
}

create_env_if_missing() {
    if [[ ! -f ".env" ]]; then
        print_info "Creando archivo .env básico..."
        cat > .env << EOF
# Yagaruete Camp - Configuración Docker
CI_ENVIRONMENT=development

# Base de datos
DB_DATABASE=bd_yagaruete_camp
DB_USERNAME=root
DB_PASSWORD=root
DB_HOSTNAME=db
DB_PORT=3306

# URLs
APP_URL=http://localhost:8080
EOF
        print_success "Archivo .env creado"
    fi
}

start_application() {
    print_info "🚀 Iniciando Yagaruete Camp..."
    echo

    print_info "Construyendo contenedores..."
    docker-compose up -d --build

    print_info "Esperando a que los servicios estén listos..."
    sleep 15

    print_info "Ejecutando migraciones..."
    docker-compose exec -T app php spark migrate

    print_info "Ejecutando seeders..."
    docker-compose exec -T app php spark db:seed

    echo
    print_success "¡Aplicación lista!"
    echo
    echo "🌐 Accede a tu aplicación en: http://localhost:8080"
    echo "🗄️  PHPMyAdmin en: http://localhost:8081"
    echo
}

stop_application() {
    print_info "🛑 Deteniendo aplicación..."
    docker-compose down
    print_success "Aplicación detenida"
}

restart_application() {
    print_info "🔄 Reiniciando aplicación..."
    docker-compose restart
    print_success "Aplicación reiniciada"
}

show_logs() {
    print_info "📋 Mostrando logs..."
    docker-compose logs -f
}

reset_application() {
    print_info "🗑️  Reset completo de la aplicación..."
    echo
    echo "⚠️  Esto eliminará TODOS los datos y contenedores"
    read -p "¿Continuar? (y/N): " -n 1 -r
    echo

    if [[ $REPLY =~ ^[Yy]$ ]]; then
        docker-compose down -v --remove-orphans
        docker system prune -f
        print_success "Reset completado. Ejecuta './deploy.sh start' para iniciar nuevamente"
    else
        print_info "Operación cancelada"
    fi
}

# =============================================================================
# EJECUCIÓN PRINCIPAL
# =============================================================================

print_banner

cd "$PROJECT_ROOT"

# Verificar Docker
check_docker

# Crear .env si no existe
create_env_if_missing

# Ejecutar comando
case "$COMMAND" in
    start)
        start_application
        ;;
    stop)
        stop_application
        ;;
    restart)
        restart_application
        ;;
    logs)
        show_logs
        ;;
    reset)
        reset_application
        ;;
    *)
        print_error "Comando desconocido: $COMMAND"
        echo
        echo "💡 Comandos disponibles:"
        echo "   start   - Iniciar aplicación"
        echo "   stop    - Detener aplicación"
        echo "   restart - Reiniciar aplicación"
        echo "   logs    - Ver logs"
        echo "   reset   - Reset completo"
        exit 1
        ;;
esac
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_step() {
    echo -e "\n${CYAN}[STEP]${NC} $1"
    echo "$(printf '%.0s-' {1..60})"
}

log_verbose() {
    if [[ "$VERBOSE" == true ]]; then
        echo -e "${BLUE}[VERBOSE]${NC} $1"
    fi
}

# Función para mostrar ayuda
show_help() {
    cat << EOF
Uso: $0 [OPCIONES] [COMANDO]

COMANDOS:
    start, up       Iniciar la aplicación completa
    stop, down      Detener la aplicación
    restart         Reiniciar la aplicación
    status          Mostrar estado de contenedores
    logs            Mostrar logs de la aplicación
    clean           Limpiar contenedores y volúmenes
    reset           Reset completo (limpia y reconstruye)

OPCIONES:
    -v, --verbose   Modo verbose (más información)
    -s, --skip-deps Saltar verificación de dependencias
    -e, --env ENV   Ambiente (development|production) [default: development]
    -h, --help      Mostrar esta ayuda

EJEMPLOS:
    $0 start                    # Iniciar aplicación
    $0 start --verbose          # Iniciar con modo verbose
    $0 reset --env production   # Reset completo en producción
    $0 logs app                 # Ver logs del contenedor app

EOF
}

# Verificar dependencias del sistema
check_dependencies() {
    if [[ "$SKIP_DEPENDENCIES" == true ]]; then
        print_warning "Saltando verificación de dependencias..."
        return 0
    fi

    print_step "Verificando dependencias del sistema"
    
    local missing_deps=()
    
    # Verificar Docker
    if ! command -v docker &> /dev/null; then
        missing_deps+=("docker")
    else
        log_verbose "Docker encontrado: $(docker --version)"
    fi
    
    # Verificar Docker Compose
    if ! command -v docker-compose &> /dev/null; then
        missing_deps+=("docker-compose")
    else
        log_verbose "Docker Compose encontrado: $(docker-compose --version)"
    fi
    
    # Verificar Node.js (opcional para desarrollo)
    if command -v node &> /dev/null; then
        log_verbose "Node.js encontrado: $(node --version)"
    fi
    
    if [[ ${#missing_deps[@]} -gt 0 ]]; then
        print_error "Dependencias faltantes: ${missing_deps[*]}"
        echo "Por favor instala las dependencias faltantes:"
        echo "- Docker: https://docs.docker.com/get-docker/"
        echo "- Docker Compose: https://docs.docker.com/compose/install/"
        exit 1
    fi
    
    print_success "Todas las dependencias están instaladas"
}

# Verificar archivos de configuración
check_config_files() {
    print_step "Verificando archivos de configuración"
    
    cd "$PROJECT_ROOT"
    
    # Verificar docker-compose.yml
    if [[ ! -f "docker-compose.yml" ]]; then
        print_error "Archivo docker-compose.yml no encontrado"
        exit 1
    fi
    
    # Verificar .env
    if [[ ! -f ".env" ]]; then
        if [[ -f ".env.docker" ]]; then
            print_info "Copiando .env.docker a .env..."
            cp .env.docker .env
        else
            print_warning "Archivo .env no encontrado. Creando uno básico..."
            cat > .env << EOF
# Configuración básica de Yagaruete Camp
DB_DATABASE=bd_yagaruete_camp
DB_USERNAME=root
DB_PASSWORD=root
DB_PORT=3306

# Configuración de entorno
CI_ENVIRONMENT=${ENVIRONMENT}
EOF
        fi
    fi
    
    log_verbose "Archivos de configuración verificados"
    print_success "Configuración verificada correctamente"
}

# Función principal para iniciar la aplicación
start_application() {
    print_step "Iniciando aplicación Yagaruete Camp"
    
    cd "$PROJECT_ROOT"
    
    # Construir e iniciar contenedores
    print_info "Construyendo e iniciando contenedores..."
    if [[ "$VERBOSE" == true ]]; then
        docker-compose up -d --build
    else
        docker-compose up -d --build > /dev/null 2>&1
    fi
    
    # Esperar a que los servicios estén listos
    print_info "Esperando a que los servicios estén listos..."
    sleep 10
    
    # Verificar estado de contenedores
    print_info "Verificando estado de contenedores..."
    docker-compose ps
    
    print_success "Aplicación iniciada correctamente"
    print_info "Accede a la aplicación en: http://localhost:8080"
    print_info "PHPMyAdmin disponible en: http://localhost:8081"
}

# Detener la aplicación
stop_application() {
    print_step "Deteniendo aplicación"
    
    cd "$PROJECT_ROOT"
    
    if [[ "$VERBOSE" == true ]]; then
        docker-compose down
    else
        docker-compose down > /dev/null 2>&1
    fi
    
    print_success "Aplicación detenida correctamente"
}

# Reiniciar la aplicación
restart_application() {
    print_step "Reiniciando aplicación"
    stop_application
    sleep 2
    start_application
}

# Mostrar estado de contenedores
show_status() {
    print_step "Estado de contenedores"
    
    cd "$PROJECT_ROOT"
    docker-compose ps
    
    echo -e "\n${CYAN}URLs de acceso:${NC}"
    echo "- Aplicación: http://localhost:8080"
    echo "- PHPMyAdmin: http://localhost:8081"
    echo "- MailHog: http://localhost:8025"
}

# Mostrar logs
show_logs() {
    local service="$1"
    
    cd "$PROJECT_ROOT"
    
    if [[ -n "$service" ]]; then
        print_info "Mostrando logs del servicio: $service"
        docker-compose logs -f "$service"
    else
        print_info "Mostrando logs de todos los servicios"
        docker-compose logs -f
    fi
}

# Limpiar contenedores y volúmenes
clean_environment() {
    print_step "Limpiando entorno"
    
    cd "$PROJECT_ROOT"
    
    print_warning "Esta acción eliminará todos los contenedores y volúmenes"
    read -p "¿Continuar? (y/N): " -n 1 -r
    echo
    
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        docker-compose down -v --remove-orphans
        docker system prune -f
        print_success "Entorno limpiado correctamente"
    else
        print_info "Operación cancelada"
    fi
}

# Reset completo del ambiente
reset_environment() {
    print_step "Reset completo del ambiente"
    
    cd "$PROJECT_ROOT"
    
    print_warning "Esta acción eliminará TODOS los datos y reconstruirá desde cero"
    read -p "¿Continuar? (y/N): " -n 1 -r
    echo
    
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        # Detener y limpiar
        docker-compose down -v --remove-orphans
        docker system prune -f
        
        # Reconstruir y iniciar
        docker-compose up -d --build --force-recreate
        
        # Ejecutar script de inicialización de BD si existe
        if [[ -f "scripts/setup/init-database.sh" ]]; then
            print_info "Ejecutando inicialización de base de datos..."
            bash scripts/setup/init-database.sh
        fi
        
        print_success "Reset completo finalizado"
        show_status
    else
        print_info "Operación cancelada"
    fi
}

# =============================================================================
# PROCESAMIENTO DE ARGUMENTOS
# =============================================================================

while [[ $# -gt 0 ]]; do
    case $1 in
        -v|--verbose)
            VERBOSE=true
            shift
            ;;
        -s|--skip-deps)
            SKIP_DEPENDENCIES=true
            shift
            ;;
        -e|--env)
            ENVIRONMENT="$2"
            shift 2
            ;;
        -h|--help)
            show_help
            exit 0
            ;;
        start|up)
            COMMAND="start"
            shift
            ;;
        stop|down)
            COMMAND="stop"
            shift
            ;;
        restart)
            COMMAND="restart"
            shift
            ;;
        status)
            COMMAND="status"
            shift
            ;;
        logs)
            COMMAND="logs"
            LOG_SERVICE="$2"
            shift
            if [[ -n "$2" && ! "$2" =~ ^- ]]; then
                shift
            fi
            ;;
        clean)
            COMMAND="clean"
            shift
            ;;
        reset)
            COMMAND="reset"
            shift
            ;;
        *)
            print_error "Opción desconocida: $1"
            show_help
            exit 1
            ;;
    esac
done

# =============================================================================
# EJECUCIÓN PRINCIPAL
# =============================================================================

main() {
    print_banner
    
    # Si no se especifica comando, usar start por defecto
    if [[ -z "$COMMAND" ]]; then
        COMMAND="start"
    fi
    
    log_verbose "Comando: $COMMAND"
    log_verbose "Ambiente: $ENVIRONMENT"
    log_verbose "Directorio del proyecto: $PROJECT_ROOT"
    
    # Verificar dependencias para la mayoría de comandos
    if [[ "$COMMAND" != "status" && "$COMMAND" != "logs" ]]; then
        check_dependencies
        check_config_files
    fi
    
    # Ejecutar comando
    case "$COMMAND" in
        start)
            start_application
            ;;
        stop)
            stop_application
            ;;
        restart)
            restart_application
            ;;
        status)
            show_status
            ;;
        logs)
            show_logs "$LOG_SERVICE"
            ;;
        clean)
            clean_environment
            ;;
        reset)
            reset_environment
            ;;
        *)
            print_error "Comando desconocido: $COMMAND"
            show_help
            exit 1
            ;;
    esac
}

# Ejecutar función principal
main "$@"
