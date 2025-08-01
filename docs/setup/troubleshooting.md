# 🔧 Solución de Problemas - Yagaruete Camp

## 🚨 Problemas Comunes y Soluciones

### 🐳 Problemas de Docker

#### Docker no está instalado o corriendo

**Síntomas:**

- "Docker not found"
- "Docker daemon not running"
- Comandos de Docker fallan

**Soluciones:**

1. **Instalar Docker Desktop:**

   - Windows/Mac: [Descargar aquí](https://docs.docker.com/desktop/)
   - Linux: `sudo apt install docker.io docker-compose`

2. **Iniciar Docker:**
   - Abrir Docker Desktop
   - Esperar que aparezca "Docker is running"
   - Verificar: `docker --version`

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
# 2. Cambiar puerto en .env si el 8080 está ocupado
echo "APACHE_PORT=8090" >> .env

# O detener el servicio que usa el puerto
docker compose down
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

#### Contenedores se reinician constantemente

**Síntomas:**

- Docker containers exit immediately
- Services keep restarting
- Health checks failing

**Diagnóstico:**

```bash
# Ver estado detallado
docker compose ps

# Ver logs de error
docker compose logs

# Ver logs de un servicio específico
docker compose logs app
docker compose logs db
```

**Soluciones:**

```bash
# 1. Limpiar todo y empezar de nuevo
docker compose down -v
docker system prune -f
docker compose up -d --build
```

### 📂 Problemas de Archivos

#### Archivo .env no existe

**Síntomas:**

- Variables de entorno no definidas
- Errores de configuración

**Solución:**

```bash
# Copiar archivo de ejemplo
copy .env.example .env    # Windows
cp .env.example .env      # Linux/macOS
```

#### Problemas de permisos en archivos

**Síntomas:**

- "Permission denied" al escribir archivos
- Logs no se generan

**Solución:**

```bash
# Asegurar permisos correctos
docker compose exec app chown -R www:www /var/www/html/writable
docker compose exec app chmod -R 755 /var/www/html/writable
```

### 🗄️ Problemas de Base de Datos

#### No se puede conectar a la base de datos

**Síntomas:**

- "Connection refused"
- "Access denied for user"

**Soluciones:**

```bash
# 1. Verificar que MySQL esté corriendo
docker compose ps db

# 2. Verificar logs de MySQL
docker compose logs db

# 3. Reiniciar base de datos
docker compose restart db

# 4. Limpiar volumen y recrear
docker compose down -v
docker compose up -d
```

#### Tablas no existen

**Síntomas:**

- "Table doesn't exist" errors
- Base de datos vacía

**Solución:**

```bash
# Ejecutar migraciones
docker compose exec app php spark migrate

# Ejecutar seeders
docker compose exec app php spark db:seed
```

#### Error en migraciones

**Síntomas:**

- Migration failed
- Duplicate column errors

**Solución:**

```bash
# Reset completo de base de datos
docker compose exec app php spark migrate:rollback
docker compose exec app php spark migrate
docker compose exec app php spark db:seed
```

### 🌐 Problemas de Conectividad

#### No se puede acceder a localhost:8080

**Síntomas:**

- "This site can't be reached"
- Timeout errors

**Diagnóstico:**

```bash
# Verificar que apache esté corriendo
docker compose ps apache

# Verificar logs
docker compose logs apache
```

**Soluciones:**

```bash
# 1. Reiniciar apache
docker compose restart apache

# 2. Verificar puerto en .env
cat .env | grep APP_URL

# 3. Probar puerto alternativo
# Editar .env y cambiar puerto en docker-compose.yml
```

#### PhpMyAdmin no carga

**Soluciones:**

```bash
# Verificar servicio
docker compose ps phpmyadmin

# Reiniciar
docker compose restart phpmyadmin

# Acceder en: http://localhost:8081
# Usuario: root, Password: dev_password_123
```

### 🔧 Comandos de Diagnóstico

#### Verificación completa del sistema

```bash
# 1. Estado de todos los servicios
docker compose ps

# 2. Logs generales
docker compose logs --tail=50

# 3. Uso de recursos
docker stats

# 4. Verificar configuración
docker compose config

# 5. Test de conectividad
curl http://localhost:8080
```

#### Limpieza completa

```bash
# Detener todo
docker compose down -v

# Limpiar imágenes, contenedores y volúmenes
docker system prune -a -f

# Reconstruir desde cero
docker compose up -d --build

# Configurar base de datos
docker compose exec app php spark migrate
docker compose exec app php spark db:seed
```

### 🚑 Soporte Adicional

Si los problemas persisten:

1. **Verificar logs detallados:**

   ```bash
   docker compose logs --follow
   ```

2. **Revisar configuración de Docker:**

   ```bash
   docker version
   docker compose version
   ```

3. **Consultar documentación:**

   - [README.md principal](../../README.md)
   - [Docker setup](docker-setup.md)

4. **Reportar issues:**
   - Incluir logs completos
   - Versión de Docker
   - Sistema operativo
   - Pasos para reproducir el problema
