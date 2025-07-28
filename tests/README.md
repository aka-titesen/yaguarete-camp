# 🧪 Tests - Yagaruete Camp

> Guía para ejecutar y escribir tests en el sistema de e-commerce Yagaruete Camp

## 📋 Descripción General

Este directorio contiene las pruebas automatizadas para el sistema Yagaruete Camp, diseñadas para garantizar la calidad y estabilidad del código mediante tests unitarios, de integración y funcionales.

## 🛠️ Configuración y Requisitos

### Dependencias

- **PHPUnit 9.x+** - Framework de testing
- **CodeIgniter 4.5+** - Framework base con soporte de testing
- **Composer** - Gestión de dependencias
- **Docker** - Entorno de desarrollo

### Instalación

```bash
# Instalar dependencias de testing
docker-compose exec app composer install

# Verificar instalación de PHPUnit
docker-compose exec app vendor/bin/phpunit --version
```

## 📊 Estructura de Tests

```
tests/
├── unit/                 # Tests unitarios
│   ├── Models/          # Tests de modelos
│   ├── Controllers/     # Tests de controladores
│   └── Helpers/         # Tests de helpers
├── integration/         # Tests de integración
├── functional/          # Tests funcionales/E2E
├── _support/           # Soporte y fixtures
│   ├── Database/       # Seeders de testing
│   └── Models/         # Modelos de test
└── README.md           # Esta documentación
```

## 🚀 Ejecutar Tests

### Comandos Básicos

```bash
# Ejecutar todos los tests
docker-compose exec app vendor/bin/phpunit

# Tests específicos por directorio
docker-compose exec app vendor/bin/phpunit tests/unit/
docker-compose exec app vendor/bin/phpunit tests/integration/

# Test específico
docker-compose exec app vendor/bin/phpunit tests/unit/Models/ProductModelTest.php

# Con reporte de cobertura
docker-compose exec app vendor/bin/phpunit --coverage-html coverage/
```

### Configuración de Base de Datos

Los tests utilizan una base de datos separada configurada en `app/Config/Database.php`:

```php
// Configuración para testing
public array $tests = [
    'DSN'      => '',
    'hostname' => 'db',
    'username' => 'yaguarete_user',
    'password' => 'secure_password_123',
    'database' => 'yaguarete_camp_test',
    'DBDriver' => 'MySQLi',
    'DBPrefix' => '',
    'pConnect' => false,
    'DBDebug'  => true,
    'charset'  => 'utf8mb4',
    'DBCollat' => 'utf8mb4_general_ci',
];
```

### Preparación del Entorno de Tests

```bash
# Crear base de datos de tests
docker-compose exec db mysql -u root -p -e "
CREATE DATABASE IF NOT EXISTS yaguarete_camp_test;
GRANT ALL PRIVILEGES ON yaguarete_camp_test.* TO 'yaguarete_user'@'%';
FLUSH PRIVILEGES;
"

# Ejecutar migraciones para tests
docker-compose exec app php spark migrate --environment testing

# Cargar datos de prueba
docker-compose exec app php spark db:seed TestDataSeeder --environment testing
```

## 📝 Escribir Tests

### Ejemplo de Test de Modelo

```php
<?php

namespace Tests\Unit\Models;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\ProductModel;

class ProductModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = true;
    protected $migrateOnce = false;
    protected $seed = 'TestProductSeeder';

    public function testCanFindActiveProducts()
    {
        $model = new ProductModel();
        $products = $model->where('status', 'active')->findAll();

        $this->assertIsArray($products);
        $this->assertGreaterThan(0, count($products));
    }

    public function testCanCreateProduct()
    {
        $model = new ProductModel();
        $data = [
            'name' => 'Test Product',
            'category_id' => 1,
            'price' => 99.99,
            'stock' => 10,
            'status' => 'active'
        ];

        $id = $model->insert($data);
        $this->assertIsNumeric($id);

        $product = $model->find($id);
        $this->assertEquals('Test Product', $product['name']);
    }
}
```

### Ejemplo de Test de Controlador

```php
<?php

namespace Tests\Integration\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;

class ProductControllerTest extends CIUnitTestCase
{
    use ControllerTestTrait, DatabaseTestTrait;

    protected $migrate = true;
    protected $seed = 'TestProductSeeder';

    public function testProductsPageLoads()
    {
        $result = $this->withURI('http://localhost:8080/productos')
                       ->controller('App\Controllers\ProductController')
                       ->execute('index');

        $this->assertTrue($result->isOK());
        $this->assertStringContainsString('productos', $result->getBody());
    }

    public function testProductDetailPage()
    {
        $result = $this->withURI('http://localhost:8080/productos/1')
                       ->controller('App\Controllers\ProductController')
                       ->execute('show', 1);

        $this->assertTrue($result->isOK());
    }
}
```

## 📊 Cobertura de Código

### Generar Reportes

```bash
# Reporte HTML (recomendado)
docker-compose exec app vendor/bin/phpunit --coverage-html coverage/

# Reporte en texto
docker-compose exec app vendor/bin/phpunit --coverage-text

# Reporte Clover XML (para CI/CD)
docker-compose exec app vendor/bin/phpunit --coverage-clover coverage.xml
```

