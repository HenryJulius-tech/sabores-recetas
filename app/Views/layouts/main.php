<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($title ?? 'Sabores & Recetas') ?> - Sabores & Recetas</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="<?= asset('css/style.css') ?>" rel="stylesheet">
<meta name="csrf-token" content="<?= csrf_token() ?>">
</head>
<body>
<div class="d-flex" id="wrapper">
    <?php require __DIR__ . '/../partials/sidebar.php'; ?>
    <div id="page-content-wrapper" class="flex-grow-1 d-flex flex-column">
        <?php require __DIR__ . '/../partials/navbar.php'; ?>
        <main class="flex-grow-1">
            <?php $flashes = \App\Core\Session::allFlashes(); ?>
            <?php if (!empty($flashes)): ?>
                <?php foreach ($flashes as $key => $msg): ?>
                    <div class="alert-modern alert-<?= $key === 'error' ? 'danger' : ($key === 'success' ? 'success' : 'info') ?> alert-dismissible fade show d-flex align-items-center justify-content-between" role="alert">
                        <span><?= e($msg) ?></span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <?= $content ?? '' ?>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= asset('js/main.js') ?>"></script>
<?php if (isset($scripts)) echo $scripts; ?>
</body>
</html>
