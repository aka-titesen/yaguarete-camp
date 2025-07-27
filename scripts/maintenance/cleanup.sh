#!/bin/bash

# =============================================================================
# YAGARUETE CAMP - SCRIPT DE LIMPIEZA DEL SISTEMA
# =============================================================================
# Descripción: Limpia logs, cache, archivos temporales y recursos Docker
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
DRY_RUN=false
CLEAN_DOCKER=false
CLEAN_LOGS=true
CLEAN_CACHE=true
CLEAN_UPLOADS=false
CLEAN_VENDOR=false

# =============================================================================
# FUNCIONES AUXILIARES
# =============================================================================

print_banner() {
    echo -e "${CYAN}"
    echo "╔══════════════════════════════════════════════════════════════╗"
    echo "║                  LIMPIEZA DEL SISTEMA                        ║"
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
    Limpia archivos temporales, logs, cache y recursos innecesarios del proyecto

OPCIONES:
    -v, --verbose       Modo verbose (más información)
    -n, --dry-run       Mostrar qué se haría sin ejecutar
    --docker            Incluir limpieza de recursos Docker
    --no-logs           No limpiar archivos de log
    --no-cache          No limpiar archivos de cache
    --uploads           Incluir limpieza de uploads (¡CUIDADO!)
    --vendor            Incluir limpieza de vendor (requiere reinstalar)
    -h, --help          Mostrar esta ayuda

EJEMPLOS:
    $0                  # Limpieza estándar (logs y cache)
    $0 --docker         # Incluir limpieza Docker
    $0 --dry-run        # Ver qué se limpiaría
    $0 --vendor         # Limpieza completa incluyendo vendor

ADVERTENCIAS:
    --uploads : Eliminará TODOS los archivos subidos por usuarios
    --vendor  : Requerirá ejecutar 'composer install' después
    --docker  : Eliminará contenedores, imágenes y volúmenes no utilizados

EOF
}

# Función para obtener tamaño de directorio
get_dir_size() {
    local dir="$1"
    if [[ -d "$dir" ]]; then
        du -sh "$dir" 2>/dev/null | cut -f1 || echo "0B"
    else
        echo "0B"
    fi
}

# Función para eliminar archivos/directorios
safe_remove() {
    local target="$1"
    local description="$2"
    
    if [[ ! -e "$target" ]]; then
        log_verbose "  $description: No existe"
        return 0
    fi
    
    local size=$(get_dir_size "$target")
    
    if [[ "$DRY_RUN" == true ]]; then
        echo "  [DRY-RUN] Eliminaría $description ($size)"
        return 0
    fi
    
    log_verbose "  Eliminando $description ($size)..."
    
    if [[ -d "$target" ]]; then
        rm -rf "$target"
    else
        rm -f "$target"
    fi
    
    if [[ $? -eq 0 ]]; then
        echo "  ✅ $description eliminado ($size)"
    else
        echo "  ❌ Error eliminando $description"
        return 1
    fi
}

# Limpiar logs
clean_logs() {
    if [[ "$CLEAN_LOGS" == false ]]; then
        print_warning "Saltando limpieza de logs"
        return 0
    fi
    
    print_step "Limpiando archivos de log"
    
    cd "$PROJECT_ROOT"
    
    local cleaned=0
    
    # Logs de CodeIgniter
    if [[ -d "writable/logs" ]]; then
        local log_files=$(find writable/logs -name "*.log" -type f 2>/dev/null | wc -l)
        if [[ $log_files -gt 0 ]]; then
            if [[ "$DRY_RUN" == true ]]; then
                echo "  [DRY-RUN] Eliminaría $log_files archivos de log"
            else
                find writable/logs -name "*.log" -type f -delete 2>/dev/null
                echo "  ✅ $log_files archivos de log eliminados"
            fi
            ((cleaned++))
        fi
    fi
    
    # Logs de DebugBar
    safe_remove "writable/debugbar" "Logs de DebugBar"
    [[ $? -eq 0 ]] && ((cleaned++))
    
    if [[ $cleaned -eq 0 ]]; then
        print_info "No hay logs para limpiar"
    else
        print_success "Limpieza de logs completada"
    fi
}

