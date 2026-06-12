<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($title ?? 'Sabores & Recetas') ?> - Sabores & Recetas</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="<?= asset('css/style.css') ?>" rel="stylesheet">
<meta name="csrf-token" content="<?= csrf_token() ?>">
</head>
<body>

<?php
$flashes = \App\Core\Session::allFlashes();
$is_login = strpos($_SERVER['REQUEST_URI'] ?? '', 'register') === false;
?>

<nav class="landing-nav">
  <a href="<?= url('login') ?>" class="brand"><i class="bi bi-book"></i><span>Sabores & Recetas</span></a>
  <div class="nav-links">
    <a href="#cursos">Cursos</a>
    <a href="#semestres">Semestres</a>
    <a href="#nosotros">Nosotros</a>
    <a href="#contacto">Contacto</a>
    <a href="<?= url('register') ?>" class="btn btn-outline-primary btn-sm <?= !$is_login ? 'd-none' : '' ?>"><i class="bi bi-person-plus"></i>Registrarse</a>
    <a href="<?= url('login') ?>" class="btn btn-primary btn-sm <?= !$is_login ? 'd-none' : '' ?>"><i class="bi bi-box-arrow-in-right"></i>Ingresar</a>
    <a href="<?= url('register') ?>" class="btn btn-primary btn-sm <?= $is_login ? 'd-none' : '' ?>"><i class="bi bi-person-plus"></i>Registro</a>
  </div>
</nav>

<?php if (!empty($flashes)): ?>
<div style="position:fixed;top:78px;left:50%;transform:translateX(-50%);z-index:1050;width:90%;max-width:500px">
  <?php foreach ($flashes as $key => $msg): ?>
  <div class="alert alert-<?= $key === 'error' ? 'danger' : ($key === 'success' ? 'success' : 'info') ?> alert-dismissible fade show d-flex align-items-center justify-content-between shadow" role="alert">
    <span><i class="bi bi-<?= $key === 'error' ? 'exclamation-circle' : ($key === 'success' ? 'check-circle' : 'info-circle') ?> me-2"></i><?= e($msg) ?></span>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- ═══ HERO ═══ -->
<section class="hero-section" id="inicio">
  <div class="hero-pattern">
    <span></span><span></span><span></span>
  </div>
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-lg-7 mb-5 mb-lg-0">
        <div class="hero-text">
          <div class="mb-3"><span class="badge bg-warning text-dark fs-6 px-3 py-2"><i class="bi bi-star-fill me-1"></i> Plataforma #1 de Cursos de Cocina</span></div>
          <h1>Aprende cocina como un <span>profesional</span></h1>
          <p>Domina técnicas culinarias de la mano de chefs expertos. Desde cocina básica hasta alta gastronomía, tenemos el curso perfecto para ti.</p>
          <div class="d-flex flex-wrap gap-3 mb-4">
            <a href="#cursos" class="btn btn-primary btn-lg"><i class="bi bi-book"></i>Ver Cursos</a>
            <a href="#semestres" class="btn btn-outline-light btn-lg"><i class="bi bi-calendar-check"></i>Explorar Semestres</a>
          </div>
          <div class="hero-features">
            <span class="hf-item"><i class="bi bi-camera-video"></i> Clases prácticas</span>
            <span class="hf-item"><i class="bi bi-award"></i> Chefs profesionales</span>
            <span class="hf-item"><i class="bi bi-people"></i> Comunidad activa</span>
          </div>
        </div>
      </div>
      <div class="col-lg-5">
        <?= $content ?? '' ?>
      </div>
    </div>
  </div>
</section>

