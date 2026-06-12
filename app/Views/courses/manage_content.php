<?php $title = $title ?? ''; $curso = $curso ?? []; $modulos = $modulos ?? []; ?>
<style>
.content-section { margin-bottom: 2rem; }
.content-section h2 { font-size: 1.3rem; margin-bottom: 1rem; }
.module-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: var(--radius); margin-bottom: 1rem; }
.module-header { padding: 1rem 1.5rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); font-weight: 600; }
.module-header-actions { display: flex; gap: .5rem; }
.module-body { padding: 0; }
.class-row { display: flex; justify-content: space-between; align-items: center; padding: .75rem 1.5rem; border-top: 1px solid var(--border); }
.class-row:first-child { border-top: none; }
.class-actions { display: flex; gap: .25rem; }
.exam-row { display: flex; justify-content: space-between; align-items: center; padding: .75rem 1.5rem; border-top: 2px dashed var(--border); background: rgba(99,102,241,.05); }
.empty-state { padding: 2rem; text-align: center; color: var(--text-muted); }
</style>
<div class="content-section">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem">
    <h1 style="margin:0;font-size:1.4rem">Contenido: <?= e($curso['title']) ?></h1>
    <button class="btn btn-modern btn-modern-primary" onclick="showModuleModal()"><i class="bi bi-plus-lg"></i> Nuevo módulo</button>
  </div>
  <a href="<?= url('cursos') ?>" class="text-muted" style="text-decoration:none">&larr; Volver a cursos</a>
</div>
<?php if (empty($modulos)): ?>
<div class="empty-state">
  <i class="bi bi-journal-text" style="font-size:3rem;display:block;margin-bottom:1rem"></i>
  <p>Este curso no tiene módulos todavía. ¡Crea el primero!</p>
</div>
<?php else: ?>
<?php foreach ($modulos as $mod): ?>
<?php $classes = \App\Models\ClassModel::getAllByModule($mod['id']); $exam = \App\Models\Exam::findByModule($mod['id']); ?>
<div class="module-card" id="mod-<?= $mod['id'] ?>">
  <div class="module-header">
    <span><?= e($mod['title']) ?></span>
    <div class="module-header-actions">
      <button class="btn-modern btn-modern-sm btn-modern-outline" onclick="showClassModal(<?= $mod['id'] ?>)"><i class="bi bi-plus-lg"></i> Clase</button>
      <?php if (!$exam): ?>
      <button class="btn-modern btn-modern-sm btn-modern-outline" onclick="showExamModal(<?= $mod['id'] ?>)"><i class="bi bi-pencil-square"></i> Examen</button>
      <?php endif; ?>
      <button class="btn-modern btn-modern-sm btn-modern-outline" onclick="editModule(<?= $mod['id'] ?>, '<?= e($mod['title']) ?>', '<?= e($mod['description']) ?>')"><i class="bi bi-pencil"></i></button>
      <button class="btn-modern btn-modern-sm btn-modern-outline text-danger" onclick="deleteModule(<?= $mod['id'] ?>)"><i class="bi bi-trash"></i></button>
    </div>
  </div>
  <div class="module-body">
    <?php if (!empty($mod['description'])): ?>
    <div style="padding:.5rem 1.5rem;font-size:.9rem;color:var(--text-muted)"><?= e($mod['description']) ?></div>
    <?php endif; ?>
    <?php foreach ($classes as $cls): ?>
    <div class="class-row">
      <span><i class="bi bi-play-circle"></i> <?= e($cls['title']) ?> <span class="text-muted" style="font-size:.85rem"><?= e($cls['duration']) ?></span></span>
      <div class="class-actions">
        <button class="btn-modern btn-modern-sm btn-modern-outline" onclick="editClass(<?= $cls['id'] ?>, '<?= e($cls['title']) ?>', '<?= e($cls['description']) ?>', '<?= e($cls['video_url']) ?>', '<?= e($cls['duration']) ?>')"><i class="bi bi-pencil"></i></button>
        <button class="btn-modern btn-modern-sm btn-modern-outline text-danger" onclick="deleteClass(<?= $cls['id'] ?>)"><i class="bi bi-trash"></i></button>
      </div>
    </div>
    <?php endforeach; ?>
    <?php if ($exam): ?>
    <div class="exam-row">
      <span><i class="bi bi-pencil-square"></i> <strong><?= e($exam['title']) ?></strong> (Nota: <?= $exam['passing_score'] ?>/100 · Intentos: <?= $exam['max_attempts'] ?>)</span>
      <div>
        <button class="btn-modern btn-modern-sm btn-modern-outline" onclick="showExamQuestionModal(<?= $exam['id'] ?>)"><i class="bi bi-plus-lg"></i> Pregunta</button>
        <button class="btn-modern btn-modern-sm btn-modern-outline" onclick="editExam(<?= $exam['id'] ?>, '<?= e($exam['title']) ?>', <?= $exam['passing_score'] ?>, <?= $exam['max_attempts'] ?>)"><i class="bi bi-pencil"></i></button>
      </div>
    </div>
    <?php $preguntas = \App\Models\Question::getAllByExam($exam['id']); ?>
    <?php if (!empty($preguntas)): ?>
    <?php $qnum = 0; foreach ($preguntas as $pq): $qnum++; $opts = json_decode($pq['options'], true); ?>
    <div style="display:flex;justify-content:space-between;align-items:center;padding:.5rem 1.5rem .5rem 3rem;border-top:1px solid var(--border);font-size:.9rem">
      <span><?= $qnum ?>. <?= e($pq['question']) ?> <span class="text-muted">(<?= e($pq['correct_answer']) ?>)</span></span>
      <button class="btn-modern btn-modern-sm btn-modern-outline text-danger" onclick="deleteQuestion(<?= $pq['id'] ?>)"><i class="bi bi-x"></i></button>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
    <?php else: ?>
    <div class="exam-row">
      <span class="text-muted"><i class="bi bi-pencil-square"></i> Sin examen</span>
    </div>
    <?php endif; ?>
  </div>
