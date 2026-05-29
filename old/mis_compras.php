<?php
require_once 'config.php';
require_role('client');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Es la subida de un comprobante
    $compra_id = $_POST['compra_id'] ?? 0;
    
    $stmt = $pdo->prepare("SELECT * FROM compras WHERE id = ? AND user_id = ?");
    $stmt->execute([$compra_id, $_SESSION['user_id']]);
    $compra = $stmt->fetch();
    
    if (!$compra) {
        set_flash_message('error', 'Compra no encontrada.');
        header('Location: mis_compras.php');
        exit;
    }
    
    if ($compra['status'] == 'approved') {
        set_flash_message('warning', 'Esta compra ya ha sido aprobada y pagada.');
        header('Location: mis_compras.php');
        exit;
    }
    
    $forma_pago = $_POST['forma_pago'] ?? '';
    if (!$forma_pago) {
        set_flash_message('error', 'Debe seleccionar una forma de pago.');
        header('Location: mis_compras.php');
        exit;
    }
    
    // Save forma_pago to compras
    $stmt = $pdo->prepare("UPDATE compras SET forma_pago = ? WHERE id = ?");
    $stmt->execute([$forma_pago, $compra_id]);
    
    $new_filename = null;
    if ($forma_pago == 'Transferencia') {
        if (isset($_FILES['proof_image']) && $_FILES['proof_image']['error'] == UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
            $filename = $_FILES['proof_image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $new_filename = time() . '_pago_' . preg_replace("/[^a-zA-Z0-9.]/", "", basename($filename));
                move_uploaded_file($_FILES['proof_image']['tmp_name'], 'uploads/' . $new_filename);
            } else {
                set_flash_message('error', 'Formato de comprobante no válido.');
                header('Location: mis_compras.php');
                exit;
            }
        } else {
            set_flash_message('error', 'Para transferencia debe adjuntar un comprobante.');
            header('Location: mis_compras.php');
            exit;
        }
    }
    
    // Comprobar si ya hay un pago registrado para actualizar o insertar
    $stmt = $pdo->prepare("SELECT * FROM pagos WHERE compra_id = ?");
    $stmt->execute([$compra_id]);
    $pago = $stmt->fetch();
    
    if ($pago) {
        if ($new_filename && $pago['proof_image_url'] && file_exists('uploads/' . $pago['proof_image_url'])) {
            @unlink('uploads/' . $pago['proof_image_url']);
        }
        $url_to_save = $new_filename ?: $pago['proof_image_url'];
        $stmt = $pdo->prepare("UPDATE pagos SET proof_image_url = ?, status = 'pending', created_at = NOW() WHERE compra_id = ?");
        $stmt->execute([$url_to_save, $compra_id]);
    } else {
        $url_to_save = $new_filename ?: '';
        $stmt = $pdo->prepare("INSERT INTO pagos (compra_id, proof_image_url, status) VALUES (?, ?, 'pending')");
        $stmt->execute([$compra_id, $url_to_save]);
    }
    
    $stmt = $pdo->prepare("UPDATE compras SET status = 'pending' WHERE id = ?");
    $stmt->execute([$compra_id]);
    
    set_flash_message('success', 'Pago reportado con éxito. El administrador lo verificará pronto.');
    header('Location: mis_compras.php');
    exit;
}

