# 🗄️ Esquema de Base de Datos - Yagaruete Camp

## 📋 Descripción General

La base de datos de **Yagaruete Camp** está diseñada para soportar un sistema de e-commerce especializado en productos outdoor. Utiliza **MySQL 8.0** con un esquema normalizado que garantiza integridad referencial y performance óptimo.

## 🏗️ Arquitectura de la Base de Datos

### Información General

| Parámetro        | Valor                          |
| ---------------- | ------------------------------ |
| **Motor**        | MySQL 8.0                      |
| **Charset**      | utf8mb4                        |
| **Collation**    | utf8mb4_unicode_ci             |
| **Zona Horaria** | America/Argentina/Buenos_Aires |
| **Nombre BD**    | yagaruete_camp                 |

### Principios de Diseño

1. **Normalización**: Esquema normalizado hasta 3NF
2. **Integridad Referencial**: Foreign keys con constraints
3. **Indexación**: Índices estratégicos para performance
4. **Auditabilidad**: Timestamps en todas las tablas
5. **Escalabilidad**: Diseño preparado para crecimiento

## 📊 Diagrama Entidad-Relación

```
┌─────────────────┐       ┌──────────────────┐       ┌─────────────────┐
│    usuarios     │       │   ventas_cabecera│       │ ventas_detalle  │
├─────────────────┤   1:N ├──────────────────┤   1:N ├─────────────────┤
│ id (PK)         │◄──────┤ usuario_id (FK)  │◄──────┤ venta_id (FK)   │
│ usuario         │       │ id (PK)          │       │ id (PK)         │
│ nombre          │       │ fecha            │       │ producto_id (FK)│
│ email           │       │ total            │       │ cantidad        │
│ password        │       │ estado           │       │ precio_unitario │
│ perfil_id       │       │ created_at       │       │ created_at      │
│ activo          │       │ updated_at       │       │ updated_at      │
│ created_at      │       └──────────────────┘       └─────────────────┘
│ updated_at      │                                           │
└─────────────────┘                                           │
                                                              │
┌─────────────────┐       ┌──────────────────┐               │
│   categorias    │       │    productos     │               │
├─────────────────┤   1:N ├──────────────────┤               │
│ id (PK)         │◄──────┤ categoria_id (FK)│◄──────────────┘
│ nombre          │       │ id (PK)          │
│ descripcion     │       │ nombre           │
│ activo          │       │ descripcion      │
│ created_at      │       │ precio           │
│ updated_at      │       │ stock            │
└─────────────────┘       │ imagen           │
                          │ activo           │
                          │ created_at       │
                          │ updated_at       │
                          └──────────────────┘

┌─────────────────┐
│    consultas    │
├─────────────────┤
│ id (PK)         │
│ nombre          │
│ email           │
│ mensaje         │
│ fecha           │
│ created_at      │
│ updated_at      │
└─────────────────┘
```

## 📋 Descripción de Tablas

### 👥 `usuarios`

**Propósito**: Gestión de usuarios del sistema con roles diferenciados

| Campo        | Tipo         | Nullable | Descripción                               |
| ------------ | ------------ | -------- | ----------------------------------------- |
| `id`         | INT          | NO       | Clave primaria auto-incremental           |
| `usuario`    | VARCHAR(50)  | NO       | Nombre de usuario único                   |
| `nombre`     | VARCHAR(255) | NO       | Nombre completo del usuario               |
| `email`      | VARCHAR(255) | NO       | Email único del usuario                   |
| `password`   | VARCHAR(255) | NO       | Contraseña hasheada                       |
| `perfil_id`  | INT          | NO       | Rol del usuario (1=Admin, 2=Cliente)      |
| `activo`     | TINYINT(1)   | NO       | Estado del usuario (1=Activo, 0=Inactivo) |
| `created_at` | TIMESTAMP    | NO       | Fecha de creación                         |
| `updated_at` | TIMESTAMP    | NO       | Fecha de última modificación              |

**Índices**:

```sql
PRIMARY KEY (id)
UNIQUE INDEX idx_usuario (usuario)
UNIQUE INDEX idx_email (email)
INDEX idx_perfil (perfil_id)
INDEX idx_activo (activo)
```

**Roles de Usuario**:

- **perfil_id = 1**: Administradores (gestión completa)
- **perfil_id = 2**: Clientes (compras y consultas)

### 🗂️ `categorias`

**Propósito**: Clasificación de productos en categorías temáticas

