# 🗄️ Base de Datos - Yagaruete Camp

> Documentación completa del esquema de base de datos y optimizaciones

## 📊 Esquema de Base de Datos

### Tablas Principales

```sql
-- Estructura optimizada con índices estratégicos
CREATE DATABASE IF NOT EXISTS yaguarete_camp;
USE yaguarete_camp;

-- 1. Usuarios del sistema
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'customer') DEFAULT 'customer',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_email (email),
    INDEX idx_role_status (role, status)
) ENGINE=InnoDB;

-- 2. Categorías de productos
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    slug VARCHAR(100) UNIQUE NOT NULL,
    image VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_slug (slug),
    INDEX idx_status_sort (status, sort_order)
) ENGINE=InnoDB;

-- 3. Productos del catálogo
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    short_description VARCHAR(500),
    price DECIMAL(10,2) NOT NULL,
    compare_price DECIMAL(10,2) NULL,
    sku VARCHAR(50) UNIQUE,
    stock INT NOT NULL DEFAULT 0,
    min_stock INT DEFAULT 5,
    weight DECIMAL(8,2),
    dimensions VARCHAR(100),
    image VARCHAR(255),
    gallery JSON,
    features JSON,
    status ENUM('active', 'inactive', 'draft') DEFAULT 'active',
    is_featured BOOLEAN DEFAULT FALSE,
    seo_title VARCHAR(255),
    seo_description VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,

    INDEX idx_category_status (category_id, status),
    INDEX idx_price_status (price, status),
    INDEX idx_stock_status (stock, status),
    INDEX idx_featured (is_featured, status),
    INDEX idx_sku (sku),
    FULLTEXT INDEX idx_search (name, description, short_description)
) ENGINE=InnoDB;

-- 4. Carritos de compra
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(128),
    user_id INT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,

    INDEX idx_session (session_id),
    INDEX idx_user (user_id),
    INDEX idx_product (product_id),
    UNIQUE KEY unique_cart_item (session_id, product_id, user_id)
) ENGINE=InnoDB;

-- 5. Órdenes de compra
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    subtotal DECIMAL(10,2) NOT NULL,
    tax_amount DECIMAL(10,2) DEFAULT 0,
    shipping_amount DECIMAL(10,2) DEFAULT 0,
    total_amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'ARS',
    payment_method VARCHAR(50),
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,

    INDEX idx_user_status (user_id, status),
    INDEX idx_order_number (order_number),
    INDEX idx_payment_status (payment_status),
    INDEX idx_created_date (created_at)
) ENGINE=InnoDB;

-- 6. Items de órdenes
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    product_sku VARCHAR(50),
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,

    INDEX idx_order (order_id),
    INDEX idx_product (product_id)
) ENGINE=InnoDB;

-- 7. Direcciones de envío
CREATE TABLE shipping_addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    company VARCHAR(100),
    address_line_1 VARCHAR(255) NOT NULL,
    address_line_2 VARCHAR(255),
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    postal_code VARCHAR(20) NOT NULL,
    country VARCHAR(100) DEFAULT 'Argentina',
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,

    INDEX idx_order (order_id)
) ENGINE=InnoDB;
```

## 🚀 Optimizaciones Implementadas

### Índices Estratégicos

```sql
-- Optimizaciones específicas para queries comunes

-- 1. Búsqueda de productos
ALTER TABLE products ADD FULLTEXT(name, description);

-- 2. Filtros por categoría y precio
ALTER TABLE products ADD INDEX idx_category_price (category_id, price, status);

-- 3. Productos destacados
ALTER TABLE products ADD INDEX idx_featured_category (is_featured, category_id, status);

-- 4. Gestión de stock
ALTER TABLE products ADD INDEX idx_stock_alerts (stock, min_stock, status);

-- 5. Órdenes por usuario y fecha
ALTER TABLE orders ADD INDEX idx_user_date (user_id, created_at DESC);

-- 6. Reportes de ventas
ALTER TABLE orders ADD INDEX idx_sales_report (status, created_at, total_amount);
```

### Query Cache Optimizado

```sql
-- Configuración MySQL para máximo rendimiento
SET GLOBAL query_cache_type = ON;
SET GLOBAL query_cache_size = 268435456;  -- 256MB
SET GLOBAL query_cache_limit = 8388608;   -- 8MB por query

-- Verificar configuración
SHOW VARIABLES LIKE 'query_cache%';
SHOW STATUS LIKE 'Qcache%';
```

### InnoDB Buffer Pool

```sql
-- Buffer pool optimizado para datos en memoria
SET GLOBAL innodb_buffer_pool_size = 536870912;  -- 512MB
SET GLOBAL innodb_log_file_size = 268435456;     -- 256MB

-- Verificar configuración
SHOW VARIABLES LIKE 'innodb_buffer_pool_size';
```

## 📊 Datos de Prueba (Seeders)

### Usuarios Incluidos

| Email            | Role     | Password   | Descripción       |
| ---------------- | -------- | ---------- | ----------------- |
| admin@test.com   | admin    | admin123   | Administrador     |
| cliente@test.com | customer | cliente123 | Cliente de prueba |

### Categorías de Productos

| Categoría  | Productos | Descripción               |
| ---------- | --------- | ------------------------- |
| Carpas     | 4         | Tiendas y refugios        |
| Sleeping   | 3         | Bolsas de dormir          |
| Mochilas   | 4         | Mochilas técnicas         |
| Cocina     | 4         | Equipos de cocina camping |
| Outdoor    | 4         | Equipos técnicos outdoor  |
| Accesorios | 4         | Accesorios diversos       |

### Productos Destacados

