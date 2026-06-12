<div class="container py-5" style="max-width: 640px;">
    <div class="card-modern">
        <div class="card-body p-4">
            <h3 class="mb-3"><i class="bi bi-chat-dots me-2"></i>Contactar Soporte</h3>
            <p class="text-muted mb-4">Déjanos tu mensaje y te responderemos a la brevedad.</p>
            <form method="post" action="<?= url('contacto/enviar') ?>" id="contactForm">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Nombre completo</label>
                    <input type="text" name="name" class="form-control" placeholder="Tu nombre" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" name="email" class="form-control" placeholder="tu@correo.com" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mensaje</label>
                    <textarea name="message" class="form-control" rows="5" placeholder="Escribe tu mensaje aquí..." required></textarea>
                </div>
                <button type="submit" class="btn btn-modern btn-modern-primary w-100"><i class="bi bi-send me-1"></i>Enviar mensaje</button>
            </form>
            <div id="contactSuccess" class="alert alert-success mt-3 d-none"><i class="bi bi-check-circle me-2"></i>Mensaje enviado con éxito. Te contactaremos pronto.</div>
        </div>
    </div>
</div>

<?php $scripts = '
<script>
document.getElementById(\'contactForm\').addEventListener(\'submit\', function(e) {
    e.preventDefault();
    var form = this;
    var data = new FormData(form);
    fetch(form.action, { method: \'POST\', body: data })
    .then(function(r){ return r.json() })
    .then(function(d){
        if (d.success) {
            form.classList.add(\'d-none\');
            document.getElementById(\'contactSuccess\').classList.remove(\'d-none\');
        }
    })
    .catch(function(){});
});
</script>
'; ?>
