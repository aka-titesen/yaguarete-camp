# 📋 Comandos Útiles - Yagaruete Camp

> Referencia rápida de comandos para desarrollo y administración

## 🐳 Docker Compose

### Gestión de Contenedores

```bash
# Iniciar servicios
docker-compose up -d                    # Modo background
docker-compose up -d --build            # Rebuilding imágenes

# Estado y monitoreo
docker-compose ps                       # Ver estado de servicios
docker-compose logs -f                  # Logs en tiempo real
docker-compose logs -f app              # Logs de servicio específico
docker-compose logs --tail=50 app       # Últimas 50 líneas

# Control de servicios
docker-compose restart                  # Reiniciar todos
docker-compose restart app              # Reiniciar servicio específico
docker-compose stop                     # Parar servicios
docker-compose down                     # Parar y remover containers

# Reset completo
docker-compose down -v --remove-orphans # Eliminar todo (incluye volúmenes)
```

### Acceso a Contenedores

```bash
# Acceder al contenedor principal
docker-compose exec app bash
docker-compose exec app sh              # Si bash no está disponible

# Acceder a MySQL
docker-compose exec db mysql -u root -p
docker-compose exec db mysql -u yaguarete_user -psecure_password_123 yaguarete_camp

# Acceder a Redis
docker-compose exec redis redis-cli
docker-compose exec redis redis-cli ping
```

## 📊 CodeIgniter Commands

### Base de Datos

```bash
# Migraciones
docker-compose exec app php spark migrate              # Ejecutar pendientes
docker-compose exec app php spark migrate:status       # Ver estado
docker-compose exec app php spark migrate:rollback     # Rollback última
docker-compose exec app php spark migrate:reset        # Reset completo

# Seeders
docker-compose exec app php spark db:seed DatabaseSeeder    # Seeder principal
docker-compose exec app php spark db:seed CategorySeeder    # Seeder específico
docker-compose exec app php spark db:seed UserSeeder        # Seeder de usuarios
docker-compose exec app php spark db:seed ProductSeeder     # Seeder de productos

# Crear archivos
docker-compose exec app php spark make:migration CreateTableName
docker-compose exec app php spark make:model ModelName
docker-compose exec app php spark make:controller ControllerName
docker-compose exec app php spark make:seeder SeederName
```

### Cache y Performance

```bash
# Cache management
docker-compose exec app php spark cache:clear          # Limpiar cache CI4
docker-compose exec redis redis-cli FLUSHALL          # Limpiar Redis cache
docker-compose exec redis redis-cli FLUSHDB           # Limpiar BD específica

# Verificar OPcache
docker-compose exec app php -i | grep opcache
docker-compose exec app php -r "var_dump(opcache_get_status());"

# Stats de Redis
docker-compose exec redis redis-cli INFO
docker-compose exec redis redis-cli INFO stats
docker-compose exec redis redis-cli MONITOR           # Monitoreo en tiempo real
```

### Desarrollo

```bash
# Ver todas las rutas
docker-compose exec app php spark routes

# Generar clave de aplicación
docker-compose exec app php spark key:generate

# Ver comandos disponibles
docker-compose exec app php spark list

# Modo de desarrollo
docker-compose exec app php spark serve               # No usar con Docker
```

## 🗄️ MySQL Commands

### Administración

```bash
# Conectar a MySQL
docker-compose exec db mysql -u root -p

# Backup
docker-compose exec db mysqldump -u root -p yaguarete_camp > backup_$(date +%Y%m%d).sql

# Restore
docker-compose exec -T db mysql -u root -p yaguarete_camp < backup.sql

# Ver bases de datos
docker-compose exec db mysql -u root -p -e "SHOW DATABASES;"

# Ver tablas
docker-compose exec db mysql -u root -p -e "USE yaguarete_camp; SHOW TABLES;"
```

### Performance y Monitoring

```bash
# Ver procesos activos
docker-compose exec db mysql -u root -p -e "SHOW PROCESSLIST;"

# Estadísticas de Query Cache
docker-compose exec db mysql -u root -p -e "SHOW STATUS LIKE 'Qcache%';"

# Estado de InnoDB
docker-compose exec db mysql -u root -p -e "SHOW ENGINE INNODB STATUS;"

# Variables de configuración
docker-compose exec db mysql -u root -p -e "SHOW VARIABLES LIKE 'query_cache%';"
docker-compose exec db mysql -u root -p -e "SHOW VARIABLES LIKE 'innodb_buffer_pool_size';"
```

## 🔴 Redis Commands

### Operaciones Básicas

```bash
# Test de conectividad
docker-compose exec redis redis-cli ping

# Información del servidor
docker-compose exec redis redis-cli INFO
docker-compose exec redis redis-cli INFO memory
docker-compose exec redis redis-cli INFO stats

# Ver todas las claves
docker-compose exec redis redis-cli KEYS "*"

# Limpiar cache
docker-compose exec redis redis-cli FLUSHALL         # Todas las BD
docker-compose exec redis redis-cli FLUSHDB          # BD actual

# Monitoreo en tiempo real
docker-compose exec redis redis-cli MONITOR
```

