<?php
/**
 * Instalador de Sabores & Recetas
 * Ejecutar UNA SOLA VEZ para configurar la base de datos.
 */
$steps = [];
$hasError = false;

// ── Verificar requisitos ──
$reqs = [];
$reqs[] = ['ok' => version_compare(PHP_VERSION, '7.4', '>='), 'msg' => 'PHP >= 7.4 (tienes ' . PHP_VERSION . ')'];
$reqs[] = ['ok' => extension_loaded('pdo'), 'msg' => 'Extensión PDO'];
$reqs[] = ['ok' => extension_loaded('pdo_mysql'), 'msg' => 'Extensión PDO MySQL'];
$reqs[] = ['ok' => extension_loaded('mbstring'), 'msg' => 'Extensión mbstring'];
$reqs[] = ['ok' => extension_loaded('gd') || extension_loaded('imagick'), 'msg' => 'Extensión GD o Imagick'];
$reqs[] = ['ok' => is_writable(__DIR__ . '/config'), 'msg' => 'Directorio config/ escribible'];
$reqs[] = ['ok' => is_writable(__DIR__ . '/public/uploads') || @chmod(__DIR__ . '/public/uploads', 0777), 'msg' => 'Directorio public/uploads/ escribible'];

foreach ($reqs as $r) {
    if (!$r['ok']) $hasError = true;
}

