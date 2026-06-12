<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4 class="page-title mb-0"><i class="bi bi-chat-dots me-2"></i>Mensajes de Contacto</h4>
    <p class="page-subtitle">Bandeja de mensajes enviados desde la landing page</p>
  </div>
</div>

<div class="card-modern">
  <div class="card-body p-0">
    <?php if (empty($mensajes)): ?>
    <div class="empty-state">
      <i class="bi bi-inbox"></i>
      <p>No hay mensajes de contacto</p>
    </div>
    <?php else: ?>
    <div class="table-responsive">
      <table class="table-modern">
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Mensaje</th>
            <th style="width:60px"></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($mensajes as $m): ?>
          <tr>
            <td style="white-space:nowrap;"><?= format_datetime($m['created_at']) ?></td>
            <td><strong><?= e($m['name']) ?></strong></td>
            <td><a href="mailto:<?= e($m['email']) ?>"><?= e($m['email']) ?></a></td>
            <td style="max-width:350px;">
              <div style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?= e($m['message']) ?>">
                <?= e($m['message']) ?>
              </div>
            </td>
            <td>
              <form method="post" action="<?= url('contactos/eliminar/' . $m['id']) ?>" onsubmit="return confirm('¿Eliminar este mensaje?')" style="display:inline;">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-sm btn-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>