- **Carpa Doite Himalaya 6** - $89,999 - Carpa profesional
- **Mochila Montagne Andinista 75L** - $45,999 - Mochila técnica
- **Bolsa Doite Alpes -15°C** - $32,999 - Sleeping profesional
- **Anafe Primus Express** - $18,999 - Cocina portátil

## 🔄 Migraciones

### Sistema de Versionado

```bash
# Ejecutar migraciones
docker-compose exec app php spark migrate

# Ver estado de migraciones
docker-compose exec app php spark migrate:status

# Rollback (si es necesario)
docker-compose exec app php spark migrate:rollback

# Crear nueva migración
docker-compose exec app php spark make:migration NombreMigracion
```

### Migraciones Disponibles

| Archivo                                   | Descripción       | Estado |
| ----------------------------------------- | ----------------- | ------ |
| `001_create_users_table.php`              | Tabla de usuarios | ✅     |
| `002_create_categories_table.php`         | Categorías        | ✅     |
| `003_create_products_table.php`           | Productos         | ✅     |
| `004_create_cart_table.php`               | Carrito           | ✅     |
| `005_create_orders_table.php`             | Órdenes           | ✅     |
| `006_create_order_items_table.php`        | Items de órdenes  | ✅     |
| `007_create_shipping_addresses_table.php` | Direcciones       | ✅     |

## 🌱 Seeders

### Cargar Datos de Prueba

```bash
# Seeder completo (recomendado)
docker-compose exec app php spark db:seed DatabaseSeeder

# Seeders individuales
docker-compose exec app php spark db:seed CategorySeeder
docker-compose exec app php spark db:seed UserSeeder
docker-compose exec app php spark db:seed ProductSeeder
```

### Estructura de Seeders

```php
// DatabaseSeeder.php - Seeder principal
class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call('CategorySeeder');    // 6 categorías
        $this->call('UserSeeder');        // 9 usuarios
        $this->call('ProductSeeder');     // 23 productos
        $this->call('SampleDataSeeder');  // Datos de ejemplo
    }
}
```

## 📈 Performance Monitoring

### Queries Más Comunes

```sql
-- 1. Listado de productos por categoría (con cache)
SELECT p.*, c.name as category_name
FROM products p
JOIN categories c ON p.category_id = c.id
WHERE p.status = 'active' AND c.id = ?
ORDER BY p.is_featured DESC, p.created_at DESC;

-- 2. Búsqueda de productos (con FULLTEXT)
SELECT *, MATCH(name, description) AGAINST(? IN NATURAL LANGUAGE MODE) as relevance
FROM products
WHERE MATCH(name, description) AGAINST(? IN NATURAL LANGUAGE MODE)
AND status = 'active'
ORDER BY relevance DESC;

-- 3. Carrito de usuario
SELECT c.*, p.name, p.image, p.price as current_price
FROM cart c
JOIN products p ON c.product_id = p.id
WHERE c.user_id = ? OR c.session_id = ?;

-- 4. Órdenes de usuario
SELECT * FROM orders
WHERE user_id = ?
ORDER BY created_at DESC;
```

### Índices de Performance

```sql
-- Verificar uso de índices
EXPLAIN SELECT * FROM products WHERE category_id = 1 AND status = 'active';

-- Análisis de queries lentas
SHOW PROCESSLIST;
SELECT * FROM INFORMATION_SCHEMA.PROCESSLIST WHERE COMMAND != 'Sleep';

-- Estadísticas de tablas
SHOW TABLE STATUS LIKE 'products';
ANALYZE TABLE products;
```

## 🔧 Mantenimiento

### Tareas Regulares

```bash
# 1. Optimizar tablas (semanal)
docker-compose exec db mysql -u root -p -e "OPTIMIZE TABLE products, orders, cart;"

# 2. Limpiar carritos antiguos (diario)
docker-compose exec db mysql -u root -p -e "
DELETE FROM cart
WHERE created_at < DATE_SUB(NOW(), INTERVAL 7 DAY)
AND user_id IS NULL;
"

# 3. Backup de base de datos
docker-compose exec db mysqldump -u root -p yaguarete_camp > backup_$(date +%Y%m%d).sql

# 4. Verificar integridad
docker-compose exec db mysqlcheck -u root -p --check yaguarete_camp
```

### Monitoreo de Performance

```sql
-- Query Cache Statistics
SHOW STATUS LIKE 'Qcache%';

-- InnoDB Buffer Pool
SHOW STATUS LIKE 'Innodb_buffer_pool%';

-- Slow Queries
SHOW STATUS LIKE 'Slow_queries';

-- Connections
SHOW STATUS LIKE 'Connections';
SHOW STATUS LIKE 'Threads_connected';
```

## 🛡️ Seguridad

### Configuración Segura

```sql
-- Usuario específico para la aplicación
CREATE USER 'yaguarete_user'@'%' IDENTIFIED BY 'secure_password_123';
GRANT SELECT, INSERT, UPDATE, DELETE ON yaguarete_camp.* TO 'yaguarete_user'@'%';
FLUSH PRIVILEGES;

-- Remover usuarios por defecto (producción)
DROP USER IF EXISTS ''@'localhost';
DROP USER IF EXISTS ''@'%';
```

### Validaciones Implementadas

- ✅ **Foreign Keys** para integridad referencial
- ✅ **Constraints** en campos críticos
- ✅ **Prepared Statements** en todas las queries
- ✅ **Input validation** en modelos CodeIgniter
- ✅ **Password hashing** con bcrypt
- ✅ **SQL Injection prevention** automático

---

**Yagaruete Camp Database** - Esquema optimizado para alto rendimiento
_Documentación actualizada: 28 de Julio, 2025_ 🗄️⚡
