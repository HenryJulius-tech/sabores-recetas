<?php
require_once 'config.php';
require_role(['admin', 'worker']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'create') {
        $mtype = $_POST['type'] ?? '';
        $amount = (float)($_POST['amount'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $mdate = $_POST['date'] ?? '';
        
        $proveedor = trim($_POST['proveedor_beneficiario'] ?? '');
        $forma_pago = trim($_POST['forma_pago'] ?? '');
        $observaciones = trim($_POST['observaciones'] ?? '');
        
        $status = (get_current_user_role() == 'admin') ? 'approved' : 'pending';
        
        $soporte_url = '';
        if (isset($_FILES['soporte']) && $_FILES['soporte']['error'] == UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
            $filename = $_FILES['soporte']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $new_filename = time() . '_soporte_' . preg_replace("/[^a-zA-Z0-9.]/", "", basename($filename));
                move_uploaded_file($_FILES['soporte']['tmp_name'], 'uploads/' . $new_filename);
                $soporte_url = $new_filename;
            }
        }
        
        if (!$mtype || !$amount || !$description || !$category || !$mdate) {
            set_flash_message('error', 'Los campos principales son obligatorios para registrar un movimiento.');
        } elseif ($amount <= 0) {
            set_flash_message('error', 'El monto debe ser un número positivo.');
        } else {
            $stmt = $pdo->prepare("INSERT INTO movimientos (type, amount, description, category, date, created_by_id, proveedor_beneficiario, forma_pago, soporte_url, observaciones, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$mtype, $amount, $description, $category, $mdate, $_SESSION['user_id'], $proveedor, $forma_pago, $soporte_url, $observaciones, $status]);
            
            if ($status == 'pending') {
                set_flash_message('success', 'Movimiento registrado como PENDIENTE. Esperando aprobación del administrador.');
            } else {
                set_flash_message('success', 'Movimiento financiero registrado correctamente.');
            }
        }
        
        $qs = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
        header("Location: movimientos.php" . $qs);
        exit;
    }
    
    if ($action == 'delete' && get_current_user_role() == 'admin') {
        $id = $_POST['id'] ?? 0;
        $stmt = $pdo->prepare("SELECT soporte_url FROM movimientos WHERE id = ?");
        $stmt->execute([$id]);
        $mov = $stmt->fetch();
        if ($mov && $mov['soporte_url'] && file_exists('uploads/' . $mov['soporte_url'])) {
            @unlink('uploads/' . $mov['soporte_url']);
        }
        
        $stmt = $pdo->prepare("DELETE FROM movimientos WHERE id = ?");
        $stmt->execute([$id]);
        set_flash_message('success', 'Movimiento eliminado correctamente.');
        
        $qs = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
        header("Location: movimientos.php" . $qs);
        exit;
    }
    
    if ($action == 'approve' && get_current_user_role() == 'admin') {
        $id = $_POST['id'] ?? 0;
        $stmt = $pdo->prepare("UPDATE movimientos SET status = 'approved' WHERE id = ?");
        $stmt->execute([$id]);
        set_flash_message('success', 'Movimiento aprobado correctamente.');
        $qs = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
        header("Location: movimientos.php" . $qs);
        exit;
    }
    
    if ($action == 'reject' && get_current_user_role() == 'admin') {
        $id = $_POST['id'] ?? 0;
        $stmt = $pdo->prepare("UPDATE movimientos SET status = 'rejected' WHERE id = ?");
        $stmt->execute([$id]);
        set_flash_message('success', 'Movimiento rechazado.');
        $qs = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
        header("Location: movimientos.php" . $qs);
        exit;
    }
}

// Filtros
$search = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');
$type = trim($_GET['type'] ?? '');
$day = trim($_GET['day'] ?? '');
$month = trim($_GET['month'] ?? '');
$year = trim($_GET['year'] ?? '');

$sql = "SELECT m.*, u.username as registrado_por_name FROM movimientos m LEFT JOIN usuarios u ON m.created_by_id = u.id WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND (m.description LIKE ? OR m.proveedor_beneficiario LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($category) {
    $sql .= " AND m.category = ?";
    $params[] = $category;
}
if ($type) {
    $sql .= " AND m.type = ?";
    $params[] = $type;
}
if ($year) {
    $sql .= " AND YEAR(m.date) = ?";
    $params[] = $year;
}
if ($month) {
    $sql .= " AND MONTH(m.date) = ?";
    $params[] = $month;
}
if ($day) {
    $sql .= " AND DAY(m.date) = ?";
    $params[] = $day;
}

$sql .= " ORDER BY m.date DESC, m.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$movements = $stmt->fetchAll();

// Categorías para el filtro
$categories = $pdo->query("SELECT DISTINCT category FROM movimientos WHERE category != ''")->fetchAll(PDO::FETCH_COLUMN);

$current_year = (int)date('Y');
$years = range($current_year - 5, $current_year + 1);

