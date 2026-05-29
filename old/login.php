<?php
require_once 'config.php';

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        set_flash_message('success', "¡Bienvenido de nuevo, {$user['username']}!");
        
        if ($user['role'] == 'client') {
            header('Location: tienda.php');
        } elseif ($user['role'] == 'worker') {
            header('Location: movimientos.php');
        } else {
            header('Location: index.php');
        }
        exit;
    } else {
        set_flash_message('error', 'Usuario o contraseña incorrectos.');
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finca La Karen - Iniciar Sesión</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>

    <!-- Contenedor para Mensajes Flash -->
    <?php 
    if (isset($_SESSION['flash'])) {
        foreach ($_SESSION['flash'] as $category => $message) {
            echo '<div class="flash-data" data-message="'.htmlspecialchars($message).'" data-category="'.htmlspecialchars($category).'"></div>';
        }
        unset($_SESSION['flash']);
    }
    ?>

    <div class="auth-wrapper">
        <div class="auth-container">
            <div class="auth-logo">
                <h2><i data-lucide="sprout"></i> Finca <span>La Karen</span></h2>
                <p>Ingresa tus credenciales para acceder al sistema de gestión</p>
            </div>
            
            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="username">Nombre de Usuario</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Ej. admin o juan_perez" required autocomplete="username">
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required autocomplete="current-password">
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <i data-lucide="log-in"></i> Iniciar Sesión
                </button>
            </form>
            
            <div class="auth-footer">
                ¿No tienes una cuenta? <a href="register.php">Regístrate aquí</a>
            </div>
        </div>
    </div>

    <script src="js/main.js"></script>
    <script>lucide.createIcons();</script>
</body>
</html>