</div>
<?php endforeach; ?>
<?php endif; ?>
<div class="modal fade" id="moduleModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
  <form id="moduleForm" method="post">
    <div class="modal-header"><h5 class="modal-title" id="moduleModalTitle">Nuevo Módulo</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
      <input type="hidden" name="curso_id" value="<?= $curso['id'] ?>">
      <input type="hidden" name="modulo_id" id="modulo_id" value="">
      <div class="mb-3"><label class="form-label">Título</label><input type="text" name="title" id="mod_title" class="form-control" required></div>
      <div class="mb-3"><label class="form-label">Descripción</label><textarea name="description" id="mod_description" class="form-control" rows="3"></textarea></div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      <button type="submit" class="btn btn-primary">Guardar</button>
    </div>
  </form>
</div></div></div>
<div class="modal fade" id="classModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
  <form id="classForm" method="post">
    <div class="modal-header"><h5 class="modal-title" id="classModalTitle">Nueva Clase</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
      <input type="hidden" name="modulo_id" id="class_modulo_id" value="">
      <input type="hidden" name="clase_id" id="clase_id" value="">
      <div class="mb-3"><label class="form-label">Título</label><input type="text" name="title" id="class_title" class="form-control" required></div>
      <div class="mb-3"><label class="form-label">Descripción</label><textarea name="description" id="class_description" class="form-control" rows="2"></textarea></div>
      <div class="mb-3"><label class="form-label">URL del video (YouTube)</label><input type="url" name="video_url" id="class_video_url" class="form-control" placeholder="https://www.youtube.com/watch?v=..." required></div>
      <div class="mb-3"><label class="form-label">Duración (ej: 15:30)</label><input type="text" name="duration" id="class_duration" class="form-control" placeholder="00:00"></div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      <button type="submit" class="btn btn-primary">Guardar</button>
    </div>
  </form>
