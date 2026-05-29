<?php
require_once 'config.php';
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    $compra_id = $_POST['compra_id'] ?? 0;
    
    $stmt = $pdo->prepare("SELECT * FROM compras WHERE id = ?");
    $stmt->execute([$compra_id]);
    $compra = $stmt->fetch();
    
    if (!$compra) {
        set_flash_message('error', 'Compra no encontrada.');
        header('Location: admin_compras.php');
        exit;
    }
    
    if ($compra['status'] == 'approved') {
        set_flash_message('warning', 'Esta compra ya estaba aprobada.');
        header('Location: admin_compras.php');
        exit;
    }
    
    if ($action == 'approve') {
        // 1. Validar stock
        $stmt = $pdo->prepare("SELECT d.*, p.name as product_name, p.stock FROM detalle_compras d JOIN productos p ON d.product_id = p.id WHERE d.compra_id = ?");
        $stmt->execute([$compra_id]);
        $detalles = $stmt->fetchAll();
        
        foreach ($detalles as $d) {
            if ($d['stock'] < $d['quantity']) {
                set_flash_message('error', "Error al aprobar: Stock insuficiente para \"{$d['product_name']}\". Disponible: {$d['stock']}");
                header('Location: admin_compras.php');
                exit;
            }
        }
        
        try {
            $pdo->beginTransaction();
            
            // 2. Descontar stock
            foreach ($detalles as $d) {
                $stmt = $pdo->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");
                $stmt->execute([$d['quantity'], $d['product_id']]);
            }
            
            // 3. Marcar compra y pago como aprobados
            $stmt = $pdo->prepare("UPDATE compras SET status = 'approved' WHERE id = ?");
            $stmt->execute([$compra_id]);
            
            $stmt = $pdo->prepare("UPDATE pagos SET status = 'approved' WHERE compra_id = ?");
            $stmt->execute([$compra_id]);
            
            // 4. Ingreso financiero
            $stmt = $pdo->prepare("SELECT username FROM usuarios WHERE id = ?");
            $stmt->execute([$compra['user_id']]);
            $cliente_username = $stmt->fetchColumn();
            
            $desc = "Venta de Productos (Compra #{$compra_id}) - Cliente: $cliente_username";
            $stmt = $pdo->prepare("INSERT INTO movimientos (type, amount, description, category, date, created_by_id) VALUES ('ingreso', ?, ?, 'Ventas', CURDATE(), ?)");
            $stmt->execute([$compra['total'], $desc, $_SESSION['user_id']]);
            
            $pdo->commit();
            set_flash_message('success', "Compra #{$compra_id} aprobada con éxito. Stock actualizado e ingreso registrado en finanzas.");
        } catch (Exception $e) {
            $pdo->rollBack();
            set_flash_message('error', 'Error interno al procesar la aprobación.');
        }
    } elseif ($action == 'reject') {
        $stmt = $pdo->prepare("UPDATE compras SET status = 'rejected' WHERE id = ?");
        $stmt->execute([$compra_id]);
        $stmt = $pdo->prepare("UPDATE pagos SET status = 'rejected' WHERE compra_id = ?");
        $stmt->execute([$compra_id]);
        set_flash_message('success', "Compra #{$compra_id} rechazada correctamente.");
    }
    
    header('Location: admin_compras.php');
    exit;
}

// GET all purchases
$stmt = $pdo->query("SELECT c.*, u.username as cliente_username FROM compras c JOIN usuarios u ON c.user_id = u.id ORDER BY c.created_at DESC");
$compras = $stmt->fetchAll();

foreach ($compras as &$c) {
    $stmt = $pdo->prepare("SELECT d.*, p.name as product_name FROM detalle_compras d JOIN productos p ON d.product_id = p.id WHERE d.compra_id = ?");
    $stmt->execute([$c['id']]);
    $c['detalles'] = $stmt->fetchAll();
    
    $stmt = $pdo->prepare("SELECT * FROM pagos WHERE compra_id = ?");
    $stmt->execute([$c['id']]);
    $c['pago'] = $stmt->fetch();
}
unset($c);

$page_title = 'Bandeja de Pagos';
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="panel">
    <div class="panel-header">
        <div class="panel-title">
            <i data-lucide="credit-card" style="color: var(--primary);"></i>
            <span>Bandeja de Pagos y Compras</span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Compra</th>
                    <th>Cliente</th>
                    <th>Detalles</th>
                    <th>Total</th>
                    <th>Comprobante</th>
                    <th>Estado</th>
                    <th style="text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($compras): ?>
                    <?php foreach ($compras as $c): ?>
                    <tr>
                        <td style="font-weight: 700;">#<?= $c['id'] ?><br><small style="color: var(--text-muted); font-weight: normal;"><?= date('Y-m-d H:i', strtotime($c['created_at'])) ?></small></td>
                        <td style="font-weight: 600;"><?= htmlspecialchars($c['cliente_username']) ?></td>
                        <td>
                            <ul style="list-style: none; padding: 0; font-size: 13px;">
                                <?php foreach ($c['detalles'] as $det): ?>
                                <li><strong><?= $det['quantity'] ?></strong>x <?= htmlspecialchars($det['product_name']) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </td>
                        <td style="font-weight: 700; color: var(--primary);"><?= format_cop($c['total']) ?></td>
                        <td>
                            <?php if ($c['pago'] && $c['pago']['proof_image_url']): ?>
                                <a href="uploads/<?= htmlspecialchars($c['pago']['proof_image_url']) ?>" target="_blank">
                                    <img src="uploads/<?= htmlspecialchars($c['pago']['proof_image_url']) ?>" style="width: 48px; height: 48px; object-fit: cover; border-radius: 6px; border: 2px solid var(--border-color);">
                                </a>
                            <?php else: ?>
                                <span style="font-size: 12px; color: var(--text-muted);">Sin Comprobante</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($c['status'] == 'pending'): ?>
                                <span class="status-badge pending">Pendiente</span>
                            <?php elseif ($c['status'] == 'approved'): ?>
                                <span class="status-badge approved">Aprobado</span>
                            <?php else: ?>
                                <span class="status-badge rejected">Rechazado</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: right;">
                            <?php if ($c['status'] != 'approved'): ?>
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    <form action="admin_compras.php" method="POST" onsubmit="return confirm('¿Aprobar esta compra? Esto descontará el stock y registrará el ingreso.');">
                                        <input type="hidden" name="action" value="approve">
                                        <input type="hidden" name="compra_id" value="<?= $c['id'] ?>">
                                        <button type="submit" class="btn btn-outline" style="padding: 6px 10px; color: var(--success); border-color: rgba(16, 185, 129, 0.2);" title="Aprobar Pago">
                                            <i data-lucide="check" style="width: 16px; height: 16px;"></i>
                                        </button>
                                    </form>
                                    
                                    <?php if ($c['status'] != 'rejected'): ?>
                                    <form action="admin_compras.php" method="POST" onsubmit="return confirm('¿Rechazar esta compra?');">
                                        <input type="hidden" name="action" value="reject">
                                        <input type="hidden" name="compra_id" value="<?= $c['id'] ?>">
                                        <button type="submit" class="btn btn-outline" style="padding: 6px 10px; color: var(--danger); border-color: rgba(239, 68, 68, 0.2);" title="Rechazar Pago">
                                            <i data-lucide="x" style="width: 16px; height: 16px;"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <span style="font-size: 13px; color: var(--success);"><i data-lucide="check-circle2" style="width: 16px; height: 16px; vertical-align: middle;"></i> Completado</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 48px; color: var(--text-muted);">
                            No hay compras registradas en el sistema.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