### Configuración XDebug

Para habilitar cobertura de código, agregar en `docker/php/php.ini`:

```ini
[xdebug]
xdebug.mode = coverage,debug
xdebug.start_with_request = yes
xdebug.client_host = host.docker.internal
xdebug.client_port = 9003
```

## 🎯 Tests por Categoría

### Tests Unitarios (Unit Tests)

Prueban funciones y métodos aislados:

- **Modelos**: Validaciones, métodos de consulta
- **Helpers**: Funciones utilitarias
- **Libraries**: Clases de negocio
- **Validators**: Reglas de validación personalizadas

### Tests de Integración

Prueban la interacción entre componentes:

- **Controller + Model**: Flujo completo de datos
- **API Endpoints**: Respuestas de API
- **Database Operations**: CRUD operations
- **Cache Integration**: Funcionamiento del cache

### Tests Funcionales/E2E

Prueban flujos completos del usuario:

- **Registro y Login**: Autenticación completa
- **Carrito de Compras**: Flujo de compra
- **Gestión de Productos**: CRUD admin
- **Formularios**: Envío y validación

## 🔧 Configuración Avanzada

### PHPUnit Configuration

```xml
<!-- phpunit.xml -->
<phpunit bootstrap="vendor/codeigniter4/framework/system/Test/bootstrap.php">
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/unit</directory>
        </testsuite>
        <testsuite name="Integration">
            <directory>tests/integration</directory>
        </testsuite>
        <testsuite name="Functional">
            <directory>tests/functional</directory>
        </testsuite>
    </testsuites>

    <filter>
        <whitelist>
            <directory suffix=".php">app/</directory>
            <exclude>
                <directory>app/Views/</directory>
                <file>app/Config/Routes.php</file>
            </exclude>
        </whitelist>
    </filter>
</phpunit>
```

### CI/CD Integration

```yaml
# .github/workflows/tests.yml
name: Tests
on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2

      - name: Setup Environment
        run: |
          cp .env.example .env
          docker-compose up -d

      - name: Install Dependencies
        run: docker-compose exec app composer install

      - name: Run Tests
        run: docker-compose exec app vendor/bin/phpunit --coverage-clover coverage.xml

      - name: Upload Coverage
        uses: codecov/codecov-action@v1
        with:
          file: ./coverage.xml
```

## 📚 Recursos

- **[CodeIgniter 4 Testing Guide](https://codeigniter.com/user_guide/testing/index.html)** - Documentación oficial
- **[PHPUnit Documentation](https://phpunit.de/documentation.html)** - Manual de PHPUnit
- **[Database Testing](https://codeigniter.com/user_guide/testing/database.html)** - Testing con base de datos
- **[Testing Best Practices](https://phpunit.de/documentation.html)** - Mejores prácticas

---

**Yagaruete Camp Tests** - Testing completo para calidad de código
_Documentación actualizada: 28 de Julio, 2025_ 🧪✅
directory name after phpunit.

```console
> ./phpunit app/Models
```

## Generating Code Coverage

To generate coverage information, including HTML reports you can view in your browser,
you can use the following command:

```console
> ./phpunit --colors --coverage-text=tests/coverage.txt --coverage-html=tests/coverage/ -d memory_limit=1024m
```

This runs all of the tests again collecting information about how many lines,
functions, and files are tested. It also reports the percentage of the code that is covered by tests.
It is collected in two formats: a simple text file that provides an overview as well
as a comprehensive collection of HTML files that show the status of every line of code in the project.

The text file can be found at **tests/coverage.txt**.
The HTML files can be viewed by opening **tests/coverage/index.html** in your favorite browser.

## PHPUnit XML Configuration

The repository has a `phpunit.xml.dist` file in the project root that's used for
PHPUnit configuration. This is used to provide a default configuration if you
do not have your own configuration file in the project root.

The normal practice would be to copy `phpunit.xml.dist` to `phpunit.xml`
(which is git ignored), and to tailor it as you see fit.
For instance, you might wish to exclude database tests, or automatically generate
HTML code coverage reports.

## Test Cases

Every test needs a _test case_, or class that your tests extend. CodeIgniter 4
provides one class that you may use directly:

- `CodeIgniter\Test\CIUnitTestCase`

Most of the time you will want to write your own test cases that extend `CIUnitTestCase`
to hold functions and services common to your test suites.

## Creating Tests

All tests go in the **tests/** directory. Each test file is a class that extends a
**Test Case** (see above) and contains methods for the individual tests. These method
names must start with the word "test" and should have descriptive names for precisely what
they are testing:
`testUserCanModifyFile()` `testOutputColorMatchesInput()` `testIsLoggedInFailsWithInvalidUser()`

Writing tests is an art, and there are many resources available to help learn how.
Review the links above and always pay attention to your code coverage.

### Database Tests

Tests can include migrating, seeding, and testing against a mock or live database.
Be sure to modify the test case (or create your own) to point to your seed and migrations
and include any additional steps to be run before tests in the `setUp()` method.
See [Testing Your Database](https://codeigniter.com/user_guide/testing/database.html)
for details.
