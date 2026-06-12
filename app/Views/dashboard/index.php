<!-- Metric Cards -->
<div class="row g-3 mb-4">
  <div class="col-6 col-md-4 col-xl-2">
    <div class="metric-card d-flex flex-column">
      <div class="d-flex align-items-center gap-3">
        <div class="metric-icon"><i class="bi bi-arrow-down-circle"></i></div>
        <div class="flex-grow-1 min-w-0">
          <div class="metric-value fs-4"><?= format_cop($total_ingresos) ?></div>
          <div class="metric-label">Ingresos</div>
        </div>
      </div>
      <div class="mt-2 small text-muted">Total acumulado</div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="metric-card d-flex flex-column">
      <div class="d-flex align-items-center gap-3">
        <div class="metric-icon"><i class="bi bi-arrow-up-circle"></i></div>
        <div class="flex-grow-1 min-w-0">
          <div class="metric-value fs-4"><?= format_cop($total_gastos) ?></div>
          <div class="metric-label">Gastos</div>
        </div>
      </div>
      <div class="mt-2 small text-muted">Total registrado</div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="metric-card d-flex flex-column">
      <div class="d-flex align-items-center gap-3">
        <div class="metric-icon"><i class="bi bi-wallet2"></i></div>
        <div class="flex-grow-1 min-w-0">
          <div class="metric-value fs-4 <?= $balance >= 0 ? '' : 'text-danger' ?>"><?= format_cop($balance) ?></div>
          <div class="metric-label">Balance</div>
        </div>
      </div>
      <div class="mt-2 small text-muted"><?= $balance >= 0 ? 'Positivo' : 'Negativo' ?></div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="metric-card d-flex flex-column">
      <div class="d-flex align-items-center gap-3">
        <div class="metric-icon"><i class="bi bi-inbox"></i></div>
        <div class="flex-grow-1 min-w-0">
          <div class="metric-value fs-4"><?= $compras_pendientes ?></div>
          <div class="metric-label">Pendientes</div>
        </div>
      </div>
      <div class="mt-2 small text-muted">Matrículas por revisar</div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="metric-card d-flex flex-column">
      <div class="d-flex align-items-center gap-3">
        <div class="metric-icon"><i class="bi bi-mortarboard"></i></div>
        <div class="flex-grow-1 min-w-0">
          <div class="metric-value fs-4"><?= $total_productos ?></div>
          <div class="metric-label">Cursos</div>
        </div>
      </div>
      <div class="mt-2 small text-muted"><?= $cursos_activos ?> activos / <?= $cursos_destacados ?> destacados</div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="metric-card d-flex flex-column">
      <div class="d-flex align-items-center gap-3">
        <div class="metric-icon"><i class="bi bi-people"></i></div>
        <div class="flex-grow-1 min-w-0">
          <div class="metric-value fs-4"><?= $total_usuarios ?></div>
          <div class="metric-label">Usuarios</div>
        </div>
      </div>
      <div class="mt-2 small text-muted"><?= $matriculas_aprobadas ?> matrículas exitosas</div>
    </div>
  </div>
</div>

<!-- Charts + Top Courses Row -->
<div class="row g-3 mb-4">
  <div class="col-xl-7">
    <div class="card-modern h-100">
      <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0 fw-bold"><i class="bi bi-graph-up me-2 text-primary"></i>Flujo de Caja</h5>
        <div class="btn-group btn-group-sm">
          <button class="btn-modern btn-modern-outline btn-modern-sm chart-filter active" data-filter="diario">Diario</button>
          <button class="btn-modern btn-modern-outline btn-modern-sm chart-filter" data-filter="semanal">Semanal</button>
          <button class="btn-modern btn-modern-outline btn-modern-sm chart-filter" data-filter="mensual">Mensual</button>
        </div>
      </div>
      <div class="card-body">
        <canvas id="financeChart" height="220"></canvas>
      </div>
    </div>
  </div>
  <div class="col-xl-5">
    <div class="card-modern h-100">
      <div class="card-header">
        <h5 class="mb-0 fw-bold"><i class="bi bi-pie-chart me-2 text-warning"></i>Cursos por Categoría</h5>
      </div>
      <div class="card-body">
        <?php if (empty($cursos_por_categoria)): ?>
        <div class="empty-state py-3"><i class="bi bi-tags"></i><p>Sin categorías</p></div>
        <?php else: ?>
        <canvas id="categoryChart" height="180"></canvas>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Top Courses -->
