<?php $title = $title ?? ''; $curso = $curso ?? []; $moduleData = $moduleData ?? []; ?>
<?php $progress = $progress ?? 0; $completedCount = $completedCount ?? 0; $totalClasses = $totalClasses ?? 0; ?>
<style>
.course-header {
    background: linear-gradient(135deg, var(--primary), #1a1a2e);
    color: white; padding: 2rem; border-radius: var(--radius);
    margin-bottom: 2rem;
}
.course-header h1 { font-size: 1.8rem; margin-bottom: 0.5rem; }
.course-header p { opacity: .85; margin-bottom: 1rem; }
.progress-info { display: flex; justify-content: space-between; font-size: .85rem; opacity: .8; margin-bottom: .5rem; }
.progress-bg { height: 10px; background: rgba(255,255,255,.2); border-radius: 5px; overflow: hidden; }
.progress-fill { height: 100%; background: #4ade80; border-radius: 5px; transition: width .5s ease; }
.module-card {
    background: var(--card-bg); border: 1px solid var(--border);
    border-radius: var(--radius); margin-bottom: 1rem; overflow: hidden;
}
.module-header {
    padding: 1rem 1.5rem; background: var(--sidebar-bg);
    cursor: pointer; display: flex; justify-content: space-between;
    align-items: center; font-weight: 600; user-select: none;
}
.module-header:hover { background: rgba(99,102,241,.1); }
.module-header .arrow { transition: transform .25s; font-size: .85rem; }
.module-header.collapsed .arrow { transform: rotate(-90deg); }
.module-body { padding: 0; }
.class-item {
    display: flex; align-items: center; padding: .85rem 1.5rem;
    border-top: 1px solid var(--border); transition: background .2s;
    text-decoration: none; color: var(--text);
}
.class-item:hover { background: rgba(99,102,241,.05); }
.class-item.completed { opacity: .7; }
.class-status {
    width: 30px; height: 30px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin-right: 1rem; font-size: .8rem; flex-shrink: 0;
}
.class-status.done { background: #4ade80; color: #fff; }
.class-status.pending { background: var(--border); color: var(--text); }
.class-info { flex: 1; }
.class-info .class-title { font-weight: 500; }
.class-info .class-meta { font-size: .78rem; color: var(--text-muted); margin-top: 2px; }
.exam-section {
    padding: 1rem 1.5rem; border-top: 2px dashed var(--border);
    display: flex; justify-content: space-between; align-items: center;
}
.exam-section .exam-title { font-weight: 600; }
.exam-section .exam-meta { font-size: .82rem; color: var(--text-muted); }
.empty-state {
    text-align: center; padding: 4rem 2rem;
    background: var(--card-bg); border-radius: var(--radius);
    border: 2px dashed var(--border);
}
.empty-state i { font-size: 3rem; color: var(--text-muted); margin-bottom: 1rem; display: block; }
.empty-state p { color: var(--text-muted); font-size: 1.05rem; }
</style>
<div class="course-header">
  <h1><?= e($curso['title']) ?></h1>
  <p><?= e($curso['description']) ?></p>
  <div>
    <div class="progress-info">
      <span>Progreso</span>
      <span><?= $completedCount ?>/<?= $totalClasses ?> clases · <?= $progress ?>%</span>
    </div>
    <div class="progress-bg"><div class="progress-fill" style="width:<?= $progress ?>%"></div></div>
  </div>
</div>
<?php if (empty($moduleData)): ?>
<div class="empty-state">
  <i class="bi bi-journal-text"></i>
  <p>Este curso aún no tiene clases creadas.</p>
  <small class="text-muted">El contenido será agregado próximamente por el instructor.</small>
</div>
<?php else: ?>
<?php foreach ($moduleData as $idx => $md): ?>
<?php $mod = $md['module']; $classes = $md['classes']; $exam = $md['exam']; ?>
<div class="module-card">
  <div class="module-header<?= $idx > 0 ? ' collapsed' : '' ?>" onclick="toggleModule(this)">
    <span><i class="bi bi-folder2-open me-2"></i><?= e($mod['title']) ?></span>
    <div>
      <span class="text-muted" style="font-size:.8rem;margin-right:.75rem"><?= count($classes) ?> clases</span>
      <span class="arrow"><i class="bi bi-chevron-down"></i></span>
    </div>
  </div>
  <div class="module-body" style="display:<?= $idx > 0 ? 'none' : '' ?>">
    <?php if (!empty($mod['description'])): ?>
    <div style="padding:.75rem 1.5rem;font-size:.88rem;color:var(--text-muted);border-bottom:1px solid var(--border)">
      <?= e($mod['description']) ?>
    </div>
    <?php endif; ?>
    <?php foreach ($classes as $cls): ?>
    <a href="<?= url('curso/' . $curso['id'] . '/clase/' . $cls['id']) ?>" class="class-item<?= $cls['is_completed'] ? ' completed' : '' ?>">
      <div class="class-status <?= $cls['is_completed'] ? 'done' : 'pending' ?>">
        <?= $cls['is_completed'] ? '<i class="bi bi-check-lg"></i>' : $cls['orden'] ?>
      </div>
      <div class="class-info">
        <div class="class-title"><?= e($cls['title']) ?></div>
        <div class="class-meta">
          <?php if (!empty($cls['duration'])): ?><i class="bi bi-clock me-1"></i><?= e($cls['duration']) ?><?php endif; ?>
          <?php if ($cls['is_completed']): ?> · <span style="color:#4ade80"><i class="bi bi-check-circle"></i> Completada</span><?php endif; ?>
        </div>
      </div>
      <i class="bi bi-play-circle-fill" style="color:var(--primary);font-size:1.3rem;opacity:.6"></i>
    </a>
    <?php endforeach; ?>
    <?php if ($exam): ?>
    <div class="exam-section">
      <div>
        <div class="exam-title"><i class="bi bi-pencil-square me-1"></i><?= e($exam['title']) ?></div>
        <div class="exam-meta">Nota mínima: <?= $exam['passing_score'] ?>/100 · Intentos: <?= $md['exam_attempts'] ?>/<?= $exam['max_attempts'] ?></div>
      </div>
      <div>
        <?php if ($md['exam_passed']): ?>
        <span style="color:#4ade80;font-weight:600"><i class="bi bi-check-circle-fill"></i> Aprobado</span>
        <?php elseif ($md['exam_attempts'] >= $exam['max_attempts']): ?>
        <span style="color:#ef4444"><i class="bi bi-x-circle-fill"></i> Sin intentos</span>
        <?php else: ?>
        <a href="<?= url('curso/' . $curso['id'] . '/examen/' . $exam['id']) ?>" class="btn btn-modern btn-modern-primary btn-modern-sm">Presentar examen</a>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>
<?php endforeach; ?>
<?php endif; ?>
<script>
function toggleModule(el) {
    el.classList.toggle('collapsed');
    var body = el.nextElementSibling;
    body.style.display = body.style.display === 'none' ? '' : 'none';
}
</script>
