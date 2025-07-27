# 🌱 Guía de Seeders - Yagaruete Camp

## 📋 Descripción General

Los **seeders** son clases especializadas que pueblan la base de datos con datos iniciales y de prueba. El sistema de Yagaruete Camp incluye seeders optimizados que crean datos realistas para un e-commerce de productos outdoor.

## 🎯 Objetivos de los Seeders

1. **Datos Consistentes**: Crear información realista y coherente
2. **Prevención de Duplicados**: Evitar conflictos en ejecuciones múltiples
3. **Relaciones Íntegras**: Respetar claves foráneas y dependencias
4. **Facilitar Testing**: Proporcionar datos para pruebas
5. **Ambiente de Demo**: Datos atractivos para demostraciones

## 📊 Seeders Disponibles

### 🗂️ CategoriasSeeder

**Archivo**: `app/Database/Seeds/CategoriasSeeder.php`

**Propósito**: Crear categorías temáticas para productos outdoor

**Datos Creados**:

```php
$categorias = [
    ['nombre' => 'Tiendas y Refugios', 'descripcion' => 'Tiendas de campaña, refugios y estructuras'],
    ['nombre' => 'Mochilas y Equipaje', 'descripcion' => 'Mochilas de trekking, bolsas y equipaje'],
    ['nombre' => 'Senderismo y Trekking', 'descripcion' => 'Equipos para caminatas y trekking'],
    ['nombre' => 'Escalada y Montañismo', 'descripcion' => 'Equipos técnicos para escalada'],
    ['nombre' => 'Camping y Vivac', 'descripcion' => 'Equipos para acampar'],
    // ... 15 categorías total
];
```

**Características**:

- ✅ Prevención de duplicados por nombre
- ✅ Descripciones detalladas
- ✅ Estado activo por defecto
- ✅ Logging de operaciones

**Ejecución**:

```bash
docker-compose exec app php spark db:seed CategoriasSeeder
```

### 👥 UsuariosSeeder

**Archivo**: `app/Database/Seeds/UsuariosSeeder.php`

**Propósito**: Crear usuarios de prueba con diferentes roles

**Tipos de Usuario**:

#### Administradores (perfil_id = 1)

```php
$admins = [
    [
        'usuario' => 'admin',
        'nombre' => 'Administrador del Sistema',
        'email' => 'admin@test.com',
        'password' => 'admin123',
        'perfil_id' => 1
    ]
];
```

#### Clientes (perfil_id = 2)

```php
$clientes = [
    [
        'usuario' => 'cliente',
        'nombre' => 'Cliente Demo',
        'email' => 'cliente@test.com',
        'password' => 'cliente123',
        'perfil_id' => 2
    ],
    // ... más clientes
];
```

**Características**:

- ✅ Contraseñas hasheadas con password_hash()
- ✅ Verificación de duplicados por email y usuario
- ✅ Cumplimiento de reglas de seguridad
- ✅ Usuarios activos por defecto

**Ejecución**:

```bash
docker-compose exec app php spark db:seed UsuariosSeeder
```

### 🛍️ ProductosSeeder

**Archivo**: `app/Database/Seeds/ProductosSeeder.php`

**Propósito**: Crear catálogo de productos outdoor realistas

**Estructura de Productos**:

```php
$productos = [
    // Tiendas y Refugios
    [
        'nombre' => 'Tienda de Campaña 4 Estaciones Doite',
        'descripcion' => 'Tienda resistente para 2 personas, ideal para condiciones extremas',
        'precio' => 189990.00,
        'stock' => 8,
        'categoria' => 'Tiendas y Refugios',
        'imagen' => 'tienda-doite-4estaciones.jpg'
    ],
    // ... productos por categoría
];
```

**Categorías de Productos**:

- **Tiendas y Refugios**: Carpas, refugios, toldos
- **Mochilas y Equipaje**: Mochilas técnicas, riñoneras, bolsas
- **Senderismo**: Bastones, GPS, brújulas
- **Escalada**: Arneses, cascos, cuerdas
- **Camping**: Sleeping bags, colchonetas
- **Electrónicos**: Linternas, radios, cargadores solares
- **Ropa Técnica**: Chaquetas, pantalones, capas base
- **Calzado**: Botas de trekking, zapatillas approach

