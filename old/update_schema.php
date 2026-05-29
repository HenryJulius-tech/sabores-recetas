<?php
require_once 'config.php';

try {
    $queries = [
        "ALTER TABLE movimientos ADD COLUMN proveedor_beneficiario VARCHAR(100) DEFAULT '' AFTER category",
        "ALTER TABLE movimientos ADD COLUMN forma_pago VARCHAR(50) DEFAULT '' AFTER amount",
        "ALTER TABLE movimientos ADD COLUMN soporte_url VARCHAR(200) DEFAULT '' AFTER forma_pago",
        "ALTER TABLE movimientos ADD COLUMN observaciones TEXT AFTER soporte_url",
        "ALTER TABLE movimientos ADD COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'approved' AFTER observaciones",
        "ALTER TABLE movimientos ADD COLUMN compra_id INT NULL AFTER id",
        "ALTER TABLE compras ADD COLUMN forma_pago VARCHAR(50) DEFAULT '' AFTER total"
    ];

    foreach ($queries as $q) {
        try {
            $pdo->exec($q);
        } catch (PDOException $e) {
            // Ignorar errores si la columna ya existe
            error_log("Columna ya existe o error menor: " . $e->getMessage());
        }
    }

    echo "La base de datos fue actualizada correctamente con los nuevos campos. <br><a href='index.php'>Volver al inicio</a>";
} catch (Exception $e) {
    echo "Error general: " . $e->getMessage();
}
?>
