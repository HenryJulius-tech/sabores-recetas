<?php $title = $title ?? ''; $exam = $exam ?? []; $attempt = $attempt ?? []; ?>
<?php $preguntas = $preguntas ?? []; $respMap = $respMap ?? []; $curso = $curso ?? []; ?>
<style>
.result-container { max-width: 800px; margin: 0 auto; text-align: center; }
.score-circle { width: 150px; height: 150px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 1.5rem auto; font-size: 2.5rem; font-weight: 700; position: relative; }
.score-circle.passed { background: rgba(74,222,128,.15); color: #4ade80; border: 4px solid #4ade80; }
.score-circle.failed { background: rgba(239,68,68,.15); color: #ef4444; border: 4px solid #ef4444; }
.result-badge { display: inline-block; padding: .5rem 2rem; border-radius: 50px; font-weight: 600; font-size: 1.1rem; margin-bottom: 2rem; }
.result-badge.passed { background: rgba(74,222,128,.15); color: #4ade80; }
.result-badge.failed { background: rgba(239,68,68,.15); color: #ef4444; }
.review-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: var(--radius); padding: 1.5rem; margin-bottom: 1rem; text-align: left; }
.review-card.correct { border-left: 4px solid #4ade80; }
.review-card.incorrect { border-left: 4px solid #ef4444; }
.review-answer { margin-top: .5rem; font-size: .9rem; }
.review-answer .correct-text { color: #4ade80; font-weight: 600; }
.review-answer .your-text { color: #ef4444; }
</style>
<div class="result-container">
  <h1 style="font-size:1.5rem;margin-bottom:.5rem"><?= e($exam['title']) ?> - Resultado</h1>
  <div class="score-circle <?= $attempt['passed'] ? 'passed' : 'failed' ?>">
    <?= $attempt['score'] ?>/100
  </div>
  <div class="result-badge <?= $attempt['passed'] ? 'passed' : 'failed' ?>">
    <?= $attempt['passed'] ? 'Aprobado' : 'Reprobado' ?>
  </div>
  <p class="text-muted">
    Nota mínima: <?= $exam['passing_score'] ?>/100 ·
    Intentos: <?= $attempt['id'] ?>
  </p>
  <hr style="margin:2rem 0">
  <h3 style="text-align:left;margin-bottom:1rem">Revisión de respuestas</h3>
  <?php $qnum = 0; ?>
  <?php foreach ($preguntas as $pq): ?>
  <?php $qnum++; $resp = $respMap[$pq['id']] ?? null; $options = json_decode($pq['options'], true); ?>
  <div class="review-card <?= ($resp && $resp['is_correct']) ? 'correct' : 'incorrect' ?>">
    <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.5rem">
      <span style="background:var(--primary);color:white;width:24px;height:24px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:.8rem"><?= $qnum ?></span>
      <strong><?= e($pq['question']) ?></strong>
    </div>
    <?php if ($resp): ?>
    <div class="review-answer">
      <span class="your-text">Tu respuesta: <?= e($resp['answer'] ?: '(sin responder)') ?></span>
    </div>
    <?php endif; ?>
    <div class="review-answer">
      <span class="correct-text">Respuesta correcta: <?= e($pq['correct_answer']) ?></span>
    </div>
    <?php if ($resp): ?>
    <div style="margin-top:.3rem;font-size:.85rem">
      Puntos: <?= $resp['points_earned'] ?>/<?= $pq['points'] ?>
    </div>
    <?php endif; ?>
  </div>
  <?php endforeach; ?>
  <div style="margin-top:2rem">
    <a href="<?= url('curso/' . $curso['id']) ?>" class="btn btn-modern btn-modern-primary" style="padding:.75rem 2rem">
      <i class="bi bi-arrow-left"></i> Volver al curso
    </a>
  </div>
</div>
