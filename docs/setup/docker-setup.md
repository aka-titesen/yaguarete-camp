# 🐳 Configuración Docker - Yagaruete Camp

## 📦 Arquitectura de Contenedores

Yagaruete Camp utiliza una arquitectura Docker multi-contenedor diseñada para ser robusta, escalable y fácil de mantener.

### 🏗️ Servicios de Desarrollo

| Servicio | Imagen | Puerto | Descripción | Credenciales |
|----------|--------|--------|-------------|--------------|
| **app** | PHP 8.2-FPM | 9000 | Aplicación CodeIgniter 4 | - |
| **nginx** | nginx:alpine | 8080 | Servidor web y proxy reverso | - |
| **db** | mysql:8.0 | 3306 | Base de datos principal | user: root, pass: dev_password_123 |
| **redis** | redis:alpine | 6379 | Cache y gestión de sesiones | - |
| **phpmyadmin** | phpmyadmin | 8081 | Administrador web de BD | user: root, pass: dev_password_123 |
| **mailhog** | mailhog | 8025 | Servidor SMTP de desarrollo | - |

### 🏢 Servicios de Producción

| Servicio | Imagen | Puerto | Descripción |
|----------|--------|--------|-------------|
| **app** | PHP 8.2-FPM | 9000 | Aplicación CodeIgniter 4 |
| **nginx** | nginx:alpine | 80, 443 | Servidor web con SSL |
| **db** | mysql:8.0 | (interno) | Base de datos (sin exposición) |
| **redis** | redis:alpine | (interno) | Cache y sesiones |

## 🚀 Despliegue Simplificado

### Inicio Súper Rápido

```bash
# Clonar e iniciar
git clone https://github.com/aka-titesen/yaguarete-camp.git
cd yaguarete-camp

# Windows
scripts\setup\deploy.bat

# Linux/Mac
./scripts/setup/deploy.sh
```

### Comandos Principales

| Comando | Función | Windows | Linux/Mac |
|---------|---------|---------|-----------|
| **start** | Iniciar servicios + migraciones + seeders | `deploy.bat start` | `./deploy.sh start` |
| **stop** | Detener servicios | `deploy.bat stop` | `./deploy.sh stop` |
| **restart** | Reiniciar servicios | `deploy.bat restart` | `./deploy.sh restart` |
| **logs** | Ver logs en tiempo real | `deploy.bat logs` | `./deploy.sh logs` |
| **reset** | Reset completo | `deploy.bat reset` | `./deploy.sh reset` |

## 🌐 URLs y Accesos

### Servicios Web de Desarrollo

| Servicio | URL | Credenciales | Descripción |
|----------|-----|--------------|-------------|
| **Aplicación** | http://localhost:8080 | - | Sitio web principal |
| **PHPMyAdmin** | http://localhost:8081 | user: `root`<br>pass: `dev_password_123` | Administrador de BD |
| **MailHog** | http://localhost:8025 | - | Testing de emails |

### Archivos de Configuración

#### Para Desarrollo (automático)
- **Docker Compose**: `docker-compose.yml`
- **Variables**: `.env` (generado automáticamente)
- **Comando**: `deploy.bat` o `deploy.sh`

#### Para Producción (manual)
- **Docker Compose**: `docker-compose.prod.yml`
- **Variables**: `.env` (basado en `.env.production.example`)
- **Comando**: `docker-compose -f docker-compose.prod.yml up -d`

## 🔧 Configuración de Variables de Entorno

### Archivo .env (Desarrollo)

El script genera automáticamente:

```bash
# Variables para desarrollo
CI_ENVIRONMENT=development
DB_DATABASE=bd_yagaruete_camp
DB_USERNAME=root
DB_PASSWORD=dev_password_123
DB_HOSTNAME=db
APP_URL=http://localhost:8080
MAIL_HOST=mailhog
MAIL_PORT=1025
```

### Archivo .env (Producción)

Para producción, copia `.env.production.example` a `.env` y modifica:

```bash
# Variables para producción
CI_ENVIRONMENT=production
DB_USERNAME=yagaruete_user
DB_PASSWORD=CAMBIAR_POR_PASSWORD_SUPER_SEGURO_123!
APP_URL=https://tu-dominio.com
MAIL_HOST=smtp.tu-proveedor.com
CI_DEBUG=false
```
|----------|-----|--------------|-------------|
| **Aplicación** | http://localhost:8080 | Ver [usuarios por defecto](#usuarios) | Sitio principal |
| **PHPMyAdmin** | http://localhost:8081 | root / yagaruete_password | Admin de BD |
| **MailHog** | http://localhost:8025 | - | Bandeja de emails |

### <a name="usuarios"></a>👥 Usuarios por Defecto

