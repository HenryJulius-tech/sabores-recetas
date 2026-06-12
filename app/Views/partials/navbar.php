<nav class="navbar-top d-flex align-items-center">
    <button class="btn btn-sm btn-outline-success d-md-none me-2" type="button" onclick="document.getElementById('sidebar').classList.toggle('show')">
        <i class="bi bi-list"></i>
    </button>
    <span class="navbar-brand"><?= e($title ?? 'Panel de Control') ?></span>
    <div class="ms-auto d-flex align-items-center gap-3">
        <span class="date-badge d-none d-sm-inline"><i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y') ?></span>
        
        <?php if (\App\Core\Session::isLoggedIn()): ?>
        <!-- Notificaciones -->
        <div class="notif-container">
            <button class="notif-trigger" onclick="toggleNotif(event)" id="notifBell">
                <i class="bi bi-bell"></i>
                <span class="notif-badge" id="notifBadge" style="display:none">0</span>
            </button>
            
            <div class="notif-dropdown" id="notifDropdown">
                <div class="notif-header">
                    <h6>Notificaciones</h6>
                    <button class="notif-mark-all" onclick="markAllNotif()">Marcar todas leídas</button>
                </div>
                <div class="notif-list" id="notifList">
                    <div class="notif-empty">Cargando...</div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- User Menu Dropdown Moderno -->
        <div class="user-menu-container">
            <button class="user-menu-trigger" onclick="toggleUserMenu(event)">
                <?php 
                    $userPhoto = \App\Core\Session::userAttribute('profile_photo');
                    if ($userPhoto): 
                ?>
                    <img src="<?= upload_url('profiles', $userPhoto) ?>" alt="<?= e(\App\Core\Session::username()) ?>" class="user-avatar">
                <?php else: ?>
                    <i class="bi bi-person-circle"></i>
                <?php endif; ?>
                <span class="d-none d-md-inline fw-medium"><?= e(\App\Core\Session::username()) ?></span>
                <i class="bi bi-chevron-down"></i>
            </button>
            
            <div class="user-menu-dropdown" id="userMenuDropdown">
                <div class="dropdown-header">
                    <?php if ($userPhoto): ?>
                        <img src="<?= upload_url('profiles', $userPhoto) ?>" alt="Avatar" class="dropdown-avatar">
                    <?php else: ?>
                        <div class="dropdown-avatar-placeholder">
                            <i class="bi bi-person"></i>
                        </div>
                    <?php endif; ?>
                    <div class="dropdown-user-info">
                        <div class="dropdown-username"><?= e(\App\Core\Session::username()) ?></div>
                        <div class="dropdown-role"><?= e(\App\Core\Session::userRole()) ?></div>
                    </div>
                </div>
                
                <div class="dropdown-divider"></div>
                
                <div class="dropdown-content">
                    <a href="<?= url('perfil') ?>" class="dropdown-item">
                        <i class="bi bi-person"></i>
                        <span>Ver perfil</span>
                    </a>
                    <a href="<?= url('perfil/editar') ?>" class="dropdown-item">
                        <i class="bi bi-pencil"></i>
                        <span>Editar perfil</span>
                    </a>
                    <a href="<?= url('mis-cursos') ?>" class="dropdown-item">
                        <i class="bi bi-book"></i>
                        <span>Mis cursos</span>
                    </a>
                    <a href="<?= url('configuracion') ?>" class="dropdown-item">
                        <i class="bi bi-gear"></i>
                        <span>Configuración</span>
                    </a>
                </div>
                
                <div class="dropdown-divider"></div>
                
                <a href="<?= url('logout') ?>" class="dropdown-item logout-btn">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Cerrar sesión</span>
                </a>
            </div>
        </div>
    </div>
</nav>

<script>
function toggleUserMenu(event) {
    event.preventDefault();
    const dropdown = document.getElementById('userMenuDropdown');
    dropdown.classList.toggle('active');
}

document.addEventListener('click', function(event) {
    const container = document.querySelector('.user-menu-container');
    if (container && !container.contains(event.target)) {
        const dropdown = document.getElementById('userMenuDropdown');
        dropdown.classList.remove('active');
    }
});
</script>

