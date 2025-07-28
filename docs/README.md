# 📚 Documentación Técnica - Yagaruete Camp

> Documentación completa del sistema de e-commerce con optimizaciones de alto rendimiento

## 🚀 Optimizaciones de Rendimiento

El sistema Yagaruete Camp incluye **optimizaciones avanzadas** que proporcionan:

- **60-80% mejora** en velocidad de carga
- **30-50% consultas más rápidas** en base de datos
- **Cache inteligente** con Redis
- **Arquitectura escalable** lista para producción

### 📊 Documentación de Performance

- **[🚀 Optimizaciones Generales](performance/README.md)** - Resumen completo de todas las optimizaciones
- **[🔧 Troubleshooting](performance/troubleshooting.md)** - Solución de problemas de rendimiento
- **[⚙️ Configuración Producción](performance/production-config.md)** - Setup optimizado para producción

## 🏗️ Arquitectura del Sistema

### Componentes Principales

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│     Nginx       │    │    PHP-FPM      │    │     MySQL       │
│   (Web Server)  │────│   (CodeIgniter) │────│   (Database)    │
│  + FastCGI      │    │   + OPcache     │    │  + Query Cache  │
└─────────────────┘    └─────────────────┘    └─────────────────┘
          │                       │                       │
          │              ┌─────────────────┐              │
          └──────────────│     Redis       │──────────────┘
                         │    (Cache)      │
                         │  + Sessions     │
                         └─────────────────┘
```

### Stack Tecnológico Optimizado

| Componente      | Tecnología       | Optimización Principal      |
| --------------- | ---------------- | --------------------------- |
| **Web Server**  | Nginx 1.24+      | FastCGI buffers aumentados  |
| **PHP Runtime** | PHP 8.2+         | OPcache habilitado          |
| **Framework**   | CodeIgniter 4.5+ | Cache Redis integrado       |
| **Database**    | MySQL 8.0+       | Query cache + Buffer pool   |
| **Cache**       | Redis 7.2+       | Distributed caching         |
| **Containers**  | Docker Compose   | Resource limits optimizados |

## 📋 Guías de Instalación

### 🚀 Setup Rápido

1. **[Quick Start Guide](../README.md#-setup-rápido)** - Instalación en 4 pasos
2. **[Docker Setup](setup/docker-setup.md)** - Configuración detallada Docker
3. **[Troubleshooting](setup/troubleshooting.md)** - Solución de problemas comunes

### ⚙️ Configuración Avanzada

- **[Variables de Entorno](security/environment-security.md)** - Configuración .env segura
- **[Base de Datos](database/README.md)** - Migraciones y seeders
- **[Performance Tuning](performance/README.md)** - Optimizaciones aplicadas

## 🗄️ Base de Datos

### Estructura y Optimizaciones

- **[📊 Esquema de BD](database/schema.md)** - Estructura completa de tablas
- **[🔄 Migraciones](database/migrations.md)** - Sistema de versionado de BD
- **[🌱 Seeders](database/seeders.md)** - Datos iniciales y de prueba
- **[⚡ Query Optimization](database/performance.md)** - Optimizaciones de consultas

### Datos de Prueba Incluidos

| Tabla        | Registros     | Descripción                 |
| ------------ | ------------- | --------------------------- |
| `usuarios`   | 9 usuarios    | Admin + Clientes de prueba  |
| `categorias` | 6 categorías  | Productos outdoor completos |
| `productos`  | 23 productos  | Catálogo completo camping   |
| `ventas_*`   | Datos muestra | Ejemplos de transacciones   |

## 🔒 Seguridad

### Configuración Segura

- **[🔐 Environment Security](security/environment-security.md)** - Manejo seguro de .env
- **[🛡️ Production Security](security/production.md)** - Hardening para producción
- **[🔑 Authentication](security/auth.md)** - Sistema de autenticación

### Buenas Prácticas Implementadas

- ✅ **Password hashing** con bcrypt
- ✅ **CSRF protection** en formularios
- ✅ **Input validation** en todos los endpoints
- ✅ **SQL injection prevention** con prepared statements
- ✅ **XSS protection** con output escaping
- ✅ **Session security** con httpOnly cookies

## 🐳 Docker y DevOps

### Contenedores Optimizados

```yaml
# Servicios incluidos:
- app: # PHP 8.2 + CodeIgniter + OPcache
- nginx: # Web server optimizado
- db: # MySQL 8.0 + Query cache
- redis: # Cache distribuido
- phpmyadmin: # Administración BD
- mailhog: # Testing de emails
```

### Scripts de Automatización

- **[🚀 Deploy Scripts](../scripts/setup/)** - Automatización de despliegue
- **[🔧 Maintenance](../scripts/maintenance/)** - Scripts de mantenimiento
- **[📊 Monitoring](performance/troubleshooting.md#-herramientas-de-diagnóstico)** - Herramientas de monitoreo

## 📊 Métricas y Monitoreo

### KPIs de Rendimiento

| Métrica              | Objetivo | Comando de Verificación      |
| -------------------- | -------- | ---------------------------- |
| **OPcache Hit Rate** | > 95%    | `opcache_get_status()`       |
| **Redis Hit Rate**   | > 90%    | `redis-cli info stats`       |
| **Query Cache Hit**  | > 80%    | `SHOW STATUS LIKE 'Qcache%'` |
| **Response Time**    | < 500ms  | `curl -w "%{time_total}"`    |
| **Memory Usage**     | < 80%    | `docker stats`               |

### Herramientas de Diagnóstico

```bash
# Health Check Completo
./scripts/maintenance/healthcheck.sh

