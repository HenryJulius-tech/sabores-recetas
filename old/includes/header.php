<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Finca La Karen - Dashboard' ?></title>
    <!-- CSS Estilos Comunes -->
    <link rel="stylesheet" href="css/style.css">
    <!-- Iconos Lucide -->
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

    <div class="layout-wrapper">
