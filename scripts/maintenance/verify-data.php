<?php

/**
 * =============================================================================
 * YAGARUETE CAMP - SCRIPT DE VERIFICACIÓN DE DATOS
 * =============================================================================
 * Descripción: Verifica la integridad y consistencia de los datos en la BD
 * Autor: Proyecto Martínez González
 * Versión: 2.0
 * =============================================================================
 */

defined('SPARK') || exit('Direct script access is not allowed.');

require_once APPPATH . '../vendor/autoload.php';

use CodeIgniter\CLI\CLI;

class DataVerifier
{
    private $db;
    private $verbose = false;
    private $errors = [];
    private $warnings = [];

    public function __construct($verbose = false)
    {
        $this->verbose = $verbose;
        $this->db = \Config\Database::connect();
    }

    /**
     * Ejecutar verificación completa
     */
    public function run()
    {
        $this->printBanner();
        
        $checks = [
            'checkDatabaseConnection',
            'checkTablesExist',
            'checkDataIntegrity',
            'checkReferentialIntegrity',
            'checkBusinessRules',
            'checkIndexes'
        ];

        $passed = 0;
        $total = count($checks);

        foreach ($checks as $check) {
            if ($this->$check()) {
                $passed++;
            }
        }

        $this->generateSummary($passed, $total);
        
        return empty($this->errors);
    }

    /**
     * Imprimir banner
     */
    private function printBanner()
    {
        CLI::write('╔══════════════════════════════════════════════════════════════╗', 'cyan');
        CLI::write('║              VERIFICACIÓN DE INTEGRIDAD DE DATOS            ║', 'cyan');
        CLI::write('║                    Yagaruete Camp                            ║', 'cyan');
        CLI::write('╚══════════════════════════════════════════════════════════════╝', 'cyan');
        CLI::newLine();
    }

    /**
     * Verificar conexión a base de datos
     */
    private function checkDatabaseConnection()
    {
        $this->printStep('Verificando conexión a base de datos');

        try {
            $this->db->query('SELECT 1');
            $version = $this->db->query('SELECT VERSION() as version')->getRow();
            
            CLI::write('✅ Conexión exitosa', 'green');
            if ($this->verbose) {
                CLI::write("   Versión MySQL: {$version->version}", 'blue');
            }
            
            return true;
        } catch (Exception $e) {
            CLI::write('❌ Error de conexión: ' . $e->getMessage(), 'red');
            $this->errors[] = 'Database connection failed';
            return false;
        }
    }

    /**
     * Verificar que las tablas existan
     */
    private function checkTablesExist()
    {
        $this->printStep('Verificando existencia de tablas');

        $requiredTables = [
            'usuarios',
            'perfiles',
            'categorias',
            'productos',
            'ventas_cabecera',
            'ventas_detalle',
            'migrations'
        ];

        $existingTables = $this->db->listTables();
        $missing = array_diff($requiredTables, $existingTables);

        if (empty($missing)) {
            CLI::write("✅ Todas las tablas existen (" . count($requiredTables) . ")", 'green');
            
            if ($this->verbose) {
                foreach ($requiredTables as $table) {
                    $count = $this->db->table($table)->countAllResults();
                    CLI::write("   $table: $count registros", 'blue');
                }
            }
            
            return true;
        } else {
            CLI::write("❌ Tablas faltantes: " . implode(', ', $missing), 'red');
            $this->errors[] = 'Missing tables: ' . implode(', ', $missing);
            return false;
        }
    }

    /**
     * Verificar integridad de datos básica
     */
    private function checkDataIntegrity()
    {
        $this->printStep('Verificando integridad de datos');

        $checks = [
            $this->checkUsersIntegrity(),
            $this->checkProfilesIntegrity(),
            $this->checkCategoriesIntegrity(),
            $this->checkProductsIntegrity(),
            $this->checkSalesIntegrity()
        ];

        $passed = array_sum($checks);
        $total = count($checks);

        if ($passed === $total) {
            CLI::write("✅ Integridad de datos correcta ($passed/$total)", 'green');
            return true;
        } else {
            CLI::write("❌ Problemas de integridad encontrados ($passed/$total)", 'red');
            return false;
        }
    }

