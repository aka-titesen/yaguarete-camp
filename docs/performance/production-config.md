# 🚀 Configuración de Producción - Alto Rendimiento

> Guía completa para desplegar Yagaruete Camp en producción con máximo rendimiento

## 🎯 Configuraciones por Entorno

### 🖥️ Desarrollo (Actual)

- **CPU**: 2-4 cores
- **RAM**: 4-8 GB
- **Usuarios simultáneos**: 5-20
- **Configuración**: Balanceada para desarrollo

### 🏢 Producción Media

- **CPU**: 4-8 cores
- **RAM**: 8-16 GB
- **Usuarios simultáneos**: 100-500
- **Configuración**: Optimizada para carga media

### 🚀 Producción Alta

- **CPU**: 8+ cores
- **RAM**: 16+ GB
- **Usuarios simultáneos**: 1000+
- **Configuración**: Máximo rendimiento

## ⚙️ Configuración PHP Producción

### 📝 docker/php/php-prod.ini (Optimizado)

```ini
[Date]
date.timezone = "America/Mexico_City"

[PHP]
# Producción - Errores ocultos
display_errors = Off
display_startup_errors = Off
error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT
log_errors = On
error_log = /var/log/php_errors.log

# Memory & Execution - Producción
memory_limit = 1024M        # Aumentado para producción
max_execution_time = 60     # Reducido para evitar procesos colgados
max_input_time = 60

# File uploads - Producción
file_uploads = On
upload_max_filesize = 20M   # Reducido para seguridad
max_file_uploads = 10
post_max_size = 20M

# Sessions - Seguridad máxima
session.gc_probability = 1
session.gc_divisor = 1000   # Menos frecuente en producción
session.gc_maxlifetime = 1440
session.cookie_httponly = 1
session.use_strict_mode = 1
session.cookie_secure = 1   # HTTPS obligatorio

# OPcache - MÁXIMO RENDIMIENTO PRODUCCIÓN
opcache.enable = 1
opcache.enable_cli = 0
opcache.memory_consumption = 256        # 4x más memoria
opcache.interned_strings_buffer = 16    # 4x más strings
opcache.max_accelerated_files = 10000   # 5x más archivos
opcache.revalidate_freq = 0             # No validar cambios (más rápido)
opcache.validate_timestamps = 0         # Sin validación = máxima velocidad
opcache.save_comments = 0               # Sin comentarios = menos memoria
opcache.fast_shutdown = 1               # Shutdown rápido
opcache.enable_file_override = 1        # Override functions para speed

# Realpath cache - Mejor rendimiento filesystem
realpath_cache_size = 4096K
realpath_cache_ttl = 7200

# Security
expose_php = Off
allow_url_fopen = Off
allow_url_include = Off
```

## 🗄️ Configuración MySQL Producción

### 📝 docker/mysql/my-prod.cnf

```ini
[mysqld]
# Configuración MySQL OPTIMIZADA PARA PRODUCCIÓN

# Logging - Mínimo para performance
general_log = 0
slow_query_log = 1
slow_query_log_file = /var/lib/mysql/slow.log
long_query_time = 0.5                    # Detectar queries > 500ms
log_queries_not_using_indexes = 1

# InnoDB - OPTIMIZADO PARA PRODUCCIÓN
innodb_buffer_pool_size = 2G             # 50-80% de RAM disponible
innodb_log_file_size = 256M              # Más logs para writes intensivos
innodb_flush_log_at_trx_commit = 2       # Mejor performance (menos seguridad)
innodb_lock_wait_timeout = 30            # Timeout corto
innodb_flush_method = O_DIRECT           # Evitar doble buffering
innodb_file_per_table = 1                # Un archivo por tabla
innodb_read_io_threads = 8               # Más threads para lectura
innodb_write_io_threads = 8              # Más threads para escritura
innodb_io_capacity = 2000                # SSD capable
innodb_adaptive_hash_index = 1           # Hash index adaptativo

# Connection settings - PRODUCCIÓN
max_connections = 500                    # Más conexiones concurrentes
connect_timeout = 10                     # Timeout conexión corto
wait_timeout = 300                       # 5 minutos timeout
interactive_timeout = 300
thread_cache_size = 50                   # Cache de threads
table_open_cache = 4000                  # Cache de tablas abiertas

# Query Cache - OPTIMIZADO
query_cache_type = 1
query_cache_size = 128M                  # 4x más cache
query_cache_limit = 8M                   # Queries más grandes
query_cache_min_res_unit = 2K            # Fragmentación mínima

# MyISAM (si se usa)
key_buffer_size = 256M
read_buffer_size = 2M
read_rnd_buffer_size = 16M
sort_buffer_size = 8M

# Network
max_allowed_packet = 64M
net_buffer_length = 32K

# Character set
character-set-server = utf8mb4
collation-server = utf8mb4_unicode_ci

# Binary logging para replicación
log_bin = mysql-bin
expire_logs_days = 7

# SQL Mode - Producción estricta
sql_mode = "STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO"
```

