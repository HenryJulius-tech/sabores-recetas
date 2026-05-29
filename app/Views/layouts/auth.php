<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($title ?? 'Finca Bananera') ?> - Finca Bananera</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="<?= asset('css/style.css') ?>" rel="stylesheet">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <i class="bi bi-tree-fill"></i>
            <h1>Finca Bananera</h1>
            <p>Sistema de Gestión Agrícola</p>
        </div>
        <?php $flashes = \App\Core\Session::allFlashes(); ?>
        <?php if (!empty($flashes)): ?>
            <div class="px-4 pt-3">
            <?php foreach ($flashes as $key => $msg): ?>
                <div class="alert-modern alert-<?= $key === 'error' ? 'danger' : ($key === 'success' ? 'success' : 'info') ?> alert-dismissible fade show d-flex align-items-center justify-content-between" role="alert">
                    <span><?= e($msg) ?></span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div class="auth-body"><?= $content ?? '' ?></div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