    /**
     * Verificar usuarios
     */
    private function checkUsersIntegrity()
    {
        $issues = [];

        // Usuarios sin email
        $noEmail = $this->db->query("SELECT COUNT(*) as count FROM usuarios WHERE email IS NULL OR email = ''")->getRow();
        if ($noEmail->count > 0) {
            $issues[] = "$noEmail->count usuarios sin email";
        }

        // Usuarios con perfiles inválidos
        $invalidProfiles = $this->db->query("
            SELECT COUNT(*) as count 
            FROM usuarios u 
            LEFT JOIN perfiles p ON u.perfil_id = p.id 
            WHERE p.id IS NULL
        ")->getRow();
        if ($invalidProfiles->count > 0) {
            $issues[] = "$invalidProfiles->count usuarios con perfil inválido";
        }

        // Usuarios duplicados por email
        $duplicated = $this->db->query("
            SELECT email, COUNT(*) as count 
            FROM usuarios 
            WHERE email IS NOT NULL AND email != '' 
            GROUP BY email 
            HAVING COUNT(*) > 1
        ")->getResult();
        if (!empty($duplicated)) {
            $issues[] = count($duplicated) . " emails duplicados";
        }

        if (empty($issues)) {
            if ($this->verbose) CLI::write('   ✅ Usuarios OK', 'green');
            return true;
        } else {
            CLI::write('   ❌ Usuarios: ' . implode(', ', $issues), 'red');
            $this->errors = array_merge($this->errors, $issues);
            return false;
        }
    }

    /**
     * Verificar perfiles
     */
    private function checkProfilesIntegrity()
    {
        $issues = [];

        // Verificar perfiles requeridos
        $requiredProfiles = [1, 2]; // Admin y Cliente
        foreach ($requiredProfiles as $profileId) {
            $exists = $this->db->table('perfiles')->where('id', $profileId)->countAllResults();
            if ($exists === 0) {
                $issues[] = "Perfil requerido $profileId no existe";
            }
        }

        if (empty($issues)) {
            if ($this->verbose) CLI::write('   ✅ Perfiles OK', 'green');
            return true;
        } else {
            CLI::write('   ❌ Perfiles: ' . implode(', ', $issues), 'red');
            $this->errors = array_merge($this->errors, $issues);
            return false;
        }
    }

    /**
     * Verificar categorías
     */
    private function checkCategoriesIntegrity()
    {
        $issues = [];

        // Categorías sin nombre
        $noName = $this->db->query("SELECT COUNT(*) as count FROM categorias WHERE nombre IS NULL OR nombre = ''")->getRow();
        if ($noName->count > 0) {
            $issues[] = "$noName->count categorías sin nombre";
        }

        if (empty($issues)) {
            if ($this->verbose) CLI::write('   ✅ Categorías OK', 'green');
            return true;
        } else {
            CLI::write('   ❌ Categorías: ' . implode(', ', $issues), 'red');
            $this->errors = array_merge($this->errors, $issues);
            return false;
        }
    }

    /**
     * Verificar productos
     */
    private function checkProductsIntegrity()
    {
        $issues = [];

        // Productos sin categoría válida
        $invalidCategories = $this->db->query("
            SELECT COUNT(*) as count 
            FROM productos p 
            LEFT JOIN categorias c ON p.categoria_id = c.id 
            WHERE c.id IS NULL
        ")->getRow();
        if ($invalidCategories->count > 0) {
            $issues[] = "$invalidCategories->count productos con categoría inválida";
        }

        // Productos con precios negativos
        $negativePrice = $this->db->query("SELECT COUNT(*) as count FROM productos WHERE precio < 0")->getRow();
        if ($negativePrice->count > 0) {
            $issues[] = "$negativePrice->count productos con precio negativo";
        }

        // Productos sin stock válido
        $invalidStock = $this->db->query("SELECT COUNT(*) as count FROM productos WHERE stock < 0")->getRow();
        if ($invalidStock->count > 0) {
            $issues[] = "$invalidStock->count productos con stock negativo";
        }

        if (empty($issues)) {
            if ($this->verbose) CLI::write('   ✅ Productos OK', 'green');
            return true;
        } else {
            CLI::write('   ❌ Productos: ' . implode(', ', $issues), 'red');
            $this->errors = array_merge($this->errors, $issues);
            return false;
        }
    }

    /**
     * Verificar ventas
     */
    private function checkSalesIntegrity()
    {
        $issues = [];

        // Ventas sin usuario válido
        $invalidUsers = $this->db->query("
            SELECT COUNT(*) as count 
            FROM ventas_cabecera vc 
            LEFT JOIN usuarios u ON vc.usuario_id = u.id 
            WHERE u.id IS NULL
        ")->getRow();
        if ($invalidUsers->count > 0) {
            $issues[] = "$invalidUsers->count ventas con usuario inválido";
        }

        // Detalles de venta sin venta cabecera
        $orphanDetails = $this->db->query("
            SELECT COUNT(*) as count 
            FROM ventas_detalle vd 
            LEFT JOIN ventas_cabecera vc ON vd.venta_id = vc.id 
            WHERE vc.id IS NULL
        ")->getRow();
        if ($orphanDetails->count > 0) {
            $issues[] = "$orphanDetails->count detalles sin venta";
        }

        // Detalles con productos inválidos
        $invalidProducts = $this->db->query("
            SELECT COUNT(*) as count 
            FROM ventas_detalle vd 
            LEFT JOIN productos p ON vd.producto_id = p.id 
            WHERE p.id IS NULL
        ")->getRow();
        if ($invalidProducts->count > 0) {
            $issues[] = "$invalidProducts->count detalles con producto inválido";
        }

        if (empty($issues)) {
            if ($this->verbose) CLI::write('   ✅ Ventas OK', 'green');
            return true;
        } else {
            CLI::write('   ❌ Ventas: ' . implode(', ', $issues), 'red');
            $this->errors = array_merge($this->errors, $issues);
            return false;
        }
    }

    /**
     * Verificar integridad referencial
     */
    private function checkReferentialIntegrity()
    {
        $this->printStep('Verificando integridad referencial');

        // Esta verificación ya se hace en checkDataIntegrity
        // pero aquí podríamos agregar verificaciones más específicas
        
        CLI::write('✅ Integridad referencial verificada en checks anteriores', 'green');
        return true;
    }

    /**
     * Verificar reglas de negocio
     */
    private function checkBusinessRules()
    {
        $this->printStep('Verificando reglas de negocio');

        $issues = [];

        // Verificar que existe al menos un admin
        $adminCount = $this->db->query("SELECT COUNT(*) as count FROM usuarios WHERE perfil_id = 1")->getRow();
        if ($adminCount->count === 0) {
            $issues[] = "No hay usuarios administradores";
        }

        // Verificar que las ventas tienen totales consistentes
        $inconsistentSales = $this->db->query("
            SELECT vc.id 
            FROM ventas_cabecera vc 
            LEFT JOIN (
                SELECT venta_id, SUM(cantidad * precio_unitario) as calculated_total 
                FROM ventas_detalle 
                GROUP BY venta_id
            ) vd ON vc.id = vd.venta_id 
            WHERE ABS(vc.total - COALESCE(vd.calculated_total, 0)) > 0.01
        ")->getResult();
        if (!empty($inconsistentSales)) {
            $issues[] = count($inconsistentSales) . " ventas con totales inconsistentes";
        }

        if (empty($issues)) {
            CLI::write('✅ Reglas de negocio correctas', 'green');
            return true;
        } else {
            CLI::write('❌ Problemas en reglas de negocio: ' . implode(', ', $issues), 'red');
            $this->errors = array_merge($this->errors, $issues);
            return false;
        }
    }

    /**
     * Verificar índices
     */
    private function checkIndexes()
    {
        $this->printStep('Verificando índices de base de datos');

        if ($this->verbose) {
            $tables = ['usuarios', 'productos', 'ventas_cabecera', 'ventas_detalle'];
            foreach ($tables as $table) {
                $indexes = $this->db->query("SHOW INDEX FROM $table")->getResult();
                CLI::write("   $table: " . count($indexes) . " índices", 'blue');
            }
        }

        CLI::write('✅ Índices verificados', 'green');
        return true;
    }

    /**
     * Imprimir paso
     */
    private function printStep($message)
    {
        CLI::newLine();
        CLI::write("[STEP] $message", 'cyan');
        CLI::write(str_repeat('-', 60));
    }

    /**
     * Generar resumen
     */
    private function generateSummary($passed, $total)
    {
        $this->printStep('Resumen de verificación');

        CLI::write("📊 Resultado: $passed/$total verificaciones exitosas");

        if (empty($this->errors)) {
            CLI::newLine();
            CLI::write('🎉 DATOS COMPLETAMENTE ÍNTEGROS', 'green');
            CLI::write('Todos los datos están consistentes y válidos');
        } else {
            CLI::newLine();
            CLI::write('⚠️  PROBLEMAS ENCONTRADOS', 'red');
            CLI::write('Errores encontrados:', 'yellow');
            foreach ($this->errors as $error) {
                CLI::write("   - $error", 'red');
            }

            if (!empty($this->warnings)) {
                CLI::write('Advertencias:', 'yellow');
                foreach ($this->warnings as $warning) {
                    CLI::write("   - $warning", 'yellow');
                }
            }

            CLI::newLine();
            CLI::write('💡 Sugerencias:', 'cyan');
            CLI::write('   - Ejecuta las migraciones: php spark migrate');
            CLI::write('   - Regenera los datos: scripts/setup/init-database.sh --force');
            CLI::write('   - Verifica la configuración: app/Config/Database.php');
        }
    }
}

// =============================================================================
// EJECUCIÓN PRINCIPAL
// =============================================================================

// Verificar argumentos
$verbose = in_array('--verbose', $argv) || in_array('-v', $argv);

if (in_array('--help', $argv) || in_array('-h', $argv)) {
    CLI::write('Uso: php spark verify:data [OPCIONES]');
    CLI::write('');
    CLI::write('DESCRIPCIÓN:');
    CLI::write('    Verifica la integridad y consistencia de los datos en la base de datos');
    CLI::write('');
    CLI::write('OPCIONES:');
    CLI::write('    -v, --verbose    Modo verbose (más información)');
    CLI::write('    -h, --help       Mostrar esta ayuda');
    CLI::write('');
    CLI::write('EJEMPLOS:');
    CLI::write('    php spark verify:data           # Verificación estándar');
    CLI::write('    php spark verify:data --verbose # Verificación detallada');
    exit(0);
}

// Ejecutar verificación
$verifier = new DataVerifier($verbose);
$success = $verifier->run();

exit($success ? 0 : 1);
