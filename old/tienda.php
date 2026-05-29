<?php
require_once 'config.php';
require_login();

// Admin y client pueden ver la tienda
$stmt = $pdo->query("SELECT * FROM productos WHERE stock > 0 ORDER BY id DESC");
$products = $stmt->fetchAll();

$page_title = 'Finca La Karen - Catálogo Agrícola';
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<!-- Si es Administrador, mostrar banner informativo -->
<?php if ($current_user['role'] == 'admin'): ?>
<div class="panel" style="background-color: var(--info-light); border-color: var(--info); margin-bottom: 24px; padding: 16px;">
    <div style="display: flex; align-items: center; gap: 12px; color: var(--info);">
        <i data-lucide="info" style="flex-shrink: 0;"></i>
        <p style="font-size: 14px; font-weight: 600;">
            Estás visualizando la tienda en modo <strong>Previsualización (Administrador)</strong>. El carrito y el checkout solo están disponibles para usuarios registrados como <strong>Cliente</strong>.
        </p>
    </div>
</div>
<?php endif; ?>

<div class="shop-layout">
    <!-- GRID DE PRODUCTOS -->
    <div style="flex-grow: 1;">
        <div class="products-grid">
            <?php if ($products): ?>
                <?php foreach ($products as $p): ?>
                <div class="product-card" id="product-card-<?= $p['id'] ?>">
                    <div class="product-image-container">
                        <?php if ($p['image_url']): ?>
                            <img src="uploads/<?= htmlspecialchars($p['image_url']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="product-image">
                        <?php else: ?>
                            <div class="product-no-image">
                                <i data-lucide="image"></i>
                            </div>
                        <?php endif; ?>
                        <span class="product-badge"><?= format_cop($p['price']) ?></span>
                    </div>
                    
                    <div class="product-info">
                        <h4 class="product-name"><?= htmlspecialchars($p['name']) ?></h4>
                        <p class="product-desc"><?= htmlspecialchars($p['description']) ?: 'No hay detalles disponibles para este producto.' ?></p>
                        
                        <div class="product-footer">
                            <span style="font-size: 13px; color: var(--text-muted); font-weight: 500;">
                                Stock: <strong><?= $p['stock'] ?> uds</strong>
                            </span>
                            
                            <?php if ($current_user['role'] == 'client'): ?>
                            <button class="btn btn-primary" 
                                    style="padding: 6px 12px; font-size: 13px;"
                                    onclick="addToCart('<?= $p['id'] ?>', '<?= htmlspecialchars($p['name'], ENT_QUOTES) ?>', <?= $p['price'] ?>, <?= $p['stock'] ?>)">
                                <i data-lucide="shopping-cart" style="width: 14px; height: 14px;"></i> Agregar
                            </button>
                            <?php else: ?>
                            <button class="btn btn-outline" style="padding: 6px 12px; font-size: 13px;" disabled>
                                Solo Clientes
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="panel" style="grid-column: 1 / -1; text-align: center; padding: 48px; color: var(--text-muted);">
                    <i data-lucide="store" style="width: 48px; height: 48px; opacity: 0.3; margin-bottom: 12px;"></i>
                    <p style="font-weight: 600;">No hay productos disponibles en este momento.</p>
                    <p style="font-size: 13px;">Regresa más tarde o contacta al administrador de la finca.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- PANEL DEL CARRITO DE COMPRAS -->
    <?php if ($current_user['role'] == 'client'): ?>
    <div class="cart-panel">
        <h3 style="font-size: 18px; font-weight: 700; color: var(--secondary); display: flex; align-items: center; gap: 8px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
            <i data-lucide="shopping-cart" style="color: var(--primary);"></i>
            <span>Carrito de Compras</span>
        </h3>
        
        <div class="cart-items-list" id="cart-items-container">
            <div class="cart-empty-msg">
                <i data-lucide="shopping-bag" class="cart-empty-icon"></i>
                <p>Tu carrito está vacío</p>
                <span style="font-size: 12px; opacity: 0.7;">Agrega productos del catálogo para comenzar.</span>
            </div>
        </div>
        
        <div class="cart-total-section">
            <div class="cart-total-row">
                <span>Total:</span>
                <span id="cart-total-amount">$0.00</span>
            </div>
        </div>
        
        <button class="btn btn-success btn-block" id="btn-checkout" style="margin-top: 8px;" disabled onclick="confirmPurchase()">
            <i data-lucide="check-circle2"></i> Confirmar Pedido
        </button>
    </div>
    
    <script src="js/shop.js"></script>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
