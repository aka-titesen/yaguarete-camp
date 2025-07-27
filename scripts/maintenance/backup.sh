#!/bin/bash

# =============================================================================
# YAGARUETE CAMP - SCRIPT DE BACKUP DE BASE DE DATOS
# =============================================================================
# Descripción: Crea backups automáticos de la base de datos MySQL
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
readonly BACKUP_DIR="$PROJECT_ROOT/backups"

# Variables de configuración
VERBOSE=false
COMPRESS=true
KEEP_DAYS=30
INCLUDE_DATA=true
INCLUDE_STRUCTURE=true
CUSTOM_NAME=""
LIST_BACKUPS=false
RESTORE_FILE=""

# Configuración de base de datos (desde docker-compose)
DB_CONTAINER="yagaruete_camp_mysql"
DB_NAME="yagaruete_camp"
DB_USER="yagaruete_user"
DB_PASSWORD="yagaruete_password"

# =============================================================================
# FUNCIONES AUXILIARES
# =============================================================================

print_banner() {
    echo -e "${CYAN}"
    echo "╔══════════════════════════════════════════════════════════════╗"
    echo "║                  BACKUP DE BASE DE DATOS                    ║"
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
    Crea backups de la base de datos MySQL del proyecto

OPCIONES:
    -v, --verbose           Modo verbose (más información)
    -n, --name NAME         Nombre personalizado para el backup
    --no-compress           No comprimir el backup
    --no-data               Solo estructura (sin datos)
    --no-structure          Solo datos (sin estructura)
    --keep-days DAYS        Días para mantener backups (default: $KEEP_DAYS)
    -l, --list              Listar backups existentes
    -r, --restore FILE      Restaurar desde backup
    -h, --help              Mostrar esta ayuda

EJEMPLOS:
    $0                      # Backup completo con timestamp
    $0 --name "pre-update"  # Backup con nombre personalizado
    $0 --no-data            # Solo estructura de BD
    $0 --list               # Listar backups
    $0 --restore backup.sql # Restaurar backup

ARCHIVOS:
    Los backups se guardan en: $BACKUP_DIR
    Formato: yagaruete_camp_YYYY-MM-DD_HH-MM-SS.sql[.gz]

EOF
}

# Verificar dependencias
check_dependencies() {
    log_verbose "Verificando dependencias..."
    
    if ! command -v docker >/dev/null 2>&1; then
        print_error "Docker no está instalado"
        exit 1
    fi
    
    if ! docker ps --filter "name=$DB_CONTAINER" --filter "status=running" --format "table {{.Names}}" | grep -q "^$DB_CONTAINER$"; then
        print_error "El contenedor de base de datos no está ejecutándose: $DB_CONTAINER"
        print_info "Inicia el proyecto con: scripts/setup/deploy.sh start"
        exit 1
    fi
    
    # Crear directorio de backups si no existe
    mkdir -p "$BACKUP_DIR"
    
    log_verbose "✅ Dependencias verificadas"
}

# Generar nombre de archivo
generate_filename() {
    local timestamp=$(date '+%Y-%m-%d_%H-%M-%S')
    local base_name="yagaruete_camp"
    
    if [[ -n "$CUSTOM_NAME" ]]; then
        base_name="${base_name}_${CUSTOM_NAME}"
    else
        base_name="${base_name}_${timestamp}"
    fi
    
    local extension=".sql"
    if [[ "$COMPRESS" == true ]]; then
        extension=".sql.gz"
    fi
    
    echo "${base_name}${extension}"
}

