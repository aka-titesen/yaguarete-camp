# 🏕️ Yagaruete Camp

> Sistema de E-commerce especializado en productos outdoor y camping

[![Docker](https://img.shields.io/badge/Docker-Ready-blue)](docker-compose.yml)
[![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4.5+-red)](https://codeigniter.com/)
[![PHP](https://img.shields.io/badge/PHP-8.2+-purple)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange)](https://www.mysql.com/)

## 🚀 Inicio Rápido

### 🔐 1. Configuración Segura del Entorno

```bash
# Clonar el repositorio
git clone https://github.com/aka-titesen/yaguarete-camp.git
cd yaguarete-camp

# Generar archivo .env con passwords seguros
# Linux/Mac:
./scripts/setup/generate-env.sh

# Windows:
scripts\setup\generate-env.bat
```

### 🐳 2. Iniciar con Docker

```bash
# Iniciar contenedores
./scripts/setup/deploy.sh start

# Inicializar base de datos
./scripts/setup/init-database.sh

# ¡Listo! Accede a http://localhost:8080
```

### ⚠️ Importante: Seguridad

- **NUNCA** uses passwords por defecto en producción
- El archivo `.env` **NO** se sube a git (está en `.gitignore`)
- Guarda las credenciales generadas en un lugar seguro
- Lee la [guía de seguridad](docs/security/environment-security.md)

## 📋 Características

- ✅ **Sistema de autenticación** con roles (Admin/Cliente)
- ✅ **Catálogo de productos** outdoor y camping
- ✅ **Carrito de compras** con sesiones persistentes
- ✅ **Gestión de ventas** completa
- ✅ **Panel administrativo** para gestión
- ✅ **Sistema dockerizado** para fácil despliegue
- ✅ **Base de datos** completamente normalizada
- ✅ **Scripts automatizados** para mantenimiento

## 🌐 URLs de Acceso

Una vez instalado, tendrás acceso a:

| Servicio | URL | Credenciales |
|----------|-----|--------------|
| **Aplicación** | http://localhost:8080 | admin@test.com / admin123 |
| **PHPMyAdmin** | http://localhost:8081 | root / yagaruete_password |
| **MailHog** | http://localhost:8025 | - |

## 🛠️ Stack Tecnológico

### Backend
- **Framework**: CodeIgniter 4.5+
- **PHP**: 8.2+ con extensiones optimizadas
- **Base de Datos**: MySQL 8.0
- **Cache**: Redis 7+

### Frontend
- **HTML5** + **CSS3** + **Bootstrap 5**
- **JavaScript** + **jQuery**
- **DataTables** para tablas dinámicas
- **Font Awesome** para iconografía

### Infraestructura
- **Docker** + **Docker Compose**
- **Nginx** como reverse proxy
- **PHP-FPM** para procesamiento PHP
- **Redis** para cache y sesiones

## 📁 Estructura del Proyecto

```
yagaruete-camp/
├── 📋 scripts/           # Scripts de automatización
│   ├── setup/            # Instalación y despliegue
│   ├── maintenance/      # Mantenimiento y backup
│   └── development/      # Herramientas de desarrollo
├── 📚 docs/              # Documentación técnica
│   ├── setup/            # Guías de instalación
│   ├── database/         # Documentación de BD
│   └── architecture/     # Arquitectura del sistema
├── 🏗️ app/              # Aplicación CodeIgniter 4
│   ├── Controllers/      # Lógica de controladores
│   ├── Models/           # Modelos de datos
│   ├── Views/            # Vistas y templates
│   └── Database/         # Migraciones y seeders
├── 🌐 public/            # Punto de entrada web
├── 🐳 docker/            # Configuración Docker
└── 🧪 tests/             # Tests automatizados
```

## 📚 Documentación

### 🚀 Instalación
- **[Guía de Instalación](docs/setup/README.md)** - Instalación completa paso a paso
- **[Configuración Docker](docs/setup/docker-setup.md)** - Setup detallado con Docker
- **[Solución de Problemas](docs/setup/troubleshooting.md)** - Troubleshooting completo

### 🗄️ Base de Datos
- **[Migraciones](docs/database/migrations.md)** - Estructura y migraciones de BD
- **[Seeders](docs/database/seeders.md)** - Datos iniciales y de prueba
- **[Esquema](docs/database/schema.md)** - Esquema completo de la BD

### 🏛️ Arquitectura
- **[Visión General](docs/architecture/overview.md)** - Arquitectura del sistema

## 🔧 Scripts Útiles

### Scripts de Setup
```bash
./scripts/setup/deploy.sh start      # Iniciar servicios
./scripts/setup/deploy.sh stop       # Detener servicios
./scripts/setup/deploy.sh restart    # Reiniciar servicios
./scripts/setup/init-database.sh     # Inicializar BD
```

### Scripts de Mantenimiento
```bash
./scripts/maintenance/healthcheck.sh  # Verificar salud del sistema
./scripts/maintenance/backup.sh       # Backup de base de datos
./scripts/maintenance/cleanup.sh      # Limpieza del sistema
```

## 👥 Usuarios por Defecto

| Rol | Email | Contraseña | Descripción |
|-----|-------|------------|-------------|
| **Administrador** | admin@test.com | admin123 | Acceso completo al sistema |
| **Cliente** | cliente@test.com | cliente123 | Usuario cliente estándar |

## 🛍️ Catálogo de Productos

El sistema incluye un catálogo completo de productos outdoor:

- 🏕️ **Tiendas y Refugios** - Carpas, toldos, refugios
- 🎒 **Mochilas y Equipaje** - Mochilas técnicas, bolsas
- 🥾 **Senderismo** - Bastones, GPS, equipos de trekking
- 🧗 **Escalada** - Arneses, cascos, cuerdas técnicas
- 🔦 **Electrónicos** - Linternas, radios, GPS
- 👕 **Ropa Técnica** - Chaquetas, pantalones técnicos
- 👢 **Calzado** - Botas de trekking, zapatillas approach

## 📊 Funcionalidades Principales

### Para Administradores
- 📈 **Dashboard** con métricas y estadísticas
- 🛍️ **Gestión de productos** (CRUD completo)
- 👥 **Gestión de usuarios** y permisos
- 💰 **Gestión de ventas** y reportes
- 📧 **Gestión de consultas** de clientes

### Para Clientes
- 🔍 **Navegación** intuitiva del catálogo
- 🛒 **Carrito de compras** persistente
- 💳 **Proceso de checkout** simplificado
- 📋 **Historial de compras**
- 📧 **Formulario de contacto**

## 🔒 Seguridad

- 🔐 **Autenticación segura** con password hashing
- 🛡️ **Autorización** basada en roles
- 🚫 **Protección CSRF** en formularios
- 🔒 **Validación** de entrada en todos los endpoints
- 📝 **Logging** de accesos y operaciones

## 🚀 Despliegue

### Desarrollo Local
```bash
git clone <repo-url> yagaruete-camp
cd yagaruete-camp
./scripts/setup/deploy.sh start
```

### Producción
```bash
# Con Docker Swarm
docker stack deploy -c docker-compose.prod.yml yagaruete-camp

# Verificar deployment
./scripts/maintenance/healthcheck.sh --verbose
```

## 🧪 Testing

```bash
# Tests automatizados
docker-compose exec app vendor/bin/phpunit

# Verificación de integridad
./scripts/maintenance/verify-data.php --verbose

# Health check completo
./scripts/maintenance/healthcheck.sh
```

## 📈 Performance

- ⚡ **Redis** para cache y sesiones
- 🗄️ **Índices optimizados** en base de datos
- 📦 **Assets optimizados** y comprimidos
- 🔄 **Lazy loading** de imágenes
- 📊 **Query optimization** en modelos

## 🤝 Contribuir

1. Fork el proyecto
2. Crea una rama feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit tus cambios (`git commit -am 'Agregar nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Abre un Pull Request

## 📞 Soporte

¿Problemas con la instalación?

1. 📖 Revisa la [documentación completa](docs/)
2. 🔧 Ejecuta el [troubleshooting](docs/setup/troubleshooting.md)
3. 🏥 Usa el health check: `./scripts/maintenance/healthcheck.sh --verbose`
4. 📝 Revisa los logs: `./scripts/setup/deploy.sh logs`

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver [LICENSE](LICENSE) para más detalles.

## 🏆 Créditos

**Yagaruete Camp** - Desarrollado como parte del proyecto académico Martínez González

- 🎯 **Objetivo**: Sistema de e-commerce completo y funcional
- 🛠️ **Tecnologías**: Stack moderno y profesional
- 📚 **Documentación**: Completa y detallada
- 🚀 **Deployment**: Automatizado con Docker

---

**¡Bienvenido a Yagaruete Camp - Tu destino para aventuras al aire libre!** 🏕️⛰️🏕️