<style>
.notif-container { position: relative; }
.notif-trigger {
    background: none; border: none; color: var(--text-muted);
    font-size: 1.3rem; padding: 6px 8px; cursor: pointer;
    position: relative; line-height: 1; transition: color 0.2s;
}
.notif-trigger:hover { color: var(--primary); }
.notif-badge {
    position: absolute; top: 0; right: 0; font-size: 0.6rem;
    background: #dc2626; color: #fff; border-radius: 50%;
    min-width: 16px; height: 16px; display: flex; align-items: center;
    justify-content: center; font-weight: 700; padding: 0 4px;
    box-shadow: 0 2px 6px rgba(220,38,38,0.3);
}
.notif-dropdown {
    position: absolute; top: calc(100% + 8px); right: -8px;
    background: #fff; border: 1px solid var(--border);
    border-radius: var(--radius-xs); box-shadow: 0 10px 40px rgba(0,0,0,0.12);
    min-width: 340px; max-width: 400px; opacity: 0; visibility: hidden;
    transform: translateY(-10px) scale(0.95);
    transition: all 0.2s cubic-bezier(0.34,1.56,0.64,1); z-index: 1051;
}
.notif-dropdown.active { opacity: 1; visibility: visible; transform: translateY(0) scale(1); }
.notif-header {
    display: flex; justify-content: space-between; align-items: center;
    padding: 12px 16px; border-bottom: 1px solid var(--border);
}
.notif-header h6 { margin: 0; font-weight: 700; font-size: 0.9rem; }
.notif-mark-all {
    background: none; border: none; color: var(--primary);
    font-size: 0.75rem; cursor: pointer; font-weight: 600;
    transition: opacity 0.2s;
}
.notif-mark-all:hover { opacity: 0.7; text-decoration: underline; }
.notif-list { max-height: 360px; overflow-y: auto; }
.notif-item {
    display: block; padding: 12px 16px; border-bottom: 1px solid #f1f5f9;
    text-decoration: none; color: var(--text); transition: background 0.2s;
    cursor: pointer;
}
.notif-item:hover { background: #FEF2F2; text-decoration: none; }
.notif-item .notif-title { font-weight: 600; font-size: 0.85rem; margin-bottom: 2px; }
.notif-item .notif-msg { font-size: 0.8rem; color: var(--text-muted); margin-bottom: 4px; }
.notif-item .notif-time { font-size: 0.7rem; color: #94a3b8; }
.notif-item.unread { border-left: 3px solid var(--primary); background: #FFF5F7; }
.notif-item.unread:hover { background: #FEF2F2; }
.notif-empty {
    padding: 32px 16px; text-align: center; color: var(--text-muted);
    font-size: 0.85rem;
}
.notif-empty i { font-size: 1.5rem; display: block; margin-bottom: 8px; opacity: 0.4; }
.notif-item-wrap { position: relative; display: flex; align-items: flex-start; }
.notif-item-wrap .notif-item { flex: 1; min-width: 0; }
.notif-delete {
    background: none; border: none; color: #94a3b8; font-size: 1rem;
    padding: 12px 8px; cursor: pointer; opacity: 0; transition: all 0.2s;
    line-height: 1; flex-shrink: 0;
}
.notif-item-wrap:hover .notif-delete { opacity: 1; }
.notif-delete:hover { color: #dc2626; }
</style>

<script>
<?php if (\App\Core\Session::isLoggedIn()): ?>
var notifPollTimer = null;
var notifBaseUrl = '<?= rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') ?>/index.php/';

function toggleNotif(e) {
    e.stopPropagation();
    var d = document.getElementById('notifDropdown');
    d.classList.toggle('active');
    if (d.classList.contains('active')) {
        loadNotif(true);
    }
}

function loadNotif(includeList) {
    fetch('<?= url('api/notificaciones') ?>')
    .then(function(r){ return r.json() })
    .then(function(data){
        var badge = document.getElementById('notifBadge');
        if (data.count > 0) {
            badge.style.display = 'flex';
            badge.textContent = data.count > 99 ? '99+' : data.count;
        } else {
            badge.style.display = 'none';
        }
        if (!includeList) return;
        var list = document.getElementById('notifList');
        if (!data.list || data.list.length === 0) {
            list.innerHTML = '<div class="notif-empty"><i class="bi bi-bell-slash"></i>Sin notificaciones</div>';
            return;
        }
        var html = '';
        data.list.forEach(function(n){
            var cls = n.read_at ? '' : ' unread';
            var link = n.link ? notifBaseUrl + n.link : '#';
            html += '<div class="notif-item-wrap">';
            html += '<a href="' + link + '" class="notif-item' + cls + '" onclick="return clickNotif(' + n.id + ',this)">';
            html += '<div class="notif-title">' + escHtml(n.title) + '</div>';
            html += '<div class="notif-msg">' + escHtml(n.message) + '</div>';
            html += '<div class="notif-time">' + timeAgo(n.created_at) + '</div>';
            html += '</a>';
            html += '<button class="notif-delete" onclick="deleteNotif(' + n.id + ',this)" title="Eliminar">&times;</button>';
            html += '</div>';
        });
        list.innerHTML = html;
    })
    .catch(function(){});
}

// Load badge on page start and poll every 30s
loadNotif(false);
notifPollTimer = setInterval(function(){ loadNotif(false); }, 30000);

function clickNotif(id, el) {
    fetch('<?= url('api/notificaciones/leer') ?>/' + id, { method:'POST' })
    .then(function(r){ return r.json() }).catch(function(){});
    return true;
}

function deleteNotif(id, btn) {
    fetch('<?= url('api/notificaciones/eliminar') ?>/' + id, { method:'POST' })
    .then(function(r){ return r.json() })
    .then(function(d){
        if (d.success) {
            var wrap = btn.closest('.notif-item-wrap');
            if (wrap) wrap.remove();
            loadNotif(false);
        }
    })
    .catch(function(){});
}

function markAllNotif() {
    fetch('<?= url('api/notificaciones/leer-todas') ?>', { method:'POST' })
    .then(function(r){ return r.json() })
    .then(function(){
        document.getElementById('notifBadge').style.display = 'none';
        document.querySelectorAll('#notifList .notif-item').forEach(function(el){ el.classList.remove('unread'); });
    })
    .catch(function(){});
}

function escHtml(s) {
    if (!s) return '';
    var d = document.createElement('div');
    d.appendChild(document.createTextNode(s));
    return d.innerHTML;
}

function timeAgo(dt) {
    if (!dt) return '';
    var now = new Date();
    var d = new Date(dt.replace(' ', 'T'));
    var diff = Math.floor((now - d) / 1000);
    if (diff < 60) return 'Ahora';
    if (diff < 3600) return Math.floor(diff/60) + 'm';
    if (diff < 86400) return Math.floor(diff/3600) + 'h';
    return d.toLocaleDateString('es-CO', {day:'numeric', month:'short'});
}

document.addEventListener('click', function(e) {
    var c = document.querySelector('.notif-container');
    if (c && !c.contains(e.target)) {
        var d = document.getElementById('notifDropdown');
        if (d) d.classList.remove('active');
    }
});
<?php endif; ?>
</script>

