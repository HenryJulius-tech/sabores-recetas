<?php
require_once 'config.php';
require_role(['admin', 'worker', 'client']);

$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Manual de Usuario</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<div class="content">
    <h1>Manual de Usuario</h1>
    <?php if ($role === 'admin'): ?>
        <p>Como <strong>Administrador</strong> puedes gestionar usuarios, aprobar movimientos y generar facturas.</p>
        <ul>
            <li>Gestión de usuarios: crear, editar y eliminar.</li>
            <li>Aprobación de gastos/ingresos pendientes.</li>
            <li>Revisión de pagos de clientes y generación de facturas.</li>
        </ul>
    <?php elseif ($role === 'worker'): ?>
        <p>Como <strong>Trabajador</strong> puedes registrar gastos e ingresos, pero no ver métricas ni finanzas.</p>
        <ul>
            <li>Registrar movimientos (pendientes de aprobación).</li>
            <li>Consultar tu historial de compras.</li>
        </ul>
    <?php else: ?>
        <p>Como <strong>Cliente</strong> puedes realizar compras y subir comprobantes de pago.</p>
        <ul>
            <li>Navegar la tienda y crear una compra.</li>
            <li>Subir comprobante de pago y seleccionar método de pago.</li>
            <li>Ver factura una vez el pago sea aprobado.</li>
        </ul>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
</body>
</html>