<div class="card-modern mb-4">
  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h5 class="mb-0 fw-bold"><i class="bi bi-trophy me-2 text-warning"></i>Top Cursos Más Vendidos</h5>
    <a href="<?= url('cursos') ?>" class="btn-modern btn-modern-outline btn-modern-sm"><i class="bi bi-arrow-right me-1"></i>Ver todos</a>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table-modern mb-0">
        <thead><tr><th style="width:40px">#</th><th>Curso</th><th>Instructor</th><th>Nivel</th><th>Precio</th><th>Matrículas</th><th>Total Vendido</th></tr></thead>
        <tbody>
          <?php if (empty($top_cursos)): ?>
          <tr><td colspan="7"><div class="empty-state"><i class="bi bi-mortarboard"></i><p>No hay cursos con matrículas aún</p></div></td></tr>
          <?php else: ?>
          <?php $rank = 1; foreach ($top_cursos as $c): ?>
          <tr>
            <td><span class="badge bg-<?= $rank === 1 ? 'warning text-dark' : ($rank === 2 ? 'secondary' : ($rank === 3 ? 'danger' : 'light text-muted')) ?> rounded-circle px-2 py-1"><?= $rank ?></span></td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <?php if (!empty($c['image_url'])): ?>
                  <img src="<?= upload_url('courses', $c['image_url']) ?>" style="width:36px;height:36px;object-fit:cover;border-radius:6px;">
                <?php else: ?>
                  <div class="bg-light rounded" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center"><i class="bi bi-book text-muted small"></i></div>
                <?php endif; ?>
                <span class="fw-bold small"><?= e($c['title']) ?></span>
              </div>
            </td>
            <td class="small"><?= e($c['instructor']) ?></td>
            <td><?= levelBadge($c['level']) ?></td>
            <td class="fw-bold"><?= format_cop($c['price']) ?></td>
            <td><span class="badge-status approved"><?= (int)$c['matriculas'] ?> mats</span></td>
            <td class="fw-bold text-success"><?= format_cop($c['total_vendido']) ?></td>
          </tr>
          <?php $rank++; endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Bottom Row: Recent Movements + Recent Enrollments -->
<div class="row g-3 mb-4">
  <div class="col-lg-6">
    <div class="card-modern h-100">
      <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0 fw-bold"><i class="bi bi-arrow-left-right me-2 text-success"></i>Últimos Movimientos</h5>
        <a href="<?= url('movimientos') ?>" class="btn-modern btn-modern-outline btn-modern-sm">Ver más</a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table-modern mb-0">
            <thead><tr><th>Fecha</th><th>Tipo</th><th>Monto</th><th>Descripción</th><th>Estado</th></tr></thead>
            <tbody>
              <?php if (empty($recent_movements)): ?>
              <tr><td colspan="5"><div class="empty-state"><i class="bi bi-archive"></i><p>Sin movimientos</p></div></td></tr>
              <?php else: ?>
              <?php foreach ($recent_movements as $m): ?>
              <tr>
                <td class="small"><?= format_date($m['date']) ?></td>
                <td><span class="badge-status <?= $m['type']==='ingreso'?'approved':'rejected' ?>"><?= ucfirst($m['type']) ?></span></td>
                <td class="fw-bold small"><?= format_cop($m['amount']) ?></td>
                <td class="small text-muted"><?= e(truncate($m['description'], 40)) ?></td>
                <td><?= statusBadge($m['status']) ?></td>
              </tr>
              <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card-modern h-100">
      <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0 fw-bold"><i class="bi bi-journal-check me-2 text-info"></i>Últimas Matrículas</h5>
        <a href="<?= url('admin/enrollments') ?>" class="btn-modern btn-modern-outline btn-modern-sm">Ver más</a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table-modern mb-0">
            <thead><tr><th>Fecha</th><th>Usuario</th><th>Curso</th><th>Monto</th><th>Estado</th></tr></thead>
            <tbody>
              <?php if (empty($recent_enrollments)): ?>
              <tr><td colspan="5"><div class="empty-state"><i class="bi bi-inbox"></i><p>Sin matrículas recientes</p></div></td></tr>
              <?php else: ?>
              <?php foreach ($recent_enrollments as $e): ?>
              <tr>
                <td class="small"><?= format_datetime($e['created_at']) ?></td>
                <td class="fw-bold small"><?= e($e['username']) ?></td>
                <td class="small text-muted"><?= e(truncate($e['course_title'], 30)) ?></td>
                <td class="fw-bold small"><?= format_cop($e['total']) ?></td>
                <td><?= statusBadge($e['status']) ?></td>
              </tr>
              <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php $scripts = '
<script>var API_FINANCE_URL="' . url('api/finance-data') . '";</script>
<script>
document.addEventListener("DOMContentLoaded",function(){
  var catCanvas=document.getElementById("categoryChart");
  if(catCanvas){
    var catData=' . json_encode($cursos_por_categoria) . ';
    var colors=["#D3545F","#E8B84B","#7C9A6D","#5C8DB8","#A37CB8","#E2725B","#F0A08C","#8B7D6B","#4A6F3A","#B8A89A"];
    new Chart(catCanvas,{
      type:"doughnut",
      data:{
        labels:catData.map(function(c){return c.name}),
        datasets:[{data:catData.map(function(c){return c.total}),backgroundColor:colors.slice(0,catData.length),borderWidth:0}]
      },
      options:{responsive:true,plugins:{legend:{position:"bottom",labels:{boxWidth:12,font:{size:11}}}},cutout:"60%"}
    });
  }
});
</script>
<script src="' . asset('js/dashboard.js') . '"></script>
'; ?>
