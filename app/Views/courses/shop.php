<div class="row mb-4">
    <div class="col-12">
        <h1 class="page-title mb-0">Catálogo de Cursos</h1>
        <p class="page-subtitle">Explora nuestra oferta gastronómica</p>
    </div>
</div>
<div class="mb-4">
    <div class="filter-bar d-flex flex-wrap gap-2">
        <a href="<?= url('catalogo') ?>" class="btn-modern btn-modern-outline btn-modern-sm <?= !isset($_GET['category']) ? 'btn-modern-primary' : '' ?>">Todos</a>
        <?php foreach ($categorias as $cat): ?>
        <a href="<?= url('catalogo?category=' . $cat['id']) ?>" class="btn-modern btn-modern-outline btn-modern-sm <?= (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'btn-modern-primary' : '' ?>">
            <?= e($cat['name']) ?> (<?= $cat['course_count'] ?>)
        </a>
        <?php endforeach; ?>
    </div>
</div>
<div class="row g-4" id="courseGrid">
    <?php if (empty($cursos)): ?>
    <div class="col-12"><div class="empty-state"><i class="bi bi-book"></i><p>No hay cursos disponibles</p></div></div>
    <?php else: ?>
    <?php foreach ($cursos as $c): ?>
    <div class="col-md-4 col-lg-3">
        <div class="card product-card shadow-sm h-100">
            <?php if (!empty($c['image_url'])): ?>
                <img src="<?= asset('uploads/' . $c['image_url']) ?>" class="card-img-top product-img" alt="<?= e($c['title']) ?>">
            <?php else: ?>
                <div class="bg-light d-flex align-items-center justify-content-center" style="height:200px"><i class="bi bi-book text-muted" style="font-size:3rem"></i></div>
            <?php endif; ?>
            <div class="card-body d-flex flex-column">
                <h5 class="card-title fw-bold"><?= e($c['title']) ?></h5>
                <p class="card-text text-muted small mb-2"><i class="bi bi-person me-1"></i><?= e($c['instructor']) ?></p>
                <div class="d-flex flex-wrap gap-2 mb-2">
                    <?= levelBadge($c['level']) ?>
                    <span class="badge bg-light text-dark"><i class="bi bi-clock me-1"></i><?= $c['duration'] ?></span>
                </div>
                <p class="card-text text-muted small flex-grow-1"><?= e(truncate($c['description'] ?? '', 100)) ?></p>
                <div class="d-flex justify-content-between align-items-center mt-auto">
                    <span class="fs-5 fw-bold text-success"><?= format_cop($c['price']) ?></span>
                </div>
                <?php if (\App\Core\Session::userRole() === 'client'): ?>
                <form method="post" action="<?= url('api/carrito/checkout') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="curso_id" value="<?= $c['id'] ?>">
                    <button type="submit" class="btn-modern btn-modern-primary w-100 mt-2"><i class="bi bi-mortarboard me-1"></i>Matricularme</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php $scripts = '<script src="' . asset('js/shop.js') . '"></script>'; ?>
