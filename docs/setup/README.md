# 🏕️ Yagaruete Camp - Guía de Instalación

## 📋 Descripción General

**Yagaruete Camp** es un sistema de e-commerce especializado en productos de camping y actividades al aire libre, desarrollado con CodeIgniter 4. Ofrece una plataforma completa para la gestión de productos, usuarios, ventas y experiencias de cliente.

## 🚀 Instalación Rápida con Docker

La forma más sencilla de poner en marcha el proyecto es usando Docker:

```bash
# Clonar el repositorio
git clone https://github.com/NahimMartinez/proyecto_Martinez_Gonzalez.git
cd yagaruete-camp

# Iniciar el proyecto
./scripts/setup/deploy.sh start

# Inicializar la base de datos
./scripts/setup/init-database.sh
```

¡Y listo! La aplicación estará disponible en:
- **Aplicación**: http://localhost:8080
- **PHPMyAdmin**: http://localhost:8081
- **MailHog**: http://localhost:8025

## 📋 Requisitos del Sistema

### Opción 1: Docker (Recomendado)
- Docker Desktop 4.0+
- Docker Compose 2.0+
- 4GB RAM disponible
- 2GB espacio en disco

### Opción 2: Instalación Manual
- PHP 8.1 o superior
- MySQL 5.7+ o MariaDB 10.3+
- Composer 2.0+
- Servidor web (Apache/Nginx)

## 🐳 Instalación con Docker

### 1. Preparar el Entorno

```bash
# Clonar el proyecto
git clone https://github.com/NahimMartinez/proyecto_Martinez_Gonzalez.git
cd yagaruete-camp

# Copiar configuración (opcional)
cp docker-compose.override.yml.example docker-compose.override.yml
```

### 2. Configurar Variables de Entorno

El proyecto incluye configuración automática para Docker, pero puedes personalizar:

```bash
# Editar configuración si es necesario
nano docker-compose.override.yml
```

### 3. Iniciar Servicios

```bash
# Opción A: Script automatizado (recomendado)
./scripts/setup/deploy.sh start

# Opción B: Docker Compose directo
docker-compose up -d
```

### 4. Inicializar Base de Datos

```bash
# Ejecutar migraciones y seeders
./scripts/setup/init-database.sh

# O de forma manual
docker-compose exec app php spark migrate
docker-compose exec app php spark db:seed DatabaseSeeder
```

### 5. Verificar Instalación

```bash
# Verificar estado de servicios
./scripts/maintenance/healthcheck.sh

# Verificar datos
docker-compose exec app php scripts/maintenance/verify-data.php
```

## 💻 Instalación Manual

### 1. Preparar el Entorno

```bash
# Clonar el proyecto
git clone https://github.com/NahimMartinez/proyecto_Martinez_Gonzalez.git
cd yagaruete-camp

# Instalar dependencias
composer install
```

### 2. Configurar Base de Datos

```bash
# Copiar configuración
cp env .env
```

Editar `.env`:
```env
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost/yagaruete-camp/'

database.default.hostname = localhost
database.default.database = yagaruete_camp
database.default.username = tu_usuario
database.default.password = tu_password
```

### 3. Crear Base de Datos

```sql
CREATE DATABASE yagaruete_camp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 4. Ejecutar Migraciones

```bash
# Ejecutar migraciones
php spark migrate

# Ejecutar seeders
php spark db:seed DatabaseSeeder
```

### 5. Configurar Permisos

```bash
# Linux/Mac
chmod -R 755 writable/

# Windows (PowerShell como administrador)
icacls writable /grant Everyone:(OI)(CI)F /T
```

## 🔧 Configuración Post-Instalación

### Verificar Funcionalidad

1. **Acceder a la aplicación**
   - URL: http://localhost:8080 (Docker) o tu URL configurada
   - Credenciales admin: admin@test.com / admin123

2. **Verificar servicios**
   ```bash
   ./scripts/maintenance/healthcheck.sh --verbose
   ```

3. **Verificar datos**
   ```bash
   docker-compose exec app php scripts/maintenance/verify-data.php
   ```

### Configuraciones Adicionales

#### Email (MailHog)
En Docker, MailHog está configurado automáticamente en http://localhost:8025

#### Uploads
```bash
# Configurar directorio de uploads
mkdir -p writable/uploads
chmod 755 writable/uploads
```

## 👥 Usuarios por Defecto

El sistema incluye usuarios de prueba:

| Rol | Email | Contraseña | Descripción |
|-----|-------|------------|-------------|
| Administrador | admin@test.com | admin123 | Acceso completo al sistema |
| Cliente | cliente@test.com | cliente123 | Acceso de cliente estándar |

## 🛠️ Comandos Útiles

### Scripts de Mantenimiento

```bash
# Estado del sistema
./scripts/maintenance/healthcheck.sh

# Backup de BD
./scripts/maintenance/backup.sh

# Limpiar sistema
./scripts/maintenance/cleanup.sh

# Verificar integridad
docker-compose exec app php scripts/maintenance/verify-data.php
```

### Scripts de Desarrollo

```bash
# Reiniciar servicios
./scripts/setup/deploy.sh restart

# Ver logs
./scripts/setup/deploy.sh logs

# Reset completo
./scripts/setup/deploy.sh reset
```

## 🌐 URLs de Acceso

Una vez instalado, tendrás acceso a:

| Servicio | URL | Descripción |
|----------|-----|-------------|
| Aplicación | http://localhost:8080 | Sitio principal |
| PHPMyAdmin | http://localhost:8081 | Administrador de BD |
| MailHog | http://localhost:8025 | Servidor de email de pruebas |

## ❗ Solución de Problemas

### Puerto ya en uso
```bash
# Verificar puertos en uso
netstat -tlnp | grep :8080

# Cambiar puertos en docker-compose.override.yml
```

### Permisos de archivos
```bash
# Linux/Mac
sudo chown -R $USER:$USER writable/
chmod -R 755 writable/

# Windows (PowerShell como administrador)
takeown /f writable /r
icacls writable /grant Everyone:(OI)(CI)F /T
```

### Base de datos no conecta
```bash
# Verificar contenedor MySQL
docker-compose ps mysql

# Ver logs de MySQL
docker-compose logs mysql

# Resetear base de datos
./scripts/setup/init-database.sh --force
```

### Servicios no inician
```bash
# Verificar Docker
docker --version
docker-compose --version

# Limpiar Docker
docker system prune -f

# Reiniciar desde cero
./scripts/setup/deploy.sh clean
./scripts/setup/deploy.sh start
```

## 📚 Documentación Adicional

- [Configuración Docker Detallada](docker-setup.md)
- [Guía de Solución de Problemas](troubleshooting.md)
- [Documentación de Base de Datos](../database/)
- [Arquitectura del Sistema](../architecture/)

## 🤝 Soporte

Si tienes problemas con la instalación:

1. Verifica que cumples todos los requisitos
2. Revisa la [guía de troubleshooting](troubleshooting.md)
3. Ejecuta el healthcheck: `./scripts/maintenance/healthcheck.sh --verbose`
4. Revisa los logs: `./scripts/setup/deploy.sh logs`

## 📄 Licencia

Este proyecto está bajo la Licencia MIT.

---

**Yagaruete Camp** - Tu destino para aventuras al aire libre 🏕️
