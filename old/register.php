<?php
require_once 'config.php';

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if (!$username || !$email || !$password) {
        set_flash_message('error', 'Por favor complete todos los campos obligatorios.');
    } elseif ($password !== $confirm_password) {
        set_flash_message('error', 'Las contraseñas no coinciden.');
    } else {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            set_flash_message('error', 'El nombre de usuario o el correo electrónico ya están registrados.');
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO usuarios (username, email, password_hash, role, address, phone) VALUES (?, ?, ?, 'client', ?, ?)");
            $stmt->execute([$username, $email, $hash, $address, $phone]);
            set_flash_message('success', 'Registro completado con éxito. Ahora puedes iniciar sesión.');
            header('Location: login.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finca La Karen - Registrarse</title>
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
                <p>Crea una cuenta como Cliente para acceder al catálogo y comprar</p>
            </div>
            
            <form action="register.php" method="POST" id="register-form">
                <div class="form-group">
                    <label for="username">Nombre de Usuario</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Ej. maricela23" required minlength="3" autocomplete="username">
                </div>
                
                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Ej. usuario@ejemplo.com" required autocomplete="email">
                </div>

                <div class="form-group">
                    <label for="address">Dirección de Casa</label>
                    <input type="text" id="address" name="address" class="form-control" placeholder="Ej. Calle 123 #45-67" required autocomplete="address">
                </div>

                <div class="form-group">
                    <label for="phone">Número de Teléfono</label>
                    <input type="tel" id="phone" name="phone" class="form-control" placeholder="Ej. +57 300 1234567" required autocomplete="tel">
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required minlength="6" autocomplete="new-password">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirmar Contraseña</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="••••••••" required autocomplete="new-password">
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <i data-lucide="user-plus"></i> Registrarse
                </button>
            </form>
            
            <div class="auth-footer">
                ¿Ya tienes una cuenta? <a href="login.php">Inicia sesión</a>
            </div>
        </div>
    </div>

    <script src="js/main.js"></script>
    <script>
        lucide.createIcons();
        
        // Validación frontend del matching de passwords
        const form = document.getElementById('register-form');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        
        if (form && password && confirmPassword) {
            form.addEventListener('submit', (e) => {
                if (password.value !== confirmPassword.value) {
                    e.preventDefault();
                    window.showToast('Las contraseñas no coinciden.', 'error');
                }
            });
        }
    </script>
</body>
</html>