**Características**:

- ✅ Productos temáticos consistentes
- ✅ Precios realistas en pesos argentinos
- ✅ Stock variado (5-50 unidades)
- ✅ Descripciones detalladas
- ✅ Relación correcta con categorías

**Ejecución**:

```bash
docker-compose exec app php spark db:seed ProductosSeeder
```

### 💰 VentasSeeder

**Archivo**: `app/Database/Seeds/VentasSeeder.php`

**Propósito**: Crear historial de ventas realistas

**Estructura de Ventas**:

```php
$ventas = [
    [
        'usuario_email' => 'cliente@test.com',
        'fecha' => '2025-07-20',
        'productos' => [
            ['nombre' => 'Tienda de Campaña 4 Estaciones Doite', 'cantidad' => 1],
            ['nombre' => 'Mochila de Trekking 70L Deuter', 'cantidad' => 1]
        ]
    ],
    // ... más ventas
];
```

**Características**:

- ✅ Solo clientes realizan compras (perfil_id = 2)
- ✅ Fechas distribuidas en período reciente
- ✅ Múltiples productos por venta
- ✅ Totales calculados automáticamente
- ✅ Estado "completada" por defecto

**Ejecución**:

```bash
docker-compose exec app php spark db:seed VentasSeeder
```

### 🎛️ DatabaseSeeder

**Archivo**: `app/Database/Seeds/DatabaseSeeder.php`

**Propósito**: Orquestador principal que ejecuta todos los seeders

**Orden de Ejecución**:

```php
public function run()
{
    echo "🌱 Iniciando seeders de Yagaruete Camp...\n";

    $this->call('CategoriasSeeder');
    $this->call('UsuariosSeeder');
    $this->call('ProductosSeeder');
    $this->call('VentasSeeder');

    echo "✅ Todos los seeders ejecutados exitosamente!\n";
}
```

**Características**:

- ✅ Ejecución en orden de dependencias
- ✅ Logging detallado de progreso
- ✅ Manejo de errores
- ✅ Verificación final de datos

**Ejecución**:

```bash
docker-compose exec app php spark db:seed DatabaseSeeder
```

## 🔧 Desarrollo de Seeders

### Crear Nuevo Seeder

```bash
# Generar seeder
docker-compose exec app php spark make:seeder NuevoSeeder

# Ubicación del archivo
# app/Database/Seeds/NuevoSeeder.php
```

### Estructura Básica

```php
<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class NuevoSeeder extends Seeder
{
    public function run()
    {
        // Verificar si ya existen datos
        $builder = $this->db->table('mi_tabla');
        if ($builder->countAllResults() > 0) {
            echo "⚠️  Datos ya existen en mi_tabla, saltando...\n";
            return;
        }

        // Datos a insertar
        $datos = [
            ['campo1' => 'valor1', 'campo2' => 'valor2'],
            // ... más datos
        ];

        // Insertar datos
        foreach ($datos as $dato) {
            $builder->insert($dato);
        }

        echo "✅ Seeder NuevoSeeder ejecutado: " . count($datos) . " registros\n";
    }
}
```

### Buenas Prácticas

#### Prevención de Duplicados

```php
// Verificar por campo único
$existing = $this->db->table('tabla')
    ->where('email', $email)
    ->get()
    ->getRow();

if (!$existing) {
    // Insertar solo si no existe
    $this->db->table('tabla')->insert($data);
}
```

#### Relaciones Seguras

```php
// Obtener ID de registro relacionado
$categoria = $this->db->table('categorias')
    ->where('nombre', $nombreCategoria)
    ->get()
    ->getRow();

if ($categoria) {
    $producto['categoria_id'] = $categoria->id;
    $this->db->table('productos')->insert($producto);
}
```

#### Logging Informativo

```php
echo "🌱 Iniciando " . get_class($this) . "...\n";
echo "✅ Insertados: $count registros\n";
echo "⚠️  Saltados: $skipped duplicados\n";
```

## 📊 Datos Generados

### Resumen por Tabla