## 🔴 Configuración Redis Producción

### 📝 docker/redis/redis-prod.conf

```ini
# Redis PRODUCCIÓN - Máximo rendimiento y seguridad

# Network
bind 0.0.0.0
port 6379
timeout 0
tcp-keepalive 300
tcp-backlog 511

# General
daemonize no
supervised no
pidfile /var/run/redis_6379.pid
loglevel notice
logfile ""

# Security - PRODUCCIÓN
protected-mode yes
requirepass "tu_password_super_seguro_aqui_2024!"

# Memory Management - PRODUCCIÓN
maxmemory 1gb                           # Límite de memoria
maxmemory-policy allkeys-lru            # Política de expulsión LRU
maxmemory-samples 5

# Persistence - BALANCEADA PARA PRODUCCIÓN
save 900 1                              # Snapshot si 1 key cambió en 15min
save 300 10                             # Snapshot si 10 keys cambiaron en 5min
save 60 10000                           # Snapshot si 10000 keys en 1min
stop-writes-on-bgsave-error yes
rdbcompression yes
rdbchecksum yes
dbfilename dump.rdb
dir /data

# AOF Persistence para máxima durabilidad
appendonly yes
appendfilename "appendonly.aof"
appendfsync everysec                    # Sync cada segundo (balanceado)
no-appendfsync-on-rewrite no
auto-aof-rewrite-percentage 100
auto-aof-rewrite-min-size 64mb

# Performance
hash-max-ziplist-entries 512
hash-max-ziplist-value 64
list-max-ziplist-size -2
set-max-intset-entries 512
zset-max-ziplist-entries 128
zset-max-ziplist-value 64

# Network tuning
tcp-keepalive 300
```

## 🌐 Configuración Nginx Producción

### 📝 docker/nginx/nginx-prod.conf

```nginx
user nginx;
worker_processes auto;                   # Un worker por CPU core
worker_rlimit_nofile 65535;             # Límite de archivos abiertos
error_log /var/log/nginx/error.log warn;
pid /var/run/nginx.pid;

events {
    worker_connections 4096;             # 4x más conexiones
    use epoll;                           # Linux optimal
    multi_accept on;                     # Aceptar múltiples conexiones
    accept_mutex off;                    # Mejor para alta carga
}

http {
    include /etc/nginx/mime.types;
    default_type application/octet-stream;

    # Logging optimizado
    log_format main '$remote_addr - $remote_user [$time_local] "$request" '
                    '$status $body_bytes_sent "$http_referer" '
                    '"$http_user_agent" "$http_x_forwarded_for" '
                    '$request_time $upstream_response_time';

    access_log /var/log/nginx/access.log main buffer=64k flush=5s;

    # Performance settings - PRODUCCIÓN
    sendfile on;
    sendfile_max_chunk 1m;
    tcp_nopush on;
    tcp_nodelay on;
    keepalive_timeout 30;                # Timeout balanceado
    keepalive_requests 1000;             # Más requests por conexión
    types_hash_max_size 2048;
    server_tokens off;                   # Ocultar versión

    # Buffer sizes - OPTIMIZADOS PARA PRODUCCIÓN
    client_body_buffer_size 256k;
    client_header_buffer_size 64k;
    client_max_body_size 20m;
    large_client_header_buffers 8 64k;

    # Timeouts - PRODUCCIÓN
    client_body_timeout 30s;
    client_header_timeout 30s;
    send_timeout 30s;
    proxy_connect_timeout 30s;
    proxy_send_timeout 30s;
    proxy_read_timeout 30s;

    # Gzip - MÁXIMA COMPRESIÓN
    gzip on;
    gzip_vary on;
    gzip_min_length 1000;
    gzip_proxied any;
    gzip_comp_level 9;                   # Máxima compresión
    gzip_types
        text/plain
        text/css
        text/xml
        text/javascript
        application/json
        application/javascript
        application/xml+rss
        application/atom+xml
        image/svg+xml
        application/x-font-ttf
        application/vnd.ms-fontobject
        font/opentype;

    # Rate limiting - SEGURIDAD
    limit_req_zone $binary_remote_addr zone=api:10m rate=10r/s;
    limit_req_zone $binary_remote_addr zone=login:10m rate=1r/s;

    # Cache zones
    proxy_cache_path /var/cache/nginx levels=1:2 keys_zone=app_cache:100m
                     max_size=1g inactive=60m use_temp_path=off;

    include /etc/nginx/conf.d/*.conf;
}
```