</div></div></div>
<div class="modal fade" id="examModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
  <form id="examFormModal" method="post">
    <div class="modal-header"><h5 class="modal-title" id="examModalTitle">Configurar Examen</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
      <input type="hidden" name="modulo_id" id="exam_modulo_id" value="">
      <input type="hidden" name="examen_id" id="examen_id" value="">
      <div class="mb-3"><label class="form-label">Título del examen</label><input type="text" name="title" id="exam_title" class="form-control" required></div>
      <div class="mb-3"><label class="form-label">Descripción</label><textarea name="description" id="exam_description" class="form-control" rows="2"></textarea></div>
      <div class="row">
        <div class="col-md-4 mb-3"><label class="form-label">Nota mínima</label><input type="number" name="passing_score" id="exam_passing_score" class="form-control" value="70" min="1" max="100"></div>
        <div class="col-md-4 mb-3"><label class="form-label">Intentos máximos</label><input type="number" name="max_attempts" id="exam_max_attempts" class="form-control" value="3" min="1" max="10"></div>
        <div class="col-md-4 mb-3"><label class="form-label">Tiempo (min)</label><input type="number" name="time_limit_min" id="exam_time_limit" class="form-control" value="30" min="1"></div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      <button type="submit" class="btn btn-primary">Guardar</button>
    </div>
  </form>
</div></div></div>
<div class="modal fade" id="questionModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content">
  <form id="questionForm" method="post">
    <div class="modal-header"><h5 class="modal-title">Agregar Pregunta</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
      <input type="hidden" name="examen_id" id="q_examen_id" value="">
      <div class="mb-3"><label class="form-label">Pregunta</label><textarea name="question" id="q_question" class="form-control" rows="2" required></textarea></div>
      <div class="mb-3"><label class="form-label">Opciones (una por línea)</label><textarea name="options_text" id="q_options" class="form-control" rows="5" placeholder="Opción 1&#10;Opción 2&#10;Opción 3&#10;Opción 4" required></textarea></div>
      <div class="mb-3"><label class="form-label">Respuesta correcta (debe coincidir exactamente con una opción)</label><input type="text" name="correct_answer" id="q_correct" class="form-control" required></div>
      <div class="mb-3"><label class="form-label">Puntos</label><input type="number" name="points" id="q_points" class="form-control" value="10" min="1"></div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      <button type="submit" class="btn btn-primary">Agregar</button>
    </div>
  </form>
