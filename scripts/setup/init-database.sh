#!/bin/bash

# =============================================================================
# YAGARUETE CAMP - SCRIPT DE INICIALIZACIÓN DE BASE DE DATOS
# =============================================================================
# Descripción: Ejecuta migraciones y seeders para inicializar la base de datos
# Autor: Proyecto Martínez González
# Versión: 1.0
# =============================================================================

set -e

# Configuración de colores
readonly RED='\033[0;31m'
readonly GREEN='\033[0;32m'
readonly YELLOW='\033[1;33m'
readonly BLUE='\033[0;34m'
readonly CYAN='\033[0;36m'
readonly NC='\033[0m'

# Configuración del proyecto
readonly SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
readonly PROJECT_ROOT="$(cd "${SCRIPT_DIR}/../.." && pwd)"

# Variables
FORCE_RESET=false
SKIP_SEEDERS=false
CONTAINER_NAME="yagaruete_camp_app"

# =============================================================================
# FUNCIONES AUXILIARES
# =============================================================================

print_banner() {
    echo -e "${CYAN}"
    echo "╔══════════════════════════════════════════════════════════════╗"
    echo "║              INICIALIZACIÓN DE BASE DE DATOS                ║"
    echo "║                    Yagaruete Camp                            ║"
    echo "╚══════════════════════════════════════════════════════════════╝"
    echo -e "${NC}"
}

print_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
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

show_help() {
    cat << EOF
Uso: $0 [OPCIONES]

DESCRIPCIÓN:
    Inicializa la base de datos ejecutando migraciones y seeders

OPCIONES:
    -f, --force         Forzar reset completo de la base de datos
    -s, --skip-seeders  Ejecutar solo migraciones, saltar seeders
    -c, --container     Nombre del contenedor PHP (default: $CONTAINER_NAME)
    -h, --help          Mostrar esta ayuda

EJEMPLOS:
    $0                  # Inicialización normal
    $0 --force          # Reset completo de la BD
    $0 --skip-seeders   # Solo migraciones, sin datos de ejemplo

EOF
}

# Verificar que Docker Compose esté funcionando
check_docker_environment() {
    print_step "Verificando entorno Docker"
    
    cd "$PROJECT_ROOT"
    
    # Verificar que docker-compose.yml existe
    if [[ ! -f "docker-compose.yml" ]]; then
        print_error "docker-compose.yml no encontrado en $PROJECT_ROOT"
        exit 1
    fi
    
    # Verificar que el contenedor PHP está corriendo
    if ! docker-compose ps | grep -q "$CONTAINER_NAME.*Up"; then
        print_error "El contenedor $CONTAINER_NAME no está corriendo"
        print_info "Ejecuta primero: scripts/setup/deploy.sh start"
        exit 1
    fi
    
    print_success "Entorno Docker verificado"
}

# Esperar a que la base de datos esté lista
wait_for_database() {
    print_step "Esperando a que la base de datos esté lista"
    
    local max_attempts=30
    local attempt=1
    
    while [[ $attempt -le $max_attempts ]]; do
        print_info "Intento $attempt/$max_attempts - Verificando conexión a la base de datos..."
        
        if docker-compose exec -T app bash -c "cd /var/www/html && php spark db:table --show-headers=false usuarios" &>/dev/null; then
            print_success "Base de datos está lista"
            return 0
        fi
        
        if [[ $attempt -eq $max_attempts ]]; then
            print_error "La base de datos no está disponible después de $max_attempts intentos"
            exit 1
        fi
        
        sleep 2
        ((attempt++))
    done
}

# Ejecutar migraciones
run_migrations() {
    print_step "Ejecutando migraciones de base de datos"
    
    cd "$PROJECT_ROOT"
    
    if [[ "$FORCE_RESET" == true ]]; then
        print_warning "Ejecutando rollback completo de migraciones..."
        docker-compose exec -T app bash -c "cd /var/www/html && php spark migrate:rollback -all" || true
    fi
    
    print_info "Ejecutando migraciones..."
    if docker-compose exec -T app bash -c "cd /var/www/html && php spark migrate"; then
        print_success "Migraciones ejecutadas correctamente"
    else
        print_error "Error al ejecutar migraciones"
        exit 1
    fi
}

