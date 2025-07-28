# 🚀 Setup y Configuración - Yagaruete Camp

> Guía completa de instalación y configuración optimizada

## ⚡ Setup Rápido (4 pasos)

### 1️⃣ Prerequisitos

```bash
# Verificar versiones mínimas
docker --version          # >= 20.10
docker-compose --version  # >= 2.0
git --version             # >= 2.30
```

### 2️⃣ Clonar y Preparar

```bash
# Clonar repositorio
git clone https://github.com/tu-usuario/yaguarete-camp.git
cd yaguarete-camp

# Configurar variables de entorno
cp .env.example .env
# Editar .env con tus configuraciones
```

### 3️⃣ Construir y Ejecutar

```bash
# Construir contenedores optimizados
docker-compose build --no-cache

# Ejecutar con todas las optimizaciones
docker-compose up -d
```

### 4️⃣ Verificar Instalación

```bash
# Health check completo
curl http://localhost:8080/health

# Verificar servicios
docker-compose ps
```

## 🔧 Configuración Detallada

### Variables de Entorno (.env)

```bash
# === APLICACIÓN ===
APP_NAME="Yagaruete Camp"
CI_ENVIRONMENT=development
APP_BASEURL=http://localhost:8080

# === BASE DE DATOS ===
database.default.hostname=db
database.default.database=yaguarete_camp
database.default.username=yaguarete_user
database.default.password=secure_password_123
database.default.DBDriver=MySQLi

# === REDIS CACHE ===
REDIS_HOST=redis
REDIS_PORT=6379
REDIS_PASSWORD=
REDIS_DATABASE=0

# === PERFORMANCE OPTIMIZATIONS ===
# OPcache
PHP_OPCACHE_ENABLE=1
PHP_OPCACHE_MEMORY=256
PHP_OPCACHE_MAX_FILES=20000

# MySQL Query Cache
MYSQL_QUERY_CACHE_SIZE=268435456
MYSQL_INNODB_BUFFER_POOL_SIZE=536870912

# Redis Memory
REDIS_MAXMEMORY=512mb
REDIS_MAXMEMORY_POLICY=allkeys-lru

# === SEGURIDAD ===
app.encryptionKey=your-32-character-secret-key-here
app.sessionDriver=CodeIgniter\Session\Handlers\RedisHandler
app.sessionCookieName=yaguarete_session
app.sessionExpiration=7200

# === EMAIL (DESARROLLO) ===
email.SMTPHost=mailhog
email.SMTPPort=1025
email.SMTPUser=
email.SMTPPass=
```

### Archivo docker-compose.yml Optimizado

```yaml
version: "3.8"

services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: yaguarete_app
    restart: unless-stopped
    volumes:
      - ./:/var/www/html
      - ./docker/php/php.ini:/usr/local/etc/php/php.ini
    environment:
      - CI_ENVIRONMENT=development
    depends_on:
      - db
      - redis
    networks:
      - yaguarete_network
    # Resource limits para performance
    deploy:
      resources:
        limits:
          memory: 512M
        reservations:
          memory: 256M

  nginx:
    image: nginx:1.24-alpine
    container_name: yaguarete_nginx
    restart: unless-stopped
    ports:
      - "8080:80"
    volumes:
      - ./:/var/www/html
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
    depends_on:
      - app
    networks:
      - yaguarete_network
    # Nginx optimizations
    deploy:
      resources:
        limits:
          memory: 128M

  db:
    image: mysql:8.0
    container_name: yaguarete_db
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: yaguarete_camp
      MYSQL_USER: yaguarete_user
      MYSQL_PASSWORD: secure_password_123
      MYSQL_ROOT_PASSWORD: root_password_123
    volumes:
      - mysql_data:/var/lib/mysql
      - ./docker/mysql/my.cnf:/etc/mysql/conf.d/custom.cnf
    ports:
      - "3306:3306"
    networks:
      - yaguarete_network
    # MySQL performance tuning
    command: >
      --query-cache-type=1
      --query-cache-size=268435456
      --innodb-buffer-pool-size=536870912
      --innodb-log-file-size=256M
    deploy:
      resources:
        limits:
          memory: 1G
        reservations:
          memory: 512M

  redis:
    image: redis:7.2-alpine
    container_name: yaguarete_redis
    restart: unless-stopped
    ports:
      - "6379:6379"
    volumes:
      - redis_data:/data
      - ./docker/redis/redis.conf:/usr/local/etc/redis/redis.conf
    command: redis-server /usr/local/etc/redis/redis.conf
    networks:
      - yaguarete_network
    # Redis memory management
    deploy:
      resources:
        limits:
          memory: 512M

  phpmyadmin:
    image: phpmyadmin/phpmyadmin:5.2
    container_name: yaguarete_phpmyadmin
    restart: unless-stopped
    environment:
      PMA_HOST: db
      PMA_USER: yaguarete_user
      PMA_PASSWORD: secure_password_123
    ports:
      - "8081:80"
    depends_on:
      - db
    networks:
      - yaguarete_network

  mailhog:
    image: mailhog/mailhog:v1.0.1
    container_name: yaguarete_mailhog
    restart: unless-stopped
    ports:
      - "1025:1025" # SMTP
      - "8025:8025" # Web UI
    networks:
      - yaguarete_network

volumes:
  mysql_data:
  redis_data:

networks:
  yaguarete_network:
    driver: bridge
```