</div></div></div>
<script>
function showModuleModal() { document.getElementById('moduleModalTitle').textContent = 'Nuevo Módulo'; document.getElementById('modulo_id').value = ''; document.getElementById('mod_title').value = ''; document.getElementById('mod_description').value = ''; new bootstrap.Modal(document.getElementById('moduleModal')).show(); }
function editModule(id, title, desc) { document.getElementById('moduleModalTitle').textContent = 'Editar Módulo'; document.getElementById('modulo_id').value = id; document.getElementById('mod_title').value = title; document.getElementById('mod_description').value = desc; new bootstrap.Modal(document.getElementById('moduleModal')).show(); }
function showClassModal(modId) { document.getElementById('classModalTitle').textContent = 'Nueva Clase'; document.getElementById('class_modulo_id').value = modId; document.getElementById('clase_id').value = ''; document.getElementById('class_title').value = ''; document.getElementById('class_description').value = ''; document.getElementById('class_video_url').value = ''; document.getElementById('class_duration').value = ''; new bootstrap.Modal(document.getElementById('classModal')).show(); }
function editClass(id, title, desc, url, dur) { document.getElementById('classModalTitle').textContent = 'Editar Clase'; document.getElementById('class_modulo_id').value = ''; document.getElementById('clase_id').value = id; document.getElementById('class_title').value = title; document.getElementById('class_description').value = desc; document.getElementById('class_video_url').value = url; document.getElementById('class_duration').value = dur; new bootstrap.Modal(document.getElementById('classModal')).show(); }
function showExamModal(modId) { document.getElementById('examModalTitle').textContent = 'Nuevo Examen'; document.getElementById('exam_modulo_id').value = modId; document.getElementById('examen_id').value = ''; document.getElementById('exam_title').value = ''; document.getElementById('exam_description').value = ''; document.getElementById('exam_passing_score').value = 70; document.getElementById('exam_max_attempts').value = 3; document.getElementById('exam_time_limit').value = 30; new bootstrap.Modal(document.getElementById('examModal')).show(); }
function editExam(id, title, passing, attempts) { document.getElementById('examModalTitle').textContent = 'Editar Examen'; document.getElementById('exam_modulo_id').value = ''; document.getElementById('examen_id').value = id; document.getElementById('exam_title').value = title; document.getElementById('exam_description').value = ''; document.getElementById('exam_passing_score').value = passing; document.getElementById('exam_max_attempts').value = attempts; new bootstrap.Modal(document.getElementById('examModal')).show(); }
function showExamQuestionModal(examId) { document.getElementById('q_examen_id').value = examId; document.getElementById('q_question').value = ''; document.getElementById('q_options').value = ''; document.getElementById('q_correct').value = ''; document.getElementById('q_points').value = 10; new bootstrap.Modal(document.getElementById('questionModal')).show(); }
function deleteModule(id) { if (!confirm('¿Eliminar este módulo y todo su contenido?')) return; fetch('<?= url('api/cursos/' . $curso['id'] . '/modulos/eliminar/') ?>' + id, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(function(r) { return r.json(); }).then(function(d) { if (d.success) location.reload(); }); }
function deleteClass(id) { if (!confirm('¿Eliminar esta clase?')) return; fetch('<?= url('api/cursos/' . $curso['id'] . '/clases/eliminar/') ?>' + id, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(function(r) { return r.json(); }).then(function(d) { if (d.success) location.reload(); }); }
function deleteQuestion(id) { if (!confirm('¿Eliminar esta pregunta?')) return; fetch('<?= url('api/preguntas/eliminar/') ?>' + id, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(function(r) { return r.json(); }).then(function(d) { if (d.success) location.reload(); }); }
document.getElementById('moduleForm').addEventListener('submit', function(e) { e.preventDefault(); var f = this; var id = f.modulo_id.value; var url = id ? '<?= url('api/cursos/' . $curso['id'] . '/modulos/actualizar/') ?>' + id : '<?= url('api/cursos/' . $curso['id'] . '/modulos/guardar') ?>'; var fd = new FormData(f); fetch(url, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(function(r) { return r.json(); }).then(function(d) { if (d.success) location.reload(); }); });
document.getElementById('classForm').addEventListener('submit', function(e) { e.preventDefault(); var f = this; var id = f.clase_id.value; var url = id ? '<?= url('api/cursos/' . $curso['id'] . '/clases/actualizar/') ?>' + id : '<?= url('api/cursos/' . $curso['id'] . '/clases/guardar') ?>'; var fd = new FormData(f); if (!id) fd.set('modulo_id', f.class_modulo_id.value); fetch(url, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(function(r) { return r.json(); }).then(function(d) { if (d.success) location.reload(); }); });
document.getElementById('examFormModal').addEventListener('submit', function(e) { e.preventDefault(); var f = this; var id = f.examen_id.value; var url = id ? '<?= url('api/cursos/' . $curso['id'] . '/examenes/actualizar/') ?>' + id : '<?= url('api/cursos/' . $curso['id'] . '/examenes/guardar') ?>'; var fd = new FormData(f); fetch(url, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(function(r) { return r.json(); }).then(function(d) { if (d.success) location.reload(); }); });
document.getElementById('questionForm').addEventListener('submit', function(e) { e.preventDefault(); var f = this; var opts = f.options_text.value.split('\n').filter(function(l) { return l.trim(); }); var fd = new FormData(); fd.append('examen_id', f.examen_id.value); fd.append('question', f.question.value); fd.append('options', JSON.stringify(opts)); fd.append('correct_answer', f.correct_answer.value); fd.append('points', f.points.value); fetch('<?= url('api/examenes/') ?>' + f.examen_id.value + '/preguntas/guardar', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(function(r) { return r.json(); }).then(function(d) { if (d.success) location.reload(); }); });
</script>

