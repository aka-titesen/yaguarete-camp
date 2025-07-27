# 🦎 Yagaruete Camp - Inicio Rápido

## Requisitos

- **Solo Docker Desktop** (incluye Docker Compose)
  - Windows: [Descargar Docker Desktop](https://docs.docker.com/desktop/windows/)
  - macOS: [Descargar Docker Desktop](https://docs.docker.com/desktop/mac/)
  - Linux: [Instalar Docker](https://docs.docker.com/engine/install/) y [Docker Compose](https://docs.docker.com/compose/install/)

## Inicio Rápido (3 pasos)

### 1. Clona el repositorio
```bash
git clone https://github.com/aka-titesen/yaguarete-camp.git
cd yaguarete-camp
```

### 2. Ejecuta el script de inicio

**Windows:**
```cmd
scripts\setup\deploy.bat
```

**Linux/macOS:**
```bash
chmod +x scripts/setup/deploy.sh
./scripts/setup/deploy.sh
```

### 3. ¡Listo! 🎉

- **Aplicación:** http://localhost:8080
- **PHPMyAdmin:** http://localhost:8081 (user: root, pass: root)
- **MailHog:** http://localhost:8025 (testing de emails)

## Configuración de Entorno

El script crea automáticamente un archivo `.env` con configuración básica:

```bash
# Variables para desarrollo
CI_ENVIRONMENT=development
DB_DATABASE=bd_yagaruete_camp
DB_USERNAME=root
DB_PASSWORD=root
DB_HOSTNAME=db
APP_URL=http://localhost:8080
```

**🔒 Buenas prácticas de .env:**
- ✅ El `.env` NO se sube a git (está en .gitignore)
- ✅ Cada entorno tiene su propio `.env`
- ✅ Para producción, cambiar passwords por seguros
- ✅ Docker lee automáticamente las variables del `.env`

## Comandos Disponibles

### Windows
```cmd
scripts\setup\deploy.bat start    # Iniciar aplicación (por defecto)
scripts\setup\deploy.bat stop     # Detener aplicación
scripts\setup\deploy.bat restart  # Reiniciar aplicación
scripts\setup\deploy.bat logs     # Ver logs en tiempo real
scripts\setup\deploy.bat reset    # Reset completo (elimina datos)
```

### Linux/macOS
```bash
./scripts/setup/deploy.sh start    # Iniciar aplicación (por defecto)
./scripts/setup/deploy.sh stop     # Detener aplicación
./scripts/setup/deploy.sh restart  # Reiniciar aplicación
./scripts/setup/deploy.sh logs     # Ver logs en tiempo real
./scripts/setup/deploy.sh reset    # Reset completo (elimina datos)
```

## ¿Qué hace el script automáticamente?

1. ✅ Verifica que Docker esté instalado y corriendo
2. ✅ Crea archivo `.env` con configuración básica para desarrollo
3. ✅ Construye e inicia los contenedores Docker
4. ✅ Ejecuta las migraciones de base de datos
5. ✅ Carga los datos iniciales (seeders)

## Estructura de Servicios

| Servicio | Puerto | Descripción | Credenciales |
|----------|--------|-------------|--------------|
| **app** | - | Aplicación CodeIgniter 4 (PHP-FPM) | - |
| **nginx** | 8080 | Servidor web | - |
| **db** | 3306 | Base de datos MySQL 8.0 | user: root, pass: root |
| **redis** | 6379 | Cache Redis | - |
| **phpmyadmin** | 8081 | Administrador de BD | user: root, pass: root |
| **mailhog** | 8025 | Testing de emails | - |

## Solución de Problemas

### El comando no funciona
- **Windows:** Asegúrate de ejecutar desde PowerShell o CMD
- **Linux/macOS:** Dale permisos: `chmod +x scripts/setup/deploy.sh`

### Docker no está corriendo
- Inicia Docker Desktop y espera que esté completamente cargado
- Verifica con: `docker --version`

### Puerto ocupado
- Si el puerto 8080 está ocupado, detén otros servicios
- O modifica el puerto en `docker-compose.yml`

### Reset completo
```bash
# Windows
scripts\setup\deploy.bat reset

# Linux/macOS
./scripts/setup/deploy.sh reset
```

## Desarrollo

Para desarrollo activo, puedes usar:
```bash
# Ver logs en tiempo real
scripts/setup/deploy.sh logs

# Acceder al contenedor de la aplicación
docker-compose exec app bash

# Ejecutar comandos de CodeIgniter
docker-compose exec app php spark migrate
docker-compose exec app php spark db:seed
```

---

**¿Problemas?** Abre un [issue](https://github.com/aka-titesen/yaguarete-camp/issues) en GitHub.
