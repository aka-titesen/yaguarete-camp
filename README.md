# 🏕️ Yagaruete Camp

> Sistema de E-commerce especializado en productos outdoor y camping

[![Docker](https://img.shields.io/badge/Docker-Ready-blue)](docker-compose.yml)
[![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4.5+-red)](https://codeigniter.com/)
[![PHP](https://img.shields.io/badge/PHP-8.2+-purple)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange)](https://www.mysql.com/)

# 🏕️ Yagaruete Camp

> Sistema de E-commerce especializado en productos outdoor y camping

[![Docker](https://img.shields.io/badge/Docker-Ready-blue)](docker-compose.yml)
[![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4.5+-red)](https://codeigniter.com/)
[![PHP](https://img.shields.io/badge/PHP-8.2+-purple)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange)](https://www.mysql.com/)

## 🚀 Inicio Súper Rápido

**Requisito único:** Docker Desktop instalado

### Para Desarrolladores - 2 pasos:

```bash
# 1. Clona y entra al proyecto
git clone https://github.com/aka-titesen/yaguarete-camp.git
cd yaguarete-camp

# 2. Ejecuta el script de inicio
# Windows:
scripts\setup\deploy.bat

# Linux/macOS:
./scripts/setup/deploy.sh
```

**¡Eso es todo!** 🎉

- **Aplicación:** http://localhost:8080
- **PHPMyAdmin:** http://localhost:8081 (user: root, pass: root)
- **MailHog:** http://localhost:8025 (testing de emails)

> 📖 **[Ver guía completa de inicio rápido](QUICK-START.md)**

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
│   └── setup/            # deploy.bat, deploy.sh, generate-env.*
├── 📚 docs/              # Documentación técnica completa
│   ├── setup/            # Guías de instalación y troubleshooting
│   ├── database/         # Documentación de BD
│   ├── security/         # Buenas prácticas de .env
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

## 📞 Soporte

¿Problemas con la instalación?

1. 📖 Revisa la [guía rápida](QUICK-START.md)
2. 🔧 Consulta [solución de problemas](docs/setup/troubleshooting.md)
3. 📝 Revisa los logs: `deploy.bat logs` o `./deploy.sh logs`
4. � Ve la [documentación completa](docs/)

## 📚 Documentación

- **[Inicio Rápido](QUICK-START.md)** - Para desarrolladores nuevos
- **[Comandos Útiles](COMMANDS.md)** - Referencia de comandos
- **[Documentación Completa](docs/)** - Guías detalladas
- **[Configuración de Seguridad](docs/security/environment-security.md)** - Buenas prácticas

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