### 📝 docker/nginx/app-prod.conf

```nginx
server {
    listen 80;
    server_name tu-dominio.com www.tu-dominio.com;
    root /var/www/html/public;
    index index.php index.html;

    # Security headers - PRODUCCIÓN
    add_header X-Frame-Options "DENY" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self';" always;

    # Rate limiting en rutas críticas
    location /login {
        limit_req zone=login burst=5 nodelay;
        try_files $uri $uri/ /index.php?$query_string;
    }

    location /api/ {
        limit_req zone=api burst=20 nodelay;
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Cache estático AGRESIVO - PRODUCCIÓN
    location ~* \.(css|js|jpg|jpeg|png|gif|ico|svg|woff|woff2|ttf|eot|webp)$ {
        expires 1y;                      # Cache 1 año
        add_header Cache-Control "public, immutable";
        add_header Pragma public;
        add_header Vary Accept-Encoding;
        try_files $uri =404;

        # Compression específica para assets
        gzip_static on;
    }

    # PHP - OPTIMIZADO PARA PRODUCCIÓN
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass app:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
        include fastcgi_params;

        # FastCGI Cache - PRODUCCIÓN
        fastcgi_cache app_cache;
        fastcgi_cache_valid 200 301 302 5m;
        fastcgi_cache_valid 404 1m;
        fastcgi_cache_use_stale error timeout updating http_500 http_503;
        fastcgi_cache_lock on;

        # Buffers MÁXIMOS para producción
        fastcgi_buffer_size 512k;
        fastcgi_buffers 16 512k;
        fastcgi_busy_buffers_size 1m;
        fastcgi_temp_file_write_size 1m;
        fastcgi_read_timeout 60;
        fastcgi_connect_timeout 30;
        fastcgi_send_timeout 30;
        fastcgi_keep_conn on;
    }

    # Logs específicos
    access_log /var/log/nginx/app_access.log main;
    error_log /var/log/nginx/app_error.log;
}
```

## 🔐 Variables de Entorno Producción

### 📝 .env.production

```bash
#--------------------------------------------------------------------
# PRODUCTION ENVIRONMENT CONFIGURATION
#--------------------------------------------------------------------

CI_ENVIRONMENT = production
CI_DEBUG = false

#--------------------------------------------------------------------
# DATABASE
#--------------------------------------------------------------------
DB_DATABASE = bd_yagaruete_camp_prod
DB_USERNAME = app_user
DB_PASSWORD = password_super_seguro_aqui_2024
DB_HOSTNAME = db
DB_PORT = 3306
DB_PREFIX =
DB_CHARSET = utf8mb4
DB_COLLATION = utf8mb4_unicode_ci

#--------------------------------------------------------------------
# SECURITY
#--------------------------------------------------------------------
# Generar con: php spark key:generate
APP_ENCRYPTION_KEY = tu_clave_encriptacion_32_caracteres

# Session segura
APP_SESSION_DRIVER = redis
APP_SESSION_COOKIE_NAME = yagaruete_session_prod
APP_SESSION_EXPIRATION = 7200
APP_SESSION_SAVE_PATH = tcp://redis:6379?auth=tu_password_redis

#--------------------------------------------------------------------
# CACHE - REDIS PRODUCCIÓN
#--------------------------------------------------------------------
CACHE_HANDLER = redis
REDIS_HOST = redis
REDIS_PORT = 6379
REDIS_PASSWORD = tu_password_super_seguro_aqui_2024
REDIS_DATABASE = 0

#--------------------------------------------------------------------
# EMAIL - SMTP REAL
#--------------------------------------------------------------------
MAIL_PROTOCOL = smtp
MAIL_HOST = smtp.tu-proveedor.com
MAIL_PORT = 587
MAIL_USERNAME = no-reply@tu-dominio.com
MAIL_PASSWORD = password_email_seguro
MAIL_ENCRYPTION = tls
MAIL_FROM_EMAIL = no-reply@tu-dominio.com
MAIL_FROM_NAME = "Yagaruete Camp"

#--------------------------------------------------------------------
# PERFORMANCE
#--------------------------------------------------------------------
# OPcache forcejado a ON
OPCACHE_ENABLE = 1
OPCACHE_VALIDATE_TIMESTAMPS = 0

# MySQL Query Cache
MYSQL_QUERY_CACHE_SIZE = 128M

# Logs mínimos para performance
LOG_THRESHOLD = 3
```

