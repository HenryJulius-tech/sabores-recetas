<?php
$current_path = basename($_SERVER['PHP_SELF']);
?>
        <!-- SIDEBAR DE NAVEGACIÓN -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-brand">
                    <i data-lucide="sprout"></i>
                    <span>La Karen</span>
                </div>
                <button class="sidebar-close">
                    <i data-lucide="x"></i>
                </button>
            </div>
            
            <ul class="sidebar-menu">
                <?php if ($current_user): ?>
                    <?php if (in_array($current_user['role'], ['admin', 'worker'])): ?>
                        <li class="sidebar-item <?= ($current_path == 'index.php') ? 'active' : '' ?>">
                            <a href="index.php">
                                <i data-lucide="layout-dashboard"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if ($current_user['role'] == 'admin'): ?>
                        <li class="sidebar-item <?= ($current_path == 'usuarios.php') ? 'active' : '' ?>">
                            <a href="usuarios.php">
                                <i data-lucide="users"></i>
                                <span>Usuarios</span>
                            </a>
                        </li>
                        <li class="sidebar-item <?= ($current_path == 'productos.php') ? 'active' : '' ?>">
                            <a href="productos.php">
                                <i data-lucide="package"></i>
                                <span>Productos</span>
                            </a>
                        </li>
                        <li class="sidebar-item <?= ($current_path == 'admin_compras.php') ? 'active' : '' ?>">
                            <a href="admin_compras.php">
                                <i data-lucide="credit-card"></i>
                                <span>Aprobar Pagos</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (in_array($current_user['role'], ['admin', 'worker'])): ?>
                        <li class="sidebar-item <?= ($current_path == 'movimientos.php') ? 'active' : '' ?>">
                            <a href="movimientos.php">
                                <i data-lucide="landmark"></i>
                                <span>Registros Diarios</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if ($current_user['role'] == 'client'): ?>
                        <li class="sidebar-item <?= ($current_path == 'tienda.php') ? 'active' : '' ?>">
                            <a href="tienda.php">
                                <i data-lucide="store"></i>
                                <span>Tienda Agrícola</span>
                            </a>
                        </li>
                        <li class="sidebar-item <?= ($current_path == 'mis_compras.php') ? 'active' : '' ?>">
                            <a href="mis_compras.php">
                                <i data-lucide="shopping-bag"></i>
                                <span>Mis Compras</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <?php if ($current_user['role'] == 'admin'): ?>
                        <li class="sidebar-item <?= ($current_path == 'tienda.php') ? 'active' : '' ?>">
                            <a href="tienda.php">
                                <i data-lucide="eye"></i>
                                <span>Vista Tienda</span>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
            
            <div class="sidebar-footer">
                <?php if ($current_user): ?>
                    <div class="sidebar-user">
                        <div class="sidebar-user-avatar">
                            <?= strtoupper(substr($current_user['username'], 0, 1)) ?>
                        </div>
                        <div class="sidebar-user-info">
                            <div class="sidebar-user-name" title="<?= htmlspecialchars($current_user['username']) ?>">
                                <?= htmlspecialchars($current_user['username']) ?>
                            </div>
                            <div class="sidebar-user-role"><?= htmlspecialchars($current_user['role']) ?></div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </aside>

        <!-- CONTENIDO PRINCIPAL -->
        <main class="main-content">
            
            <!-- TOP NAVBAR -->
            <nav class="navbar">
                <div class="navbar-left">
                    <button class="sidebar-toggle">
                        <i data-lucide="menu"></i>
                    </button>
                    <span class="navbar-title">
                        <?= $page_title ?? 'Finca La Karen' ?>
                    </span>
                </div>
                
                <div class="navbar-right">
                    <?php if ($current_user): ?>
                        <span class="role-badge <?= htmlspecialchars($current_user['role']) ?>">
                            <?= htmlspecialchars($current_user['role']) ?>
                        </span>
                        <a href="logout.php" class="btn btn-outline" style="padding: 8px 16px; font-size: 13px;">
                            <i data-lucide="log-out" style="width: 16px; height: 16px;"></i>
                            <span>Salir</span>
                        </a>
                    <?php endif; ?>
                </div>
            </nav>
            
            <!-- CONTENEDOR DE CONTENIDO -->
            <div class="content-container">