# Ejecutar seeders
run_seeders() {
    if [[ "$SKIP_SEEDERS" == true ]]; then
        print_warning "Saltando ejecución de seeders"
        return 0
    fi
    
    print_step "Ejecutando seeders de base de datos"
    
    cd "$PROJECT_ROOT"
    
    print_info "Ejecutando DatabaseSeeder (todos los seeders)..."
    if docker-compose exec -T app bash -c "cd /var/www/html && php spark db:seed DatabaseSeeder"; then
        print_success "Seeders ejecutados correctamente"
    else
        print_error "Error al ejecutar seeders"
        exit 1
    fi
}

# Verificar el estado final de la base de datos
verify_database_state() {
    print_step "Verificando estado final de la base de datos"
    
    cd "$PROJECT_ROOT"
    
    print_info "Verificando tablas y datos..."
    
    # Verificar migraciones
    if docker-compose exec -T app bash -c "cd /var/www/html && php spark migrate:status" | grep -q "Up"; then
        print_success "✓ Migraciones aplicadas correctamente"
    else
        print_warning "⚠ Algunas migraciones pueden no estar aplicadas"
    fi
    
    # Verificar datos si no se saltaron los seeders
    if [[ "$SKIP_SEEDERS" == false ]]; then
        # Verificar usuarios
        local user_count=$(docker-compose exec -T app bash -c "cd /var/www/html && php spark db:table usuarios --show-headers=false" | wc -l)
        if [[ $user_count -gt 0 ]]; then
            print_success "✓ Usuarios creados: $user_count"
        else
            print_warning "⚠ No se encontraron usuarios en la base de datos"
        fi
        
        # Verificar productos
        local product_count=$(docker-compose exec -T app bash -c "cd /var/www/html && php spark db:table productos --show-headers=false" | wc -l)
        if [[ $product_count -gt 0 ]]; then
            print_success "✓ Productos creados: $product_count"
        else
            print_warning "⚠ No se encontraron productos en la base de datos"
        fi
    fi
    
    echo -e "\n${GREEN}🎉 INICIALIZACIÓN DE BASE DE DATOS COMPLETADA${NC}"
    
    if [[ "$SKIP_SEEDERS" == false ]]; then
        echo -e "\n${CYAN}🔑 Credenciales de administrador:${NC}"
        echo "   Email: admin@yagaruete.com"
        echo "   Contraseña: Admin123!"
        echo -e "\n${CYAN}🌐 URLs de acceso:${NC}"
        echo "   - Aplicación: http://localhost:8080"
        echo "   - PHPMyAdmin: http://localhost:8081"
    fi
}

# =============================================================================
# PROCESAMIENTO DE ARGUMENTOS
# =============================================================================

while [[ $# -gt 0 ]]; do
    case $1 in
        -f|--force)
            FORCE_RESET=true
            shift
            ;;
        -s|--skip-seeders)
            SKIP_SEEDERS=true
            shift
            ;;
        -c|--container)
            CONTAINER_NAME="$2"
            shift 2
            ;;
        -h|--help)
            show_help
            exit 0
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
    
    print_info "Configuración:"
    print_info "- Proyecto: $PROJECT_ROOT"
    print_info "- Contenedor: $CONTAINER_NAME"
    print_info "- Force reset: $FORCE_RESET"
    print_info "- Skip seeders: $SKIP_SEEDERS"
    
    # Verificar entorno
    check_docker_environment
    
    # Esperar a que la BD esté lista
    wait_for_database
    
    # Ejecutar migraciones
    run_migrations
    
    # Ejecutar seeders
    run_seeders
    
    # Verificar estado final
    verify_database_state
}

# Ejecutar función principal
main "$@"
