# 🗄️ Guía de Migraciones y Seeders - Yagaruete Camp

## 📋 Descripción General

El sistema de base de datos de Yagaruete Camp utiliza **migraciones** para manejar la estructura de la base de datos y **seeders** para poblar las tablas con datos iniciales y de prueba.

## 🏗️ Migraciones Disponibles

### Lista de Migraciones

| Archivo                                           | Descripción                  | Estado    |
| ------------------------------------------------- | ---------------------------- | --------- |
| `2023-06-16-123456_CreateConsultasTable.php`      | Tabla de consultas/contactos | ✅ Activa |
| `2025-07-26-162400_CreateCategoriasTable.php`     | Categorías de productos      | ✅ Activa |
| `2025-07-26-162500_CreateUsuariosTable.php`       | Sistema de usuarios          | ✅ Activa |
| `2025-07-26-162540_CreateProductosTable.php`      | Catálogo de productos        | ✅ Activa |
| `2025-07-26-162612_CreateVentasCabeceraTable.php` | Cabecera de ventas           | ✅ Activa |
| `2025-07-26-162642_CreateVentasDetalleTable.php`  | Detalle de ventas            | ✅ Activa |

### Estructura de Tablas

#### `categorias`

```sql
CREATE TABLE categorias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### `usuarios`

```sql
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    perfil_id INT NOT NULL,
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_usuario (usuario),
    INDEX idx_email (email),
    INDEX idx_perfil (perfil_id)
);
```

#### `productos`

```sql
CREATE TABLE productos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    categoria_id INT,
    imagen VARCHAR(255),
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (categoria_id) REFERENCES categorias(id),
    INDEX idx_categoria (categoria_id),
    INDEX idx_precio (precio),
    INDEX idx_stock (stock)
);
```

#### `ventas_cabecera`

```sql
CREATE TABLE ventas_cabecera (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    fecha DATE NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    estado VARCHAR(50) DEFAULT 'completada',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    INDEX idx_usuario (usuario_id),
    INDEX idx_fecha (fecha),
    INDEX idx_estado (estado)
);
```

#### `ventas_detalle`

```sql
CREATE TABLE ventas_detalle (
    id INT PRIMARY KEY AUTO_INCREMENT,
    venta_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (venta_id) REFERENCES ventas_cabecera(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id),
    INDEX idx_venta (venta_id),
    INDEX idx_producto (producto_id)
);
```

### Comandos de Migración

```bash
# Ejecutar todas las migraciones pendientes
docker-compose exec app php spark migrate

# Ver estado de migraciones
docker-compose exec app php spark migrate:status

# Rollback de la última migración
docker-compose exec app php spark migrate:rollback

# Rollback completo (CUIDADO: elimina todas las tablas)
docker-compose exec app php spark migrate:rollback --all

# Refrescar migraciones (rollback + migrate)
docker-compose exec app php spark migrate:refresh
```

## 🌱 Seeders Disponibles

### Lista de Seeders Optimizados

| Archivo                | Descripción             | Registros | Dependencias         |
| ---------------------- | ----------------------- | --------- | -------------------- |
| `CategoriasSeeder.php` | Categorías de productos | 15        | Ninguna              |
| `UsuariosSeeder.php`   | Usuarios del sistema    | 10        | Ninguna              |
| `ProductosSeeder.php`  | Catálogo de productos   | 30+       | Categorías           |
| `VentasSeeder.php`     | Ventas completas        | 15+       | Usuarios, Productos  |
| `DatabaseSeeder.php`   | Orquestador principal   | -         | Todos los anteriores |

### Descripción Detallada

#### `CategoriasSeeder.php`

Crea 15 categorías de productos outdoor:

- Tiendas y Refugios
- Mochilas y Equipaje
- Senderismo y Trekking
- Escalada y Montañismo
- Camping y Vivac
- Electrónicos Outdoor
- Ropa Técnica
- Calzado Outdoor
- Supervivencia y Bushcraft
- Pesca y Náutica
- Ciclismo de Montaña
- Deportes de Invierno
- Cocina y Alimentación
- Hidratación
- Accesorios Generales

#### `UsuariosSeeder.php`

Crea usuarios de prueba con roles específicos:

**Administradores (perfil_id = 1):**

- admin@test.com / admin123
- Gestores del sistema sin capacidad de compra

**Clientes (perfil_id = 2):**

- cliente@test.com / cliente123
- Usuarios finales con capacidad de compra

#### `ProductosSeeder.php`

Productos temáticos de camping y outdoor:

- Tiendas de campaña y carpas
- Mochilas de trekking
- Equipos de escalada
- Ropa técnica
- Calzado especializado
- Electrónicos outdoor
- Herramientas de supervivencia

#### `VentasSeeder.php`

Ventas realistas con:

- Múltiples productos por venta
- Fechas distribuidas
- Totales calculados correctamente
- Solo clientes realizan compras

### Comandos de Seeders

```bash
# Ejecutar todos los seeders (RECOMENDADO)
docker-compose exec app php spark db:seed DatabaseSeeder

# Ejecutar seeders individuales
docker-compose exec app php spark db:seed CategoriasSeeder
docker-compose exec app php spark db:seed UsuariosSeeder
docker-compose exec app php spark db:seed ProductosSeeder
docker-compose exec app php spark db:seed VentasSeeder