# Crear backup
create_backup() {
    print_step "Creando backup de base de datos"
    
    local filename=$(generate_filename)
    local filepath="$BACKUP_DIR/$filename"
    
    print_info "Archivo: $filename"
    
    # Construir opciones de mysqldump
    local dump_options="--single-transaction --routines --triggers"
    
    if [[ "$INCLUDE_STRUCTURE" == false ]]; then
        dump_options="$dump_options --no-create-info"
        log_verbose "Solo datos (sin estructura)"
    fi
    
    if [[ "$INCLUDE_DATA" == false ]]; then
        dump_options="$dump_options --no-data"
        log_verbose "Solo estructura (sin datos)"
    fi
    
    log_verbose "Opciones mysqldump: $dump_options"
    
    # Ejecutar backup
    echo -n "📦 Creando backup... "
    
    if [[ "$COMPRESS" == true ]]; then
        if docker exec "$DB_CONTAINER" mysqldump -u"$DB_USER" -p"$DB_PASSWORD" $dump_options "$DB_NAME" 2>/dev/null | gzip > "$filepath"; then
            echo -e "${GREEN}✅ COMPLETADO${NC}"
        else
            echo -e "${RED}❌ ERROR${NC}"
            print_error "Falló la creación del backup"
            return 1
        fi
    else
        if docker exec "$DB_CONTAINER" mysqldump -u"$DB_USER" -p"$DB_PASSWORD" $dump_options "$DB_NAME" > "$filepath" 2>/dev/null; then
            echo -e "${GREEN}✅ COMPLETADO${NC}"
        else
            echo -e "${RED}❌ ERROR${NC}"
            print_error "Falló la creación del backup"
            return 1
        fi
    fi
    
    # Verificar archivo creado
    if [[ -f "$filepath" ]]; then
        local size=$(du -h "$filepath" | cut -f1)
        print_success "Backup creado: $filename ($size)"
        
        if [[ "$VERBOSE" == true ]]; then
            log_verbose "Ruta completa: $filepath"
            log_verbose "Tamaño: $size"
            log_verbose "Comprimido: $COMPRESS"
        fi
    else
        print_error "El archivo de backup no se creó correctamente"
        return 1
    fi
}