# Limpiar cache
clean_cache() {
    if [[ "$CLEAN_CACHE" == false ]]; then
        print_warning "Saltando limpieza de cache"
        return 0
    fi
    
    print_step "Limpiando archivos de cache"
    
    cd "$PROJECT_ROOT"
    
    local cleaned=0
    
    # Cache de CodeIgniter
    safe_remove "writable/cache" "Cache de CodeIgniter"
    [[ $? -eq 0 ]] && ((cleaned++))
    
    # Cache de sesiones
    if [[ -d "writable/session" ]]; then
        local session_files=$(find writable/session -name "ci_session*" -type f 2>/dev/null | wc -l)
        if [[ $session_files -gt 0 ]]; then
            if [[ "$DRY_RUN" == true ]]; then
                echo "  [DRY-RUN] Eliminaría $session_files archivos de sesión"
            else
                find writable/session -name "ci_session*" -type f -delete 2>/dev/null
                echo "  ✅ $session_files archivos de sesión eliminados"
            fi
            ((cleaned++))
        fi
    fi
    
    # Cache de Composer
    if command -v composer >/dev/null 2>&1; then
        if [[ "$DRY_RUN" == true ]]; then
            echo "  [DRY-RUN] Limpiaría cache de Composer"
        else
            log_verbose "  Limpiando cache de Composer..."
            composer clear-cache >/dev/null 2>&1 && echo "  ✅ Cache de Composer limpiado"
        fi
        ((cleaned++))
    fi
    
    if [[ $cleaned -eq 0 ]]; then
        print_info "No hay cache para limpiar"
    else
        print_success "Limpieza de cache completada"
    fi
}

# Limpiar uploads (CUIDADO)
clean_uploads() {
    if [[ "$CLEAN_UPLOADS" == false ]]; then
        print_warning "Saltando limpieza de uploads (usar --uploads para incluir)"
        return 0
    fi
    
    print_step "Limpiando archivos subidos (⚠️  PELIGROSO)"
    
    cd "$PROJECT_ROOT"
    
    if [[ "$DRY_RUN" == false ]]; then
        echo -e "${RED}¡ADVERTENCIA!${NC} Esto eliminará TODOS los archivos subidos por usuarios"
        echo -n "¿Estás seguro? (escriba 'YES' para continuar): "
        read -r confirmation
        if [[ "$confirmation" != "YES" ]]; then
            print_info "Limpieza de uploads cancelada"
            return 0
        fi
    fi
    
    safe_remove "writable/uploads" "Archivos subidos"
    
    # Recrear directorio
    if [[ "$DRY_RUN" == false ]]; then
        mkdir -p writable/uploads
        echo "index.html" > writable/uploads/index.html
        print_info "Directorio uploads recreado"
    fi
}

# Limpiar vendor
clean_vendor() {
    if [[ "$CLEAN_VENDOR" == false ]]; then
        print_warning "Saltando limpieza de vendor (usar --vendor para incluir)"
        return 0
    fi
    
    print_step "Limpiando dependencias de Composer (⚠️  REQUIERE REINSTALAR)"
    
    cd "$PROJECT_ROOT"
    
    if [[ "$DRY_RUN" == false ]]; then
        echo -e "${RED}¡ADVERTENCIA!${NC} Esto eliminará todas las dependencias de Composer"
        echo "Deberás ejecutar 'composer install' después"
        echo -n "¿Estás seguro? (escriba 'YES' para continuar): "
        read -r confirmation
        if [[ "$confirmation" != "YES" ]]; then
            print_info "Limpieza de vendor cancelada"
            return 0
        fi
    fi
    
    safe_remove "vendor" "Dependencias de Composer"
    safe_remove "composer.lock" "Lock file de Composer"
}