# Ver lista de seeders disponibles
docker-compose exec app php spark db:seed --list
```

## 🚀 Inicialización Completa

### Script Automatizado

```bash
# Inicialización completa con script
./scripts/setup/init-database.sh

# Opciones disponibles
./scripts/setup/init-database.sh --help

# Forzar recreación completa
./scripts/setup/init-database.sh --force

# Solo migraciones (sin seeders)
./scripts/setup/init-database.sh --skip-seeders
```

### Proceso Manual

```bash
# 1. Ejecutar migraciones
docker-compose exec app php spark migrate

# 2. Verificar estructura
docker-compose exec app php spark db:table --list

# 3. Ejecutar seeders
docker-compose exec app php spark db:seed DatabaseSeeder

# 4. Verificar datos
docker-compose exec mysql mysql -u yagaruete_user -p yagaruete_camp -e "
  SELECT
    (SELECT COUNT(*) FROM categorias) as categorias,
    (SELECT COUNT(*) FROM usuarios) as usuarios,
    (SELECT COUNT(*) FROM productos) as productos,
    (SELECT COUNT(*) FROM ventas_cabecera) as ventas;
"
```

## 👥 Usuarios por Defecto

### Administradores (perfil_id = 1)

| Usuario | Nombre        | Email          | Contraseña | Función          |
| ------- | ------------- | -------------- | ---------- | ---------------- |
| admin   | Administrador | admin@test.com | admin123   | Gestión completa |

### Clientes (perfil_id = 2)

| Usuario | Nombre       | Email            | Contraseña | Función |
| ------- | ------------ | ---------------- | ---------- | ------- |
| cliente | Cliente Demo | cliente@test.com | cliente123 | Compras |

> **Nota**: Todas las contraseñas cumplen con los requisitos de seguridad (8+ caracteres, mayúsculas, minúsculas, números, símbolos).

## 📊 Verificación de Datos

### Scripts de Verificación

```bash
# Verificación completa de integridad
docker-compose exec app php scripts/maintenance/verify-data.php --verbose

# Verificación específica de tablas
docker-compose exec app php spark db:table usuarios
docker-compose exec app php spark db:table productos
docker-compose exec app php spark db:table ventas_cabecera
```

### Consultas de Verificación Manual

```sql
-- Verificar relaciones
SELECT
  c.nombre as categoria,
  COUNT(p.id) as productos
FROM categorias c
LEFT JOIN productos p ON c.id = p.categoria_id
GROUP BY c.id, c.nombre;

-- Verificar ventas
SELECT
  u.nombre as cliente,
  COUNT(vc.id) as total_ventas,
  SUM(vc.total) as monto_total
FROM usuarios u
LEFT JOIN ventas_cabecera vc ON u.id = vc.usuario_id
WHERE u.perfil_id = 2
GROUP BY u.id, u.nombre;

-- Verificar integridad referencial
SELECT
  'Productos sin categoría' as check_name,
  COUNT(*) as count
FROM productos p
LEFT JOIN categorias c ON p.categoria_id = c.id
WHERE c.id IS NULL

UNION ALL

SELECT
  'Ventas sin usuario' as check_name,
  COUNT(*) as count
FROM ventas_cabecera vc
LEFT JOIN usuarios u ON vc.usuario_id = u.id
WHERE u.id IS NULL;
```

## 🔄 Mantenimiento de Base de Datos

### Backup y Restauración

```bash
# Crear backup antes de cambios
./scripts/maintenance/backup.sh --name "pre-migration"

# Restaurar si algo sale mal
./scripts/maintenance/backup.sh --restore backup.sql

# Backup automático con rotación
./scripts/maintenance/backup.sh --keep-days 30
```

### Reset de Desarrollo

```bash
# Reset completo para desarrollo
docker-compose down -v
docker-compose up -d mysql
sleep 10  # Esperar que MySQL inicie
./scripts/setup/init-database.sh --force
```

### Actualización de Esquema

```bash
# Crear nueva migración
docker-compose exec app php spark make:migration CreateNewTable

# Ejecutar migraciones pendientes
docker-compose exec app php spark migrate

# Verificar estado
docker-compose exec app php spark migrate:status
```

## 🛠️ Troubleshooting

### Problemas Comunes

**Migraciones fallan:**

```bash
# Ver detalles del error
docker-compose exec app php spark migrate --verbose

# Verificar conexión BD
docker-compose exec app php spark db:table --show-headers=false usuarios
```

**Seeders no ejecutan:**

```bash
# Verificar estructura de tablas
docker-compose exec app php spark db:table --list

# Ejecutar seeder específico con debug
docker-compose exec app php spark db:seed CategoriasSeeder --verbose
```

**Datos inconsistentes:**

```bash
# Verificar integridad
docker-compose exec app php scripts/maintenance/verify-data.php

# Limpiar y regenerar
./scripts/setup/init-database.sh --force
```

## 📚 Referencias

- [CodeIgniter 4 Migrations](https://codeigniter.com/user_guide/dbmgmt/migration.html)
- [CodeIgniter 4 Database Seeding](https://codeigniter.com/user_guide/dbmgmt/seeds.html)
- [MySQL 8.0 Reference](https://dev.mysql.com/doc/refman/8.0/en/)

---

**Yagaruete Camp** - Sistema de base de datos robusto y bien estructurado 🗄️
