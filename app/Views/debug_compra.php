<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Depuración de Compra #<?= $cabecera['id'] ?? 'N/A' ?></title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        h1, h2, h3 { color: #333; }
        .container { 
            max-width: 1200px; 
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        pre {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 3px;
            padding: 10px;
            overflow: auto;
        }
        .product {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .product-image {
            max-width: 200px;
            max-height: 150px;
            border: 1px solid #eee;
        }
        .product-info {
            margin-left: 15px;
        }
        .flex {
            display: flex;
            align-items: flex-start;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Depuración de Compra #<?= $cabecera['id'] ?? 'N/A' ?></h1>
        </div>
        
        <h2>1. Datos de Cabecera</h2>
        <table>
            <tr>
                <th>ID</th>
                <td><?= $cabecera['id'] ?? 'N/A' ?></td>
            </tr>
            <tr>
                <th>Fecha</th>
                <td><?= $cabecera['fecha'] ?? 'N/A' ?> (Formato en BD)</td>
            </tr>
            <tr>
                <th>Fecha formateada</th>
                <td><?= isset($cabecera['fecha']) ? date('d/m/Y', strtotime($cabecera['fecha'])) : 'N/A' ?></td>
            </tr>
            <tr>
                <th>Total</th>
                <td>$<?= number_format($cabecera['total_venta'] ?? 0, 2, ',', '.') ?></td>
            </tr>
            <tr>
                <th>Usuario ID</th>
                <td><?= $cabecera['usuario_id'] ?? 'N/A' ?></td>
            </tr>
        </table>
        
        <h2>2. Detalles de la Compra (<?= count($detalles) ?> productos)</h2>
        
        <?php if (empty($detalles)): ?>
            <p>No se encontraron detalles para esta compra.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($detalles as $detalle): ?>
                        <tr>
                            <td><?= $detalle['id'] ?></td>
                            <td><?= $detalle['nombre_prod'] ?></td>
                            <td><?= $detalle['cantidad'] ?></td>
                            <td>$<?= number_format($detalle['precio'], 2, ',', '.') ?></td>
                            <td>$<?= number_format($detalle['precio'] * $detalle['cantidad'], 2, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        
        <h2>3. Información Completa de Productos</h2>
        
        <?php foreach ($detalles as $index => $detalle): ?>
            <div class="product">
                <h3><?= ($index + 1) ?>. <?= $detalle['nombre_prod'] ?></h3>
                <div class="flex">
                    <div>
                        <?php 
                        $imagen = $detalle['imagen'];
                        $ruta_imagen = !empty($imagen) ? $imagen : 'assets/img/producto-sin-imagen.jpg';
                        
                        // Si no comienza con assets/, agregarlo
                        if (!empty($imagen) && strpos($imagen, 'assets/') !== 0) {
                            $ruta_imagen = 'assets/' . $imagen;
                        }
                        
                        // Verificar si el archivo existe
                        $existe = file_exists(FCPATH . $ruta_imagen);
                        ?>
                        
                        <p><strong>Ruta en BD:</strong> <?= $detalle['imagen'] ?></p>
                        <p><strong>Ruta completa:</strong> <?= $ruta_imagen ?></p>
                        <p><strong>¿Existe?</strong> <?= $existe ? 'SÍ' : 'NO' ?></p>
                        
                        <img src="<?= base_url($ruta_imagen) ?>" class="product-image" alt="<?= $detalle['nombre_prod'] ?>">
                    </div>
                    <div class="product-info">
                        <p><strong>ID Producto:</strong> <?= $detalle['producto_id'] ?></p>
                        <p><strong>Descripción:</strong> <?= $detalle['descripcion'] ?></p>
                        <p><strong>Precio unitario:</strong> $<?= number_format($detalle['precio'], 2, ',', '.') ?></p>
                        <p><strong>Cantidad:</strong> <?= $detalle['cantidad'] ?></p>
                        <p><strong>Subtotal:</strong> $<?= number_format($detalle['precio'] * $detalle['cantidad'], 2, ',', '.') ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        
        <p><a href="<?= base_url('mis-compras') ?>">Volver a mis compras</a></p>
    </div>
</body>
</html>
