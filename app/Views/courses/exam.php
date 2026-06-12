<?php $title = $title ?? ''; $exam = $exam ?? []; $preguntas = $preguntas ?? []; $curso = $curso ?? []; ?>
<style>
.exam-container { max-width: 800px; margin: 0 auto; }
.question-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: var(--radius); padding: 1.5rem; margin-bottom: 1.5rem; }
.question-text { font-weight: 600; margin-bottom: 1rem; font-size: 1.05rem; }
.options-list { list-style: none; padding: 0; margin: 0; }
.options-list li { margin-bottom: .5rem; }
.options-list label { display: flex; align-items: center; gap: .75rem; padding: .75rem 1rem; border: 1px solid var(--border); border-radius: var(--radius); cursor: pointer; transition: all .2s; }
.options-list label:hover { border-color: var(--primary); background: rgba(99,102,241,.05); }
.options-list input[type="radio"]:checked + span { color: var(--primary); font-weight: 600; }
.question-number { display: inline-block; background: var(--primary); color: white; width: 28px; height: 28px; border-radius: 50%; text-align: center; line-height: 28px; font-size: .85rem; margin-bottom: .5rem; }
.exam-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border); }
</style>
<div class="exam-container">
  <div class="exam-header">
    <div>
      <h1 style="margin:0;font-size:1.4rem"><?= e($exam['title']) ?></h1>
      <small class="text-muted">Nota mínima: <?= $exam['passing_score'] ?>/100 · <?= count($preguntas) ?> preguntas</small>
    </div>
  </div>
  <form id="examForm">
    <?php $qnum = 0; ?>
    <?php foreach ($preguntas as $pq): ?>
    <?php $qnum++; $options = json_decode($pq['options'], true); ?>
    <div class="question-card">
      <div class="question-number"><?= $qnum ?></div>
      <div class="question-text"><?= e($pq['question']) ?></div>
      <ul class="options-list">
        <?php foreach ($options as $opt): ?>
        <li>
          <label>
            <input type="radio" name="answers[<?= $pq['id'] ?>]" value="<?= e($opt) ?>" required>
            <span><?= e($opt) ?></span>
          </label>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php endforeach; ?>
    <div style="text-align:center;margin-top:2rem">
      <button type="submit" class="btn btn-modern btn-modern-primary" style="padding:.75rem 3rem;font-size:1.1rem">
        <i class="bi bi-send-fill"></i> Enviar examen
      </button>
    </div>
  </form>
</div>
<script>
document.getElementById('examForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Enviando...';
    var formData = new FormData(this);
    formData.append('_token', '<?= e($_SESSION['_token'] ?? '') ?>');
    fetch(window.location.href + '/enviar', { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(function(r) { return r.json(); })
    .then(function(d) {
        if (d.redirect) { window.location.href = d.redirect; }
        else { alert(d.error || 'Error al enviar'); btn.disabled = false; btn.innerHTML = '<i class="bi bi-send-fill"></i> Enviar examen'; }
    })
    .catch(function() { alert('Error de conexión'); btn.disabled = false; btn.innerHTML = '<i class="bi bi-send-fill"></i> Enviar examen'; });
});
</script>
