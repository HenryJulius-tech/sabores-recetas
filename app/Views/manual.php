<h1 class="page-title mb-4">Manual de Usuario</h1>
<div class="row g-4">
    <div class="col-md-3">
        <div class="list-group shadow-sm">
            <a class="list-group-item list-group-item-action active" href="#intro" data-bs-toggle="list">IntroducciÃ³n</a>
            <?php if ($role === 'admin'): ?>
            <a class="list-group-item list-group-item-action" href="#dashboard" data-bs-toggle="list">Dashboard</a>
            <a class="list-group-item list-group-item-action" href="#cursos" data-bs-toggle="list">Cursos</a>
            <a class="list-group-item list-group-item-action" href="#matriculas" data-bs-toggle="list">Aprobar Matrículas</a>
            <a class="list-group-item list-group-item-action" href="#movimientos" data-bs-toggle="list">Movimientos</a>
            <a class="list-group-item list-group-item-action" href="#usuarios" data-bs-toggle="list">Usuarios</a>
            <?php elseif ($role === 'client'): ?>
            <a class="list-group-item list-group-item-action" href="#catalogo" data-bs-toggle="list">Catálogo</a>
            <a class="list-group-item list-group-item-action" href="#matricularse" data-bs-toggle="list">Cómo Matricularse</a>
            <a class="list-group-item list-group-item-action" href="#pagos" data-bs-toggle="list">Pagos</a>
            <?php elseif ($role === 'worker'): ?>
            <a class="list-group-item list-group-item-action" href="#movimientos" data-bs-toggle="list">Movimientos</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-9">
        <div class="tab-content card-modern p-4">
            <div class="tab-pane fade show active" id="intro">
                <h5>Bienvenido a Sabores & Recetas</h5>
                <p>Plataforma de cursos de cocina. Permite administrar cursos, matrículas, movimientos financieros y usuarios.</p>
                <p>Tu rol actual es: <strong class="text-success"><?= ucfirst($role) ?></strong></p>
                <hr>
                <h6>Â¿Necesitas ayuda?</h6>
                <p class="text-muted">Contacta al administrador del sistema para cualquier inquietud.</p>
            </div>
            <?php if ($role === 'admin'): ?>
            <div class="tab-pane fade" id="dashboard">
                <h5>Dashboard</h5>
                <p>Panel principal con resumen financiero, grÃ¡fico de flujo de caja y estado del sistema.</p>
                <ul>
                    <li><strong>Ingresos/Gastos/Balance:</strong> Tarjetas con totales del perÃ­odo.</li>
                    <li><strong>GrÃ¡fico:</strong> Muestra ingresos vs gastos. Filtra por dÃ­a/semana/mes.</li>
                    <li><strong>Ãšltimos movimientos:</strong> Tabla con los 5 movimientos mÃ¡s recientes.</li>
                </ul>
            </div>
            <div class="tab-pane fade" id="cursos">
                <h5>Gestión de Cursos</h5>
                <p>CRUD completo de cursos. Desde aquí puedes crear, editar y eliminar cursos del catálogo.</p>
                <ul>
                    <li><strong>Crear:</strong> Botón "Nuevo Curso" → formulario con nombre, precio, cupo, descripción e imagen.</li>
                    <li><strong>Editar:</strong> Icono de lápiz en cada fila de la tabla.</li>
                    <li><strong>Eliminar:</strong> Icono de papelera (pide confirmación).</li>
                    <li>El cupo se actualiza automáticamente al aprobar/rechazar matrículas.</li>
                </ul>
            </div>
            <div class="tab-pane fade" id="matriculas">
                <h5>Aprobar Matrículas</h5>
                <p>Revisa las matrículas realizadas por los clientes, visualiza los comprobantes de pago y aprueba o rechaza.</p>
                <ul>
                    <li><strong>Aprobar:</strong> Registra el ingreso financiero automáticamente.</li>
                    <li><strong>Rechazar:</strong> Libera el cupo del curso.</li>
                </ul>
            </div>
            <div class="tab-pane fade" id="matriculas">
                <h5>Aprobar Matrículas</h5>
                <p>Revisa las matrículas realizadas por los clientes, visualiza los comprobantes de pago y aprueba o rechaza.</p>
                <ul>
                    <li><strong>Aprobar:</strong> Registra el ingreso financiero automáticamente.</li>
                    <li><strong>Rechazar:</strong> Libera el cupo del curso.</li>
                </ul>
            </div>
            <div class="tab-pane fade" id="movimientos">
                <h5>Movimientos Financieros</h5>
                <p>Registro de ingresos y gastos con filtros por tipo, categorÃ­a, estado y bÃºsqueda por texto.</p>
                <ul>
                    <li><strong>Crear:</strong> Los movimientos de admin se aprueban automÃ¡ticamente.</li>
                    <li><strong>Filtros:</strong> Busca por descripciÃ³n, tipo, estado y categorÃ­a.</li>
                    <li><strong>Exportar:</strong> Descarga los resultados filtrados a CSV.</li>
                </ul>
            </div>
            <div class="tab-pane fade" id="usuarios">
                <h5>GestiÃ³n de Usuarios</h5>
                <p>Administra los usuarios del sistema con tres roles: admin, worker (trabajador) y client (cliente).</p>
                <ul>
                    <li>No puedes eliminarte a ti mismo.</li>
                    <li>Debe haber al menos un administrador.</li>
                    <li>Los usuarios con compras o movimientos asociados no pueden eliminarse.</li>
                </ul>
            </div>
            <?php elseif ($role === 'client'): ?>
            <div class="tab-pane fade" id="catalogo">
                <h5>Catálogo de Cursos</h5>
                <p>Catálogo de cursos disponibles. Cada curso muestra nombre, descripción, precio y cupo disponible.</p>
                <p>Haz clic en "Matricularme" para inscribirte en un curso.</p>
            </div>
            <div class="tab-pane fade" id="matricularse">
                <h5>Cómo Matricularse</h5>
                <ol>
                    <li>Navega por el catálogo y elige un curso.</li>
                    <li>Haz clic en "Matricularme" para generar tu solicitud.</li>
                    <li>Sube el comprobante de pago desde "Mis Matrículas".</li>
                    <li>Espera la aprobación del administrador.</li>
                </ol>
            </div>
            <div class="tab-pane fade" id="matricularse">
                <h5>Cómo Matricularse</h5>
                <ol>
                    <li>Navega por el catálogo y elige un curso.</li>
                    <li>Haz clic en "Matricularme" para generar tu solicitud.</li>
                    <li>Sube el comprobante de pago desde "Mis Matrículas".</li>
                    <li>Espera la aprobación del administrador.</li>
                </ol>
            </div>
            <?php elseif ($role === 'worker'): ?>
            <div class="tab-pane fade" id="movimientos">
                <h5>Movimientos Financieros</h5>
                <p>Puedes registrar movimientos (ingresos/gastos), pero quedarán pendientes hasta que un admin los apruebe.</p>
                <p>Usa los filtros para buscar movimientos por tipo, categoría o texto.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