# Listar backups
list_backups() {
    print_step "Listando backups existentes"
    
    if [[ ! -d "$BACKUP_DIR" ]]; then
        print_info "No existe directorio de backups: $BACKUP_DIR"
        return 0
    fi
    
    local backups=($(find "$BACKUP_DIR" -name "yagaruete_camp_*.sql*" -type f 2>/dev/null | sort -r))
    
    if [[ ${#backups[@]} -eq 0 ]]; then
        print_info "No se encontraron backups"
        return 0
    fi
    
    echo "📋 Backups encontrados (${#backups[@]}):"
    echo ""
    
    for backup in "${backups[@]}"; do
        local filename=$(basename "$backup")
        local size=$(du -h "$backup" | cut -f1)
        local date=$(stat -c %y "$backup" 2>/dev/null | cut -d' ' -f1,2 | cut -d'.' -f1 || echo "N/A")
        
        echo "   📦 $filename"
        echo "      Tamaño: $size"
        echo "      Fecha: $date"
        echo ""
    done
    
    # Mostrar espacio total
    if command -v du >/dev/null 2>&1; then
        local total_size=$(du -sh "$BACKUP_DIR" 2>/dev/null | cut -f1 || echo "N/A")
        print_info "Espacio total usado por backups: $total_size"
    fi
}

# Restaurar backup
restore_backup() {
    local backup_file="$1"
    
    print_step "Restaurando backup de base de datos"
    
    # Verificar archivo
    if [[ ! -f "$backup_file" ]]; then
        # Intentar encontrar en directorio de backups
        local backup_path="$BACKUP_DIR/$backup_file"
        if [[ -f "$backup_path" ]]; then
            backup_file="$backup_path"
        else
            print_error "Archivo de backup no encontrado: $backup_file"
            return 1
        fi
    fi
    
    print_info "Archivo: $(basename "$backup_file")"
    
    # Confirmar restauración
    echo -e "${RED}¡ADVERTENCIA!${NC} Esto sobrescribirá la base de datos actual"
    echo -n "¿Estás seguro? (escriba 'YES' para continuar): "
    read -r confirmation
    if [[ "$confirmation" != "YES" ]]; then
        print_info "Restauración cancelada"
        return 0
    fi
    
    # Crear backup de seguridad antes de restaurar
    print_info "Creando backup de seguridad antes de restaurar..."
    CUSTOM_NAME="pre-restore-$(date '+%H%M%S')"
    create_backup
    CUSTOM_NAME=""
    
    # Ejecutar restauración
    echo -n "📥 Restaurando backup... "
    
    if [[ "$backup_file" == *.gz ]]; then
        # Archivo comprimido
        if gunzip -c "$backup_file" | docker exec -i "$DB_CONTAINER" mysql -u"$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" 2>/dev/null; then
            echo -e "${GREEN}✅ COMPLETADO${NC}"
        else
            echo -e "${RED}❌ ERROR${NC}"
            print_error "Falló la restauración del backup"
            return 1
        fi
    else
        # Archivo sin comprimir
        if docker exec -i "$DB_CONTAINER" mysql -u"$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" < "$backup_file" 2>/dev/null; then
            echo -e "${GREEN}✅ COMPLETADO${NC}"
        else
            echo -e "${RED}❌ ERROR${NC}"
            print_error "Falló la restauración del backup"
            return 1
        fi
    fi
    
    print_success "Base de datos restaurada exitosamente"
    
    # Verificar restauración
    print_info "Verificando datos restaurados..."
    if docker exec "$DB_CONTAINER" mysql -u"$DB_USER" -p"$DB_PASSWORD" -e "USE $DB_NAME; SHOW TABLES;" >/dev/null 2>&1; then
        local table_count=$(docker exec "$DB_CONTAINER" mysql -u"$DB_USER" -p"$DB_PASSWORD" -e "USE $DB_NAME; SHOW TABLES;" 2>/dev/null | wc -l)
        print_success "Verificación exitosa: $((table_count - 1)) tablas restauradas"
    else
        print_warning "No se pudo verificar la restauración"
    fi
}

# Limpiar backups antiguos
cleanup_old_backups() {
    if [[ "$KEEP_DAYS" -le 0 ]]; then
        log_verbose "Limpieza de backups antiguos deshabilitada"
        return 0
    fi
    
    print_step "Limpiando backups antiguos (> $KEEP_DAYS días)"
    
    if [[ ! -d "$BACKUP_DIR" ]]; then
        log_verbose "No existe directorio de backups"
        return 0
    fi
    
    local old_backups=($(find "$BACKUP_DIR" -name "yagaruete_camp_*.sql*" -type f -mtime +$KEEP_DAYS 2>/dev/null))
    
    if [[ ${#old_backups[@]} -eq 0 ]]; then
        print_info "No hay backups antiguos para limpiar"
        return 0
    fi
    
    echo "🗑️  Eliminando ${#old_backups[@]} backups antiguos:"
    
    for backup in "${old_backups[@]}"; do
        local filename=$(basename "$backup")
        local size=$(du -h "$backup" | cut -f1)
        
        rm -f "$backup"
        echo "   ❌ $filename ($size)"
    done
    
    print_success "Limpieza completada"
}

# Generar resumen
generate_summary() {
    print_step "Resumen de operación"
    
    if [[ "$LIST_BACKUPS" == true ]]; then
        print_info "Listado de backups completado"
    elif [[ -n "$RESTORE_FILE" ]]; then
        print_info "Restauración completada"
        print_warning "Verifica que la aplicación funcione correctamente"
    else
        print_info "Backup completado exitosamente"
        
        echo ""
        echo "📋 Información:"
        echo "   - Directorio: $BACKUP_DIR"
        echo "   - Compresión: $([ "$COMPRESS" == true ] && echo "Habilitada" || echo "Deshabilitada")"
        echo "   - Retención: $KEEP_DAYS días"
        echo "   - Estructura: $([ "$INCLUDE_STRUCTURE" == true ] && echo "Incluida" || echo "Excluida")"
        echo "   - Datos: $([ "$INCLUDE_DATA" == true ] && echo "Incluidos" || echo "Excluidos")"
        
        echo ""
        echo "💡 Comandos útiles:"
        echo "   - Listar backups: $0 --list"
        echo "   - Restaurar: $0 --restore <archivo>"
        echo "   - Backup personalizado: $0 --name \"mi-backup\""
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
        -n|--name)
            CUSTOM_NAME="$2"
            shift 2
            ;;
        --no-compress)
            COMPRESS=false
            shift
            ;;
        --no-data)
            INCLUDE_DATA=false
            shift
            ;;
        --no-structure)
            INCLUDE_STRUCTURE=false
            shift
            ;;
        --keep-days)
            KEEP_DAYS="$2"
            shift 2
            ;;
        -l|--list)
            LIST_BACKUPS=true
            shift
            ;;
        -r|--restore)
            RESTORE_FILE="$2"
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
    log_verbose "- Directorio backups: $BACKUP_DIR"
    log_verbose "- Contenedor BD: $DB_CONTAINER"
    log_verbose "- Base de datos: $DB_NAME"
    log_verbose "- Comprimir: $COMPRESS"
    log_verbose "- Retener días: $KEEP_DAYS"
    
    check_dependencies
    
    if [[ "$LIST_BACKUPS" == true ]]; then
        list_backups
    elif [[ -n "$RESTORE_FILE" ]]; then
        restore_backup "$RESTORE_FILE"
    else
        # Verificar que se incluya algo
        if [[ "$INCLUDE_DATA" == false && "$INCLUDE_STRUCTURE" == false ]]; then
            print_error "Debes incluir al menos datos o estructura"
            exit 1
        fi
        
        create_backup
        cleanup_old_backups
    fi
    
    generate_summary
}

# Ejecutar función principal
main "$@"