<!-- ═══ CURSOS DESTACADOS ═══ -->
<section class="section-generic" id="cursos">
  <div class="container-fluid">
    <div class="section-title">
      <div class="mb-3"><span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2"><i class="bi bi-mortarboard me-1"></i> Nuestra oferta</span></div>
      <h2>Cursos destacados</h2>
      <p>Selección de nuestros mejores cursos de cocina, diseñados para todos los niveles</p>
    </div>
    <div class="row g-4">
      <?php $displayed = array_slice($cursos ?? [], 0, 8); ?>
      <?php if (empty($displayed)): ?>
      <div class="col-12 text-center"><p class="text-muted">Próximamente nuevos cursos</p></div>
      <?php else: ?>
      <?php foreach ($displayed as $c): ?>
      <div class="col-md-6 col-lg-3">
        <div class="course-landing-card" data-course='<?= e(json_encode($c, JSON_UNESCAPED_UNICODE)) ?>'>
          <div class="cl-img">
            <?php if (!empty($c['image_url'])): ?>
            <img src="<?= upload_url('courses', $c['image_url']) ?>" alt="<?= e($c['title']) ?>">
            <?php else: ?>
            <i class="bi bi-book"></i>
            <?php endif; ?>
            <span class="cl-level"><?= ucfirst($c['level']) ?></span>
          </div>
          <div class="cl-body">
            <div class="cl-category"><?= e($c['category_name'] ?? '') ?></div>
            <h5><?= e($c['title']) ?></h5>
            <div class="cl-meta">
              <span><i class="bi bi-person"></i><?= e($c['instructor']) ?></span>
              <span><i class="bi bi-clock"></i><?= e($c['duration']) ?></span>
            </div>
            <div class="cl-desc"><?= e(truncate($c['description'] ?? '', 90)) ?></div>
            <div class="cl-footer">
              <span class="cl-price"><?= format_cop($c['price']) ?></span>
              <button class="btn btn-primary btn-sm" onclick="showCourseDetail(this)"><i class="bi bi-eye"></i>Ver más</button>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
      <?php endif; ?>
    </div>
    <?php if (isset($cursos) && count($cursos) > 8): ?>
    <div class="text-center mt-4">
      <a href="<?= url('register') ?>" class="btn-modern btn-modern-primary"><i class="bi bi-arrow-right"></i>Ver todos los cursos</a>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- ═══ SEMESTRES / PERIODOS ═══ -->
<section class="section-generic" id="semestres">
  <div class="container-fluid">
    <div class="section-title">
      <div class="mb-3"><span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2"><i class="bi bi-calendar-range me-1"></i> Organización académica</span></div>
      <h2>Cursos por semestre</h2>
      <p>Planifica tu aprendizaje con nuestra estructura por periodos académicos</p>
    </div>
    <?php
    $periods = [];
    if (!empty($cursos)) {
      foreach ($cursos as $c) {
        $p = $c['period'] ?: 'General';
        $periods[$p][] = $c;
      }
    }
    ?>
    <?php if (empty($periods)): ?>
    <div class="text-center"><p class="text-muted">Los periodos se anunciarán próximamente</p></div>
    <?php else: ?>
    <?php foreach ($periods as $periodName => $periodCourses): ?>
    <div class="period-section">
      <div class="period-header">
        <i class="bi bi-calendar-event"></i>
        <h3><?= e($periodName) ?></h3>
        <span class="period-count"><?= count($periodCourses) ?> cursos</span>
      </div>
      <div class="row g-3">
        <?php foreach (array_slice($periodCourses, 0, 4) as $pc): ?>
        <div class="col-md-6 col-lg-3">
          <div class="course-landing-card" data-course='<?= e(json_encode($pc, JSON_UNESCAPED_UNICODE)) ?>'>
            <div class="cl-img" style="height:140px;">
              <?php if (!empty($pc['image_url'])): ?>
              <img src="<?= upload_url('courses', $pc['image_url']) ?>" alt="<?= e($pc['title']) ?>">
              <?php else: ?>
              <i class="bi bi-book"></i>
              <?php endif; ?>
              <span class="cl-level"><?= ucfirst($pc['level']) ?></span>
            </div>
            <div class="cl-body" style="padding:14px;">
              <h5 style="font-size:0.9rem;"><?= e($pc['title']) ?></h5>
              <div class="cl-meta">
                <span><i class="bi bi-clock"></i><?= e($pc['duration']) ?></span>
              </div>
              <div class="cl-footer" style="padding-top:8px;">
                <span class="cl-price" style="font-size:1rem;"><?= format_cop($pc['price']) ?></span>
                <button class="btn btn-primary btn-sm" onclick="showCourseDetail(this)">Ver más</button>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php if (count($periodCourses) > 4): ?>
      <div class="text-center mt-3">
        <a href="<?= url('register') ?>" class="btn-modern btn-modern-outline btn-modern-sm">Ver +<?= count($periodCourses)-4 ?> cursos de <?= e($periodName) ?></a>
      </div>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>