$page_title = 'Finca La Karen - Módulo Financiero';
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<!-- BARRA DE BÚSQUEDA Y FILTRADO -->
<div class="search-filter-bar">
    <form action="movimientos.php" method="GET" class="filters-left" id="filter-form">
        <div class="search-input-wrapper">
            <i data-lucide="search" class="search-icon"></i>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="search-input"
                placeholder="Buscar por descripción/prov..." onchange="this.form.submit()">
        </div>
        <select name="type" class="select-filter" onchange="this.form.submit()">
            <option value="">Todos los tipos</option>
            <option value="ingreso" <?= $type == 'ingreso' ? 'selected' : '' ?>>Ingresos</option>
            <option value="gasto" <?= $type == 'gasto' ? 'selected' : '' ?>>Gastos</option>
        </select>
        <select name="category" class="select-filter" onchange="this.form.submit()">
            <option value="">Todas las categorías</option>
            <?php foreach ($categories as $cat): ?>
            <option value="<?= htmlspecialchars($cat) ?>" <?= $category == $cat ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="day" class="select-filter" onchange="this.form.submit()">
            <option value="">Día</option>
            <?php for ($d = 1; $d <= 31; $d++): ?>
            <option value="<?= $d ?>" <?= $day == $d ? 'selected' : '' ?>><?= $d ?></option>
            <?php endfor; ?>
        </select>
        <select name="month" class="select-filter" onchange="this.form.submit()">
            <option value="">Mes</option>
            <?php for ($m = 1; $m <= 12; $m++): ?>
            <option value="<?= $m ?>" <?= $month == $m ? 'selected' : '' ?>><?= $m ?></option>
            <?php endfor; ?>
        </select>
        <select name="year" class="select-filter" onchange="this.form.submit()">
            <option value="">Año</option>
            <?php foreach ($years as $y): ?>
            <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>><?= $y ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-outline" style="padding: 8px 16px;">
            <i data-lucide="filter"></i> Filtrar
        </button>
        <?php if ($search || $type || $category || $day || $month || $year): ?>
        <a href="movimientos.php" class="btn btn-outline"
            style="color: var(--danger); border-color: var(--danger-light); padding: 8px 16px;">
            <i data-lucide="x-circle"></i> Limpiar
        </a>
        <?php endif; ?>
    </form>

    <div style="display: flex; gap: 12px;">
        <a href="exportar_movimientos.php?<?= $_SERVER['QUERY_STRING'] ?>"
            class="btn btn-outline" title="Exportar reporte filtrado a Excel (CSV)">
            <i data-lucide="file-spreadsheet" style="color: #047857;"></i> Exportar a Excel
        </a>
        <button class="btn btn-primary" onclick="openModal('modalCreateMov')">
            <i data-lucide="plus-circle"></i> Nuevo Registro
        </button>
    </div>
</div>

<!-- TABLA DE MOVIMIENTOS -->
<div class="panel">
    <div class="panel-header">
        <div class="panel-title">
            <i data-lucide="landmark" style="color: var(--primary);"></i>
            <span>Historial de Movimientos de Caja</span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table" style="font-size: 13px;">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Categoría</th>
                    <th>Descripción / Proveedor</th>
                    <th>F. Pago</th>
                    <th>Estado</th>
                    <th>Soporte</th>
                    <th style="text-align: right;">Monto</th>
                    <?php if ($current_user['role'] == 'admin'): ?>
                    <th style="text-align: right;">Acciones</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if ($movements): ?>
                <?php foreach ($movements as $m): ?>
                <tr style="<?= $m['status'] == 'rejected' ? 'opacity: 0.5;' : '' ?>">
                    <td style="font-weight: 500; color: var(--text-muted);"><?= date('Y-m-d', strtotime($m['date'])) ?></td>
                    <td>
                        <?php if ($m['type'] == 'ingreso'): ?>
                        <span class="status-badge approved">Ingreso</span>
                        <?php else: ?>
                        <span class="status-badge rejected">Gasto</span>
                        <?php endif; ?>
                    </td>
                    <td><span style="font-weight: 600;"><?= htmlspecialchars($m['category']) ?></span></td>
                    <td>
                        <?= htmlspecialchars($m['description']) ?>
                        <?php if ($m['proveedor_beneficiario']): ?>
                            <br><small style="color: var(--text-muted);">Prov/Ben: <?= htmlspecialchars($m['proveedor_beneficiario']) ?></small>
                        <?php endif; ?>
                        <?php if ($m['compra_id']): ?>
                            <br><small style="color: var(--primary);">Compra #<?= $m['compra_id'] ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($m['forma_pago'] ?: 'N/A') ?></td>
                    <td>
                        <?php if ($m['status'] == 'pending'): ?>
                            <span class="status-badge pending">Pendiente</span>
                        <?php elseif ($m['status'] == 'approved'): ?>
                            <span class="status-badge approved">Aprobado</span>
                        <?php else: ?>
                            <span class="status-badge rejected">Rechazado</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($m['soporte_url']): ?>
                            <a href="uploads/<?= htmlspecialchars($m['soporte_url']) ?>" target="_blank" title="Ver soporte" style="color: var(--primary);">
                                <i data-lucide="file-text" style="width: 20px; height: 20px;"></i>
                            </a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td style="text-align: right; font-weight: 700; font-size: 14px; color: <?= $m['type'] == 'ingreso' ? 'var(--success)' : 'var(--danger)' ?>;">
                        <?= $m['type'] == 'ingreso' ? '+' : '-' ?><?= format_cop($m['amount']) ?>
                    </td>
                    <?php if ($current_user['role'] == 'admin'): ?>
                    <td style="text-align: right;">
                        <div style="display: flex; gap: 4px; justify-content: flex-end;">
                            <?php if ($m['status'] == 'pending'): ?>
                                <form action="movimientos.php?<?= $_SERVER['QUERY_STRING'] ?>" method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="approve">
                                    <input type="hidden" name="id" value="<?= $m['id'] ?>">
                                    <button type="submit" class="btn btn-outline" style="padding: 4px 8px; color: var(--success);" title="Aprobar">
                                        <i data-lucide="check" style="width: 14px; height: 14px;"></i>
                                    </button>
                                </form>
                                <form action="movimientos.php?<?= $_SERVER['QUERY_STRING'] ?>" method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="reject">
                                    <input type="hidden" name="id" value="<?= $m['id'] ?>">
                                    <button type="submit" class="btn btn-outline" style="padding: 4px 8px; color: var(--warning);" title="Rechazar">
                                        <i data-lucide="x" style="width: 14px; height: 14px;"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                            <form action="movimientos.php?<?= $_SERVER['QUERY_STRING'] ?>" method="POST" style="display:inline;" onsubmit="return confirm('¿Seguro de eliminar?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $m['id'] ?>">
                                <button type="submit" class="btn btn-outline" style="padding: 4px 8px; color: var(--danger);" title="Eliminar">
                                    <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="<?= $current_user['role'] == 'admin' ? '9' : '8' ?>" style="text-align: center; padding: 48px; color: var(--text-muted);">
                        No se encontraron movimientos financieros.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL: REGISTRAR MOVIMIENTO -->
