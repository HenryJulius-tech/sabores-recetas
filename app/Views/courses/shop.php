<div class="catalog-header fade-in">
  <h1><i class="bi bi-book me-2"></i>Catálogo de Cursos</h1>
  <p>Explora nuestra oferta gastronómica y encuentra el curso perfecto para ti</p>
</div>

<div class="filter-bar mb-4">
  <a href="<?= url('catalogo') ?>" class="<?= !isset($_GET['category']) ? 'active' : '' ?>">Todos</a>
  <?php foreach ($categorias as $cat): ?>
  <a href="<?= url('catalogo?category=' . $cat['id']) ?>" class="<?= (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'active' : '' ?>">
    <?= e($cat['name']) ?> (<?= $cat['course_count'] ?>)
  </a>
  <?php endforeach; ?>
</div>

<div class="row g-4" id="courseGrid">
  <?php if (empty($cursos)): ?>
  <div class="col-12">
    <div class="empty-state">
      <i class="bi bi-book"></i>
      <p>No hay cursos disponibles en esta categoría</p>
      <a href="<?= url('catalogo') ?>" class="btn btn-primary btn-sm mt-2">Ver todos</a>
    </div>
  </div>
  <?php else: ?>
  <?php foreach ($cursos as $c): ?>
  <div class="col-md-6 col-lg-4 col-xl-3">
    <div class="product-card" data-course='<?= e(json_encode($c, JSON_UNESCAPED_UNICODE)) ?>'>
      <div class="card-img-top">
        <?php if (!empty($c['image_url'])): ?>
        <img src="<?= upload_url('courses', $c['image_url']) ?>" alt="<?= e($c['title']) ?>">
        <?php else: ?>
        <i class="bi bi-book"></i>
        <?php endif; ?>
        <?= levelBadge($c['level'], true) ?>
      </div>
      <div class="card-body">
        <div style="font-size:0.72rem;text-transform:uppercase;letter-spacing:0.06em;color:var(--red);font-weight:600;margin-bottom:4px;">
          <?= e($c['category_name'] ?? '') ?>
        </div>
        <h5 class="card-title"><?= e($c['title']) ?></h5>
        <div class="d-flex flex-wrap gap-2 mb-2">
          <span style="font-size:0.78rem;color:var(--text-muted);"><i class="bi bi-person me-1"></i><?= e($c['instructor']) ?></span>
          <span style="font-size:0.78rem;color:var(--text-muted);"><i class="bi bi-clock me-1"></i><?= e($c['duration']) ?></span>
        </div>
        <p class="card-text mt-2"><?= e(truncate($c['description'] ?? '', 100)) ?></p>
        <div class="d-flex justify-content-between align-items-center mt-auto pt-3" style="border-top:1px solid var(--border);">
          <span class="price-tag"><?= format_cop($c['price']) ?></span>
           <div class="d-flex gap-2">
             <button class="btn btn-sm btn-outline-primary" onclick='showCatalogCourseDetail(<?= e(json_encode($c, JSON_UNESCAPED_UNICODE)) ?>)'><i class="bi bi-eye"></i></button>
             <?php if (\App\Core\Session::userRole() === 'client'): ?>
             <a href="<?= url('inscripcion/' . $c['id']) ?>" class="btn btn-sm btn-primary"><i class="bi bi-mortarboard"></i>Inscribirme</a>
             <?php endif; ?>
           </div>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
  <?php endif; ?>
</div>

<!-- Course Detail Modal (reuse from landing page) -->
<div class="modal fade" id="catalogCourseModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content" style="border:none;border-radius:var(--radius-sm);overflow:hidden;">
      <div class="detail-modal-header">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <h3 id="cmTitle"></h3>
            <div id="cmCategory" style="font-size:0.82rem;opacity:0.7;"></div>
          </div>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="dm-meta">
          <span><i class="bi bi-person-badge"></i> Instructor: <strong id="cmInstructor"></strong></span>
          <span><i class="bi bi-clock"></i> Duración: <strong id="cmDuration"></strong></span>
          <span><i class="bi bi-bar-chart"></i> Nivel: <strong id="cmLevel"></strong></span>
        </div>
      </div>
      <div class="detail-modal-body">
        <div class="dm-section">
          <h6><i class="bi bi-info-circle"></i> Descripción</h6>
          <p id="cmDescription"></p>
        </div>
        <div class="dm-section">
          <h6><i class="bi bi-list-check"></i> Temas que incluye</h6>
          <div class="dm-topics" id="cmTopics"></div>
        </div>
        <div class="dm-section">
          <h6><i class="bi bi-grid"></i> Información del curso</h6>
          <div class="dm-info-row">
            <div class="dm-info-item"><div class="dm-info-value" id="cmDurationInfo"></div><div class="dm-info-label">Duración</div></div>
            <div class="dm-info-item"><div class="dm-info-value" id="cmLevelInfo"></div><div class="dm-info-label">Nivel</div></div>
            <div class="dm-info-item"><div class="dm-info-value" id="cmCategoryInfo"></div><div class="dm-info-label">Categoría</div></div>
            <div class="dm-info-item"><div class="dm-info-value" id="cmInstructorInfo"></div><div class="dm-info-label">Instructor</div></div>
          </div>
        </div>
      </div>
      <div class="detail-modal-footer">
        <div>
          <div style="font-size:0.78rem;color:var(--text-muted);">Precio del curso</div>
          <div class="dm-price" id="cmPrice"></div>
        </div>
        <a id="cmEnrollBtn" href="#" class="btn-enroll"><i class="bi bi-mortarboard"></i>Inscribirme ahora</a>
      </div>
    </div>
  </div>
</div>

<?php $scripts = '
<script>
function showCatalogCourseDetail(data) {
  document.getElementById("cmTitle").textContent = data.title || "";
  document.getElementById("cmCategory").textContent = data.category_name || "";
  document.getElementById("cmInstructor").textContent = data.instructor || "Por definir";
  document.getElementById("cmDuration").textContent = data.duration || "Por definir";
  document.getElementById("cmLevel").textContent = data.level ? data.level.charAt(0).toUpperCase() + data.level.slice(1) : "";
  document.getElementById("cmDescription").textContent = data.description || "Sin descripción disponible";
  document.getElementById("cmDurationInfo").textContent = data.duration || "—";
  document.getElementById("cmLevelInfo").textContent = data.level ? data.level.charAt(0).toUpperCase() + data.level.slice(1) : "—";
  document.getElementById("cmCategoryInfo").textContent = data.category_name || "—";
  document.getElementById("cmInstructorInfo").textContent = data.instructor || "—";

  var topicsEl = document.getElementById("cmTopics");
  if (data.topics) {
    topicsEl.innerHTML = data.topics.split(",").map(function(t){ return "<span>" + t.trim() + "</span>"; }).join("");
  } else {
    topicsEl.innerHTML = "<span class=\"text-muted\">Contenido próximamente</span>";
  }

  var price = parseFloat(data.price) || 0;
  document.getElementById("cmPrice").textContent = "$" + price.toLocaleString("es-CO");

  document.getElementById("cmEnrollBtn").href = "' . url('inscripcion/') . '" + data.id;
  document.getElementById("cmEnrollBtn").onclick = null;

  var modal = new bootstrap.Modal(document.getElementById("catalogCourseModal"));
  modal.show();
}
</script>
'; ?>
