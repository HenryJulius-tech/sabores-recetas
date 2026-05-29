<?php
require_once 'config.php';
try {
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    
    // Primero intentamos actualizar el usuario si ya existe
    $stmt = $pdo->prepare("UPDATE usuarios SET password_hash = ? WHERE username = 'admin'");
    $stmt->execute([$hash]);
    
    if ($stmt->rowCount() > 0) {
        echo "Contraseña del usuario 'admin' reseteada con éxito a 'admin123'. <br><a href='login.php'>Ir al Login</a>";
    } else {
        // Si no existe, lo insertamos
        $stmt = $pdo->prepare("INSERT INTO usuarios (username, email, password_hash, role) VALUES ('admin', 'admin@fincalakaren.com', ?, 'admin')");
        $stmt->execute([$hash]);
        echo "Usuario 'admin' creado con éxito con contraseña 'admin123'. <br><a href='login.php'>Ir al Login</a>";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
