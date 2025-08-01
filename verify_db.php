<?php

echo "=== VERIFICACIÓN DE MIGRACIONES Y SEEDERS ===\n";

// Cargar CodeIgniter
define('APPPATH', __DIR__ . '/app/');
define('SYSTEMPATH', __DIR__ . '/system/');
define('ROOTPATH', __DIR__ . '/');
define('FCPATH', __DIR__ . '/public/');
define('WRITEPATH', __DIR__ . '/writable/');

require_once SYSTEMPATH . 'bootstrap.php';

$app = \Config\Services::codeigniter();
$app->initialize();

echo "✅ CodeIgniter cargado\n";

// Conectar a la base de datos
$db = \Config\Database::connect();
echo "✅ Conectado a la BD: " . $db->getDatabase() . "\n\n";

// Verificar tabla de migraciones
echo "🔍 VERIFICANDO TABLA DE MIGRACIONES:\n";
echo str_repeat("=", 50) . "\n";

if ($db->tableExists('migrations')) {
    echo "✅ Tabla 'migrations' existe\n";
    
    $migrations = $db->table('migrations')->get()->getResultArray();
    echo "📋 Migraciones registradas: " . count($migrations) . "\n";
    
    foreach($migrations as $i => $migration) {
        echo ($i + 1) . ". " . $migration['version'] . " - " . $migration['class'] . " (" . $migration['batch'] . ")\n";
    }
} else {
    echo "❌ Tabla 'migrations' NO existe\n";
}

echo "\n" . str_repeat("=", 50) . "\n";

// Verificar archivos de migración
echo "📁 ARCHIVOS DE MIGRACIÓN DISPONIBLES:\n";
echo str_repeat("=", 50) . "\n";

$migrationPath = APPPATH . 'Database/Migrations/';
if (is_dir($migrationPath)) {
    $files = scandir($migrationPath);
    $migrationFiles = array_filter($files, function($file) {
        return pathinfo($file, PATHINFO_EXTENSION) === 'php' && $file !== 'index.html';
    });
    
    echo "📋 Archivos encontrados: " . count($migrationFiles) . "\n";
    foreach($migrationFiles as $i => $file) {
        echo ($i + 1) . ". " . $file . "\n";
    }
} else {
    echo "❌ Directorio de migraciones NO existe\n";
}

echo "\n" . str_repeat("=", 50) . "\n";

// Verificar archivos de seeders
echo "🌱 ARCHIVOS DE SEEDERS DISPONIBLES:\n";
echo str_repeat("=", 50) . "\n";

$seederPath = APPPATH . 'Database/Seeds/';
if (is_dir($seederPath)) {
    $files = scandir($seederPath);
    $seederFiles = array_filter($files, function($file) {
        return pathinfo($file, PATHINFO_EXTENSION) === 'php' && $file !== 'index.html';
    });
    
    echo "📋 Archivos encontrados: " . count($seederFiles) . "\n";
    foreach($seederFiles as $i => $file) {
        echo ($i + 1) . ". " . $file . "\n";
    }
} else {
    echo "❌ Directorio de seeders NO existe\n";
}

echo "\n" . str_repeat("=", 50) . "\n";

// Verificar todas las tablas
echo "🗄️ TODAS LAS TABLAS EN LA BD:\n";
echo str_repeat("=", 50) . "\n";

$tables = $db->listTables();
echo "📋 Tablas encontradas: " . count($tables) . "\n";
foreach($tables as $i => $table) {
    $count = $db->table($table)->countAllResults();
    echo ($i + 1) . ". " . $table . " (" . $count . " registros)\n";
}

echo "\n✅ Verificación completada!\n";

?>