| Tabla             | Registros | Descripción                  |
| ----------------- | --------- | ---------------------------- |
| `categorias`      | 15        | Categorías outdoor completas |
| `usuarios`        | 10+       | Admins y clientes de prueba  |
| `productos`       | 30+       | Catálogo outdoor variado     |
| `ventas_cabecera` | 15+       | Histórico de ventas          |
| `ventas_detalle`  | 40+       | Detalles de compras          |

### Estadísticas Típicas

```sql
-- Productos por categoría
SELECT
    c.nombre as categoria,
    COUNT(p.id) as productos,
    AVG(p.precio) as precio_promedio
FROM categorias c
LEFT JOIN productos p ON c.id = p.categoria_id
GROUP BY c.id, c.nombre
ORDER BY productos DESC;

-- Ventas por usuario
SELECT
    u.nombre as cliente,
    COUNT(vc.id) as total_ventas,
    SUM(vc.total) as monto_total
FROM usuarios u
LEFT JOIN ventas_cabecera vc ON u.id = vc.usuario_id
WHERE u.perfil_id = 2
GROUP BY u.id, u.nombre
ORDER BY monto_total DESC;
```

## 🚀 Ejecución y Scripts

### Ejecución Manual

```bash
# Todos los seeders
docker-compose exec app php spark db:seed DatabaseSeeder

# Seeder específico
docker-compose exec app php spark db:seed CategoriasSeeder

# Con información detallada
docker-compose exec app php spark db:seed DatabaseSeeder --verbose
```

### Script Automatizado

```bash
# Inicialización completa
./scripts/setup/init-database.sh

# Solo seeders (asume que migraciones ya están)
./scripts/setup/init-database.sh --skip-migrations

# Forzar recreación
./scripts/setup/init-database.sh --force
```

### Testing y Desarrollo

```bash
# Reset completo para testing
docker-compose down -v
docker-compose up -d mysql
sleep 10
./scripts/setup/init-database.sh --force

# Verificar datos
docker-compose exec app php scripts/maintenance/verify-data.php
```

## 🔍 Verificación de Seeders

### Comandos de Verificación

```bash
# Verificar que las tablas tienen datos
docker-compose exec app php spark db:table usuarios
docker-compose exec app php spark db:table productos --limit 10

# Contar registros por tabla
docker-compose exec mysql mysql -u yagaruete_user -p yagaruete_camp -e "
SELECT
  'categorias' as tabla, COUNT(*) as registros FROM categorias
UNION ALL
SELECT
  'usuarios' as tabla, COUNT(*) as registros FROM usuarios
UNION ALL
SELECT
  'productos' as tabla, COUNT(*) as registros FROM productos
UNION ALL
SELECT
  'ventas_cabecera' as tabla, COUNT(*) as registros FROM ventas_cabecera
UNION ALL
SELECT
  'ventas_detalle' as tabla, COUNT(*) as registros FROM ventas_detalle;
"
```

### Script de Verificación

```bash
# Verificación completa de integridad
docker-compose exec app php scripts/maintenance/verify-data.php --verbose
```

## ⚡ Optimización y Performance

### Tips de Performance

1. **Inserts en Lote**:

   ```php
   // En lugar de múltiples insert()
   $this->db->table('tabla')->insertBatch($datos);
   ```

2. **Verificación Eficiente**:

   ```php
   // Usar countAllResults() en lugar de get()
   $exists = $this->db->table('tabla')->where('campo', $valor)->countAllResults() > 0;
   ```

3. **Transacciones para Consistencia**:
   ```php
   $this->db->transStart();
   // ... operaciones de seeding
   $this->db->transComplete();
   ```

### Seeders Condicionales

```php
// Solo ejecutar en ambiente de desarrollo
if (ENVIRONMENT === 'development') {
    $this->call('DemoDataSeeder');
}

// Diferentes datos según ambiente
$datos = ENVIRONMENT === 'production' ? $prodData : $devData;
```

## 📚 Referencias y Recursos

- [CodeIgniter 4 Database Seeding](https://codeigniter.com/user_guide/dbmgmt/seeds.html)
- [PHP password_hash()](https://www.php.net/manual/en/function.password-hash.php)
- [MySQL Faker Data](https://fakerphp.github.io/)

---

**Yagaruete Camp** - Seeders inteligentes para datos realistas 🌱