<!-- ═══ CATEGORÍAS ═══ -->
<?php if (!empty($categorias)): ?>
<section class="section-categories">
  <div class="container-fluid">
    <h2><i class="bi bi-grid me-2"></i>Explora por categoría</h2>
    <div class="row g-3">
      <?php foreach ($categorias as $cat): ?>
      <div class="col-6 col-md-4 col-lg-2">
        <div class="cat-landing-item">
          <?php
          $icons = [
            1=>'bi-egg-fried',2=>'bi-droplet',3=>'bi-cake',4=>'bi-flower1',
            5=>'bi-globe2',6=>'bi-flag',7=>'bi-heart-pulse',8=>'bi-tree',
            9=>'bi-fire',10=>'bi-cup-straw'
          ];
          $icon = $icons[$cat['id']] ?? 'bi-book';
          ?>
          <i class="bi <?= $icon ?>"></i>
          <h6><?= e($cat['name']) ?></h6>
          <small><?= $cat['course_count'] ?> cursos</small>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ═══ SOBRE NOSOTROS ═══ -->
<section class="section-generic" id="nosotros">
  <div class="container-fluid">
    <div class="about-grid">
      <div class="about-image">
        <i class="bi bi-book"></i>
      </div>
      <div class="about-content">
        <div class="mb-3"><span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2"><i class="bi bi-info-circle me-1"></i> Sobre nosotros</span></div>
        <h3>Transformamos tu pasión por la cocina en habilidades reales</h3>
        <p>Sabores & Recetas nació con la misión de hacer la educación culinaria accesible para todos. Creemos que cocinar es una habilidad esencial que conecta culturas, despierta creatividad y alimenta el alma.</p>
        <p>Nuestros cursos están diseñados por chefs profesionales con años de experiencia en gastronomía nacional e internacional. Cada lección combina teoría y práctica para que aprendas de verdad.</p>
        <div class="about-stats">
          <div class="stat">
            <div class="num">50+</div>
            <div class="label">Cursos</div>
          </div>
          <div class="stat">
            <div class="num">15+</div>
            <div class="label">Chefs</div>
          </div>
          <div class="stat">
            <div class="num">10</div>
            <div class="label">Categorías</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══ CONTACTO ═══ -->