| Rol | Email | Contraseña | Perfil ID |
|-----|-------|------------|-----------|
| Administrador | admin@test.com | admin123 | 1 |
| Cliente | cliente@test.com | cliente123 | 2 |

## ⚙️ Configuración de Entorno

### Variables de Entorno Principales

```env
# Base de datos
DB_HOST=yagaruete_camp_mysql
DB_DATABASE=yagaruete_camp
DB_USERNAME=yagaruete_user
DB_PASSWORD=yagaruete_password

# Cache
CACHE_HANDLER=redis
REDIS_HOST=yagaruete_camp_redis

# Email (desarrollo)
MAIL_HOST=yagaruete_camp_mailhog
MAIL_PORT=1025
MAIL_USERNAME=
MAIL_PASSWORD=
```

### Personalización de Puertos

Crear `docker-compose.override.yml`:

```yaml
version: '3.8'
services:
  nginx:
    ports:
      - "8090:80"  # Cambiar puerto de la app
  
  phpmyadmin:
    ports:
      - "8082:80"  # Cambiar puerto de PHPMyAdmin
  
  mailhog:
    ports:
      - "8026:8025"  # Cambiar puerto de MailHog
```

## 📁 Estructura de Archivos Docker

```
docker/
├── nginx/
│   ├── nginx.conf              # Configuración principal
│   ├── default.conf            # Virtual host para la app
│   └── snippets/
│       ├── ssl.conf            # Configuración SSL
│       └── security.conf       # Headers de seguridad
├── php/
│   ├── Dockerfile              # Imagen personalizada PHP
│   ├── php.ini                 # Configuración PHP
│   ├── www.conf                # Configuración PHP-FPM
│   └── supervisor.conf         # Supervisor para procesos
├── mysql/
│   ├── init/
│   │   └── 01-create-database.sql  # Script de inicialización
│   └── conf.d/
│       └── mysql.cnf           # Configuración MySQL
└── redis/
    └── redis.conf              # Configuración Redis
```

## 🔧 Configuración Detallada

### PHP-FPM (Aplicación)

**Características:**
- PHP 8.2 con extensiones optimizadas
- Composer preinstalado
- Xdebug habilitado para desarrollo
- Supervisor para gestión de procesos

**Extensiones incluidas:**
- pdo_mysql, mysqli, redis
- gd, zip, curl, mbstring
- intl, opcache, xdebug

### Nginx (Servidor Web)

**Configuración optimizada:**
- Gzip habilitado
- Headers de seguridad
- Cache de archivos estáticos
- Proxy pass a PHP-FPM

**Performance:**
- Worker processes auto
- Keepalive habilitado
- Buffer sizes optimizados

### MySQL (Base de Datos)

**Configuración:**
- MySQL 8.0 con innodb optimizado
- Buffer pool size: 256M
- Max connections: 200
- Character set: utf8mb4

**Bases de datos:**
- `yagaruete_camp` (principal)
- `yagaruete_camp_test` (testing)

### Redis (Cache)

**Uso:**
- Cache de sesiones de CodeIgniter
- Cache de datos de aplicación
- Queue de trabajos (futuro)

## 🛠️ Desarrollo

### Ejecutar Comandos

```bash
# CodeIgniter Spark
./scripts/setup/deploy.sh exec app php spark migrate
./scripts/setup/deploy.sh exec app php spark db:seed DatabaseSeeder

# Composer
./scripts/setup/deploy.sh exec app composer install
./scripts/setup/deploy.sh exec app composer update

# Acceso directo a contenedor
docker-compose exec app bash
docker-compose exec mysql mysql -u root -p
```

### Hot Reload y Debugging

**Archivos sincronizados automáticamente:**
- `app/` → `/var/www/html/app/`
- `public/` → `/var/www/html/public/`
- `writable/` → `/var/www/html/writable/`

**Xdebug configurado:**
- Puerto: 9003
- IDE Key: PHPSTORM
- Auto start habilitado

### Testing

```bash
# PHPUnit
./scripts/setup/deploy.sh exec app vendor/bin/phpunit

# Testing con BD separada
./scripts/setup/deploy.sh exec app php spark migrate --env=testing
./scripts/setup/deploy.sh exec app vendor/bin/phpunit --env=testing
```

## 🚀 Producción

### Configuración de Producción

```yaml
# docker-compose.prod.yml
version: '3.8'
services:
  app:
    environment:
      - CI_ENVIRONMENT=production
      - APP_FORCE_HTTPS=true
    volumes:
      - ./docker/php/php-prod.ini:/usr/local/etc/php/conf.d/99-custom.ini
  
  nginx:
    volumes:
      - ./docker/nginx/prod.conf:/etc/nginx/conf.d/default.conf
      - ./ssl:/etc/nginx/ssl
```

