<?php
require_once 'config.php';
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'create') {
        $name = trim($_POST['name'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $stock = (int)($_POST['stock'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        
        $image_url = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $new_filename = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "", basename($filename));
                move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $new_filename);
                $image_url = $new_filename;
            } else {
                set_flash_message('warning', 'Formato de imagen inválido. Solo jpg, png, gif.');
            }
        }
        
        $stmt = $pdo->prepare("INSERT INTO productos (name, price, stock, description, image_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $price, $stock, $description, $image_url]);
        set_flash_message('success', "Producto \"$name\" creado correctamente.");
        header("Location: productos.php");
        exit;
    }
    
    if ($action == 'edit') {
        $id = $_POST['id'] ?? 0;
        $name = trim($_POST['name'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $stock = (int)($_POST['stock'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        
        $stmt = $pdo->prepare("SELECT image_url FROM productos WHERE id = ?");
        $stmt->execute([$id]);
        $prod = $stmt->fetch();
        $image_url = $prod ? $prod['image_url'] : null;
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $new_filename = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "", basename($filename));
                move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $new_filename);
                if ($image_url && file_exists('uploads/' . $image_url)) {
                    @unlink('uploads/' . $image_url);
                }
                $image_url = $new_filename;
            }
        }
        
        $stmt = $pdo->prepare("UPDATE productos SET name=?, price=?, stock=?, description=?, image_url=? WHERE id=?");
        $stmt->execute([$name, $price, $stock, $description, $image_url, $id]);
        set_flash_message('success', "Producto \"$name\" actualizado correctamente.");
        header("Location: productos.php");
        exit;
    }
    
    if ($action == 'delete') {
        $id = $_POST['id'] ?? 0;
        $stmt = $pdo->prepare("SELECT image_url FROM productos WHERE id = ?");
        $stmt->execute([$id]);
        $prod = $stmt->fetch();
        if ($prod && $prod['image_url'] && file_exists('uploads/' . $prod['image_url'])) {
            @unlink('uploads/' . $prod['image_url']);
        }
        
        $stmt = $pdo->prepare("DELETE FROM productos WHERE id = ?");
        $stmt->execute([$id]);
        set_flash_message('success', 'Producto eliminado correctamente.');
        header("Location: productos.php");
        exit;
    }
}

$stmt = $pdo->query("SELECT * FROM productos ORDER BY id DESC");
$products = $stmt->fetchAll();

$page_title = 'Gestión de Productos';
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<div class="panel">
    <div class="panel-header">
        <div class="panel-title">
            <i data-lucide="package" style="color: var(--primary);"></i>
            <span>Catálogo de Productos</span>
        </div>
        <button class="btn btn-primary" onclick="openModal('modalCreate')">
            <i data-lucide="plus"></i> Nuevo Producto
        </button>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th width="80">Imagen</th>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th style="text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td>
                        <?php if ($p['image_url']): ?>
                            <img src="uploads/<?= htmlspecialchars($p['image_url']) ?>" alt="Img" style="width: 48px; height: 48px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border-color);">
                        <?php else: ?>
                            <div style="width: 48px; height: 48px; border-radius: 6px; background-color: var(--bg-color); display: flex; align-items: center; justify-content: center; border: 1px solid var(--border-color);">
                                <i data-lucide="image" style="color: var(--text-muted); width: 20px;"></i>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td style="font-weight: 500;">
                        <?= htmlspecialchars($p['name']) ?>
                        <?php if ($p['description']): ?>
                        <div style="font-size: 12px; color: var(--text-muted); font-weight: normal; margin-top: 4px; max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <?= htmlspecialchars($p['description']) ?>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td style="font-weight: 600; color: var(--primary);"><?= format_cop($p['price']) ?></td>
                    <td>
                        <span class="status-badge <?= $p['stock'] > 10 ? 'approved' : ($p['stock'] > 0 ? 'pending' : 'rejected') ?>">
                            <?= $p['stock'] ?> en stock
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <div style="display: flex; gap: 8px; justify-content: flex-end;">
                            <button type="button" class="btn btn-outline" style="padding: 6px 10px;" 
                                onclick='openEditModal(<?= htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8') ?>)'>
                                <i data-lucide="edit-3" style="width: 16px; height: 16px;"></i>
                            </button>
                            <form action="productos.php" method="POST" style="display: inline;" onsubmit="return confirm('¿Seguro que deseas eliminar este producto?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                <button type="submit" class="btn btn-outline" style="padding: 6px 10px; color: var(--danger); border-color: rgba(239, 68, 68, 0.2);">
                                    <i data-lucide="trash-2" style="width: 16px; height: 16px;"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Crear Producto -->
<div id="modalCreate" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i data-lucide="package-plus" style="display: inline; vertical-align: middle;"></i> Nuevo Producto</h3>
            <button class="modal-close" onclick="closeModal('modalCreate')"><i data-lucide="x"></i></button>
        </div>
        <form action="productos.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="create">
            <div class="modal-body">
                <div class="form-group">
                    <label>Nombre del Producto</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="dashboard-row" style="margin-bottom: 0; gap: 16px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Precio (COP)</label>
                        <input type="number" name="price" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Stock Inicial</label>
                        <input type="number" name="stock" class="form-control" min="0" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Descripción <small style="color: var(--text-muted);">(Opcional)</small></label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Imagen del Producto</label>
                    <input type="file" name="image" class="form-control" accept="image/png, image/jpeg, image/gif">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalCreate')">Cancelar</button>
                <button type="submit" class="btn btn-primary">Crear Producto</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Editar Producto -->
<div id="modalEdit" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i data-lucide="edit-3" style="display: inline; vertical-align: middle;"></i> Editar Producto</h3>
            <button class="modal-close" onclick="closeModal('modalEdit')"><i data-lucide="x"></i></button>
        </div>
        <form action="productos.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="edit_id">
            <div class="modal-body">
                <div class="form-group">
                    <label>Nombre del Producto</label>
                    <input type="text" name="name" id="edit_name" class="form-control" required>
                </div>
                <div class="dashboard-row" style="margin-bottom: 0; gap: 16px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Precio (COP)</label>
                        <input type="number" name="price" id="edit_price" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Stock</label>
                        <input type="number" name="stock" id="edit_stock" class="form-control" min="0" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Descripción</label>
                    <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Nueva Imagen <small style="color: var(--text-muted);">(Opcional)</small></label>
                    <input type="file" name="image" class="form-control" accept="image/png, image/jpeg, image/gif">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalEdit')">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(prod) {
    document.getElementById('edit_id').value = prod.id;
    document.getElementById('edit_name').value = prod.name;
    document.getElementById('edit_price').value = prod.price;
    document.getElementById('edit_stock').value = prod.stock;
    document.getElementById('edit_description').value = prod.description || '';
    openModal('modalEdit');
}
</script>

<?php include 'includes/footer.php'; ?>