### Cache Management

```bash
# Ver claves de cache de la app
docker-compose exec redis redis-cli KEYS "yaguarete_*"

# Ver una clave específica
docker-compose exec redis redis-cli GET "cache_key_name"

# Eliminar clave específica
docker-compose exec redis redis-cli DEL "cache_key_name"

# Ver TTL de una clave
docker-compose exec redis redis-cli TTL "cache_key_name"

# Configuración
docker-compose exec redis redis-cli CONFIG GET "*"
```

## 🔧 Sistema y Debugging

### Logs y Debugging

```bash
# Ver logs de la aplicación
docker-compose logs -f app
tail -f writable/logs/log-$(date +%Y-%m-%d).log

# Ver logs de Nginx
docker-compose logs -f nginx

# Ver logs de MySQL
docker-compose logs -f db

# Ver logs de Redis
docker-compose logs -f redis

# Logs del sistema
docker-compose exec app tail -f /var/log/php_errors.log
```

### Performance Monitoring

```bash
# Uso de recursos
docker stats --no-stream
docker stats --no-stream --format "table {{.Container}}\t{{.CPUPerc}}\t{{.MemUsage}}"

# Test de carga básico (si tienes apache-utils)
ab -n 100 -c 10 http://localhost:8080/

# Verificar puertos
netstat -tulpn | grep :8080
netstat -tulpn | grep :3306

# Espacio en disco
docker system df
docker system prune                    # Limpiar recursos no usados
```

### Troubleshooting

```bash
# Verificar conectividad entre servicios
docker-compose exec app ping db
docker-compose exec app ping redis

# Ver configuración PHP
docker-compose exec app php -i
docker-compose exec app php -m         # Módulos cargados

# Verificar archivos de configuración
docker-compose exec app cat /usr/local/etc/php/php.ini
docker-compose exec nginx cat /etc/nginx/conf.d/default.conf

# Permisos (Linux/Mac)
sudo chown -R $USER:$USER .
chmod -R 755 writable/
```

## 🧪 Testing

### Ejecutar Tests

```bash
# Todos los tests
docker-compose exec app vendor/bin/phpunit

# Tests específicos
docker-compose exec app vendor/bin/phpunit tests/unit/
docker-compose exec app vendor/bin/phpunit tests/integration/

# Con cobertura
docker-compose exec app vendor/bin/phpunit --coverage-html coverage/

# Test específico
docker-compose exec app vendor/bin/phpunit tests/unit/Models/ProductModelTest.php
```

## 📦 Composer

### Gestión de Dependencias

```bash
# Instalar dependencias
docker-compose exec app composer install

# Actualizar dependencias
docker-compose exec app composer update

# Instalar paquete específico
docker-compose exec app composer require vendor/package

# Autoload
docker-compose exec app composer dump-autoload

# Verificar dependencias
docker-compose exec app composer show
docker-compose exec app composer outdated
```

## 🚀 Deployment

### Preparación para Producción

```bash
# Optimizaciones de Composer
docker-compose exec app composer install --no-dev --optimize-autoloader

# Limpiar cache y logs
docker-compose exec app rm -rf writable/cache/*
docker-compose exec app rm -rf writable/logs/*

# Verificar configuración
docker-compose exec app php spark env

# Backup antes de deploy
docker-compose exec db mysqldump -u root -p yaguarete_camp > backup_pre_deploy.sql
```

### Variables de Entorno

```bash
# Ver variables actuales
docker-compose exec app printenv | grep CI_
docker-compose exec app php -r "echo getenv('CI_ENVIRONMENT');"

# Verificar configuración
docker-compose exec app php spark env
```

## 📚 Comandos Frecuentes

### Setup Inicial

```bash
# Setup completo desde cero
git clone https://github.com/aka-titesen/yaguarete-camp.git
cd yaguarete-camp
cp .env.example .env
docker-compose up -d --build
# Esperar 30 segundos
docker-compose exec app php spark migrate
docker-compose exec app php spark db:seed DatabaseSeeder
```

### Reset de Desarrollo

```bash
# Reset BD solamente
docker-compose exec app php spark migrate:reset
docker-compose exec app php spark migrate
docker-compose exec app php spark db:seed DatabaseSeeder

# Reset completo (BD + Cache)
docker-compose down -v
docker-compose up -d --build
# Esperar y repetir migraciones
```

### Health Check Rápido

```bash
# Verificar que todo funciona
curl http://localhost:8080/                          # App responde
docker-compose exec redis redis-cli ping             # Redis OK
docker-compose exec db mysql -u root -p -e "SELECT 1;" # MySQL OK
docker-compose exec app php spark migrate:status     # BD actualizada
```

---

**Yagaruete Camp Commands** - Referencia completa para desarrollo
_Actualizado: 28 de Julio, 2025_ 📋⚡