| Campo         | Tipo         | Nullable | Descripción                     |
| ------------- | ------------ | -------- | ------------------------------- |
| `id`          | INT          | NO       | Clave primaria auto-incremental |
| `nombre`      | VARCHAR(255) | NO       | Nombre de la categoría          |
| `descripcion` | TEXT         | YES      | Descripción detallada           |
| `activo`      | TINYINT(1)   | NO       | Estado de la categoría          |
| `created_at`  | TIMESTAMP    | NO       | Fecha de creación               |
| `updated_at`  | TIMESTAMP    | NO       | Fecha de última modificación    |

**Índices**:

```sql
PRIMARY KEY (id)
UNIQUE INDEX idx_nombre (nombre)
INDEX idx_activo (activo)
```

### 🛍️ `productos`

**Propósito**: Catálogo de productos del e-commerce

| Campo          | Tipo          | Nullable | Descripción                     |
| -------------- | ------------- | -------- | ------------------------------- |
| `id`           | INT           | NO       | Clave primaria auto-incremental |
| `nombre`       | VARCHAR(255)  | NO       | Nombre del producto             |
| `descripcion`  | TEXT          | YES      | Descripción detallada           |
| `precio`       | DECIMAL(10,2) | NO       | Precio en pesos argentinos      |
| `stock`        | INT           | NO       | Cantidad disponible             |
| `categoria_id` | INT           | YES      | Referencia a categorías         |
| `imagen`       | VARCHAR(255)  | YES      | Nombre del archivo de imagen    |
| `activo`       | TINYINT(1)    | NO       | Estado del producto             |
| `created_at`   | TIMESTAMP     | NO       | Fecha de creación               |
| `updated_at`   | TIMESTAMP     | NO       | Fecha de última modificación    |

**Índices**:

```sql
PRIMARY KEY (id)
FOREIGN KEY (categoria_id) REFERENCES categorias(id)
INDEX idx_categoria (categoria_id)
INDEX idx_precio (precio)
INDEX idx_stock (stock)
INDEX idx_activo (activo)
INDEX idx_nombre (nombre)
```

### 💰 `ventas_cabecera`

**Propósito**: Registro de ventas realizadas (cabecera de factura)

| Campo        | Tipo          | Nullable | Descripción                     |
| ------------ | ------------- | -------- | ------------------------------- |
| `id`         | INT           | NO       | Clave primaria auto-incremental |
| `usuario_id` | INT           | NO       | Usuario que realizó la compra   |
| `fecha`      | DATE          | NO       | Fecha de la venta               |
| `total`      | DECIMAL(10,2) | NO       | Monto total de la venta         |
| `estado`     | VARCHAR(50)   | NO       | Estado de la venta              |
| `created_at` | TIMESTAMP     | NO       | Fecha de creación               |
| `updated_at` | TIMESTAMP     | NO       | Fecha de última modificación    |

**Índices**:

```sql
PRIMARY KEY (id)
FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
INDEX idx_usuario (usuario_id)
INDEX idx_fecha (fecha)
INDEX idx_estado (estado)
INDEX idx_total (total)
```

**Estados de Venta**:

- `pendiente`: Venta iniciada pero no confirmada
- `completada`: Venta finalizada exitosamente
- `cancelada`: Venta cancelada
- `reembolsada`: Venta reembolsada

### 🧾 `ventas_detalle`

**Propósito**: Detalle de productos vendidos en cada venta

| Campo             | Tipo          | Nullable | Descripción                     |
| ----------------- | ------------- | -------- | ------------------------------- |
| `id`              | INT           | NO       | Clave primaria auto-incremental |
| `venta_id`        | INT           | NO       | Referencia a venta cabecera     |
| `producto_id`     | INT           | NO       | Producto vendido                |
| `cantidad`        | INT           | NO       | Cantidad vendida                |
| `precio_unitario` | DECIMAL(10,2) | NO       | Precio al momento de la venta   |
| `created_at`      | TIMESTAMP     | NO       | Fecha de creación               |
| `updated_at`      | TIMESTAMP     | NO       | Fecha de última modificación    |

**Índices**:

```sql
PRIMARY KEY (id)
FOREIGN KEY (venta_id) REFERENCES ventas_cabecera(id) ON DELETE CASCADE
FOREIGN KEY (producto_id) REFERENCES productos(id)
INDEX idx_venta (venta_id)
INDEX idx_producto (producto_id)
UNIQUE INDEX idx_venta_producto (venta_id, producto_id)
```

### 📧 `consultas`

**Propósito**: Formulario de contacto y consultas de clientes