## 🚀 Docker Compose Producción

### 📝 docker-compose.prod.yml

```yaml
version: "3.8"

services:
  app:
    build:
      context: .
      target: production
      dockerfile: Dockerfile
    container_name: yagaruete_camp_app_prod
    restart: unless-stopped
    environment:
      - CI_ENVIRONMENT=production
    volumes:
      - app_data:/var/www/html/writable
      - uploads:/var/www/html/public/assets/uploads
    networks:
      - app-network
    depends_on:
      - db
      - redis
    deploy:
      resources:
        limits:
          memory: 2G
          cpus: "2.0"
        reservations:
          memory: 1G
          cpus: "1.0"

  nginx:
    image: nginx:1.24-alpine
    container_name: yagaruete_camp_nginx_prod
    restart: unless-stopped
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./docker/nginx/nginx-prod.conf:/etc/nginx/nginx.conf
      - ./docker/nginx/app-prod.conf:/etc/nginx/conf.d/default.conf
      - ./public:/var/www/html/public:ro
      - nginx_cache:/var/cache/nginx
      - ./ssl:/etc/nginx/ssl:ro # Certificados SSL
    networks:
      - app-network
    depends_on:
      - app
    deploy:
      resources:
        limits:
          memory: 512M
          cpus: "1.0"

  db:
    image: mysql:8.0
    container_name: yagaruete_camp_mysql_prod
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: ${DB_DATABASE}
      MYSQL_ROOT_PASSWORD: ${DB_PASSWORD}
      MYSQL_USER: app_user
      MYSQL_PASSWORD: ${DB_PASSWORD}
    volumes:
      - db_data:/var/lib/mysql
      - ./docker/mysql/my-prod.cnf:/etc/mysql/conf.d/my.cnf
    networks:
      - app-network
    deploy:
      resources:
        limits:
          memory: 4G
          cpus: "2.0"
        reservations:
          memory: 2G
          cpus: "1.0"

  redis:
    image: redis:7.2-alpine
    container_name: yagaruete_camp_redis_prod
    restart: unless-stopped
    volumes:
      - redis_data:/data
      - ./docker/redis/redis-prod.conf:/usr/local/etc/redis/redis.conf
    command: redis-server /usr/local/etc/redis/redis.conf
    networks:
      - app-network
    deploy:
      resources:
        limits:
          memory: 1G
          cpus: "1.0"

volumes:
  db_data:
    driver: local
  redis_data:
    driver: local
  app_data:
    driver: local
  uploads:
    driver: local
  nginx_cache:
    driver: local

networks:
  app-network:
    driver: bridge
```

## 📊 Monitoreo Producción

### 🔍 Scripts de Monitoreo

```bash
#!/bin/bash
# monitoring-prod.sh - Monitoreo continuo producción

THRESHOLD_CPU=80
THRESHOLD_MEMORY=85
THRESHOLD_RESPONSE=2000  # 2 segundos

# Function to send alerts (configurar con tu sistema de alertas)
send_alert() {
    echo "ALERT: $1" | mail -s "Yagaruete Camp Alert" admin@tu-dominio.com
}

# Check response time
RESPONSE_TIME=$(curl -o /dev/null -s -w "%{time_total}" http://localhost/)
if (( $(echo "$RESPONSE_TIME > $THRESHOLD_RESPONSE" | bc -l) )); then
    send_alert "Response time high: ${RESPONSE_TIME}ms"
fi

# Check container resources
docker stats --no-stream --format "table {{.Name}}\t{{.CPUPerc}}\t{{.MemPerc}}" | while read line; do
    if [[ $line =~ ([0-9.]+)% ]]; then
        if (( $(echo "${BASH_REMATCH[1]} > $THRESHOLD_CPU" | bc -l) )); then
            send_alert "High CPU usage: $line"
        fi
    fi
done
```

---

_Configuración de producción optimizada - 28 de Julio, 2025_ 🚀
