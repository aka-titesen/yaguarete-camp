# 🛠️ Comandos Útiles - Yagaruete Camp

## Scripts Principales

### Windows
```cmd
# Iniciar aplicación completa (incluye migraciones y seeders)
scripts\setup\deploy.bat start

# Detener aplicación
scripts\setup\deploy.bat stop

# Ver logs en tiempo real
scripts\setup\deploy.bat logs

# Reset completo (elimina datos)
scripts\setup\deploy.bat reset
```

### Linux/macOS
```bash
# Iniciar aplicación completa (incluye migraciones y seeders)
./scripts/setup/deploy.sh start

# Detener aplicación
./scripts/setup/deploy.sh stop

# Ver logs en tiempo real
./scripts/setup/deploy.sh logs

# Reset completo (elimina datos)
./scripts/setup/deploy.sh reset
```

## Comandos Docker Directos

```bash
# Ver estado de contenedores
docker-compose ps

# Ver logs específicos
docker-compose logs app
docker-compose logs db
docker-compose logs nginx

# Acceder al contenedor de la aplicación
docker-compose exec app bash

# Acceder a MySQL
docker-compose exec db mysql -u root -p

# Reiniciar un servicio específico
docker-compose restart app
docker-compose restart db
```

## Comandos de CodeIgniter

```bash
# Ejecutar migraciones
docker-compose exec app php spark migrate

# Rollback migraciones
docker-compose exec app php spark migrate:rollback

# Ejecutar seeders
docker-compose exec app php spark db:seed

# Limpiar caché
docker-compose exec app php spark cache:clear

# Ver rutas
docker-compose exec app php spark routes

# Crear migración
docker-compose exec app php spark make:migration NombreMigracion

# Crear modelo
docker-compose exec app php spark make:model NombreModelo

# Crear controlador
docker-compose exec app php spark make:controller NombreController
```

## Comandos de Base de Datos

```bash
# Backup de BD
docker-compose exec db mysqldump -u root -p bd_yagaruete_camp > backup.sql

# Restaurar BD
docker-compose exec -T db mysql -u root -p bd_yagaruete_camp < backup.sql

# Conectar a la BD
docker-compose exec db mysql -u root -p bd_yagaruete_camp
```

## Solución de Problemas

```bash
# Reconstruir contenedores
docker-compose up -d --build --force-recreate

# Limpiar todo Docker
docker system prune -a

# Ver uso de espacio
docker system df

# Eliminar volúmenes huérfanos
docker volume prune
```

## URLs de Acceso

- **Aplicación:** http://localhost:8080
- **PHPMyAdmin:** http://localhost:8081 (user: root, pass: root)
- **MailHog:** http://localhost:8025 (testing de emails)
- **Base de datos directa:** localhost:3306

## Configuración del .env

El archivo `.env` contiene la configuración del proyecto:

```bash
# Variables principales para desarrollo
CI_ENVIRONMENT=development
DB_DATABASE=bd_yagaruete_camp
DB_USERNAME=root
DB_PASSWORD=root
DB_HOSTNAME=db
APP_URL=http://localhost:8080
```

⚠️ **Importante:**
- El `.env` NO se sube a git
- Cada entorno (desarrollo/producción) tiene su propio `.env`
- Para producción, usa passwords seguros diferentes