if (!$hasError) {
    try {
        // Crear .env si no existe
        $envFile = __DIR__ . '/config/.env';
        $envExample = __DIR__ . '/config/.env.example';
        if (!file_exists($envFile) && file_exists($envExample)) {
            copy($envExample, $envFile);
            $steps[] = ['ok' => true, 'msg' => 'Archivo .env creado desde .env.example'];
        } elseif (file_exists($envFile)) {
            $steps[] = ['ok' => true, 'msg' => 'Archivo .env ya existe'];
        }

        $config = require __DIR__ . '/config/database.php';
        $dsn = "mysql:host={$config['host']};port={$config['port']};charset={$config['charset']}";
        $pdo = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $steps[] = ['ok' => true, 'msg' => 'Conexión a MySQL exitosa ('.$config['host'].')'];

        $schema = file_get_contents(__DIR__ . '/database/schema.sql');
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$config['dbname']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `{$config['dbname']}`");
        $statements = array_filter(array_map('trim', explode(';', $schema)));
        $count = 0;
        foreach ($statements as $stmt) {
            if (!empty($stmt) && stripos($stmt, 'CREATE DATABASE') === false && stripos($stmt, 'USE ') === false) {
                try { $pdo->exec($stmt); $count++; } catch (Exception $e) {
                    if (stripos($e->getMessage(), 'Duplicate') === false) throw $e;
                }
            }
        }
        $steps[] = ['ok' => true, 'msg' => "Base de datos '{$config['dbname']}' lista ({$count} consultas ejecutadas)"];

        // Admin
        $admin = $pdo->query("SELECT id, username FROM usuarios WHERE username='admin'")->fetch();
        if ($admin) {
            $steps[] = ['ok' => true, 'msg' => "Admin existente (ID: {$admin['id']})"];
        } else {
            $hash = password_hash('admin123', PASSWORD_DEFAULT);
            $pdo->prepare("INSERT INTO usuarios (username, email, password_hash, role) VALUES (?,?,?,?)")
                ->execute(['admin', 'admin@saboresyrecetas.com', $hash, 'admin']);
            $steps[] = ['ok' => true, 'msg' => 'Admin creado. Usuario: admin / Contraseña: admin123'];
        }

        // Cliente demo
        $cliente = $pdo->query("SELECT id FROM usuarios WHERE username='cliente'")->fetch();
        if (!$cliente) {
            $hash = password_hash('cliente123', PASSWORD_DEFAULT);
            $pdo->prepare("INSERT INTO usuarios (username, email, password_hash, role) VALUES (?,?,?,?)")
                ->execute(['cliente', 'cliente@saboresyrecetas.com', $hash, 'client']);
            $steps[] = ['ok' => true, 'msg' => 'Cliente demo creado. Usuario: cliente / Contraseña: cliente123'];
        } else {
            $steps[] = ['ok' => true, 'msg' => 'Cliente demo existente'];
        }
    } catch (Exception $e) {
        $steps[] = ['ok' => false, 'msg' => 'Error: ' . $e->getMessage()];
        $hasError = true;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalador - Sabores & Recetas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #1a0f0a 0%, #2C1810 30%, #5C2E1E 60%, #8B3A2A 100%); min-height: 100vh; display: flex; align-items: center; font-family: 'Poppins', sans-serif; }
        .card-install { border: none; border-radius: 16px; box-shadow: 0 24px 80px rgba(0,0,0,0.3); overflow: hidden; }
        .card-install .card-header { background: linear-gradient(135deg, #E63946, #C1121F); color: #fff; text-align: center; padding: 28px; border: none; }
        .card-install .card-header i { font-size: 2.5rem; }
        .card-install .card-body { padding: 28px; background: #fff; }
        .alert-install { border-radius: 8px; padding: 10px 14px; font-size: 0.85rem; font-weight: 500; margin-bottom: 8px; border-left: 4px solid; border-top: none; border-right: none; border-bottom: none; }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="card card-install">
                <div class="card-header">
                    <i class="bi bi-book"></i>
                    <h4 class="mt-2 fw-bold">Sabores & Recetas</h4>
                    <p class="mb-0" style="opacity:0.8;font-size:0.9rem;">Instalación del sistema</p>
                </div>
                <div class="card-body">
                    <h6 class="fw-bold mb-3" style="color:#E63946;"><i class="bi bi-check2-circle me-1"></i>Requisitos del sistema</h6>
                    <?php foreach ($reqs as $r): ?>
                    <div class="alert-install <?= $r['ok'] ? 'alert-success' : 'alert-danger' ?>" style="background:<?= $r['ok'] ? '#E8F5E9' : '#FFEBEE' ?>;border-left-color:<?= $r['ok'] ? '#4CAF50' : '#E63946' ?>;color:<?= $r['ok'] ? '#2E7D32' : '#C62828' ?>;">
                        <i class="bi bi-<?= $r['ok'] ? 'check-circle' : 'exclamation-circle' ?> me-1"></i><?= $r['msg'] ?>
                    </div>
                    <?php endforeach; ?>

                    <?php if (!empty($steps)): ?>
                    <hr class="my-3">
                    <h6 class="fw-bold mb-3" style="color:#E63946;"><i class="bi bi-gear me-1"></i>Instalación</h6>
                    <?php foreach ($steps as $s): ?>
                    <div class="alert-install <?= $s['ok'] ? 'alert-success' : 'alert-danger' ?>" style="background:<?= $s['ok'] ? '#E8F5E9' : '#FFEBEE' ?>;border-left-color:<?= $s['ok'] ? '#4CAF50' : '#E63946' ?>;color:<?= $s['ok'] ? '#2E7D32' : '#C62828' ?>;">
                        <i class="bi bi-<?= $s['ok'] ? 'check-circle' : 'exclamation-circle' ?> me-1"></i><?= $s['msg'] ?>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>

                    <hr class="my-3">
                    <div class="d-flex gap-2">
                        <a href="<?= $hasError ? '#' : 'index.php/login' ?>" class="btn fw-bold flex-fill <?= $hasError ? 'btn-secondary disabled' : 'btn-danger' ?>" style="border-radius:8px;padding:12px;">
                            <i class="bi bi-box-arrow-in-right me-1"></i><?= $hasError ? 'Corrige los errores' : 'Ir al Sistema' ?>
                        </a>
                    </div>
                    <p class="text-muted small text-center mt-3 mb-0">Elimina este archivo (install.php) después de la instalación.</p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