## 🗄️ Configuración de Base de Datos

### Inicialización Automática

```bash
# Ejecutar migraciones
docker-compose exec app php spark migrate

# Cargar datos de prueba
docker-compose exec app php spark db:seed MainSeeder

# Verificar instalación
docker-compose exec app php spark db:table users
```

### Seeders Incluidos

```php
// database/Seeds/MainSeeder.php
<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MainSeeder extends Seeder
{
    public function run()
    {
        // 1. Crear categorías
        $this->call('CategorySeeder');

        // 2. Crear usuarios (admin + clientes)
        $this->call('UserSeeder');

        // 3. Crear productos con stock
        $this->call('ProductSeeder');

        // 4. Crear datos de prueba de ventas
        $this->call('SaleSeeder');

        echo "✅ Base de datos inicializada con datos de prueba\n";
        echo "👤 Usuario admin: admin@yaguarete.com / admin123\n";
        echo "🛍️ 23 productos en 6 categorías\n";
        echo "📊 Datos de ventas de ejemplo incluidos\n";
    }
}
```

**Datos Iniciales Incluidos:**

- **👤 9 usuarios** - 1 admin + 8 clientes de prueba
- **📂 6 categorías** - Carpas, Sleeping, Mochilas, Cocina, Outdoor, Accesorios
- **🛍️ 23 productos** - Catálogo completo con precios y stock
- **📊 Datos de ventas** - Transacciones de ejemplo para dashboards

## ⚙️ Configuraciones de Performance

### PHP Optimizado (docker/php/php.ini)

```ini
[PHP]
; Performance optimizations
memory_limit = 256M
max_execution_time = 60
max_input_time = 60
max_input_vars = 3000
post_max_size = 32M
upload_max_filesize = 32M

; OPcache optimizations
[opcache]
opcache.enable=1
opcache.enable_cli=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.max_wasted_percentage=5
opcache.use_cwd=1
opcache.validate_timestamps=0
opcache.revalidate_freq=0
opcache.save_comments=1
opcache.enable_file_override=0

; Security optimizations
expose_php = Off
allow_url_fopen = Off
allow_url_include = Off
display_errors = Off
log_errors = On
error_log = /var/log/php_errors.log

; Session optimizations
session.cookie_httponly = 1
session.cookie_secure = 0
session.use_strict_mode = 1
session.cookie_samesite = "Lax"
```

### MySQL Optimizado (docker/mysql/my.cnf)

```ini
[mysqld]
# Performance optimizations
query_cache_type = 1
query_cache_size = 256M
query_cache_limit = 8M

# InnoDB optimizations
innodb_buffer_pool_size = 512M
innodb_log_file_size = 256M
innodb_log_buffer_size = 64M
innodb_flush_log_at_trx_commit = 2
innodb_file_per_table = 1

# Connection optimizations
max_connections = 100
thread_cache_size = 50
table_open_cache = 2000
table_definition_cache = 1400

# Timeout optimizations
interactive_timeout = 600
wait_timeout = 600
lock_wait_timeout = 120

# Binary logging
server-id = 1
log-bin = mysql-bin
binlog_format = ROW
expire_logs_days = 7
```

### Nginx Optimizado (docker/nginx/default.conf)

```nginx
server {
    listen 80;
    server_name localhost;
    root /var/www/html/public;
    index index.php index.html;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types text/plain text/css text/xml text/javascript application/javascript application/xml+rss application/json;

    # Static files caching
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|pdf|zip|tar|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    # PHP-FPM optimizations
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass app:9000;
        fastcgi_index index.php;

        # FastCGI buffer optimizations
        fastcgi_buffer_size 128k;
        fastcgi_buffers 4 256k;
        fastcgi_busy_buffers_size 256k;
        fastcgi_temp_file_write_size 256k;
        fastcgi_connect_timeout 60s;
        fastcgi_send_timeout 60s;
        fastcgi_read_timeout 60s;

        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }

    # CodeIgniter URL rewriting
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Deny access to sensitive files
    location ~ /\. {
        deny all;
        access_log off;
        log_not_found off;
    }
}
```

### Redis Optimizado (docker/redis/redis.conf)

```redis
# Memory management
maxmemory 512mb
maxmemory-policy allkeys-lru

# Persistence optimizations
save 900 1
save 300 10
save 60 10000

# Network optimizations
timeout 300
tcp-keepalive 300
tcp-backlog 511

# Security (desarrollo)
protected-mode no
bind 0.0.0.0

# Performance tuning
databases 16
hash-max-ziplist-entries 512
hash-max-ziplist-value 64
list-max-ziplist-size -2
set-max-intset-entries 512
zset-max-ziplist-entries 128
zset-max-ziplist-value 64

# Logging
loglevel notice
syslog-enabled yes
syslog-ident redis
```

