# 🔧 Troubleshooting de Rendimiento - Yagaruete Camp

> Guía completa para diagnosticar y resolver problemas de rendimiento

## 🚨 Problemas Comunes y Soluciones

### 1. 🐌 Aplicación Muy Lenta (>5 segundos de carga)

#### Diagnóstico

```bash
# 1. Verificar estado de contenedores
docker-compose ps

# 2. Ver uso de recursos
docker stats --no-stream

# 3. Verificar logs de errores
docker-compose logs --tail=50 app
```

#### ✅ Soluciones

```bash
# A. Verificar OPcache
docker-compose exec app php -r "var_dump(opcache_get_status()['opcache_enabled']);"

# B. Reiniciar servicios si es necesario
docker-compose restart app apache

# C. Limpiar cache si hay problemas
docker-compose exec redis redis-cli FLUSHALL
```

### 2. 🔴 Redis No Conecta

#### Síntomas

- Error: "Connection refused" en logs
- Cache fallback a FileHandler
- Sessions no persisten entre requests

#### Diagnóstico

```bash
# 1. Verificar Redis activo
docker-compose exec redis redis-cli ping

# 2. Ver configuración Redis
docker-compose exec redis redis-cli CONFIG GET "*"

# 3. Verificar conectividad desde app
docker-compose exec app php -r "
try {
    \$r = new Redis();
    \$r->connect('redis', 6379);
    echo 'Redis OK';
} catch(Exception \$e) {
    echo 'Error: ' . \$e->getMessage();
}"
```

#### ✅ Soluciones

```bash
# A. Reiniciar Redis
docker-compose restart redis

# B. Verificar configuración protected-mode
docker-compose exec redis redis-cli CONFIG GET protected-mode
docker-compose exec redis redis-cli CONFIG SET protected-mode no

# C. Verificar network connectivity
docker-compose exec app ping redis
```

### 3. 🗄️ Base de Datos Lenta

#### Síntomas

- Queries >1 segundo
- Timeouts de conexión
- Alto CPU usage en contenedor DB

#### Diagnóstico

```bash
# 1. Ver procesos activos
docker-compose exec db mysql -u root -p -e "SHOW PROCESSLIST;"

# 2. Verificar Query Cache
docker-compose exec db mysql -u root -p -e "SHOW STATUS LIKE 'Qcache%';"

# 3. Ver slow queries
docker-compose exec db mysql -u root -p -e "SHOW STATUS LIKE 'Slow_queries';"
```

#### ✅ Soluciones

```bash
# A. Optimizar tablas
docker-compose exec app php spark db:optimize

# B. Verificar configuración MySQL
docker-compose exec db mysql -u root -p -e "SHOW VARIABLES LIKE '%query_cache%';"

# C. Reiniciar DB si es necesario
docker-compose restart db
```

### 4. 📈 Alto Uso de Memoria

#### Diagnóstico

```bash
# 1. Ver uso de memoria por contenedor
docker stats --no-stream --format "table {{.Name}}\t{{.CPUPerc}}\t{{.MemUsage}}"

# 2. Verificar OPcache memory
docker-compose exec app php -r "var_dump(opcache_get_status()['memory_usage']);"

# 3. Ver procesos PHP
docker-compose exec app ps aux | grep php
```

#### ✅ Soluciones

```bash
# A. Ajustar memory_limit PHP si es necesario
# Editar docker/php/php-dev.ini
memory_limit = 1024M

# B. Limpiar OPcache si está lleno
docker-compose exec app php -r "opcache_reset();"

# C. Reiniciar contenedor app
docker-compose restart app
```

## 🔍 Herramientas de Diagnóstico

### 📊 Script de Health Check Completo

```bash
#!/bin/bash
# health-check.sh - Diagnóstico completo del sistema

echo "=== YAGARUETE CAMP - HEALTH CHECK ==="
echo "Fecha: $(date)"
echo

# 1. Estado de contenedores
echo "📦 ESTADO DE CONTENEDORES:"
docker-compose ps
echo

# 2. Uso de recursos
echo "💾 USO DE RECURSOS:"
docker stats --no-stream --format "table {{.Name}}\t{{.CPUPerc}}\t{{.MemUsage}}\t{{.NetIO}}"
echo

# 3. OPcache Status
echo "⚡ OPCACHE STATUS:"
docker-compose exec -T app php -r "
\$status = opcache_get_status();
if (\$status && \$status['opcache_enabled']) {
    echo '✅ OPcache: ENABLED\n';
    echo 'Hit Rate: ' . round(\$status['opcache_statistics']['opcache_hit_rate'], 2) . '%\n';
    echo 'Memory Used: ' . round(\$status['memory_usage']['used_memory'] / 1024 / 1024, 2) . ' MB\n';
} else {
    echo '❌ OPcache: DISABLED\n';
}
"
echo

# 4. Redis Status
echo "🔴 REDIS STATUS:"
if docker-compose exec -T redis redis-cli ping > /dev/null 2>&1; then
    echo "✅ Redis: CONNECTED"
    docker-compose exec -T redis redis-cli info stats | grep -E "(keyspace_hits|keyspace_misses)" | head -2
else
    echo "❌ Redis: CONNECTION FAILED"
fi
echo

# 5. MySQL Status
echo "🗄️ MYSQL STATUS:"
docker-compose exec -T db mysql -u root -p${DB_PASSWORD:-root} -e "
SELECT
    'Query Cache Hit Rate' as Metric,
    CONCAT(ROUND(Qcache_hits/(Qcache_hits + Qcache_inserts) * 100, 2), '%') as Value
FROM
    (SELECT
        VARIABLE_VALUE as Qcache_hits
    FROM information_schema.GLOBAL_STATUS
    WHERE VARIABLE_NAME = 'Qcache_hits') as hits,
    (SELECT
        VARIABLE_VALUE as Qcache_inserts
    FROM information_schema.GLOBAL_STATUS
    WHERE VARIABLE_NAME = 'Qcache_inserts') as inserts;
" 2>/dev/null || echo "❌ MySQL connection failed"

echo
echo "=== HEALTH CHECK COMPLETADO ==="
```

