# Documentación del Proyecto Martínez González

## Descripción General
Sistema web de comercio electrónico desarrollado con CodeIgniter 4, que permite la gestión de productos, usuarios, ventas y consultas.

## Estructura del Proyecto

### Arquitectura MVC
- **Models**: Gestión de datos y lógica de negocio
- **Views**: Presentación e interfaz de usuario  
- **Controllers**: Lógica de control y flujo de aplicación

## Configuración Inicial

### Variables de Entorno (.env)
```env
# Configuración básica
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost/proyecto_Martinez_Gonzalez/'

# Base de datos
database.default.hostname = localhost
database.default.database = bd_martinez_gonzalez
database.default.username = root
database.default.password = tu_password
database.default.DBDriver = MySQLi
database.default.port = 3306
```

### Filtros de Autenticación
- **auth**: Requiere usuario logueado (cualquier perfil)
- **cliente**: Requiere usuario con perfil de cliente (perfil_id = 1)

## Rutas del Sistema

### Rutas Públicas
| Ruta | Método | Controlador | Descripción |
|------|--------|-------------|-------------|
| `/` | GET | Home::index | Página principal |
| `/sobreNosotros` | GET | Home::sobreNosotros | Página sobre nosotros |
| `/catalogo` | GET | Home::aCatalogoProductos | Catálogo de productos |
| `/producto/(:num)` | GET | Home::aDetalleProducto | Detalle de producto específico |
| `/login` | GET | LoginController::index | Formulario de login |
| `/login/auth` | POST | LoginController::auth | Autenticación de usuario |

### Rutas de Administración (Requieren autenticación)
| Ruta | Método | Controlador | Descripción |
|------|--------|-------------|-------------|
| `/admin_usuarios` | GET | UsuarioCrudController::index | Gestión de usuarios |
| `/store` | POST | UsuarioCrudController::store | Crear nuevo usuario |
| `/deletelogico/(:num)` | GET | UsuarioCrudController::deleteLogico | Desactivar usuario |
| `/activar/(:num)` | GET | UsuarioCrudController::activar | Activar usuario |
| `/administrar_productos` | GET | ProductoController::index | Gestión de productos |

### Rutas del Carrito
| Ruta | Método | Controlador | Descripción |
|------|--------|-------------|-------------|
| `/carrito/add` | POST | CarritoController::add | Agregar producto al carrito |
| `/carrito_elimina/(:any)` | GET | CarritoController::remove | Eliminar producto del carrito |
| `/carrito_suma/(:any)` | GET | CarritoController::suma | Incrementar cantidad |
| `/carrito_resta/(:any)` | GET | CarritoController::resta | Decrementar cantidad |

### Rutas de Ventas (Cliente)
| Ruta | Método | Controlador | Descripción |
|------|--------|-------------|-------------|
| `/mis-compras` | GET | Ventascontroller::misCompras | Ver compras del usuario |
| `/detalle-compra/(:num)` | GET | Ventascontroller::verFactura | Ver detalle de compra |
| `/carrito-comprar` | GET | Ventascontroller::registrarVenta | Procesar compra |

## Perfiles de Usuario

### Tipos de Perfil
1. **Cliente** (perfil_id = 1): Usuarios regulares que pueden comprar
2. **Administrador** (perfil_id = 2): Acceso total al sistema
3. **Vendedor** (perfil_id = 3): Gestión de productos y ventas

### Restricciones de Seguridad
- Los administradores no pueden eliminar su propio usuario
- Solo usuarios autenticados pueden acceder a áreas administrativas
- Los clientes solo pueden ver sus propias compras

## Base de Datos

### Tablas Principales
- **usuarios**: Gestión de usuarios del sistema
- **productos**: Catálogo de productos
- **categorias**: Clasificación de productos
- **ventas_cabecera**: Información general de ventas
- **ventas_detalle**: Detalle de productos vendidos
- **consultas**: Sistema de consultas de clientes

## Funcionalidades Especiales

### Sistema de Carrito
- Manejo de sesiones para productos temporales
- Actualización dinámica de cantidades
- Cálculo automático de totales

### Sistema de Autenticación
- Hash seguro de contraseñas
- Validación de roles y permisos
- Regeneración de sesiones por seguridad

### Validaciones
- Validación de formularios tanto en frontend como backend
- Verificación de unicidad de emails y usernames
- Validación de permisos por perfil de usuario

## Convenciones de Código

### Naming Conventions
- Controladores: PascalCase con sufijo "Controller"
- Modelos: PascalCase con sufijo "Model"
- Métodos: camelCase
- Variables: camelCase

### Estructura de Archivos
```
app/
├── Controllers/     # Controladores del sistema
├── Models/         # Modelos de datos
├── Views/          # Vistas y plantillas
├── Config/         # Configuraciones
└── Filters/        # Filtros de autenticación
```

## Seguridad Implementada

### Medidas de Seguridad
1. **Hash de contraseñas**: Uso de `password_hash()` y `password_verify()`
2. **Validación de entrada**: Sanitización y validación de todos los inputs
3. **Filtros de autenticación**: Control de acceso por rutas
4. **Protección CSRF**: Tokens en formularios críticos
5. **Validación de permisos**: Verificación de roles antes de acciones

### Validaciones de Contraseña
- Mínimo 8 caracteres, máximo 32
- Al menos una mayúscula y una minúscula
- Al menos un número
- Al menos un símbolo especial

## Mantenimiento

### Logs del Sistema
Los logs se almacenan en `writable/logs/` y incluyen:
- Errores de aplicación
- Intentos de login fallidos
- Operaciones administrativas

### Archivos Temporales
- `writable/cache/`: Cache de la aplicación
- `writable/session/`: Datos de sesión
- `writable/uploads/`: Archivos subidos por usuarios
