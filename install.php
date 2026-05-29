<?php
/**
 * Instalador de Finca Bananera v2
 * Ejecutar UNA SOLA VEZ para configurar la base de datos.
 */
$steps = [];
try {
    $config = require __DIR__ . '/config/database.php';
    $dsn = "mysql:host={$config['host']};port={$config['port']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    $steps[] = ['ok' => true, 'msg' => 'ConexiÃ³n a MySQL exitosa'];

    $schema = file_get_contents(__DIR__ . '/database/schema.sql');
    $pdo->exec("CREATE DATABASE IF NOT EXISTS {$config['dbname']} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE {$config['dbname']}");
    $pdo->exec($schema);
    $steps[] = ['ok' => true, 'msg' => 'Base de datos creada / tablas listas'];

    $stmt = $pdo->query("SELECT id, username FROM usuarios WHERE username='admin'");
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($admin) {
        $steps[] = ['ok' => true, 'msg' => "Admin existente (ID: {$admin['id']})"];
    } else {
        $hash = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO usuarios (username, email, password_hash, role) VALUES (?,?,?,?)")
            ->execute(['admin', 'admin@fincalakaren.com', $hash, 'admin']);
        $steps[] = ['ok' => true, 'msg' => 'Admin creado. Usuario: admin / ContraseÃ±a: admin123'];
    }
} catch (Exception $e) {
    $steps[] = ['ok' => false, 'msg' => 'Error: ' . $e->getMessage()];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalador - Finca Bananera</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 text-center">
                    <i class="bi bi-tree-fill text-success display-4"></i>
                    <h3 class="mt-2">InstalaciÃ³n - Finca Bananera v2</h3>
                    <hr>
                    <?php foreach ($steps as $s): ?>
                    <div class="alert alert-<?= $s['ok'] ? 'success' : 'danger' ?> py-2"><?= $s['msg'] ?></div>
                    <?php endforeach; ?>
                    <hr>
                    <p class="text-muted small">Elimina este archivo despuÃ©s de la instalaciÃ³n.</p>
                    <a href="index.php/login" class="btn btn-success">Ir al Sistema</a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