// GET mis compras
$stmt = $pdo->prepare("SELECT * FROM compras WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$purchases = $stmt->fetchAll();

// Add detalles and pago
foreach ($purchases as &$c) {
    $stmt = $pdo->prepare("SELECT d.*, p.name as product_name FROM detalle_compras d JOIN productos p ON d.product_id = p.id WHERE d.compra_id = ?");
    $stmt->execute([$c['id']]);
    $c['detalles'] = $stmt->fetchAll();
    
    $stmt = $pdo->prepare("SELECT * FROM pagos WHERE compra_id = ?");
    $stmt->execute([$c['id']]);
    $c['pago'] = $stmt->fetch();
}
unset($c);

$page_title = 'Finca La Karen - Mis Compras';
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="panel">
    <div class="panel-header">
        <div class="panel-title">
            <i data-lucide="shopping-bag" style="color: var(--primary);"></i>
            <span>Historial de Pedidos Realizados</span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Código Pedido</th>
                    <th>Fecha</th>
                    <th>Productos Detalle</th>
                    <th>Total a Pagar</th>
                    <th>Estado de Pago</th>
                    <th>Comprobante Subido</th>
                    <th style="text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($purchases): ?>
                    <?php foreach ($purchases as $c): ?>
                    <tr>
                        <td style="font-weight: 700; color: var(--secondary);">#<?= $c['id'] ?></td>
                        <td style="color: var(--text-muted);"><?= date('Y-m-d H:i', strtotime($c['created_at'])) ?></td>
                        <td>
                            <ul style="list-style: none; padding: 0;">
                                <?php foreach ($c['detalles'] as $det): ?>
                                <li style="font-size: 13px;">
                                    <strong><?= $det['quantity'] ?></strong> x <?= htmlspecialchars($det['product_name']) ?> 
                                    <span style="color: var(--text-muted);">($<?= number_format($det['price'], 2) ?>)</span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </td>
                        <td style="font-weight: 700; color: var(--primary); font-size: 16px;">
                            <?= format_cop($c['total']) ?>
                        </td>
                        <td>
                            <?php if ($c['status'] == 'pending'): ?>
                                <?php if ($c['pago']): ?>
                                    <span class="status-badge pending">En Verificación</span>
                                <?php else: ?>
                                    <span class="status-badge pending" style="background-color: #fef3c7; color: #d97706;">Pendiente de Pago</span>
                                <?php endif; ?>
                            <?php elseif ($c['status'] == 'approved'): ?>
                                <span class="status-badge approved">Aprobado / Pagado</span>
                            <?php else: ?>
                                <span class="status-badge rejected">Pago Rechazado</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($c['pago']): ?>
                                <a href="uploads/<?= htmlspecialchars($c['pago']['proof_image_url']) ?>" target="_blank" title="Ver comprobante">
                                    <img src="uploads/<?= htmlspecialchars($c['pago']['proof_image_url']) ?>" alt="Comprobante #<?= $c['id'] ?>" class="proof-thumbnail" style="width: 48px; height: 48px; object-fit: cover; border-radius: 6px;">
                                </a>
                            <?php else: ?>
                                <span style="font-size: 13px; color: var(--text-muted); font-style: italic;">No cargado</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: right;">
                            <?php if ($c['status'] != 'approved'): ?>
                                <button class="btn btn-primary" 
                                        style="padding: 6px 12px; font-size: 13px;"
                                        onclick="openUploadModal('<?= $c['id'] ?>', '<?= format_cop($c['total']) ?>')">
                                    <i data-lucide="upload"></i> Subir Pago
                                </button>
                            <?php else: ?>
                                <button class="btn btn-outline" style="padding: 6px 12px; font-size: 13px;" disabled>
                                    <i data-lucide="check"></i> Completado
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 48px; color: var(--text-muted);">
                            <i data-lucide="shopping-bag" style="width: 48px; height: 48px; opacity: 0.3; margin-bottom: 12px;"></i>
                            <p style="font-weight: 600;">Aún no tienes pedidos registrados.</p>
                            <a href="tienda.php" class="btn btn-primary" style="margin-top: 12px; padding: 8px 16px; font-size: 14px;">
                                <i data-lucide="store"></i> Ir a la Tienda
                            </a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL PARA SUBIR COMPROBANTE -->
<div class="modal-overlay" id="modalUploadPayment">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i data-lucide="upload" style="display: inline; vertical-align: middle;"></i> Registrar Pago <span id="modal-compra-title"></span></h3>
            <button class="modal-close" onclick="closeModal('modalUploadPayment')"><i data-lucide="x"></i></button>
        </div>
        <form action="mis_compras.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="compra_id" id="upload_compra_id">
            <div class="modal-body">
                <div style="background-color: var(--primary-light); color: var(--primary); padding: 12px 16px; border-radius: 6px; font-size: 13px; font-weight: 600; margin-bottom: 20px;">
                    Monto total a transferir: <span id="modal-compra-amount" style="font-size: 16px; font-weight: 700;"></span>
                </div>
                
                <div class="form-group">
                    <label>Forma de Pago</label>
                    <select name="forma_pago" id="upload_forma_pago" class="form-control" required onchange="toggleProofRequirement()">
                        <option value="">Seleccionar...</option>
                        <option value="Efectivo">Efectivo (Pago presencial o contra entrega)</option>
                        <option value="Tarjeta">Tarjeta (Crédito / Débito en sitio)</option>
                        <option value="Transferencia">Transferencia Bancaria</option>
                    </select>
                </div>
                
                <div class="form-group" id="proof_group" style="display: none;">
                    <label>Subir Comprobante (Captura o Foto de Transferencia) <span style="color:var(--danger)">*</span></label>
                    <input type="file" name="proof_image" id="proof_image" class="form-control" accept="image/*">
                    <small style="color: var(--text-muted); display: block; margin-top: 4px;">Obligatorio para pagos por transferencia.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalUploadPayment')">Cancelar</button>
                <button type="submit" class="btn btn-success">
                    <i data-lucide="send"></i> Enviar Pago
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openUploadModal(compraId, totalAmount) {
    document.getElementById('upload_compra_id').value = compraId;
    document.getElementById('modal-compra-title').textContent = `#${compraId}`;
    document.getElementById('modal-compra-amount').textContent = totalAmount;
    document.getElementById('upload_forma_pago').value = "";
    toggleProofRequirement();
    openModal('modalUploadPayment');
}

function toggleProofRequirement() {
    var select = document.getElementById('upload_forma_pago');
    var group = document.getElementById('proof_group');
    var input = document.getElementById('proof_image');
    if (select.value === 'Transferencia') {
        group.style.display = 'block';
        input.required = true;
    } else {
        group.style.display = 'none';
        input.required = false;
        input.value = ''; // Limpiar si cambia de opinión
    }
}
</script>

<?php include 'includes/footer.php'; ?>
