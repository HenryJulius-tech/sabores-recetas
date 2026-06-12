<?php $title = $title ?? ''; $clase = $clase ?? []; $curso = $curso ?? []; $modulo = $modulo ?? []; ?>
<?php $nextClass = $nextClass ?? null; $isCompleted = $isCompleted ?? false; ?>
<?php
function getYoutubeEmbed($url) {
    $patterns = [
        '~youtube\.com/watch\?v=([^&]+)~',
        '~youtu\.be/([^?]+)~',
        '~youtube\.com/embed/([^?]+)~',
    ];
    foreach ($patterns as $p) {
        if (preg_match($p, $url, $m)) return 'https://www.youtube.com/embed/' . $m[1];
    }
    return $url;
}
?>
<style>
.video-container { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: var(--radius); margin-bottom: 1.5rem; background: #000; }
.video-container iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0; }
.class-content { max-width: 900px; margin: 0 auto; }
.class-nav { display: flex; justify-content: space-between; align-items: center; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border); }
.btn-complete { display: inline-flex; align-items: center; gap: .5rem; padding: .75rem 2rem; font-size: 1rem; }
.completed-badge { display: inline-flex; align-items: center; gap: .5rem; background: rgba(74,222,128,.15); color: #4ade80; padding: .75rem 2rem; border-radius: var(--radius); font-weight: 600; }
</style>
<div class="class-content">
  <div style="margin-bottom:1rem">
    <a href="<?= url('curso/' . $curso['id']) ?>" class="text-muted" style="text-decoration:none">&larr; Volver al curso</a>
    <span style="margin:0 .5rem;color:var(--text-muted)">·</span>
    <span class="text-muted"><?= e($modulo['title']) ?></span>
  </div>
  <h1 style="margin-bottom:.5rem"><?= e($clase['title']) ?></h1>
  <?php if (!empty($clase['description'])): ?>
  <p style="color:var(--text-muted);margin-bottom:1.5rem"><?= e($clase['description']) ?></p>
  <?php endif; ?>
  <?php if (!empty($clase['video_url'])): ?>
  <div class="video-container">
    <iframe src="<?= e(getYoutubeEmbed($clase['video_url'])) ?>" allowfullscreen allow="autoplay; encrypted-media"></iframe>
  </div>
  <?php endif; ?>
  <div class="class-nav">
    <div>
      <?php if ($isCompleted): ?>
      <span class="completed-badge"><i class="bi bi-check-circle-fill"></i> Completada</span>
      <?php endif; ?>
    </div>
    <div>
      <?php if (!$isCompleted): ?>
      <button onclick="completeClass(<?= $curso['id'] ?>, <?= $clase['id'] ?>)" class="btn btn-modern btn-modern-primary btn-complete">
        <i class="bi bi-check-lg"></i> Completar y continuar
      </button>
      <?php elseif ($nextClass): ?>
      <a href="<?= url('curso/' . $curso['id'] . '/clase/' . $nextClass['id']) ?>" class="btn btn-modern btn-modern-primary btn-complete">
        Siguiente clase <i class="bi bi-arrow-right"></i>
      </a>
      <?php else: ?>
      <a href="<?= url('curso/' . $curso['id']) ?>" class="btn btn-modern btn-modern-primary btn-complete">
        Volver al curso <i class="bi bi-arrow-right"></i>
      </a>
      <?php endif; ?>
    </div>
  </div>
</div>
<script>
function completeClass(cursoId, claseId) {
    var btn = event.target.closest('button');
    if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>'; }
    fetch('<?= url('api/curso/') ?>' + cursoId + '/clase/' + claseId + '/completar', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(function(r) { return r.json(); })
    .then(function(d) { if (d.redirect) { window.location.href = d.redirect; } })
    .catch(function() { if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bi bi-check-lg"></i> Completar y continuar'; } });
}
</script>