# Limpiar recursos Docker
clean_docker() {
    if [[ "$CLEAN_DOCKER" == false ]]; then
        print_warning "Saltando limpieza de Docker (usar --docker para incluir)"
        return 0
    fi
    
    print_step "Limpiando recursos de Docker"
    
    if ! command -v docker >/dev/null 2>&1; then
        print_error "Docker no está instalado"
        return 1
    fi
    
    cd "$PROJECT_ROOT"
    
    # Parar contenedores del proyecto
    if [[ -f "docker-compose.yml" ]]; then
        if [[ "$DRY_RUN" == true ]]; then
            echo "  [DRY-RUN] Pararía contenedores del proyecto"
        else
            log_verbose "  Parando contenedores del proyecto..."
            docker-compose down >/dev/null 2>&1 && echo "  ✅ Contenedores parados"
        fi
    fi
    
    # Limpiar contenedores parados
    local stopped_containers=$(docker ps -aq --filter "status=exited" 2>/dev/null | wc -l)
    if [[ $stopped_containers -gt 0 ]]; then
        if [[ "$DRY_RUN" == true ]]; then
            echo "  [DRY-RUN] Eliminaría $stopped_containers contenedores parados"
        else
            docker container prune -f >/dev/null 2>&1 && echo "  ✅ $stopped_containers contenedores parados eliminados"
        fi
    fi
    
    # Limpiar imágenes sin usar
    local dangling_images=$(docker images -q --filter "dangling=true" 2>/dev/null | wc -l)
    if [[ $dangling_images -gt 0 ]]; then
        if [[ "$DRY_RUN" == true ]]; then
            echo "  [DRY-RUN] Eliminaría $dangling_images imágenes sin usar"
        else
            docker image prune -f >/dev/null 2>&1 && echo "  ✅ $dangling_images imágenes sin usar eliminadas"
        fi
    fi
    
    # Limpiar volúmenes sin usar
    local unused_volumes=$(docker volume ls -q --filter "dangling=true" 2>/dev/null | wc -l)
    if [[ $unused_volumes -gt 0 ]]; then
        if [[ "$DRY_RUN" == true ]]; then
            echo "  [DRY-RUN] Eliminaría $unused_volumes volúmenes sin usar"
        else
            docker volume prune -f >/dev/null 2>&1 && echo "  ✅ $unused_volumes volúmenes sin usar eliminados"
        fi
    fi
    
    # Limpiar redes sin usar
    if [[ "$DRY_RUN" == true ]]; then
        echo "  [DRY-RUN] Limpiaría redes sin usar"
    else
        docker network prune -f >/dev/null 2>&1 && echo "  ✅ Redes sin usar eliminadas"
    fi
    
    print_success "Limpieza de Docker completada"
}

# Limpiar archivos temporales del sistema
clean_temp_files() {
    print_step "Limpiando archivos temporales del sistema"
    
    cd "$PROJECT_ROOT"
    
    local cleaned=0
    
    # Archivos .tmp
    local tmp_files=$(find . -name "*.tmp" -type f 2>/dev/null | wc -l)
    if [[ $tmp_files -gt 0 ]]; then
        if [[ "$DRY_RUN" == true ]]; then
            echo "  [DRY-RUN] Eliminaría $tmp_files archivos .tmp"
        else
            find . -name "*.tmp" -type f -delete 2>/dev/null
            echo "  ✅ $tmp_files archivos .tmp eliminados"
        fi
        ((cleaned++))
    fi
    
    # Archivos .bak
    local bak_files=$(find . -name "*.bak" -type f 2>/dev/null | wc -l)
    if [[ $bak_files -gt 0 ]]; then
        if [[ "$DRY_RUN" == true ]]; then
            echo "  [DRY-RUN] Eliminaría $bak_files archivos .bak"
        else
            find . -name "*.bak" -type f -delete 2>/dev/null
            echo "  ✅ $bak_files archivos .bak eliminados"
        fi
        ((cleaned++))
    fi
    
    # Archivos de editor
    local editor_files=$(find . \( -name "*~" -o -name "*.swp" -o -name "*.swo" \) -type f 2>/dev/null | wc -l)
    if [[ $editor_files -gt 0 ]]; then
        if [[ "$DRY_RUN" == true ]]; then
            echo "  [DRY-RUN] Eliminaría $editor_files archivos de editor"
        else
            find . \( -name "*~" -o -name "*.swp" -o -name "*.swo" \) -type f -delete 2>/dev/null
            echo "  ✅ $editor_files archivos de editor eliminados"
        fi
        ((cleaned++))
    fi
    
    if [[ $cleaned -eq 0 ]]; then
        print_info "No hay archivos temporales para limpiar"
    else
        print_success "Limpieza de archivos temporales completada"
    fi
}

