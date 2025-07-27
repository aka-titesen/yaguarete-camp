# 🔧 Solución de Problemas - Yagaruete Camp

## 🚨 Problemas Comunes y Soluciones

### � Problemas con Scripts

#### El comando deploy.bat/deploy.sh no funciona

**Síntomas:**
- "Command not found"
- "Permission denied"
- "No such file or directory"

**Soluciones:**

```bash
# Windows - Ejecutar desde PowerShell o CMD
cd yagaruete-camp
scripts\setup\deploy.bat start

# Linux/Mac - Dar permisos
chmod +x scripts/setup/deploy.sh
./scripts/setup/deploy.sh start

# Si nada funciona - Docker directo
docker-compose up -d
```

#### Docker no está instalado o corriendo

**Síntomas:**
- "Docker not found"
- "Docker daemon not running"
- Scripts se detienen con error de Docker

**Soluciones:**
1. **Instalar Docker Desktop:**
   - Windows/Mac: [Descargar aquí](https://docs.docker.com/desktop/)
   - Linux: `sudo apt install docker.io docker-compose`

2. **Iniciar Docker:**
   - Abrir Docker Desktop
   - Esperar que aparezca "Docker is running"
   - Verificar: `docker --version`

### 🐳 Problemas de Docker

#### Puerto ya en uso

**Síntomas:**
- Error: "Port 8080 is already in use"
- "bind: address already in use"

**Soluciones:**

```bash
# 1. Verificar qué proceso usa el puerto
# Windows
netstat -ano | findstr :8080

# Linux/Mac
sudo lsof -i :8080
```

```bash
# 2. Cambiar puerto en .env (crear si no existe)
echo "NGINX_PORT=8090" >> .env

# O editar docker-compose.yml temporalmente
# Cambiar "8080:80" por "8090:80"
```

#### Error de permisos

**Síntomas:**
- "Permission denied"
- "Cannot connect to Docker daemon"

**Soluciones:**

```bash
# Linux - Añadir usuario al grupo docker
sudo usermod -aG docker $USER
# Logout y login nuevamente

# Windows - Ejecutar como Administrador
# Clic derecho en PowerShell > "Ejecutar como administrador"
```

**Síntomas:**
- Docker containers exit immediately
- Services keep restarting
- Health checks failing

**Diagnóstico:**
```bash
# Ver estado detallado
docker-compose ps

# Ver logs de error
docker-compose logs app
docker-compose logs mysql
docker-compose logs nginx

# Verificar configuración
docker-compose config
```

**Soluciones:**
```bash
# 1. Reset completo del ambiente
./scripts/setup/deploy.sh clean
./scripts/setup/deploy.sh start

# 2. Verificar recursos disponibles
docker system df
docker system prune -f

# 3. Recrear volúmenes si es necesario
docker-compose down -v
docker-compose up -d
```

#### Problemas de volúmenes/permisos

**Síntomas:**
- Permission denied en writable/
- Cannot write to directory
- 500 Internal Server Error

**Soluciones:**

```bash
# Linux/Mac
sudo chown -R $USER:$USER writable/
chmod -R 755 writable/

# Para Docker
sudo chown -R 33:33 writable/  # www-data user
```

```powershell
# Windows (PowerShell como administrador)
takeown /f writable /r
icacls writable /grant Everyone:(OI)(CI)F /T
```

### 🗄️ Problemas de Base de Datos

#### MySQL no se conecta

**Síntomas:**
- SQLSTATE[HY000] [2002] Connection refused
- Database connection failed
- Unable to connect to database

**Diagnóstico:**
```bash
# Verificar estado de MySQL
docker-compose ps mysql
docker-compose logs mysql

# Probar conexión manual
docker-compose exec mysql mysql -u yagaruete_user -p
```

**Soluciones:**

```bash
# 1. Reiniciar MySQL
docker-compose restart mysql

# 2. Verificar variables de entorno
docker-compose exec app env | grep DB_

# 3. Reset completo de BD
./scripts/setup/init-database.sh --force

# 4. Recrear contenedor MySQL
docker-compose stop mysql
docker-compose rm mysql
docker volume rm yagaruete-camp_mysql_data  # ¡CUIDADO! Elimina datos
docker-compose up -d mysql
```

#### Tablas no existen

**Síntomas:**
- Table 'yagaruete_camp.usuarios' doesn't exist
- Base table or view not found
- No migrations found

**Soluciones:**
```bash
# 1. Ejecutar migraciones
docker-compose exec app php spark migrate

# 2. Verificar estado de migraciones
docker-compose exec app php spark migrate:status

# 3. Reset y regenerar BD
./scripts/setup/init-database.sh --force

# 4. Verificar integridad de datos
docker-compose exec app php scripts/maintenance/verify-data.php
```

#### Datos corruptos o inconsistentes

**Síntomas:**
- Foreign key constraint fails
- Data integrity violations
- Unexpected application behavior

**Soluciones:**
```bash
# 1. Verificar integridad
docker-compose exec app php scripts/maintenance/verify-data.php --verbose

# 2. Crear backup antes de reparar
./scripts/maintenance/backup.sh --name "pre-repair"

# 3. Reparar BD
docker-compose exec mysql mysqlcheck -u root -p --repair yagaruete_camp

# 4. Regenerar datos si es necesario
./scripts/setup/init-database.sh --force
```

### 🌐 Problemas de Aplicación Web

#### Error 500 - Internal Server Error

**Síntomas:**
- Pantalla blanca o error 500
- Application error page
- Server cannot complete request

**Diagnóstico:**
```bash
# Ver logs de aplicación
docker-compose logs app
docker-compose logs nginx

# Ver logs de CodeIgniter
docker-compose exec app tail -f writable/logs/log-$(date +%Y-%m-%d).log

# Verificar configuración PHP
docker-compose exec app php -v
docker-compose exec app php -m
```

**Soluciones:**

```bash
# 1. Verificar permisos writable/
docker-compose exec app ls -la writable/
docker-compose exec app chmod -R 755 writable/

# 2. Limpiar cache
./scripts/maintenance/cleanup.sh --cache

# 3. Verificar configuración CodeIgniter
docker-compose exec app cat app/Config/App.php | grep baseURL
docker-compose exec app cat .env | grep app.baseURL

# 4. Reset de aplicación
./scripts/setup/deploy.sh restart
```

#### Error 404 - Page Not Found

**Síntomas:**
- 404 para rutas existentes
- Only homepage works
- Routes not working

**Soluciones:**
```bash
# 1. Verificar configuración Nginx
docker-compose exec nginx nginx -t
docker-compose logs nginx

# 2. Verificar rutas de CodeIgniter
docker-compose exec app cat app/Config/Routes.php

# 3. Verificar .htaccess equivalente en Nginx
docker-compose exec nginx cat /etc/nginx/conf.d/default.conf

# 4. Reiniciar Nginx
docker-compose restart nginx
```

#### Problemas de autenticación

**Síntomas:**
- Cannot login with correct credentials
- Session not persisting
- Redirected to login repeatedly

**Soluciones:**
```bash
# 1. Verificar configuración de sesiones
docker-compose exec app cat app/Config/App.php | grep sessionDriver

# 2. Limpiar sesiones
./scripts/maintenance/cleanup.sh --cache
docker-compose exec redis redis-cli FLUSHALL

# 3. Verificar usuarios en BD
docker-compose exec mysql mysql -u yagaruete_user -p yagaruete_camp -e "SELECT * FROM usuarios LIMIT 5;"

# 4. Reset de passwords si es necesario
./scripts/setup/init-database.sh --skip-migrations
```

### 📧 Problemas de Email

#### MailHog no funciona

**Síntomas:**
- Emails not appearing in MailHog
- Cannot access MailHog interface
- SMTP connection failed

**Soluciones:**
```bash
# 1. Verificar estado de MailHog
docker-compose ps mailhog
docker-compose logs mailhog

# 2. Verificar configuración email
docker-compose exec app cat .env | grep MAIL_

# 3. Probar envío manual
docker-compose exec app php spark tinker
# En tinker: Email::send('test@example.com', 'Test', 'Test message');

# 4. Acceder a MailHog
# http://localhost:8025
```

### 🔧 Problemas de Performance

#### Aplicación lenta

**Síntomas:**
- Long page load times
- Database queries timeout
- High resource usage

**Diagnóstico:**
```bash
# Verificar recursos Docker
docker stats

# Ver procesos en contenedores
docker-compose exec app top
docker-compose exec mysql top

# Verificar logs de slow queries
docker-compose exec mysql mysql -u root -p -e "SHOW VARIABLES LIKE 'slow_query_log';"
```

**Soluciones:**
```bash
# 1. Optimizar configuración PHP
# Aumentar memory_limit en docker/php/php.ini

# 2. Optimizar MySQL
# Ajustar innodb_buffer_pool_size en docker/mysql/my.cnf

# 3. Habilitar cache
docker-compose exec app cat .env | grep CACHE_HANDLER

# 4. Limpiar logs antiguos
./scripts/maintenance/cleanup.sh --logs
```

### 🔒 Problemas de Seguridad

#### Archivos no seguros detectados

**Síntomas:**
- Security warnings in logs
- Suspicious file uploads
- Unauthorized access attempts

**Soluciones:**
```bash
# 1. Verificar directorio uploads
docker-compose exec app find writable/uploads -type f -name "*.php"

# 2. Limpiar uploads sospechosos
# ¡CUIDADO! Esto elimina TODOS los uploads
./scripts/maintenance/cleanup.sh --uploads

# 3. Verificar logs de acceso
docker-compose logs nginx | grep -E "(40[0-9]|50[0-9])"

# 4. Actualizar configuración de seguridad
# Revisar docker/nginx/snippets/security.conf
```

## 🛠️ Herramientas de Diagnóstico

### Health Check Completo

```bash
# Verificación completa del sistema
./scripts/maintenance/healthcheck.sh --verbose

# Verificación rápida
./scripts/maintenance/healthcheck.sh
```

### Scripts de Verificación

```bash
# Verificar integridad de datos
docker-compose exec app php scripts/maintenance/verify-data.php --verbose

# Verificar configuración
docker-compose config

# Verificar estado de servicios
./scripts/setup/deploy.sh status
```

### Logs Centralizados

```bash
# Todos los logs
./scripts/setup/deploy.sh logs

# Logs específicos con timestamps
./scripts/setup/deploy.sh logs -t app

# Seguir logs en tiempo real
./scripts/setup/deploy.sh logs -f app nginx mysql
```

## 🚨 Procedimientos de Emergencia

### Reset Completo del Sistema

```bash
# ADVERTENCIA: Esto eliminará TODOS los datos
./scripts/setup/deploy.sh clean
./scripts/setup/deploy.sh start
./scripts/setup/init-database.sh --force
```

### Backup de Emergencia

```bash
# Backup rápido antes de cambios importantes
./scripts/maintenance/backup.sh --name "emergency-$(date +%Y%m%d-%H%M%S)"

# Verificar backup
./scripts/maintenance/backup.sh --list
```

### Restaurar Sistema

```bash
# Restaurar desde backup
./scripts/maintenance/backup.sh --restore <archivo-backup>

# Verificar restauración
./scripts/maintenance/healthcheck.sh --verbose
docker-compose exec app php scripts/maintenance/verify-data.php
```

## 📞 Obtener Ayuda

### Información del Sistema

```bash
# Versiones de software
docker --version
docker-compose --version
./scripts/setup/deploy.sh --version

# Información del contenedor
docker-compose exec app php --version
docker-compose exec mysql mysql --version
docker-compose exec nginx nginx -v
```

### Generar Reporte de Debug

```bash
# Crear reporte completo para soporte
{
  echo "=== YAGARUETE CAMP DEBUG REPORT ==="
  echo "Date: $(date)"
  echo "=== Docker Version ==="
  docker --version
  echo "=== Container Status ==="
  docker-compose ps
  echo "=== Health Check ==="
  ./scripts/maintenance/healthcheck.sh
  echo "=== Recent Logs ==="
  docker-compose logs --tail=50
} > debug-report-$(date +%Y%m%d-%H%M%S).txt
```

### Contacto de Soporte

Si los problemas persisten:

1. **Ejecuta health check**: `./scripts/maintenance/healthcheck.sh --verbose`
2. **Genera reporte de debug** (comando anterior)
3. **Revisa esta guía** de troubleshooting
4. **Verifica logs**: `./scripts/setup/deploy.sh logs`

## 📋 Checklist de Diagnóstico

### 🔄 Comandos de Recuperación Rápida

#### Reset Completo (Recomendado)

```bash
# Windows
scripts\setup\deploy.bat reset

# Linux/Mac
./scripts/setup/deploy.sh reset
```

#### Comandos de Emergencia

```bash
# Parar todo y limpiar
docker-compose down -v
docker system prune -f

# Reconstruir desde cero
docker-compose up -d --build --force-recreate

# Ver estado detallado
docker-compose ps
docker-compose logs --tail=50
```

### 📋 Checklist de Diagnóstico

Antes de reportar un problema, verifica:

- [ ] ¿Docker Desktop está instalado y corriendo?
- [ ] ¿Ejecutaste `deploy.bat` o `deploy.sh` desde la carpeta correcta?
- [ ] ¿Los puertos 8080 y 8081 están disponibles?
- [ ] ¿Los contenedores están corriendo? (`docker-compose ps`)
- [ ] ¿Los logs muestran errores específicos? (`deploy.sh logs`)
- [ ] ¿Probaste hacer reset completo? (`deploy.sh reset`)

### 📞 Obtener Ayuda

Si nada funciona:

1. **Ejecuta diagnóstico:**
   ```bash
   docker --version
   docker-compose --version
   docker-compose ps
   docker-compose logs --tail=50
   ```

2. **Crea un issue:** [GitHub Issues](https://github.com/aka-titesen/yaguarete-camp/issues)
   - Incluye el output de los comandos de arriba
   - Describe qué estabas intentando hacer
   - Especifica tu sistema operativo (Windows/Mac/Linux)

---

**Yagaruete Camp** - Solución rápida y efectiva de problemas 🔧
