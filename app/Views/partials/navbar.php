<nav class="navbar-top d-flex align-items-center">
    <button class="btn btn-sm btn-outline-success d-md-none me-2" type="button" onclick="document.getElementById('sidebar').classList.toggle('show')">
        <i class="bi bi-list"></i>
    </button>
    <span class="navbar-brand"><?= e($title ?? 'Panel de Control') ?></span>
    <div class="ms-auto date-badge"><i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y') ?></div>
</nav>