### Variables de Producción

```env
# Generar claves seguras
APP_ENCRYPTION_KEY=<clave-32-caracteres>
DB_ROOT_PASSWORD=<password-muy-seguro>
DB_PASSWORD=<password-aplicacion-seguro>

# Configurar dominio
APP_BASE_URL=https://yagaruete-camp.com
APP_FORCE_HTTPS=true

# Cache y performance
CACHE_HANDLER=redis
APP_DEBUG=false
```

### SSL/TLS

```bash
# Generar certificados (desarrollo)
mkdir -p docker/nginx/ssl
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout docker/nginx/ssl/nginx.key \
  -out docker/nginx/ssl/nginx.crt
```

## 📊 Monitoreo y Debugging

### Logs de Sistema

```bash
# Todos los servicios
./scripts/setup/deploy.sh logs

# Servicio específico
./scripts/setup/deploy.sh logs app
./scripts/setup/deploy.sh logs nginx
./scripts/setup/deploy.sh logs mysql

# Seguir logs en tiempo real
./scripts/setup/deploy.sh logs -f app
```

### Health Checks

```bash
# Verificación completa del sistema
./scripts/maintenance/healthcheck.sh --verbose

# Estado de contenedores
docker-compose ps

# Recursos utilizados
docker stats
```

### Debugging de Base de Datos

```bash
# Conectar a MySQL
docker-compose exec mysql mysql -u yagaruete_user -p yagaruete_camp

# Exportar BD
./scripts/maintenance/backup.sh

# Importar BD
./scripts/maintenance/backup.sh --restore backup.sql
```

## 🛡️ Seguridad

### Desarrollo

- **Credenciales por defecto** para facilidad de desarrollo
- **Xdebug habilitado** para debugging
- **Logs detallados** para troubleshooting
- **Auto-reload** de archivos

### Producción

- **Variables de entorno seguras** sin valores por defecto
- **SSL/TLS obligatorio** para todas las conexiones
- **Headers de seguridad** configurados en Nginx
- **Logs mínimos** para mejor rendimiento
- **OPcache habilitado** para mejor performance

### Buenas Prácticas

```bash
# Usar secrets en producción
echo "mi_password_seguro" | docker secret create db_password -

# Configurar firewalls
ufw allow 80
ufw allow 443
ufw deny 3306  # MySQL solo interno
```

## 🔄 Backup y Recuperación

### Backup Automático

```bash
# Backup completo
./scripts/maintenance/backup.sh --verbose

# Backup con nombre personalizado
./scripts/maintenance/backup.sh --name "pre-update"

# Solo estructura
./scripts/maintenance/backup.sh --no-data
```

### Restauración

```bash
# Restaurar backup
./scripts/maintenance/backup.sh --restore backup.sql

# Listar backups disponibles
./scripts/maintenance/backup.sh --list
```

### Backup de Volúmenes

```bash
# Backup completo con volúmenes
docker run --rm \
  -v yagaruete-camp_mysql_data:/data \
  -v $(pwd)/backups:/backup \
  alpine tar czf /backup/mysql_volumes_$(date +%Y%m%d_%H%M%S).tar.gz -C /data .
```

## 🔧 Troubleshooting

### Problemas Comunes

**Puerto ya en uso:**
```bash
# Ver qué proceso usa el puerto
netstat -tlnp | grep :8080
sudo lsof -i :8080

# Cambiar puerto en docker-compose.override.yml
```

**Contenedores no inician:**
```bash
# Ver logs detallados
docker-compose logs app

# Limpiar y reiniciar
./scripts/setup/deploy.sh clean
./scripts/setup/deploy.sh start
```

**Base de datos no conecta:**
```bash
# Verificar estado MySQL
docker-compose ps mysql
docker-compose logs mysql

# Reset de BD
./scripts/setup/init-database.sh --force
```

**Permisos de archivos:**
```bash
# Linux/Mac
sudo chown -R $USER:$USER writable/
chmod -R 755 writable/

# Windows (PowerShell como admin)
takeown /f writable /r
icacls writable /grant Everyone:(OI)(CI)F /T
```

### Comandos de Diagnóstico

```bash
# Sistema completo
./scripts/maintenance/healthcheck.sh --verbose

# Docker específico
docker system df
docker system prune -f

# Verificar configuración
docker-compose config
```

## 📚 Referencias

- [Docker Compose Reference](https://docs.docker.com/compose/)
- [CodeIgniter 4 Documentation](https://codeigniter.com/user_guide/)
- [Nginx Configuration](https://nginx.org/en/docs/)
- [MySQL 8.0 Reference](https://dev.mysql.com/doc/refman/8.0/en/)

---

**Yagaruete Camp** - Configuración Docker profesional para desarrollo y producción 🐳
