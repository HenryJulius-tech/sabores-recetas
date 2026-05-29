<div class="row mb-4">
    <div class="col-12">
        <h1 class="page-title mb-0">Tienda</h1>
    </div>
</div>
<div class="row g-4" id="productGrid">
    <?php if (empty($productos)): ?>
    <div class="col-12"><div class="empty-state"><i class="bi bi-cart"></i><p>No hay productos disponibles</p></div></div>
    <?php else: ?>
    <?php foreach ($productos as $p): ?>
    <div class="col-md-4 col-lg-3">
        <div class="card product-card shadow-sm h-100">
            <?php if (!empty($p['image_url'])): ?>
                <img src="<?= asset('uploads/' . $p['image_url']) ?>" class="card-img-top" alt="<?= e($p['name']) ?>">
            <?php else: ?>
                <div class="bg-light d-flex align-items-center justify-content-center" style="height:200px"><i class="bi bi-box text-muted" style="font-size:3rem"></i></div>
            <?php endif; ?>
            <div class="card-body d-flex flex-column">
                <h5 class="card-title fw-bold"><?= e($p['name']) ?></h5>
                <p class="card-text text-muted small flex-grow-1"><?= e(truncate($p['description'] ?? '', 80)) ?></p>
                <div class="d-flex justify-content-between align-items-center mt-auto">
                    <span class="fs-5 fw-bold text-success"><?= format_cop($p['price']) ?></span>
                    <?= stockBadge($p['stock']) ?>
                </div>
                <?php if (\App\Core\Session::userRole() === 'client'): ?>
                <button class="btn-modern btn-modern-primary w-100 mt-2" onclick="addToCart(<?= $p['id'] ?>, '<?= e(addslashes($p['name'])) ?>', <?= $p['price'] ?>, <?= $p['stock'] ?>)">
                    <i class="bi bi-cart-plus me-1"></i>Agregar
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php if (\App\Core\Session::userRole() === 'client'): ?>
<div class="cart-overlay" id="cartOverlay" onclick="toggleCart()"></div>
<div class="cart-panel" id="cartPanel">
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold"><i class="bi bi-cart me-2"></i>Carrito</h5>
        <button class="btn-modern btn-modern-outline btn-modern-sm" onclick="toggleCart()"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="p-3 flex-grow-1 overflow-auto" id="cartItems"></div>
    <div class="p-3 border-top">
        <div class="d-flex justify-content-between mb-2">
            <span class="fw-bold">Total:</span>
            <span class="fw-bold fs-5 text-success" id="cartTotal">$0</span>
        </div>
        <button class="btn-modern btn-modern-primary w-100" onclick="confirmPurchase()">
            <i class="bi bi-check2-circle me-1"></i>Confirmar Compra
        </button>
    </div>
</div>
<script>var API_CHECKOUT_URL="<?= url('api/carrito/checkout') ?>";</script>
<script src="<?= asset('js/shop.js') ?>"></script>
<?php endif; ?>