<section class="section-generic" id="contacto">
  <div class="container-fluid">
    <div class="section-title">
      <div class="mb-3"><span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2"><i class="bi bi-chat-dots me-1"></i> Contacto</span></div>
      <h2>¿Tienes dudas?</h2>
      <p>Estamos aquí para ayudarte. Escríbenos y te responderemos a la brevedad</p>
    </div>
    <div class="contact-grid">
      <div class="contact-info">
        <h4>Hablemos</h4>
        <p>Si tienes preguntas sobre nuestros cursos, horarios, precios o cualquier otra cosa, no dudes en contactarnos.</p>
        <div class="contact-item">
          <i class="bi bi-envelope"></i>
          <div>
            <strong>Email</strong>
            <small>info@saboresyrecetas.com</small>
          </div>
        </div>
        <div class="contact-item">
          <i class="bi bi-telephone"></i>
          <div>
            <strong>Teléfono</strong>
            <small>+57 (1) 234 5678</small>
          </div>
        </div>
        <div class="contact-item">
          <i class="bi bi-geo-alt"></i>
          <div>
            <strong>Ubicación</strong>
            <small>Bogotá, Colombia</small>
          </div>
        </div>
        <div class="contact-item">
          <i class="bi bi-clock"></i>
          <div>
            <strong>Horario</strong>
            <small>Lun - Vie: 8:00 am - 6:00 pm</small>
          </div>
        </div>
      </div>
      <div class="contact-form">
        <form method="post" action="<?= url('contacto/enviar') ?>" id="contactForm">
          <?= csrf_field() ?>
          <div class="form-group">
            <label>Nombre completo</label>
            <input type="text" name="name" placeholder="Tu nombre" required>
          </div>
          <div class="form-group">
            <label>Correo electrónico</label>
            <input type="email" name="email" placeholder="tu@correo.com" required>
          </div>
          <div class="form-group">
            <label>Mensaje</label>
            <textarea name="message" placeholder="Escribe tu mensaje aquí..." required></textarea>
          </div>
          <button type="submit" class="btn btn-primary w-100"><i class="bi bi-send"></i>Enviar mensaje</button>
        </form>
        <div id="contactSuccess" class="alert alert-success mt-3 d-none"><i class="bi bi-check-circle me-2"></i>Mensaje enviado con éxito. Te contactaremos pronto.</div>
      </div>
    </div>
  </div>
</section>

<!-- ═══ CTA ═══ -->
<section class="section-cta">
  <div class="container-fluid">
    <h2>¿Listo para empezar tu viaje culinario?</h2>
    <p>Únete a nuestra comunidad y descubre el chef que llevas dentro. Tu primera clase te espera.</p>
    <a href="<?= url('register') ?>" class="btn btn-light btn-lg"><i class="bi bi-person-plus"></i>Crear cuenta gratis</a>
  </div>
</section>

<!-- ═══ COURSE DETAIL MODAL ═══ -->
<div class="modal fade" id="courseDetailModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content" style="border:none;border-radius:var(--radius-sm);overflow:hidden;">
      <div class="detail-modal-header">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <h3 id="dmTitle"></h3>
            <div id="dmCategory" style="font-size:0.82rem;opacity:0.7;"></div>
          </div>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="dm-meta">
          <span><i class="bi bi-person-badge"></i> Instructor: <strong id="dmInstructor"></strong></span>
          <span><i class="bi bi-clock"></i> Duración: <strong id="dmDuration"></strong></span>
          <span><i class="bi bi-bar-chart"></i> Nivel: <strong id="dmLevel"></strong></span>
        </div>
      </div>
      <div class="detail-modal-body">
        <div class="dm-section">
          <h6><i class="bi bi-info-circle"></i> Descripción</h6>
          <p id="dmDescription"></p>
        </div>
        <div class="dm-section">
          <h6><i class="bi bi-list-check"></i> Temas que incluye</h6>
          <div class="dm-topics" id="dmTopics"></div>
        </div>
        <div class="dm-section">
          <h6><i class="bi bi-grid"></i> Información del curso</h6>
          <div class="dm-info-row">
            <div class="dm-info-item">
              <div class="dm-info-value" id="dmDurationInfo"></div>
              <div class="dm-info-label">Duración</div>
            </div>
            <div class="dm-info-item">
              <div class="dm-info-value" id="dmLevelInfo"></div>
              <div class="dm-info-label">Nivel</div>
            </div>
            <div class="dm-info-item">
              <div class="dm-info-value" id="dmCategoryInfo"></div>
              <div class="dm-info-label">Categoría</div>
            </div>
            <div class="dm-info-item">
              <div class="dm-info-value" id="dmInstructorInfo"></div>
              <div class="dm-info-label">Instructor</div>
            </div>
          </div>
        </div>
      </div>
      <div class="detail-modal-footer">
        <div>
          <div style="font-size:0.78rem;color:var(--text-muted);">Precio del curso</div>
          <div class="dm-price" id="dmPrice"></div>
        </div>
        <a id="dmEnrollBtn" href="#" class="btn btn-primary btn-lg"><i class="bi bi-mortarboard"></i>Inscribirme ahora</a>
      </div>
    </div>
  </div>