# Mostrar estadísticas de espacio
show_space_stats() {
    print_step "Estadísticas de espacio en disco"
    
    cd "$PROJECT_ROOT"
    
    echo "📊 Uso de espacio por directorio:"
    
    local dirs=("writable/logs" "writable/cache" "writable/session" "writable/uploads" "vendor" "node_modules")
    
    for dir in "${dirs[@]}"; do
        if [[ -d "$dir" ]]; then
            local size=$(get_dir_size "$dir")
            echo "   $dir: $size"
        fi
    done
    
    if command -v docker >/dev/null 2>&1; then
        echo ""
        echo "🐳 Uso de Docker:"
        if [[ "$VERBOSE" == true ]]; then
            docker system df 2>/dev/null || print_warning "No se pudo obtener información de Docker"
        else
            docker system df --format "table {{.Type}}\t{{.Size}}" 2>/dev/null | tail -n +2 | while read -r line; do
                echo "   $line"
            done
        fi
    fi
}

# Generar resumen
generate_summary() {
    print_step "Resumen de limpieza"
    
    if [[ "$DRY_RUN" == true ]]; then
        echo "🔍 MODO DRY-RUN - No se eliminó nada realmente"
        echo "Ejecuta sin --dry-run para realizar la limpieza"
    else
        echo "🧹 Limpieza completada exitosamente"
    fi
    
    echo ""
    echo "📋 Operaciones realizadas:"
    echo "   - Logs: $([ "$CLEAN_LOGS" == true ] && echo "✅" || echo "❌")"
    echo "   - Cache: $([ "$CLEAN_CACHE" == true ] && echo "✅" || echo "❌")"
    echo "   - Uploads: $([ "$CLEAN_UPLOADS" == true ] && echo "✅" || echo "❌")"
    echo "   - Vendor: $([ "$CLEAN_VENDOR" == true ] && echo "✅" || echo "❌")"
    echo "   - Docker: $([ "$CLEAN_DOCKER" == true ] && echo "✅" || echo "❌")"
    
    if [[ "$CLEAN_VENDOR" == true && "$DRY_RUN" == false ]]; then
        echo ""
        echo -e "${YELLOW}⚠️  IMPORTANTE:${NC} Debes ejecutar 'composer install' para restaurar las dependencias"
    fi
    
    if [[ "$CLEAN_UPLOADS" == true && "$DRY_RUN" == false ]]; then
        echo -e "${YELLOW}⚠️  IMPORTANTE:${NC} Todos los archivos subidos han sido eliminados"
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
        -n|--dry-run)
            DRY_RUN=true
            shift
            ;;
        --docker)
            CLEAN_DOCKER=true
            shift
            ;;
        --no-logs)
            CLEAN_LOGS=false
            shift
            ;;
        --no-cache)
            CLEAN_CACHE=false
            shift
            ;;
        --uploads)
            CLEAN_UPLOADS=true
            shift
            ;;
        --vendor)
            CLEAN_VENDOR=true
            shift
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
    log_verbose "- Dry run: $DRY_RUN"
    log_verbose "- Limpiar logs: $CLEAN_LOGS"
    log_verbose "- Limpiar cache: $CLEAN_CACHE"
    log_verbose "- Limpiar uploads: $CLEAN_UPLOADS"
    log_verbose "- Limpiar vendor: $CLEAN_VENDOR"
    log_verbose "- Limpiar Docker: $CLEAN_DOCKER"
    
    # Mostrar estadísticas antes
    if [[ "$VERBOSE" == true ]]; then
        show_space_stats
    fi
    
    # Ejecutar limpieza
    clean_logs
    clean_cache
    clean_temp_files
    clean_uploads
    clean_vendor
    clean_docker
    
    # Mostrar estadísticas después
    if [[ "$VERBOSE" == true && "$DRY_RUN" == false ]]; then
        show_space_stats
    fi
    
    generate_summary
}

# Ejecutar función principal
main "$@"