### 🚀 Script de Optimización Automática

```bash
#!/bin/bash
# optimize.sh - Optimización automática del sistema

echo "🚀 OPTIMIZACIÓN AUTOMÁTICA INICIADA"

# 1. Limpiar cache Redis si está muy lleno
echo "🔴 Limpiando cache Redis..."
docker-compose exec -T redis redis-cli info memory | grep used_memory_human
docker-compose exec -T redis redis-cli FLUSHDB

# 2. Reiniciar OPcache
echo "⚡ Reiniciando OPcache..."
docker-compose exec -T app php -r "opcache_reset(); echo 'OPcache reset OK\n';"

# 3. Optimizar tablas MySQL
echo "🗄️ Optimizando tablas MySQL..."
docker-compose exec -T app php spark db:optimize

# 4. Verificar que todo funcione
echo "✅ Verificando servicios..."
docker-compose exec -T redis redis-cli ping
docker-compose exec -T app php -r "echo 'PHP OK\n';"
docker-compose exec -T db mysql -u root -p${DB_PASSWORD:-root} -e "SELECT 'MySQL OK';" 2>/dev/null

echo "🎉 OPTIMIZACIÓN COMPLETADA"
```

## 📈 Monitoreo Continuo

### 🔍 Alertas de Rendimiento

```bash
# Script para monitoreo continuo (cron job)
#!/bin/bash

# Verificar OPcache hit rate
HIT_RATE=$(docker-compose exec -T app php -r "
\$status = opcache_get_status();
echo round(\$status['opcache_statistics']['opcache_hit_rate'], 2);
")

if (( $(echo "$HIT_RATE < 90" | bc -l) )); then
    echo "⚠️  ALERTA: OPcache hit rate bajo: $HIT_RATE%"
fi

# Verificar memoria Redis
REDIS_MEMORY=$(docker-compose exec -T redis redis-cli info memory | grep used_memory_human | cut -d: -f2 | tr -d '\r')
echo "Redis memory usage: $REDIS_MEMORY"

# Verificar conexiones MySQL
CONNECTIONS=$(docker-compose exec -T db mysql -u root -p${DB_PASSWORD} -e "SHOW STATUS LIKE 'Threads_connected';" 2>/dev/null | tail -1 | awk '{print $2}')
echo "MySQL connections: $CONNECTIONS"
```

## 🎯 Configuración Avanzada

### 📊 Tuning para Diferentes Cargas

#### 🏠 Desarrollo Local (Pocos usuarios)

```ini
# PHP
opcache.memory_consumption = 64
memory_limit = 512M

# MySQL
innodb_buffer_pool_size = 256M
query_cache_size = 16M

# Redis
maxmemory = 128mb
```

#### 🏢 Producción Media (100-500 usuarios)

```ini
# PHP
opcache.memory_consumption = 256
memory_limit = 1024M

# MySQL
innodb_buffer_pool_size = 1G
query_cache_size = 64M

# Redis
maxmemory = 512mb
```

#### 🚀 Producción Alta (1000+ usuarios)

```ini
# PHP
opcache.memory_consumption = 512
memory_limit = 2048M

# MySQL
innodb_buffer_pool_size = 4G
query_cache_size = 128M

# Redis
maxmemory = 2gb
```

## 📞 Soporte y Escalación

### 🆘 Cuando Contactar Soporte

1. **OPcache hit rate < 80%** por más de 1 hora
2. **Redis connection errors** persistentes
3. **MySQL slow queries > 50%** del total
4. **Memory usage > 90%** en cualquier contenedor
5. **Response time > 10 segundos** consistentemente

### 📋 Información a Recopilar

```bash
# Ejecutar este comando y enviar el output
docker-compose logs --tail=100 > logs-$(date +%Y%m%d-%H%M%S).txt
docker stats --no-stream >> logs-$(date +%Y%m%d-%H%M%S).txt
```

---

_Guía de troubleshooting actualizada - 28 de Julio, 2025_ 🔧