<div class="modal-overlay" id="modalCreateMov">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h3><i data-lucide="plus-circle" style="display: inline; vertical-align: middle;"></i> Registrar Movimiento Financiero</h3>
            <button class="modal-close" onclick="closeModal('modalCreateMov')"><i data-lucide="x"></i></button>
        </div>
        <form action="movimientos.php?<?= $_SERVER['QUERY_STRING'] ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="create">
            <div class="modal-body">
                
                <div class="dashboard-row" style="margin-bottom: 0; gap: 16px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Tipo de Operación</label>
                        <select name="type" class="form-control" required>
                            <option value="" disabled selected>Seleccione...</option>
                            <option value="ingreso">Ingreso (Entrada)</option>
                            <option value="gasto">Gasto (Salida)</option>
                        </select>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Monto ($)</label>
                        <input type="number" name="amount" class="form-control" placeholder="0.00" step="0.01" min="0.01" required>
                    </div>
                </div>

                <div class="dashboard-row" style="margin-bottom: 0; gap: 16px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Categoría</label>
                        <input type="text" name="category" class="form-control" placeholder="Ej. Insumos, Ventas..." required list="cat-suggestions">
                        <datalist id="cat-suggestions">
                            <option value="Ventas">
                            <option value="Insumos">
                            <option value="Salarios">
                            <option value="Servicios Públicos">
                        </datalist>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Fecha de Transacción</label>
                        <input type="date" name="date" class="form-control" required value="<?= date('Y-m-d') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Concepto / Descripción</label>
                    <input type="text" name="description" class="form-control" required>
                </div>
                
                <div class="dashboard-row" style="margin-bottom: 0; gap: 16px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Proveedor o Beneficiario</label>
                        <input type="text" name="proveedor_beneficiario" class="form-control" placeholder="Nombre de persona/empresa">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Forma de Pago</label>
                        <select name="forma_pago" class="form-control">
                            <option value="">Seleccionar...</option>
                            <option value="Efectivo">Efectivo</option>
                            <option value="Transferencia Bancaria">Transferencia Bancaria</option>
                            <option value="Tarjeta de Crédito">Tarjeta de Crédito</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Soporte (Imagen o PDF)</label>
                    <input type="file" name="soporte" class="form-control" accept="image/*,application/pdf">
                </div>

                <div class="form-group">
                    <label>Observaciones Adicionales</label>
                    <textarea name="observaciones" class="form-control" rows="2"></textarea>
                </div>
                
                <?php if ($current_user['role'] == 'worker'): ?>
                <div style="background-color: var(--warning-light); color: var(--warning); padding: 12px; border-radius: 6px; font-size: 13px; margin-bottom: 16px;">
                    <i data-lucide="info" style="width: 16px; height: 16px; vertical-align: middle;"></i>
                    Como trabajador, este movimiento quedará como <strong>Pendiente de Aprobación</strong>.
                </div>
                <?php endif; ?>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalCreateMov')">Cancelar</button>
                <button type="submit" class="btn btn-primary">Registrar Movimiento</button>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
