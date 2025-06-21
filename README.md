# Proyecto Martínez González - Sistema de E-commerce

## Descripción
Sistema web de comercio electrónico desarrollado con **CodeIgniter 4** que permite la gestión integral de productos, usuarios, ventas y consultas de clientes.

## Tecnologías Utilizadas
- **Backend**: CodeIgniter 4 (PHP)
- **Base de datos**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5
- **Dependencias**: jQuery, DataTables, Font Awesome

## Instalación y Configuración

### Requisitos del Sistema
- PHP 8.1 o superior
- MySQL 5.7+ o MariaDB 10.3+
- Servidor web (Apache/Nginx)
- Composer

### Pasos de Instalación

1. **Clonar el repositorio**
   ```bash
   git clone [URL_DEL_REPOSITORIO]
   cd proyecto_Martinez_Gonzalez
   ```

2. **Instalar dependencias**
   ```bash
   composer install
   ```

3. **Configurar variables de entorno**
   ```bash
   cp env .env
   ```
   
   Editar el archivo `.env` con tus configuraciones:
   ```env
   CI_ENVIRONMENT = development
   app.baseURL = 'http://localhost/proyecto_Martinez_Gonzalez/'
   
   database.default.hostname = localhost
   database.default.database = bd_martinez_gonzalez
   database.default.username = root
   database.default.password = tu_password
   ```

4. **Crear base de datos**
   - Crear la base de datos `bd_martinez_gonzalez`
   - Importar el script SQL incluido en el proyecto

5. **Configurar permisos**
   ```bash
   chmod -R 755 writable/
   ```

## Estructura del Proyecto

```
proyecto_Martinez_Gonzalez/
├── app/
│   ├── Controllers/          # Controladores del sistema
│   │   ├── LoginController.php      # Autenticación
│   │   ├── UsuarioCrudController.php # Gestión de usuarios
│   │   ├── ProductoController.php   # Gestión de productos
│   │   ├── CarritoController.php    # Carrito de compras
│   │   └── VentasController.php     # Gestión de ventas
│   ├── Models/               # Modelos de datos
│   │   ├── UsuariosModel.php        # Modelo de usuarios
│   │   ├── ProductoModel.php        # Modelo de productos
│   │   └── VentasCabeceraModel.php  # Modelo de ventas
│   ├── Views/                # Vistas del sistema
│   │   ├── front/                   # Vistas del frontend
│   │   └── back/                    # Vistas del backend
│   ├── Filters/              # Filtros de autenticación
│   │   ├── Auth.php                 # Filtro para administradores
│   │   └── Cliente.php              # Filtro para clientes
│   └── Config/               # Configuraciones
│       ├── Routes.php               # Definición de rutas
│       └── Database.php             # Configuración de BD
├── public/                   # Archivos públicos
├── assets/                   # Recursos (CSS, JS, imágenes)
└── writable/                 # Archivos escribibles (logs, cache)
```

## Perfiles de Usuario

### 1. Cliente (perfil_id = 1)
- **Permisos**: Navegar catálogo, realizar compras, ver historial
- **Restricciones**: No acceso a áreas administrativas

### 2. Administrador (perfil_id = 2)
- **Permisos**: Acceso completo al sistema
- **Funciones**: Gestión de usuarios, productos, ventas y consultas
- **Restricciones**: No puede eliminar su propio usuario

### 3. Vendedor (perfil_id = 3)
- **Permisos**: Gestión de productos y ventas
- **Restricciones**: No gestión de usuarios

## Funcionalidades Principales

### Sistema de Autenticación
- Login seguro con hash de contraseñas
- Filtros de autorización por perfil
- Regeneración de sesiones por seguridad
- Protección contra auto-eliminación de administradores

### Gestión de Productos
- CRUD completo de productos
- Categorización de productos
- Eliminación lógica (activar/desactivar)
- Subida de imágenes

### Carrito de Compras
- Manejo por sesiones
- Actualización dinámica de cantidades
- Cálculo automático de totales
- Persistencia durante la sesión

### Sistema de Ventas
- Registro de ventas con cabecera y detalle
- Historial de compras por cliente
- Administración de ventas para administradores
- Generación de facturas

## Documentación Completa
Para información detallada sobre la implementación, estructura de código y guías de desarrollo, consultar:
- **DOCUMENTACION.md**: Documentación técnica completa
- Comentarios en código fuente de controladores y modelos

## Licencia
Este proyecto está bajo la Licencia MIT.

---
*Basado en CodeIgniter 4 Framework*
for better security and separation of components.

This means that you should configure your web server to "point" to your project's *public* folder, and
not to the project root. A better practice would be to configure a virtual host to point there. A poor practice would be to point your web server to the project root and expect to enter *public/...*, as the rest of your logic and the
framework are exposed.

**Please** read the user guide for a better explanation of how CI4 works!

## Repository Management

We use GitHub issues, in our main repository, to track **BUGS** and to track approved **DEVELOPMENT** work packages.
We use our [forum](http://forum.codeigniter.com) to provide SUPPORT and to discuss
FEATURE REQUESTS.

This repository is a "distribution" one, built by our release preparation script.
Problems with it can be raised on our forum, or as issues in the main repository.

## Contributing

We welcome contributions from the community.

Please read the [*Contributing to CodeIgniter*](https://github.com/codeigniter4/CodeIgniter4/blob/develop/CONTRIBUTING.md) section in the development repository.

## Server Requirements

PHP version 8.1 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

> [!WARNING]
> - The end of life date for PHP 7.4 was November 28, 2022.
> - The end of life date for PHP 8.0 was November 26, 2023.
> - If you are still using PHP 7.4 or 8.0, you should upgrade immediately.
> - The end of life date for PHP 8.1 will be December 31, 2025.

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) if you plan to use MySQL
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library
=======
# proyectoTaller_l
>>>>>>> 5acc34908a4d8d77ff0ec42c7c6fa3289d0057ea
