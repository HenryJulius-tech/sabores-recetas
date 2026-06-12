<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

// Requerir sesión activa (admin o estudiante)
if (!session_isLoggedIn()) {
    session_setFlash('error', 'Debes iniciar sesión para ver los manuales.');
    redirect(BASE_URL . 'auth/login.php');
}

$role   = session_userRole();
$titulo = 'Ayuda & Manuales de Usuario';

// Determinar qué header incluir según rol
if ($role === 'admin') {
    include __DIR__ . '/admin/header.php';
} else {
    include __DIR__ . '/estudiante/header.php';
}
?>

<style>
/* ══ Estilos del Manual ════════════════════════════════════════════════════ */
.manual-hero {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    border-radius: 24px;
    padding: 3rem 2.5rem;
    color: #fff;
    margin-bottom: 2.5rem;
    position: relative;
    overflow: hidden;
}
.manual-hero::after {
    content: '📖';
    position: absolute;
    right: 2.5rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 5.5rem;
    opacity: 0.12;
}
.manual-tabs .nav-link {
    border-radius: 50px;
    padding: 0.65rem 1.5rem;
    font-weight: 600;
    color: #555;
    border: 2px solid transparent;
    transition: all 0.25s;
}
.manual-tabs .nav-link.active {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: #fff;
    border-color: transparent;
    box-shadow: 0 4px 15px rgba(102,126,234,0.35);
}
.manual-tabs .nav-link:hover:not(.active) {
    background: #f0f2ff;
    border-color: #667eea;
    color: #667eea;
}
.paso-card {
    border: none;
    border-radius: 16px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    margin-bottom: 1.25rem;
    transition: transform 0.2s, box-shadow 0.2s;
    overflow: hidden;
}
.paso-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 24px rgba(0,0,0,0.1);
}
.paso-numero {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 1.1rem;
    flex-shrink: 0;
}
.paso-admin   { background: linear-gradient(135deg, #0f3460, #16213e); color: #fff; }
.paso-student { background: linear-gradient(135deg, #e11d48, #be185d); color: #fff; }
.seccion-titulo {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1a1a2e;
    border-left: 4px solid #667eea;
    padding-left: 0.85rem;
    margin: 2rem 0 1rem;
}
.seccion-titulo.pink { border-left-color: #e11d48; }
.tip-box {
    background: linear-gradient(135deg, #f0f2ff, #e8eaff);
    border-left: 4px solid #667eea;
    border-radius: 12px;
    padding: 1rem 1.25rem;
    margin-top: 0.5rem;
    font-size: 0.9rem;
    color: #444;
}
.tip-box.warning { background: linear-gradient(135deg, #fff3cd, #fef9e7); border-left-color: #ffc107; }
.tip-box.danger  { background: linear-gradient(135deg, #f8d7da, #fef5f5); border-left-color: #dc3545; }
.faq-item { border: none; border-radius: 12px; margin-bottom: 0.5rem; overflow: hidden; }
.faq-item .accordion-button {
    background: #f8fafc;
    font-weight: 600;
    color: #1a1a2e;
    box-shadow: none;
    border-radius: 12px;
}
.faq-item .accordion-button:not(.collapsed) {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: #fff;
}
</style>

<!-- Hero -->
<div class="manual-hero">
    <p class="text-uppercase small opacity-60 mb-1 fw-600 ls-1">Centro de Ayuda</p>
    <h1 class="fw-bold mb-2" style="font-size:2rem;">Manuales de Usuario</h1>
    <p class="opacity-75 mb-0">Guías paso a paso para aprovechar al máximo <strong>Sabores &amp; Recetas</strong></p>
</div>

<!-- Tabs de selección de manual -->
<ul class="nav manual-tabs mb-4 gap-2 flex-wrap" id="manualTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link <?= $role === 'admin' ? 'active' : '' ?>"
                id="tab-admin"
                data-bs-toggle="tab"
                data-bs-target="#panelAdmin"
                type="button" role="tab">
            <i class="bi bi-shield-lock-fill me-2"></i>Manual del Administrador
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link <?= $role !== 'admin' ? 'active' : '' ?>"
                id="tab-estudiante"
                data-bs-toggle="tab"
                data-bs-target="#panelEstudiante"
                type="button" role="tab">
            <i class="bi bi-mortarboard-fill me-2"></i>Manual del Estudiante
        </button>
    </li>
</ul>

<div class="tab-content" id="manualTabContent">

    <!-- ══════════════════════════════════════════════════════════════════
         MANUAL DEL ADMINISTRADOR
    ══════════════════════════════════════════════════════════════════════ -->
    <div class="tab-pane fade <?= $role === 'admin' ? 'show active' : '' ?>" id="panelAdmin" role="tabpanel">

        <!-- ── 1. Gestionar Cursos ── -->
        <h2 class="seccion-titulo"><i class="bi bi-book-fill me-2"></i>1. Gestionar Cursos</h2>

        <?php $pasosGestionCursos = [
            ['titulo' => 'Acceder al módulo de cursos', 'desc' => 'Desde el panel lateral, haz clic en <strong>Gestión de Cursos</strong>. Verás la lista de todos los cursos publicados en la plataforma.'],
            ['titulo' => 'Crear un nuevo curso', 'desc' => 'Haz clic en el botón <strong>"+ Nuevo Curso"</strong> de la esquina superior derecha. Completa el formulario con: título, descripción, nivel (Principiante / Intermedio / Avanzado), precio, instructor y portada (imagen JPG/PNG).'],
            ['titulo' => 'Agregar módulos y clases', 'desc' => 'Dentro del curso recién creado, usa el botón <strong>"Agregar Módulo"</strong>. Luego, dentro de cada módulo, usa <strong>"Agregar Clase"</strong> ingresando el título y la URL del video de YouTube o Vimeo.'],
            ['titulo' => 'Publicar o editar un curso', 'desc' => 'Desde la lista de cursos, haz clic en el ícono de lápiz <i class="bi bi-pencil-fill"></i> para editar cualquier dato. Para despublicarlo temporalmente, cambia su estado a <em>Borrador</em>.'],
            ['titulo' => 'Eliminar un curso', 'desc' => 'Haz clic en el ícono de papelera <i class="bi bi-trash3-fill text-danger"></i> y confirma la acción. <strong>¡Atención!</strong> Eliminar un curso borra también sus módulos, clases y registros de progreso de los estudiantes.'],
        ]; ?>
        <?php foreach ($pasosGestionCursos as $i => $paso): ?>
        <div class="paso-card card">
            <div class="card-body p-4">
                <div class="d-flex gap-3">
                    <div class="paso-numero paso-admin"><?= $i + 1 ?></div>
                    <div>
                        <h6 class="fw-bold mb-1"><?= $paso['titulo'] ?></h6>
                        <p class="text-muted mb-0 small" style="line-height:1.65;"><?= $paso['desc'] ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- ── 2. Registro de Auditoría ── -->
        <h2 class="seccion-titulo"><i class="bi bi-clock-history me-2"></i>2. Revisar el Registro de Auditoría</h2>

        <?php $pasosAuditoria = [
            ['titulo' => 'Abrir el módulo de Auditoría', 'desc' => 'Desde el menú lateral, haz clic en <strong>Auditoría</strong>. Verás una tabla cronológica con todos los eventos registrados en la plataforma.'],
            ['titulo' => 'Filtrar y buscar eventos', 'desc' => 'Usa los campos de filtro en la parte superior para buscar por <strong>usuario</strong>, <strong>rol</strong>, <strong>tipo de acción</strong> o rango de fechas.'],
            ['titulo' => 'Entender cada columna', 'desc' => '<strong>Acción</strong>: qué hizo el usuario. <strong>Detalles</strong>: descripción completa del evento. <strong>IP</strong>: dirección desde la que se realizó. <strong>Fecha</strong>: marca de tiempo exacta.'],
            ['titulo' => 'Exportar el registro', 'desc' => 'Si necesitas guardar el historial, usa la función de impresión del navegador (<kbd>Ctrl+P</kbd>) o copia la tabla directamente para pegarla en Excel.'],
        ]; ?>
        <?php foreach ($pasosAuditoria as $i => $paso): ?>
        <div class="paso-card card">
            <div class="card-body p-4">
                <div class="d-flex gap-3">
                    <div class="paso-numero paso-admin"><?= $i + 1 ?></div>
                    <div>
                        <h6 class="fw-bold mb-1"><?= $paso['titulo'] ?></h6>
                        <p class="text-muted mb-0 small" style="line-height:1.65;"><?= $paso['desc'] ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- ── 3. Aprobar pagos ── -->
        <h2 class="seccion-titulo"><i class="bi bi-credit-card-fill me-2"></i>3. Aprobar Pagos de Inscripción</h2>

        <?php $pasosPagos = [
            ['titulo' => 'Ir al módulo de Pagos', 'desc' => 'Desde el menú lateral, haz clic en <strong>Pagos</strong>. Verás la lista de todos los comprobantes subidos por los estudiantes con estado <em>Pendiente</em>.'],
            ['titulo' => 'Revisar el comprobante', 'desc' => 'Haz clic en el botón <strong>"Ver comprobante"</strong> (<i class="bi bi-eye"></i>) para abrir la imagen del recibo de pago en una ventana emergente.'],
            ['titulo' => 'Aprobar el pago', 'desc' => 'Si el comprobante es válido, haz clic en <strong>"Aprobar"</strong> (<i class="bi bi-check-circle-fill text-success"></i>). Esto cambia el estado de la inscripción a <em>Aprobado</em> y da acceso al estudiante al contenido del curso.'],
            ['titulo' => 'Rechazar el pago', 'desc' => 'Si el comprobante es inválido, borroso o no corresponde al monto, haz clic en <strong>"Rechazar"</strong> (<i class="bi bi-x-circle-fill text-danger"></i>). El sistema registrará la acción en Auditoría y el estudiante deberá volver a subir el comprobante.'],
        ]; ?>
        <?php foreach ($pasosPagos as $i => $paso): ?>
        <div class="paso-card card">
            <div class="card-body p-4">
                <div class="d-flex gap-3">
                    <div class="paso-numero paso-admin"><?= $i + 1 ?></div>
                    <div>
                        <h6 class="fw-bold mb-1"><?= $paso['titulo'] ?></h6>
                        <p class="text-muted mb-0 small" style="line-height:1.65;"><?= $paso['desc'] ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- ── 4. Gestionar Usuarios ── -->
        <h2 class="seccion-titulo"><i class="bi bi-people-fill me-2"></i>4. Crear y Eliminar Usuarios</h2>

        <?php $pasosUsuarios = [
            ['titulo' => 'Ir al módulo de Usuarios', 'desc' => 'Haz clic en <strong>Usuarios</strong> en el menú lateral para ver la lista de todos los usuarios registrados con su rol, correo y cantidad de cursos inscritos.'],
            ['titulo' => 'Crear un usuario nuevo', 'desc' => 'Haz clic en <strong>"+ Agregar Usuario"</strong> en la esquina superior derecha. Completa el formulario con: Nombre, Correo, Contraseña y Rol (Administrador o Estudiante) y haz clic en <strong>"Crear Cuenta"</strong>.'],
            ['titulo' => 'Eliminar un usuario', 'desc' => 'En la fila del usuario, haz clic en el botón rojo <strong>"Eliminar"</strong> y confirma la acción. El sistema eliminará en cascada sus inscripciones, pagos y notificaciones, preservando los registros de auditoría.'],
            ['titulo' => 'Restricción de seguridad', 'desc' => 'No puedes eliminar tu propia cuenta de administrador desde esta sección. Esta medida de seguridad evita bloqueos accidentales del sistema.'],
        ]; ?>
        <?php foreach ($pasosUsuarios as $i => $paso): ?>
        <div class="paso-card card">
            <div class="card-body p-4">
                <div class="d-flex gap-3">
                    <div class="paso-numero paso-admin"><?= $i + 1 ?></div>
                    <div>
                        <h6 class="fw-bold mb-1"><?= $paso['titulo'] ?></h6>
                        <p class="text-muted mb-0 small" style="line-height:1.65;"><?= $paso['desc'] ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <div class="tip-box warning mt-4">
            <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
            <strong>Buenas prácticas:</strong> Registra en la Auditoría cualquier cambio manual en la base de datos. Mantén al menos 2 administradores activos en el sistema para evitar pérdida de acceso.
        </div>

    </div><!-- /panelAdmin -->

    <!-- ══════════════════════════════════════════════════════════════════
         MANUAL DEL ESTUDIANTE
    ══════════════════════════════════════════════════════════════════════ -->
    <div class="tab-pane fade <?= $role !== 'admin' ? 'show active' : '' ?>" id="panelEstudiante" role="tabpanel">

        <!-- ── 1. Explorar e inscribirse ── -->
        <h2 class="seccion-titulo pink"><i class="bi bi-compass me-2"></i>1. Explorar y Matricularse en Cursos</h2>

        <?php $pasosExplorar = [
            ['titulo' => 'Buscar cursos disponibles', 'desc' => 'En el menú lateral, haz clic en <strong>Explorar Cursos</strong>. Verás todos los cursos publicados con su nivel, instructor y precio.'],
            ['titulo' => 'Ver los detalles de un curso', 'desc' => 'Haz clic en la tarjeta del curso que te interesa para ver su descripción completa, lista de módulos y clases, duración y precio de inscripción.'],
            ['titulo' => 'Solicitar inscripción', 'desc' => 'Haz clic en el botón <strong>"Inscribirme"</strong>. Se abrirá el proceso de pago para que puedas subir tu comprobante.'],
            ['titulo' => 'Esperar aprobación', 'desc' => 'Una vez que el administrador revise y apruebe tu pago, recibirás una notificación y el curso aparecerá en <strong>Mis Cursos</strong> con estado <em>Aprobado</em>.'],
        ]; ?>
        <?php foreach ($pasosExplorar as $i => $paso): ?>
        <div class="paso-card card">
            <div class="card-body p-4">
                <div class="d-flex gap-3">
                    <div class="paso-numero paso-student"><?= $i + 1 ?></div>
                    <div>
                        <h6 class="fw-bold mb-1"><?= $paso['titulo'] ?></h6>
                        <p class="text-muted mb-0 small" style="line-height:1.65;"><?= $paso['desc'] ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- ── 2. Reportar pago ── -->
        <h2 class="seccion-titulo pink"><i class="bi bi-receipt me-2"></i>2. Reportar un Pago (Subir Comprobante)</h2>

        <?php $pasosPago = [
            ['titulo' => 'Completar el proceso de inscripción', 'desc' => 'Después de hacer clic en "Inscribirme", se abrirá el formulario de pago. Selecciona el <strong>método de pago</strong> (Transferencia, Nequi, Daviplata, PSE) que corresponde a la transacción que realizaste.'],
            ['titulo' => 'Adjuntar el comprobante', 'desc' => 'Haz clic en el campo de carga de archivo o arrástralo. El comprobante debe ser una imagen <strong>JPG o PNG</strong> legible, donde se vea claramente el valor, la fecha y el número de transacción. Máximo 2 MB.'],
            ['titulo' => 'Añadir referencia (opcional)', 'desc' => 'Puedes añadir el número de referencia o transacción en el campo de texto para facilitar la verificación del administrador.'],
            ['titulo' => 'Enviar y esperar', 'desc' => 'Haz clic en <strong>"Enviar Comprobante"</strong>. Tu inscripción quedará en estado <em>Pendiente</em> hasta que el administrador la apruebe. Puedes ver el estado en <strong>Mis Cursos</strong>.'],
        ]; ?>
        <?php foreach ($pasosPago as $i => $paso): ?>
        <div class="paso-card card">
            <div class="card-body p-4">
                <div class="d-flex gap-3">
                    <div class="paso-numero paso-student"><?= $i + 1 ?></div>
                    <div>
                        <h6 class="fw-bold mb-1"><?= $paso['titulo'] ?></h6>
                        <p class="text-muted mb-0 small" style="line-height:1.65;"><?= $paso['desc'] ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <div class="tip-box warning">
            <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
            <strong>Errores comunes al subir el comprobante:</strong> Asegúrate de que la imagen no sea un PDF (usa JPG/PNG), que pese menos de 2 MB y que no esté borrosa. Si el archivo es rechazado, el sistema te mostrará el motivo.
        </div>

        <!-- ── 3. Avanzar en lecciones ── -->
        <h2 class="seccion-titulo pink mt-4"><i class="bi bi-play-circle-fill me-2"></i>3. Avanzar en las Lecciones</h2>

        <?php $pasosLecciones = [
            ['titulo' => 'Acceder a tu curso', 'desc' => 'En el menú lateral, haz clic en <strong>Mis Cursos</strong> y selecciona el curso activo. Verás el listado de módulos y clases con tu barra de progreso.'],
            ['titulo' => 'Ver una clase de video', 'desc' => 'Haz clic en el nombre de cualquier clase para abrir el reproductor de video. El contenido se carga directamente desde YouTube sin salir de la plataforma.'],
            ['titulo' => 'Marcar una clase como completada', 'desc' => 'Después de ver el video, haz clic en el botón <strong>"Marcar como completada"</strong> (verde). Tu progreso se actualizará automáticamente en la barra lateral.'],
            ['titulo' => 'Navegar entre clases', 'desc' => 'Usa los botones <strong>"Clase anterior"</strong> y <strong>"Siguiente clase"</strong> en la parte inferior del reproductor para avanzar sin volver al índice del curso.'],
            ['titulo' => 'Revisar tu progreso', 'desc' => 'El porcentaje de avance es visible tanto en la vista de la clase como en la página del curso. Al llegar al <strong>100%</strong>, se desbloqueará el Examen Final.'],
        ]; ?>
        <?php foreach ($pasosLecciones as $i => $paso): ?>
        <div class="paso-card card">
            <div class="card-body p-4">
                <div class="d-flex gap-3">
                    <div class="paso-numero paso-student"><?= $i + 1 ?></div>
                    <div>
                        <h6 class="fw-bold mb-1"><?= $paso['titulo'] ?></h6>
                        <p class="text-muted mb-0 small" style="line-height:1.65;"><?= $paso['desc'] ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- ── 4. Examen final ── -->
        <h2 class="seccion-titulo pink"><i class="bi bi-trophy-fill me-2"></i>4. Presentar el Examen Final</h2>

        <?php $pasosExamen = [
            ['titulo' => '¿Cuándo se activa el examen?', 'desc' => 'El botón de <strong>"Presentar Examen Final"</strong> se activa automáticamente cuando hayas completado el <strong>100%</strong> de las clases del curso. Aparece en la barra lateral de la vista del curso y en la vista de cada clase.'],
            ['titulo' => 'Iniciar el examen', 'desc' => 'Haz clic en el botón amarillo <strong>"¡Presentar Examen Final!"</strong>. Se cargará un cuestionario de 5 preguntas de selección múltiple relacionadas con la cocina.'],
            ['titulo' => 'Responder y enviar', 'desc' => 'Selecciona una respuesta para cada pregunta. Cuando termines, haz clic en <strong>"Enviar Examen"</strong> y confirma. <strong>No podrás cambiar las respuestas después de enviar.</strong>'],
            ['titulo' => 'Ver el resultado', 'desc' => 'El sistema calificará tu examen al instante. Necesitas un mínimo del <strong>80%</strong> (4 de 5 respuestas correctas) para aprobar.'],
            ['titulo' => 'Si apruebas', 'desc' => 'Tu curso cambia a estado <strong>"Aprobado 🎉"</strong> en el panel. El logro queda registrado en tu historial de la plataforma.'],
            ['titulo' => 'Si no apruebas', 'desc' => 'No hay problema. Puedes revisar las respuestas correctas, repasar el material del curso y volver a intentarlo cuantas veces quieras haciendo clic en <strong>"Volver a Intentarlo"</strong>.'],
        ]; ?>
        <?php foreach ($pasosExamen as $i => $paso): ?>
        <div class="paso-card card">
            <div class="card-body p-4">
                <div class="d-flex gap-3">
                    <div class="paso-numero paso-student"><?= $i + 1 ?></div>
                    <div>
                        <h6 class="fw-bold mb-1"><?= $paso['titulo'] ?></h6>
                        <p class="text-muted mb-0 small" style="line-height:1.65;"><?= $paso['desc'] ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- FAQ rápido -->
        <h2 class="seccion-titulo pink mt-4"><i class="bi bi-patch-question-fill me-2"></i>Preguntas Frecuentes</h2>
        <div class="accordion" id="faqAccordion">
            <?php $faqs = [
                ['p' => '¿Puedo desmarcar una clase que ya completé?', 'r' => 'Sí. En la vista de la clase, el botón verde "¡Clase completada!" actúa como un toggle. Al hacer clic de nuevo, se desmarcará y el porcentaje de progreso se ajustará automáticamente.'],
                ['p' => '¿Puedo tomar el examen más de una vez?', 'r' => 'Sí, puedes intentarlo cuantas veces necesites. Una vez que apruebes, el curso quedará marcado como "Aprobado" de forma definitiva y no tendrás que volver a presentarlo.'],
                ['p' => '¿Qué pasa si cierro el examen sin enviar?', 'r' => 'Tus respuestas no quedarán guardadas. Puedes ingresar nuevamente con el botón del examen y responder desde el principio.'],
                ['p' => '¿Cómo elimino mi cuenta?', 'r' => 'Ve a tu perfil (ícono de usuario en la esquina superior derecha → Editar Perfil) y en la sección "Zona de Peligro" encontrarás el botón de "Eliminar mi cuenta permanentemente". Esta acción es irreversible.'],
            ]; foreach ($faqs as $fi => $faq): ?>
            <div class="accordion-item faq-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq<?= $fi ?>">
                        <i class="bi bi-question-circle me-2 text-danger"></i><?= e($faq['p']) ?>
                    </button>
                </h2>
                <div id="faq<?= $fi ?>" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted small" style="line-height:1.7;"><?= e($faq['r']) ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="tip-box mt-4">
            <i class="bi bi-lightbulb-fill text-primary me-2"></i>
            <strong>Consejo:</strong> Completa las clases en orden para construir el conocimiento de forma progresiva. Cada módulo es la base del siguiente.
        </div>

    </div><!-- /panelEstudiante -->

</div><!-- /tab-content -->

<div class="text-center mt-5 text-muted small">
    <i class="bi bi-envelope-fill me-1"></i>
    ¿Tienes dudas adicionales? Contacta al administrador de la plataforma.
</div>

<?php
if ($role === 'admin') {
    include __DIR__ . '/admin/footer.php';
} else {
    include __DIR__ . '/estudiante/footer.php';
}
?>
