<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

$error = '';
$success = '';

if ($_POST) {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if (!$nombre || !$email || !$password) {
        $error = 'Todos los campos son obligatorios';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email no válido';
    } elseif ($password !== $confirm) {
        $error = 'Las contraseñas no coinciden';
    } elseif (strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Este email ya está registrado';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, role) VALUES (?, ?, ?, 'estudiante')");
            $stmt->execute([$nombre, $email, $hash]);
            $success = 'Registro exitoso. Ahora puedes iniciar sesión.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Sabores & Recetas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); min-height: 100vh; display: flex; align-items: center; position: relative; font-family: 'Inter', sans-serif; }
        body::after { content: ''; position: absolute; top: 0; right: 0; width: 40%; height: 100%; background: linear-gradient(135deg, rgba(233,30,99,0.2), transparent); clip-path: polygon(20% 0%, 100% 0, 100% 100%, 0% 100%); z-index: 0; }
        .container { position: relative; z-index: 10; }
        .register-card { background: #fff; border-radius: 20px; padding: 40px; box-shadow: 0 20px 60px rgba(0,0,0,0.2); max-width: 500px; width: 100%; margin: 0 auto; border: 1px solid rgba(255,255,255,0.1); }
        .register-logo { text-align: center; margin-bottom: 30px; }
        .register-logo i { font-size: 48px; color: #e91e63; }
        .register-logo h1 { font-size: 28px; font-weight: 800; color: #1a1a2e; margin-top: 10px; }
        .form-control { border-radius: 12px; padding: 12px 16px; border: 2px solid #e9ecef; font-size: 15px; }
        .form-control:focus { border-color: #e91e63; box-shadow: 0 0 0 4px rgba(233,30,99,0.1); }
        .btn-register { background: linear-gradient(135deg, #e91e63, #c2185b); border: none; border-radius: 12px; padding: 14px; font-weight: 600; font-size: 16px; width: 100%; color: #fff; transition: all 0.3s ease; }
        .btn-register:hover { transform: translateY(-3px); box-shadow: 0 10px 25px rgba(233,30,99,0.3); }
        .login-link { text-align: center; margin-top: 24px; font-size: 15px; color: #6c757d; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="register-card">
            <div class="register-logo">
                <i class="bi bi-person-plus-fill"></i>
                <h1>Crear Cuenta</h1>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3"><i class="bi bi-exclamation-circle me-2"></i><?= e($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success border-0 shadow-sm rounded-3 text-center">
                    <i class="bi bi-check-circle d-block mb-2" style="font-size: 48px; color: #198754;"></i>
                    <h5 class="fw-bold text-dark">¡Excelente!</h5>
                    <p class="mb-3"><?= e($success) ?></p>
                    <a href="login.php" class="btn btn-success w-100 rounded-pill fw-bold">Ir a Iniciar sesión</a>
                </div>
            <?php else: ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Nombre completo</label>
                        <input type="text" name="nombre" class="form-control" value="<?= e(old('nombre')) ?>" required placeholder="Ej. Juan Pérez">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Correo electrónico</label>
                        <input type="email" name="email" class="form-control" value="<?= e(old('email')) ?>" required placeholder="tu@email.com">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-dark">Contraseña</label>
                            <input type="password" name="password" class="form-control" required placeholder="••••••••">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold text-dark">Confirmar</label>
                            <input type="password" name="confirm" class="form-control" required placeholder="••••••••">
                        </div>
                    </div>
                    <button type="submit" class="btn-register"><i class="bi bi-person-plus me-2"></i>Registrarme ahora</button>
                </form>
                <div class="login-link">
                    ¿Ya tienes cuenta? <a href="login.php" style="color:#e91e63; font-weight:700; text-decoration:none;">Inicia sesión</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
