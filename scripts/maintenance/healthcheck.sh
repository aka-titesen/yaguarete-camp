#!/bin/bash

# =============================================================================
# YAGARUETE CAMP - SCRIPT DE VERIFICACIÓN DE SALUD
# =============================================================================
# Descripción: Verifica el estado de todos los servicios y la conectividad
# Autor: Proyecto Martínez González
# Versión: 2.0
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

# Variables de configuración
VERBOSE=false
CHECK_ENDPOINTS=true
CHECK_DATABASE=true
TIMEOUT=10

# Servicios a verificar
declare -A SERVICES=(
    ["yagaruete_camp_app"]="9000"
    ["yagaruete_camp_nginx"]="80"
    ["yagaruete_camp_mysql"]="3306"
    ["yagaruete_camp_redis"]="6379"
    ["yagaruete_camp_phpmyadmin"]="80"
    ["yagaruete_camp_mailhog"]="1025"
)

# URLs a verificar
declare -A ENDPOINTS=(
    ["Aplicación Principal"]="http://localhost:8080"
    ["PHPMyAdmin"]="http://localhost:8081"
    ["MailHog"]="http://localhost:8025"
)

# =============================================================================
# FUNCIONES AUXILIARES
# =============================================================================

print_banner() {
    echo -e "${CYAN}"
    echo "╔══════════════════════════════════════════════════════════════╗"
    echo "║              VERIFICACIÓN DE SALUD DEL SISTEMA              ║"
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

log_verbose() {
    if [[ "$VERBOSE" == true ]]; then
        echo -e "${BLUE}[VERBOSE]${NC} $1"
    fi
}

show_help() {
    cat << EOF
Uso: $0 [OPCIONES]

DESCRIPCIÓN:
    Verifica el estado de salud de todos los servicios de Yagaruete Camp

OPCIONES:
    -v, --verbose       Modo verbose (más información)
    --skip-endpoints    No verificar endpoints HTTP
    --skip-database     No verificar conexión a base de datos
    -t, --timeout SEC   Timeout para verificaciones (default: $TIMEOUT)
    -h, --help          Mostrar esta ayuda

EJEMPLOS:
    $0                  # Verificación completa
    $0 --verbose        # Verificación con información detallada
    $0 --skip-database  # Verificar solo contenedores y endpoints

EOF
}

# Verificar estado de contenedores Docker
check_containers() {
    print_step "Verificando estado de contenedores Docker"
    
    cd "$PROJECT_ROOT"
    
    local all_healthy=true
    local total_services=${#SERVICES[@]}
    local healthy_services=0
    
    for container in "${!SERVICES[@]}"; do
        local port="${SERVICES[$container]}"
        echo -n "🔍 Verificando $container:$port... "
        
        if docker ps --filter "name=$container" --filter "status=running" --format "table {{.Names}}" | grep -q "^$container$"; then
            echo -e "${GREEN}✅ RUNNING${NC}"
            ((healthy_services++))
            
            if [[ "$VERBOSE" == true ]]; then
                local uptime=$(docker inspect --format='{{.State.StartedAt}}' "$container" 2>/dev/null || echo "N/A")
                log_verbose "  Iniciado: $uptime"
                
                local cpu_usage=$(docker stats --no-stream --format "table {{.CPUPerc}}" "$container" 2>/dev/null | tail -n 1 || echo "N/A")
                local mem_usage=$(docker stats --no-stream --format "table {{.MemUsage}}" "$container" 2>/dev/null | tail -n 1 || echo "N/A")
                log_verbose "  CPU: $cpu_usage, Memoria: $mem_usage"
            fi
        else
            echo -e "${RED}❌ NOT RUNNING${NC}"
            all_healthy=false
            
            # Verificar si el contenedor existe pero está parado
            if docker ps -a --filter "name=$container" --format "table {{.Names}}" | grep -q "^$container$"; then
                local status=$(docker inspect --format='{{.State.Status}}' "$container" 2>/dev/null || echo "unknown")
                print_warning "  Estado: $status"
            else
                print_warning "  Contenedor no encontrado"
            fi
        fi
    done
    
    echo ""
    if [[ $all_healthy == true ]]; then
        print_success "Todos los contenedores están funcionando ($healthy_services/$total_services)"
    else
        print_error "Algunos contenedores no están funcionando ($healthy_services/$total_services)"
        return 1
    fi
}

# Verificar endpoints HTTP
check_endpoints() {
    if [[ "$CHECK_ENDPOINTS" == false ]]; then
        print_warning "Saltando verificación de endpoints HTTP"
        return 0
    fi
    
    print_step "Verificando endpoints HTTP"
    
    local all_accessible=true
    local total_endpoints=${#ENDPOINTS[@]}
    local accessible_endpoints=0
    
    for service in "${!ENDPOINTS[@]}"; do
        local url="${ENDPOINTS[$service]}"
        echo -n "🌐 Verificando $service ($url)... "
        
        if curl -s --max-time "$TIMEOUT" --fail "$url" >/dev/null 2>&1; then
            echo -e "${GREEN}✅ ACCESSIBLE${NC}"
            ((accessible_endpoints++))
            
            if [[ "$VERBOSE" == true ]]; then
                local response_time=$(curl -s --max-time "$TIMEOUT" -w "%{time_total}" -o /dev/null "$url" 2>/dev/null || echo "N/A")
                log_verbose "  Tiempo de respuesta: ${response_time}s"
            fi
        else
            echo -e "${RED}❌ NOT ACCESSIBLE${NC}"
            all_accessible=false
            
            if [[ "$VERBOSE" == true ]]; then
                local http_code=$(curl -s --max-time "$TIMEOUT" -w "%{http_code}" -o /dev/null "$url" 2>/dev/null || echo "000")
                log_verbose "  Código HTTP: $http_code"
            fi
        fi
    done
    
    echo ""
    if [[ $all_accessible == true ]]; then
        print_success "Todos los endpoints están accesibles ($accessible_endpoints/$total_endpoints)"
    else
        print_error "Algunos endpoints no están accesibles ($accessible_endpoints/$total_endpoints)"
        return 1
    fi
}

# Verificar conexión a base de datos
check_database() {
    if [[ "$CHECK_DATABASE" == false ]]; then
        print_warning "Saltando verificación de base de datos"
        return 0
    fi
    
    print_step "Verificando conexión a base de datos"
    
    cd "$PROJECT_ROOT"
    
    echo -n "🗄️  Verificando conexión MySQL... "
    
    if docker-compose exec -T app bash -c "cd /var/www/html && php spark db:table --show-headers=false usuarios" >/dev/null 2>&1; then
        echo -e "${GREEN}✅ CONNECTED${NC}"
        
        if [[ "$VERBOSE" == true ]]; then
            # Obtener información adicional de la BD
            local db_version=$(docker-compose exec -T mysql mysql --version 2>/dev/null | head -n 1 || echo "N/A")
            log_verbose "  Versión MySQL: $db_version"
            
            local table_count=$(docker-compose exec -T app bash -c "cd /var/www/html && php spark db:table --list" 2>/dev/null | wc -l || echo "N/A")
            log_verbose "  Tablas en BD: $table_count"
        fi
        
        print_success "Conexión a base de datos exitosa"
    else
        echo -e "${RED}❌ CONNECTION FAILED${NC}"
        print_error "No se pudo conectar a la base de datos"
        return 1
    fi
}

# Verificar recursos del sistema
check_system_resources() {
    if [[ "$VERBOSE" == false ]]; then
        return 0
    fi
    
    print_step "Verificando recursos del sistema"
    
    # Verificar espacio en disco
    local disk_usage=$(df -h . | tail -n 1 | awk '{print $5}' | sed 's/%//')
    echo "💾 Uso de disco: ${disk_usage}%"
    
    if [[ $disk_usage -gt 90 ]]; then
        print_warning "Espacio en disco bajo (${disk_usage}%)"
    fi
    
    # Verificar memoria Docker
    local docker_stats=$(docker system df --format "table {{.Type}}\t{{.Size}}" 2>/dev/null | tail -n +2)
    if [[ -n "$docker_stats" ]]; then
        echo "🐳 Uso de Docker:"
        echo "$docker_stats" | while read -r line; do
            echo "   $line"
        done
    fi
}

# Generar resumen final
generate_summary() {
    print_step "Resumen de verificación de salud"
    
    local total_checks=0
    local passed_checks=0
    
    # Contar verificaciones realizadas
    ((total_checks++))
    if check_containers >/dev/null 2>&1; then
        ((passed_checks++))
    fi
    
    if [[ "$CHECK_ENDPOINTS" == true ]]; then
        ((total_checks++))
        if check_endpoints >/dev/null 2>&1; then
            ((passed_checks++))
        fi
    fi
    
    if [[ "$CHECK_DATABASE" == true ]]; then
        ((total_checks++))
        if check_database >/dev/null 2>&1; then
            ((passed_checks++))
        fi
    fi
    
    echo "📊 Resultado: $passed_checks/$total_checks verificaciones exitosas"
    
    if [[ $passed_checks -eq $total_checks ]]; then
        echo -e "\n${GREEN}🎉 SISTEMA COMPLETAMENTE SALUDABLE${NC}"
        echo "Todos los servicios están funcionando correctamente"
        
        echo -e "\n${CYAN}🌐 URLs disponibles:${NC}"
        for service in "${!ENDPOINTS[@]}"; do
            echo "   - $service: ${ENDPOINTS[$service]}"
        done
        
        return 0
    else
        echo -e "\n${RED}⚠️  SISTEMA CON PROBLEMAS${NC}"
        echo "Algunos servicios requieren atención"
        
        echo -e "\n${YELLOW}💡 Sugerencias:${NC}"
        echo "   - Ejecuta: scripts/setup/deploy.sh restart"
        echo "   - Verifica logs: scripts/setup/deploy.sh logs"
        echo "   - Para reset completo: scripts/setup/deploy.sh reset"
        
        return 1
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
        --skip-endpoints)
            CHECK_ENDPOINTS=false
            shift
            ;;
        --skip-database)
            CHECK_DATABASE=false
            shift
            ;;
        -t|--timeout)
            TIMEOUT="$2"
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
    
    log_verbose "Configuración:"
    log_verbose "- Proyecto: $PROJECT_ROOT"
    log_verbose "- Timeout: ${TIMEOUT}s"
    log_verbose "- Check endpoints: $CHECK_ENDPOINTS"
    log_verbose "- Check database: $CHECK_DATABASE"
    
    local exit_code=0
    
    # Ejecutar verificaciones
    check_containers || exit_code=1
    check_endpoints || exit_code=1
    check_database || exit_code=1
    check_system_resources
    
    # Generar resumen
    generate_summary || exit_code=1
    
    exit $exit_code
}

# Ejecutar función principal
main "$@"