| Campo        | Tipo         | Nullable | Descripción                     |
| ------------ | ------------ | -------- | ------------------------------- |
| `id`         | INT          | NO       | Clave primaria auto-incremental |
| `nombre`     | VARCHAR(255) | NO       | Nombre del consultante          |
| `email`      | VARCHAR(255) | NO       | Email de contacto               |
| `mensaje`    | TEXT         | NO       | Contenido de la consulta        |
| `fecha`      | TIMESTAMP    | NO       | Fecha de la consulta            |
| `created_at` | TIMESTAMP    | NO       | Fecha de creación               |
| `updated_at` | TIMESTAMP    | NO       | Fecha de última modificación    |

**Índices**:

```sql
PRIMARY KEY (id)
INDEX idx_fecha (fecha)
INDEX idx_email (email)
```

## 🔗 Relaciones y Constraints

### Relaciones Principales

1. **usuarios ← ventas_cabecera** (1:N)

   - Un usuario puede tener múltiples ventas
   - Solo usuarios con perfil_id=2 (clientes) realizan compras

2. **ventas_cabecera ← ventas_detalle** (1:N)

   - Una venta puede tener múltiples productos
   - Eliminación en cascada: si se elimina venta, se eliminan detalles

3. **productos ← ventas_detalle** (1:N)

   - Un producto puede estar en múltiples ventas
   - Se preserva el precio al momento de la venta

4. **categorias ← productos** (1:N)
   - Una categoría puede tener múltiples productos
   - Los productos pueden no tener categoría (NULL)

### Constraints de Integridad

```sql
-- Foreign Keys con constraints
ALTER TABLE productos
ADD CONSTRAINT fk_productos_categoria
FOREIGN KEY (categoria_id) REFERENCES categorias(id)
ON UPDATE CASCADE ON DELETE SET NULL;

ALTER TABLE ventas_cabecera
ADD CONSTRAINT fk_ventas_usuario
FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE ventas_detalle
ADD CONSTRAINT fk_detalle_venta
FOREIGN KEY (venta_id) REFERENCES ventas_cabecera(id)
ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ventas_detalle
ADD CONSTRAINT fk_detalle_producto
FOREIGN KEY (producto_id) REFERENCES productos(id)
ON UPDATE CASCADE ON DELETE RESTRICT;
```

### Reglas de Negocio

```sql
-- Check constraints para validaciones
ALTER TABLE productos
ADD CONSTRAINT chk_precio_positivo
CHECK (precio > 0);

ALTER TABLE productos
ADD CONSTRAINT chk_stock_no_negativo
CHECK (stock >= 0);

ALTER TABLE ventas_detalle
ADD CONSTRAINT chk_cantidad_positiva
CHECK (cantidad > 0);

ALTER TABLE ventas_detalle
ADD CONSTRAINT chk_precio_unitario_positivo
CHECK (precio_unitario > 0);
```

## 📈 Optimización y Performance

### Estrategia de Índices

#### Índices de Búsqueda Frecuente

```sql
-- Búsquedas de productos
CREATE INDEX idx_productos_busqueda ON productos(activo, categoria_id, precio);
CREATE FULLTEXT INDEX idx_productos_texto ON productos(nombre, descripcion);

-- Consultas de ventas
CREATE INDEX idx_ventas_usuario_fecha ON ventas_cabecera(usuario_id, fecha);
CREATE INDEX idx_ventas_periodo ON ventas_cabecera(fecha, estado);

-- Autenticación de usuarios
CREATE INDEX idx_usuarios_login ON usuarios(email, activo);
```

#### Índices Compuestos

```sql
-- Para reportes de ventas
CREATE INDEX idx_ventas_reporte ON ventas_detalle(producto_id, venta_id);
CREATE INDEX idx_productos_categoria_activo ON productos(categoria_id, activo, precio);
```

### Configuración MySQL

```sql
-- Variables de configuración recomendadas
SET GLOBAL innodb_buffer_pool_size = 268435456;  -- 256MB
SET GLOBAL max_connections = 200;
SET GLOBAL query_cache_size = 67108864;  -- 64MB
SET GLOBAL tmp_table_size = 67108864;    -- 64MB
SET GLOBAL max_heap_table_size = 67108864;
```

## 📊 Consultas Típicas

### Consultas de Negocio

#### Top Productos Más Vendidos

```sql
SELECT
    p.nombre,
    p.precio,
    SUM(vd.cantidad) as total_vendido,
    SUM(vd.cantidad * vd.precio_unitario) as revenue
FROM productos p
JOIN ventas_detalle vd ON p.id = vd.producto_id
JOIN ventas_cabecera vc ON vd.venta_id = vc.id
WHERE vc.estado = 'completada'
    AND vc.fecha >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
GROUP BY p.id, p.nombre, p.precio
ORDER BY total_vendido DESC
LIMIT 10;
```

