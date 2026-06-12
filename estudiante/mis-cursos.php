<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/functions.php';
requireEstudiante();

$userId = session_userId();

// Procesamiento de pago delegado a procesar_pago.php de forma asíncrona

$inscripciones = db_fetchAll("SELECT i.*, c.titulo, c.imagen, c.nivel, c.instructor, c.precio, c.duracion,
    (SELECT id FROM pagos WHERE inscripcion_id=i.id LIMIT 1) as tiene_pago,
    (SELECT estado FROM pagos WHERE inscripcion_id=i.id ORDER BY id DESC LIMIT 1) as pago_estado,
    COALESCE((SELECT progreso_porcentaje FROM progreso_estudiantes WHERE usuario_id=i.user_id AND curso_id=i.curso_id LIMIT 1), 0) as progreso,
    COALESCE((SELECT estado_curso FROM progreso_estudiantes WHERE usuario_id=i.user_id AND curso_id=i.curso_id LIMIT 1), 'Inscrito') as estado_curso
    FROM inscripciones i JOIN cursos c ON i.curso_id=c.id WHERE i.user_id=? ORDER BY i.created_at DESC", [$userId]);

$titulo = 'Mis Cursos';
include __DIR__ . '/header.php';
?>
<div class="mb-5">
    <h2 class="fw-bold" style="color:#1a1a2e;">Mis Cursos y Progreso</h2>
    <p class="text-muted fs-5">Aquí encuentras todos los cursos a los que te has inscrito.</p>
</div>

<?php if (empty($inscripciones)): ?>
    <div class="text-center py-5 bg-white rounded-4 shadow-sm border-0" style="padding: 60px 20px;">
        <i class="bi bi-journal-x" style="font-size:80px;color:#e9ecef;"></i>
        <h4 class="fw-bold text-dark mt-4">Aún no tienes cursos</h4>
        <p class="text-muted mb-4 fs-5">El conocimiento te espera, ¡inscríbete en tu primer curso!</p>
        <a href="cursos.php" class="btn btn-primary rounded-pill btn-lg px-5 fw-bold"><i class="bi bi-search me-2"></i> Explorar Catálogo</a>
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($inscripciones as $i): ?>
            <div class="col-md-6 col-lg-4 col-xl-4">
                <div class="card course-card h-100 border-0 shadow-sm">
                    <div class="course-img-wrapper">
                        <img src="<?= upload('cursos/' . $i['imagen']) ?>" alt="<?= e($i['titulo']) ?>">
                    </div>
                    <div class="card-body d-flex flex-column p-4">
                        <h5 class="fw-bold mb-3" style="color:#1a1a2e;"><?= e($i['titulo']) ?></h5>
                        
                        <div class="mb-4 d-flex flex-wrap gap-2">
                            <?= nivelBadge($i['nivel']) ?>
                            <?= estadoBadge($i['estado']) ?>
                            <?php if ($i['pago_estado']): ?>
                                <span class="badge bg-info text-dark">Pago <?= e($i['pago_estado']) ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="text-muted small flex-grow-1 mb-4">
                            <div class="mb-2"><i class="bi bi-person-badge text-primary me-2"></i><strong>Instructor:</strong> <?= e($i['instructor']) ?></div>
                            <div><i class="bi bi-clock-history text-primary me-2"></i><strong>Duración:</strong> <?= e($i['duracion'] ?: 'Variable') ?></div>
                        </div>

                        <div class="pt-3 border-top mt-auto">
                            <?php if ($i['estado'] === 'aprobado'): ?>
                                <!-- Barra de progreso del curso -->
                                <?php if ($i['progreso'] > 0 || $i['estado_curso'] !== 'Inscrito'): ?>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="text-muted fw-500">Tu progreso</small>
                                        <?php if ($i['estado_curso'] === 'Aprobado'): ?>
                                            <span class="badge bg-success rounded-pill"><i class="bi bi-trophy-fill me-1"></i>Aprobado</span>
                                        <?php else: ?>
                                            <small class="fw-bold <?= $i['progreso'] >= 100 ? 'text-warning' : 'text-primary' ?>"><?= $i['progreso'] ?>%</small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="progress rounded-pill" style="height:8px;">
                                        <div class="progress-bar <?= $i['estado_curso'] === 'Aprobado' ? 'bg-success' : ($i['progreso'] >= 100 ? 'bg-warning' : 'bg-primary') ?> rounded-pill"
                                             role="progressbar"
                                             style="width:<?= $i['estado_curso'] === 'Aprobado' ? 100 : $i['progreso'] ?>%;"
                                             aria-valuenow="<?= $i['progreso'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php if ($i['estado_curso'] === 'Aprobado'): ?>
                                    <a href="ver-curso.php?id=<?= $i['curso_id'] ?>" class="btn btn-success w-100 rounded-pill fw-bold"><i class="bi bi-trophy-fill me-2"></i>Ver Curso Aprobado ✓</a>
                                <?php elseif ($i['progreso'] >= 100): ?>
                                    <a href="examen.php?id=<?= $i['curso_id'] ?>" class="btn btn-warning w-100 rounded-pill fw-bold"><i class="bi bi-trophy-fill me-2"></i>¡Presentar Examen!</a>
                                <?php else: ?>
                                    <a href="ver-curso.php?id=<?= $i['curso_id'] ?>" class="btn btn-success w-100 rounded-pill fw-bold"><i class="bi bi-play-circle-fill me-2"></i><?= $i['progreso'] > 0 ? 'Continuar' : 'Acceder a las Clases' ?></a>
                                <?php endif; ?>
                            <?php elseif ($i['estado'] === 'pendiente'): ?>
                                <?php if (!$i['tiene_pago']): ?>
                                    <button class="btn btn-warning w-100 rounded-pill fw-bold" 
                                            data-open-pago="true"
                                            data-insc-id="<?= $i['id'] ?>"
                                            data-precio="<?= format_cop($i['precio']) ?>"
                                            data-titulo="<?= e($i['titulo']) ?>">
                                        <i class="bi bi-credit-card me-2"></i> Subir comprobante de pago
                                    </button>
                                <?php else: ?>
                                    <div class="alert alert-light text-center border rounded-pill py-2 mb-0 text-muted">
                                        <i class="bi bi-hourglass-split text-warning me-2"></i> Esperando aprobación
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Modal de Pago Universal (Unificado para evitar parpadeos y bugs en el loop) -->
<div class="modal fade" id="pagoModalUniversal" tabindex="-1" aria-labelledby="pagoModalUniversalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form id="formReportarPago" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="inscripcion_id" id="pagoInscripcionId">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark" id="pagoModalUniversalLabel">Reportar Pago</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="alert alert-info rounded-3 mb-4 d-flex align-items-center">
                        <i class="bi bi-info-circle-fill me-3 fs-4 text-primary"></i>
                        <div>
                            <span class="text-muted small d-block">Curso seleccionado:</span>
                            <span class="fw-bold text-dark d-block mb-1" id="pagoTituloCurso">Curso</span>
                            <span class="text-muted small d-block">Monto a pagar:</span>
                            <strong class="fs-5 text-dark" id="pagoMonto"></strong>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="pagoMetodo" class="form-label fw-bold text-dark">Método de pago utilizado</label>
                        <select name="metodo" id="pagoMetodo" class="form-select form-select-lg rounded-3" required>
                            <option value="">Seleccionar...</option>
                            <option value="transferencia">Transferencia Bancaria</option>
                            <option value="tarjeta">Tarjeta de Crédito</option>
                            <option value="paypal">PayPal</option>
                            <option value="efectivo">Efectivo</option>
                        </select>
                    </div>
                    
                    <div class="mb-2">
                        <label class="form-label fw-bold text-dark">Subir Comprobante (opcional)</label>
                        <div class="file-upload-zone border-2 border-dashed rounded-3 p-4 text-center cursor-pointer" style="border-color: #cbd5e1; background-color: #f8fafc; transition: all 0.2s ease;">
                            <i class="bi bi-cloud-arrow-up text-primary fs-1"></i>
                            <p class="mb-1 fw-semibold text-dark mt-2">Arrastra tu comprobante aquí o haz clic para buscar</p>
                            <p class="text-muted small mb-0">Formatos: JPG, PNG, WEBP, PDF (Máx. 2MB)</p>
                            <input type="file" name="comprobante" id="pagoComprobanteInput" class="d-none" accept="image/*,application/pdf">
                        </div>
                        <div class="file-name alert alert-success py-2 px-3 mt-3 mb-0 rounded-3 text-start" style="display: none;"></div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="submit" class="btn btn-primary rounded-pill w-100 fw-bold py-2">Enviar Comprobante</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.course-card { border-radius: 16px; overflow: hidden; transition: all 0.3s ease; background: #fff; }
.course-card:hover { transform: translateY(-6px); box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important; }
.course-img-wrapper { height: 160px; overflow: hidden; }
.course-img-wrapper img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
.course-card:hover .course-img-wrapper img { transform: scale(1.05); }
.modal-backdrop.show { opacity: 0.6; }
</style>
<?php include __DIR__ . '/footer.php'; ?>