# Performance Test
curl -w "@curl-format.txt" -s -o /dev/null http://localhost:8080

# Resource Monitoring
docker stats --no-stream
```

## 🎯 Roadmap de Optimizaciones

### ✅ Implementado (Actual)

- **OPcache PHP** para cache de bytecode
- **Redis Cache** para consultas frecuentes
- **MySQL Query Cache** para optimización DB
- **Nginx FastCGI** con buffers optimizados

### 🔄 En Consideración (Futuro)

- **CDN Integration** para assets estáticos
- **Database Replication** para escalabilidad
- **Load Balancing** para múltiples instancias
- **APM Integration** para monitoreo avanzado

## 📞 Soporte Técnico

### 🆘 Escalación de Problemas

#### Nivel 1 - Auto-diagnóstico

1. Revisar [Troubleshooting Guide](performance/troubleshooting.md)
2. Ejecutar health check: `./scripts/maintenance/healthcheck.sh`
3. Verificar logs: `docker-compose logs --tail=50`

#### Nivel 2 - Configuración

1. Revisar [Production Config](performance/production-config.md)
2. Verificar variables de entorno
3. Validar configuraciones de performance

#### Nivel 3 - Soporte Avanzado

- **Performance issues** persistentes
- **Scale planning** para alta carga
- **Custom optimizations** específicas

### 📋 Información para Soporte

```bash
# Recopilar información de diagnóstico
echo "=== SYSTEM INFO ===" > support-info.txt
docker-compose ps >> support-info.txt
docker stats --no-stream >> support-info.txt
docker-compose logs --tail=100 >> support-info.txt
```

## 📚 Referencias Externas

### Documentación Oficial

- **[CodeIgniter 4 Documentation](https://codeigniter.com/user_guide/)** - Framework oficial
- **[PHP OPcache Manual](https://www.php.net/manual/en/book.opcache.php)** - Optimización PHP
- **[Redis Documentation](https://redis.io/documentation)** - Cache distribuido
- **[MySQL Performance Tuning](https://dev.mysql.com/doc/refman/8.0/en/optimization.html)** - Optimización BD
- **[Nginx Performance Guide](https://nginx.org/en/docs/http/ngx_http_core_module.html)** - Web server

### Recursos de Aprendizaje

- **[Docker Best Practices](https://docs.docker.com/develop/best-practices/)** - Contenedores optimizados
- **[Web Performance Optimization](https://developers.google.com/web/fundamentals/performance)** - Performance web
- **[Database Optimization Techniques](https://use-the-index-luke.com/)** - Optimización SQL

---

**Yagaruete Camp** - Sistema de e-commerce de alto rendimiento
_Documentación actualizada: 28 de Julio, 2025_ 📚🚀