#### Ventas por Cliente

```sql
SELECT
    u.nombre,
    u.email,
    COUNT(vc.id) as total_ventas,
    SUM(vc.total) as monto_total,
    AVG(vc.total) as ticket_promedio,
    MAX(vc.fecha) as ultima_compra
FROM usuarios u
JOIN ventas_cabecera vc ON u.id = vc.usuario_id
WHERE u.perfil_id = 2
    AND vc.estado = 'completada'
GROUP BY u.id, u.nombre, u.email
ORDER BY monto_total DESC;
```

#### Stock Bajo

```sql
SELECT
    p.nombre,
    p.stock,
    c.nombre as categoria,
    p.precio
FROM productos p
LEFT JOIN categorias c ON p.categoria_id = c.id
WHERE p.activo = 1
    AND p.stock <= 5
ORDER BY p.stock ASC, p.nombre;
```

#### Reporte de Ventas por Período

```sql
SELECT
    DATE(vc.fecha) as fecha,
    COUNT(vc.id) as num_ventas,
    SUM(vc.total) as total_dia,
    AVG(vc.total) as ticket_promedio,
    COUNT(DISTINCT vc.usuario_id) as clientes_unicos
FROM ventas_cabecera vc
WHERE vc.estado = 'completada'
    AND vc.fecha >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
GROUP BY DATE(vc.fecha)
ORDER BY fecha DESC;
```

### Consultas de Integridad

#### Verificar Consistencia de Totales

```sql
SELECT
    vc.id as venta_id,
    vc.total as total_cabecera,
    SUM(vd.cantidad * vd.precio_unitario) as total_calculado,
    ABS(vc.total - SUM(vd.cantidad * vd.precio_unitario)) as diferencia
FROM ventas_cabecera vc
LEFT JOIN ventas_detalle vd ON vc.id = vd.venta_id
GROUP BY vc.id, vc.total
HAVING diferencia > 0.01;
```

#### Productos Sin Categoría

```sql
SELECT
    p.id,
    p.nombre,
    p.precio,
    p.stock
FROM productos p
WHERE p.categoria_id IS NULL
    AND p.activo = 1;
```

## 🛠️ Mantenimiento

### Scripts de Mantenimiento

#### Limpieza de Datos Antiguos

```sql
-- Eliminar consultas antiguas (> 1 año)
DELETE FROM consultas
WHERE fecha < DATE_SUB(CURDATE(), INTERVAL 1 YEAR);

-- Archivar ventas antiguas (opcional)
CREATE TABLE ventas_historicas LIKE ventas_cabecera;
INSERT INTO ventas_historicas
SELECT * FROM ventas_cabecera
WHERE fecha < DATE_SUB(CURDATE(), INTERVAL 2 YEAR);
```

#### Optimización de Tablas

```sql
-- Analizar y optimizar tablas
ANALYZE TABLE productos, ventas_cabecera, ventas_detalle;
OPTIMIZE TABLE productos, ventas_cabecera, ventas_detalle;
```

### Backup Strategy

```bash
# Backup completo diario
./scripts/maintenance/backup.sh --name "daily-$(date +%Y%m%d)"

# Backup solo estructura (para development)
./scripts/maintenance/backup.sh --no-data --name "schema-only"

# Backup incremental (solo cambios)
mysqldump --single-transaction --flush-logs --master-data=2 \
  yagaruete_camp > incremental_backup.sql
```

## 🔒 Seguridad

### Configuración de Usuarios

```sql
-- Usuario de aplicación (solo permisos necesarios)
CREATE USER 'yagaruete_user'@'%' IDENTIFIED BY 'secure_password';
GRANT SELECT, INSERT, UPDATE, DELETE ON yagaruete_camp.* TO 'yagaruete_user'@'%';

-- Usuario de backup (solo lectura)
CREATE USER 'backup_user'@'localhost' IDENTIFIED BY 'backup_password';
GRANT SELECT, LOCK TABLES ON yagaruete_camp.* TO 'backup_user'@'localhost';
```

### Auditabilidad

Todas las tablas incluyen:

- `created_at`: Timestamp de creación
- `updated_at`: Timestamp de última modificación
- Campos `activo` para soft delete

## 📚 Referencias

- [MySQL 8.0 Reference Manual](https://dev.mysql.com/doc/refman/8.0/en/)
- [CodeIgniter 4 Database](https://codeigniter.com/user_guide/database/index.html)
- [Database Design Best Practices](https://www.mysqltutorial.org/mysql-database-design/)

---

**Yagaruete Camp** - Esquema de base de datos robusto y escalable 🗄️
