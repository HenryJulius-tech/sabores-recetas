<?php $title = $title ?? ''; $students = $students ?? []; $courses = $courses ?? []; ?>
<?php $selectedCourse = $_GET['curso'] ?? ''; $search = $_GET['search'] ?? ''; ?>
<style>
.progress-table { width: 100%; border-collapse: collapse; }
.progress-table th { background: var(--sidebar-bg); padding: .75rem 1rem; text-align: left; font-size: .85rem; text-transform: uppercase; letter-spacing: .5px; color: var(--text-muted); border-bottom: 2px solid var(--border); }
.progress-table td { padding: .75rem 1rem; border-bottom: 1px solid var(--border); vertical-align: middle; }
.progress-bar-sm { height: 8px; background: var(--border); border-radius: 4px; overflow: hidden; min-width: 100px; }
.progress-bar-sm div { height: 100%; border-radius: 4px; transition: width .3s; }
.filter-bar { display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
.filter-bar select, .filter-bar input { padding: .5rem .75rem; border: 1px solid var(--border); border-radius: var(--radius); background: var(--card-bg); color: var(--text); }
</style>
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem">
  <h1 style="margin:0;font-size:1.4rem">Progreso de Estudiantes</h1>
</div>
<form method="get" class="filter-bar">
  <select name="curso" onchange="this.form.submit()">
    <option value="">Todos los cursos</option>
    <?php foreach ($courses as $c): ?>
    <option value="<?= $c['id'] ?>" <?= $selectedCourse == $c['id'] ? 'selected' : '' ?>><?= e($c['title']) ?></option>
    <?php endforeach; ?>
  </select>
  <input type="text" name="search" placeholder="Buscar estudiante..." value="<?= e($search) ?>">
  <button type="submit" class="btn-modern btn-modern-primary btn-modern-sm">Filtrar</button>
  <?php if ($selectedCourse || $search): ?>
  <a href="<?= url('admin/progreso') ?>" class="btn-modern btn-modern-sm btn-modern-outline">Limpiar</a>
  <?php endif; ?>
</form>
<?php if (empty($students)): ?>
<div class="text-center py-5"><p class="text-muted">No hay datos de progreso disponibles.</p></div>
<?php else: ?>
<div style="overflow-x:auto">
<table class="progress-table">
  <thead>
    <tr>
      <th>Estudiante</th>
      <th>Curso</th>
      <th>Progreso</th>
      <th>Clases</th>
      <th>Exámenes</th>
      <th>Estado</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($students as $s): ?>
    <?php
    $barColor = $s['progress'] >= 80 ? '#4ade80' : ($s['progress'] >= 40 ? '#facc15' : '#ef4444');
    $statusLabel = $s['progress'] >= 100 ? 'Completado' : ($s['progress'] > 0 ? 'En progreso' : 'Sin iniciar');
    $statusColor = $s['progress'] >= 100 ? '#4ade80' : ($s['progress'] > 0 ? '#facc15' : '#ef4444');
    ?>
    <tr>
      <td><strong><?= e($s['fullname'] ?: $s['username']) ?></strong></td>
      <td><?= e($s['course_title']) ?></td>
      <td>
        <div style="display:flex;align-items:center;gap:.75rem">
          <div class="progress-bar-sm"><div style="width:<?= $s['progress'] ?>%;background:<?= $barColor ?>"></div></div>
          <span style="font-weight:600;font-size:.9rem"><?= $s['progress'] ?>%</span>
        </div>
      </td>
      <td><?= $s['completed_classes'] ?>/<?= $s['total_classes'] ?></td>
      <td><?= $s['passed_exams'] ?>/<?= $s['total_exams'] ?></td>
      <td><span style="color:<?= $statusColor ?>;font-weight:600"><?= $statusLabel ?></span></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
<?php endif; ?>