## 🧪 Verificación de Instalación

### Health Check Completo

```bash
#!/bin/bash
# scripts/setup/healthcheck.sh

echo "🔍 Verificando instalación de Yagaruete Camp..."

# 1. Verificar contenedores
echo "📦 Estado de contenedores:"
docker-compose ps

# 2. Verificar conectividad web
echo "🌐 Verificando conectividad web:"
if curl -f http://localhost:8080/ > /dev/null 2>&1; then
    echo "✅ Web server respondiendo"
else
    echo "❌ Web server no responde"
fi

# 3. Verificar base de datos
echo "🗄️ Verificando base de datos:"
if docker-compose exec -T db mysql -u yaguarete_user -psecure_password_123 -e "SELECT 1;" > /dev/null 2>&1; then
    echo "✅ Base de datos conectada"
else
    echo "❌ Base de datos no disponible"
fi

# 4. Verificar Redis
echo "🚀 Verificando Redis:"
if docker-compose exec -T redis redis-cli ping | grep -q "PONG"; then
    echo "✅ Redis respondiendo"
else
    echo "❌ Redis no disponible"
fi

# 5. Verificar OPcache
echo "⚡ Verificando OPcache:"
OPCACHE_STATUS=$(docker-compose exec -T app php -r "echo opcache_get_status()['opcache_enabled'] ? 'enabled' : 'disabled';")
echo "📊 OPcache: $OPCACHE_STATUS"

# 6. Performance test básico
echo "🏃 Test de performance básico:"
RESPONSE_TIME=$(curl -w "%{time_total}" -s -o /dev/null http://localhost:8080/)
echo "⏱️ Tiempo de respuesta: ${RESPONSE_TIME}s"

echo "✅ Verificación completada"
```

### Tests de Performance

```bash
# Test de carga básico
ab -n 100 -c 10 http://localhost:8080/

# Test con URLs específicas
ab -n 50 -c 5 http://localhost:8080/productos
ab -n 50 -c 5 http://localhost:8080/categorias

# Verificar cache hit rates
docker-compose exec redis redis-cli info stats | grep cache
docker-compose exec db mysql -u root -proot_password_123 -e "SHOW STATUS LIKE 'Qcache%';"
```

## 🛠️ Troubleshooting

### Problemas Comunes

#### 1. **Contenedores no inician**

```bash
# Verificar logs
docker-compose logs app
docker-compose logs db
docker-compose logs redis

# Reiniciar servicios
docker-compose down
docker-compose up -d --force-recreate
```

#### 2. **Error de permisos en archivos**

```bash
# Ajustar permisos en writable/
sudo chmod -R 777 writable/
sudo chown -R www-data:www-data writable/

# En Windows con WSL
chmod -R 777 writable/
```

#### 3. **Base de datos no conecta**

```bash
# Verificar variables de entorno
cat .env | grep database

# Recrear base de datos
docker-compose exec db mysql -u root -proot_password_123 -e "DROP DATABASE IF EXISTS yaguarete_camp; CREATE DATABASE yaguarete_camp;"

# Ejecutar migraciones nuevamente
docker-compose exec app php spark migrate
```

#### 4. **Redis no conecta**

```bash
# Verificar configuración Redis
docker-compose exec redis redis-cli ping

# Verificar configuración en .env
grep REDIS .env

# Reiniciar solo Redis
docker-compose restart redis
```

### Logs de Diagnóstico

```bash
# Ver logs en tiempo real
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f db
docker-compose logs -f redis

# Logs de errores PHP
docker-compose exec app tail -f /var/log/php_errors.log

# Logs de performance
docker stats --no-stream
```

## 📊 Configuración para Producción

### Variables de Entorno Producción

```bash
# .env.production
CI_ENVIRONMENT=production
APP_BASEURL=https://tu-dominio.com

# Seguridad mejorada
app.forceGlobalSecureRequests=true
app.sessionCookieSecure=true
app.CSRFProtection=true

# Performance optimizada
PHP_OPCACHE_VALIDATE_TIMESTAMPS=0
REDIS_MAXMEMORY=1gb
MYSQL_INNODB_BUFFER_POOL_SIZE=1073741824

# Logging optimizado
CI_LOG_THRESHOLD=1  # Solo errores críticos
```

### Docker Compose Producción

```yaml
# docker-compose.prod.yml
version: "3.8"

services:
  app:
    build:
      context: .
      dockerfile: docker/php/Dockerfile.prod
    restart: always
    environment:
      - CI_ENVIRONMENT=production
    deploy:
      resources:
        limits:
          memory: 1G
          cpus: "1.0"
        reservations:
          memory: 512M
          cpus: "0.5"

  nginx:
    image: nginx:1.24-alpine
    restart: always
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./docker/nginx/nginx.prod.conf:/etc/nginx/conf.d/default.conf
      - ./docker/ssl:/etc/nginx/ssl
    deploy:
      resources:
        limits:
          memory: 256M
```

---

**Yagaruete Camp Setup** - Configuración optimizada para desarrollo y producción
_Documentación actualizada: 28 de Julio, 2025_ 🚀⚙️