</div>

<footer class="landing-footer">
  <i class="bi bi-book me-2"></i>Sabores & Recetas &mdash; Plataforma de Cursos de Cocina &copy; <?= date('Y') ?>. Todos los derechos reservados.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= asset('js/main.js') ?>"></script>
<script>
setTimeout(function(){ document.querySelectorAll('.alert-dismissible').forEach(function(a){ var bs=bootstrap.Alert.getInstance(a); if(bs)bs.close() }) }, 5000);

function showCourseDetail(btn) {
  var card = btn.closest('[data-course]');
  if (!card) return;
  var data = JSON.parse(card.getAttribute('data-course'));
  document.getElementById('dmTitle').textContent = data.title || '';
  document.getElementById('dmCategory').textContent = data.category_name || '';
  document.getElementById('dmInstructor').textContent = data.instructor || 'Por definir';
  document.getElementById('dmDuration').textContent = data.duration || 'Por definir';
  document.getElementById('dmLevel').textContent = data.level ? data.level.charAt(0).toUpperCase() + data.level.slice(1) : '';
  document.getElementById('dmDescription').textContent = data.description || 'Sin descripción disponible';
  document.getElementById('dmDurationInfo').textContent = data.duration || '—';
  document.getElementById('dmLevelInfo').textContent = data.level ? data.level.charAt(0).toUpperCase() + data.level.slice(1) : '—';
  document.getElementById('dmCategoryInfo').textContent = data.category_name || '—';
  document.getElementById('dmInstructorInfo').textContent = data.instructor || '—';

  var topicsEl = document.getElementById('dmTopics');
  if (data.topics) {
    var topics = data.topics.split(',');
    topicsEl.innerHTML = topics.map(function(t){ return '<span>' + t.trim() + '</span>'; }).join('');
  } else {
    topicsEl.innerHTML = '<span class="text-muted">Contenido próximamente</span>';
  }

  var price = parseFloat(data.price) || 0;
  document.getElementById('dmPrice').textContent = '$' + price.toLocaleString('es-CO');

  var enrollBtn = document.getElementById('dmEnrollBtn');
  enrollBtn.href = '<?= url('register') ?>';
  <?php if (\App\Core\Session::isLoggedIn()): ?>
  enrollBtn.href = '<?= url('inscripcion/') ?>' + data.id;
  <?php endif; ?>

  var modal = new bootstrap.Modal(document.getElementById('courseDetailModal'));
  modal.show();
}

document.getElementById('contactForm') && document.getElementById('contactForm').addEventListener('submit', function(e) {
  e.preventDefault();
  var form = this;
  var btn = form.querySelector('button[type="submit"]');
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Enviando...';
  var formData = new FormData(form);
  fetch(form.action, { method: 'POST', body: formData })
  .then(function(r){ return r.json(); })
  .then(function(d) {
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-send"></i>Enviar mensaje';
    if (d.success) {
      form.reset();
      document.getElementById('contactSuccess').classList.remove('d-none');
      setTimeout(function(){ document.getElementById('contactSuccess').classList.add('d-none'); }, 5000);
    } else {
      alert('Error: ' + (d.error || 'No se pudo enviar'));
    }
  })
  .catch(function() {
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-send"></i>Enviar mensaje';
    alert('Error de conexión');
  });
});
</script>
</body>
</html>
