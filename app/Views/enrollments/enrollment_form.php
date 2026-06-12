<div class="enrollment-container">
  <!-- Información del Curso -->
  <div class="row mb-5">
    <div class="col-lg-7">
      <div class="card fade-in">
        <div class="card-header">
          <h5 class="mb-0"><i class="bi bi-book me-2"></i>Detalles del Curso</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-4 mb-3 mb-md-0">
              <div class="course-preview">
                <?php if (!empty($curso['image_url'])): ?>
                <img src="<?= upload_url('courses', $curso['image_url']) ?>" alt="<?= e($curso['title']) ?>" class="img-fluid" style="border-radius: var(--radius); height: 200px; object-fit: cover;">
                <?php else: ?>
                <div style="background: #F1F5F9; height: 200px; display: flex; align-items: center; justify-content: center; border-radius: var(--radius);">
                  <i class="bi bi-book" style="font-size: 3rem; color: #CBD5E1;"></i>
                </div>
                <?php endif; ?>
              </div>
            </div>
            <div class="col-md-8">
              <h4 style="font-weight: 700; margin-bottom: 8px;"><?= e($curso['title']) ?></h4>
              <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 12px;"><?= e($curso['category_name']) ?></p>
              <div style="display: flex; gap: 16px; margin-bottom: 16px; flex-wrap: wrap;">
                <div style="font-size: 0.85rem;">
                  <i class="bi bi-person" style="color: var(--accent);"></i>
                  <strong>Instructor:</strong> <?= e($curso['instructor']) ?>
                </div>
                <div style="font-size: 0.85rem;">
                  <i class="bi bi-clock" style="color: var(--accent);"></i>
                  <strong>Duración:</strong> <?= e($curso['duration']) ?>
                </div>
                <div style="font-size: 0.85rem;">
                  <i class="bi bi-bar-chart" style="color: var(--accent);"></i>
                  <strong>Nivel:</strong> <?= ucfirst($curso['level']) ?>
                </div>
              </div>
              <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6; margin-bottom: 0;">
                <?= e($curso['description']) ?>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Resumen de Precio -->
    <div class="col-lg-5">
      <div class="card fade-in" style="background: linear-gradient(135deg, #F8FAFC 0%, #fff 100%); border: 1px solid var(--border);">
        <div class="card-body">
          <h5 style="font-weight: 700; margin-bottom: 20px; text-align: center;">Resumen de Inscripción</h5>
          
          <div style="display: flex; justify-content: space-between; margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid var(--border);">
            <span>Precio del curso:</span>
            <strong><?= format_cop($curso['price']) ?></strong>
          </div>

          <div style="display: flex; justify-content: space-between; margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid var(--border);">
            <span>Descuentos:</span>
            <strong>$0</strong>
          </div>

          <div style="display: flex; justify-content: space-between; margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid var(--border);">
            <span>Impuestos:</span>
            <strong>$0</strong>
          </div>

          <div style="display: flex; justify-content: space-between; font-size: 1.2rem; font-weight: 700; color: var(--primary); margin-bottom: 24px;">
            <span>Total:</span>
            <span><?= format_cop($curso['price']) ?></span>
          </div>

          <div style="background: #EFF6FF; border-left: 3px solid #3B82F6; padding: 12px; border-radius: var(--radius-xs); font-size: 0.85rem; color: #1E40AF;">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Nota:</strong> Después de completar la inscripción, podrás subir el comprobante de pago.
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Formulario de Inscripción -->
  <div class="row">
    <div class="col-lg-7">
      <div class="card fade-in">
        <div class="card-header">
          <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Tus Datos</h5>
        </div>
        <div class="card-body">
          <form method="post" action="<?= url('inscripcion/procesar') ?>" id="enrollmentForm">
            <?= csrf_field() ?>
            <input type="hidden" name="curso_id" value="<?= $curso['id'] ?>">

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Nombre de usuario</label>
                <input type="text" class="form-control" value="<?= e($user['username'] ?? '') ?>" disabled>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" value="<?= e($user['email'] ?? '') ?>" disabled>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Nombre completo</label>
                <input type="text" class="form-control" value="<?= e($user['fullname'] ?? '') ?>" disabled>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Teléfono</label>
                <input type="tel" class="form-control" value="<?= e($user['phone'] ?? '') ?>" disabled>
              </div>
            </div>

            <!-- Métodos de Pago -->
            <div class="mt-4">
              <h6 style="font-weight: 700; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                <i class="bi bi-credit-card"></i>
                Selecciona un Método de Pago
              </h6>

              <!-- Transferencia Bancaria -->
              <div class="payment-method-card mb-3">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="payment_method" id="payment_transferencia" value="transferencia" onchange="updatePaymentForm(this)">
                  <label class="form-check-label" for="payment_transferencia">
                    <strong><i class="bi bi-bank me-2"></i>Transferencia Bancaria</strong>
                    <small class="d-block mt-1" style="color: var(--text-muted);">Realiza una transferencia a nuestra cuenta bancaria</small>
                  </label>
                </div>
                <div class="payment-details mt-2" id="details_transferencia" style="display: none; background: #F8FAFC; padding: 12px; border-radius: var(--radius-xs); border-left: 3px solid var(--accent);">
                  <p style="font-size: 0.85rem; margin-bottom: 8px;"><strong>Detalles bancarios:</strong></p>
                  <ul style="font-size: 0.85rem; margin-bottom: 0; padding-left: 20px;">
                    <li>Banco: Banco de Colombia</li>
                    <li>Cuenta: 12345678</li>
                    <li>Tipo: Cuenta Corriente</li>
                    <li>Titular: Sabores & Recetas</li>
                  </ul>
                  <div class="mt-3">
                    <label class="form-label">Comprobante de transferencia</label>
                    <input type="file" class="form-control" name="proof" accept="image/*" />
                    <small class="text-muted">Sube una captura de pantalla o comprobante de tu transferencia</small>
                  </div>
                </div>
              </div>

              <!-- Tarjeta de Crédito -->
              <div class="payment-method-card mb-3">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="payment_method" id="payment_tarjeta" value="tarjeta" onchange="updatePaymentForm(this)">
                  <label class="form-check-label" for="payment_tarjeta">
                    <strong><i class="bi bi-credit-card-2-front me-2"></i>Tarjeta de Crédito/Débito</strong>
                    <small class="d-block mt-1" style="color: var(--text-muted);">Paga directamente con tu tarjeta</small>
                  </label>
                </div>
                <div class="payment-details mt-2" id="details_tarjeta" style="display: none; background: #F8FAFC; padding: 12px; border-radius: var(--radius-xs); border-left: 3px solid var(--primary);">
                  <p style="font-size: 0.85rem; margin-bottom: 12px;"><strong>Redirigirás a nuestro procesador seguro de pagos</strong></p>
                  <div class="alert alert-info" style="font-size: 0.85rem; margin-bottom: 0;">
                    <i class="bi bi-shield-check me-2"></i>Tu transacción está protegida con encriptación SSL
                  </div>
                </div>
              </div>

              <!-- PayPal -->
              <div class="payment-method-card mb-3">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="payment_method" id="payment_paypal" value="paypal" onchange="updatePaymentForm(this)">
                  <label class="form-check-label" for="payment_paypal">
                    <strong><i class="bi bi-wallet me-2"></i>PayPal</strong>
                    <small class="d-block mt-1" style="color: var(--text-muted);">Paga de forma segura con tu cuenta PayPal</small>
                  </label>
                </div>
                <div class="payment-details mt-2" id="details_paypal" style="display: none; background: #F8FAFC; padding: 12px; border-radius: var(--radius-xs); border-left: 3px solid #003087;">
                  <p style="font-size: 0.85rem; margin-bottom: 0;"><strong>Serás redirigido a PayPal para completar tu pago de forma segura</strong></p>
                </div>
              </div>

              <!-- Pago en Efectivo -->
              <div class="payment-method-card mb-3">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="payment_method" id="payment_cash" value="cash" onchange="updatePaymentForm(this)">
                  <label class="form-check-label" for="payment_cash">
                    <strong><i class="bi bi-cash-coin me-2"></i>Pago en Efectivo</strong>
                    <small class="d-block mt-1" style="color: var(--text-muted);">Realiza tu pago en nuestras oficinas</small>
                  </label>
                </div>
                <div class="payment-details mt-2" id="details_cash" style="display: none; background: #F8FAFC; padding: 12px; border-radius: var(--radius-xs); border-left: 3px solid #10B981;">
                  <p style="font-size: 0.85rem; margin-bottom: 8px;"><strong>Ubicación:</strong></p>
                  <p style="font-size: 0.85rem; margin-bottom: 8px;">Calle Principal #123, Bogotá, Colombia</p>
                  <div class="mt-3">
                    <label class="form-label">Comprobante de pago</label>
                    <input type="file" class="form-control" name="proof" accept="image/*" />
                    <small class="text-muted">Sube el comprobante de tu pago</small>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-check mt-4 mb-4">
              <input class="form-check-input" type="checkbox" id="terms" required>
              <label class="form-check-label" for="terms">
                Acepto los <a href="#" style="color: var(--primary); text-decoration: none; font-weight: 600;">términos y condiciones</a> y la <a href="#" style="color: var(--primary); text-decoration: none; font-weight: 600;">política de privacidad</a>
              </label>
            </div>

            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary" style="flex: 1;">
                <i class="bi bi-check-circle me-2"></i>Completar Inscripción
              </button>
              <a href="<?= url('catalogo') ?>" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left me-2"></i>Volver
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Guía de Pasos -->
    <div class="col-lg-5">
      <div class="card fade-in" style="background: linear-gradient(135deg, #F0F9FF 0%, #fff 100%); border: 1px solid #BFDBFE;">
        <div class="card-header" style="border-bottom: 1px solid #BFDBFE;">
          <h5 class="mb-0"><i class="bi bi-list-check me-2" style="color: #3B82F6;"></i>Pasos para tu inscripción</h5>
        </div>
        <div class="card-body">
          <div class="step-guide">
            <!-- Paso 1 -->
            <div class="step-item">
              <div class="step-number" style="background: #3B82F6; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0;">1</div>
              <div style="flex: 1;">
                <h6 style="font-weight: 700; margin-bottom: 4px;">Elige método de pago</h6>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0;">Selecciona la opción que mejor se adapte a ti</p>
              </div>
            </div>

            <!-- Paso 2 -->
            <div class="step-item">
              <div class="step-number" style="background: #3B82F6; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0;">2</div>
              <div style="flex: 1;">
                <h6 style="font-weight: 700; margin-bottom: 4px;">Completa tu inscripción</h6>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0;">Haz clic en "Completar Inscripción"</p>
              </div>
            </div>

            <!-- Paso 3 -->
            <div class="step-item">
              <div class="step-number" style="background: #3B82F6; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0;">3</div>
              <div style="flex: 1;">
                <h6 style="font-weight: 700; margin-bottom: 4px;">Realiza el pago</h6>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0;">Sigue las instrucciones según tu método seleccionado</p>
              </div>
            </div>

            <!-- Paso 4 -->
            <div class="step-item">
              <div class="step-number" style="background: #3B82F6; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0;">4</div>
              <div style="flex: 1;">
                <h6 style="font-weight: 700; margin-bottom: 4px;">Confirma tu pago</h6>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0;">Sube el comprobante en "Mis Matrículas"</p>
              </div>
            </div>

            <!-- Paso 5 -->
            <div class="step-item">
              <div class="step-number" style="background: #10B981; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0;"><i class="bi bi-check-lg"></i></div>
              <div style="flex: 1;">
                <h6 style="font-weight: 700; margin-bottom: 4px; color: #10B981;">¡Listo!</h6>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0;">Accede a tu curso cuando sea aprobado</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- FAQ / Preguntas Frecuentes -->
      <div class="card fade-in mt-3">
        <div class="card-header">
          <h5 class="mb-0"><i class="bi bi-question-circle me-2"></i>Preguntas frecuentes</h5>
        </div>
        <div class="card-body" style="font-size: 0.85rem;">
          <div class="mb-3">
            <strong>¿Cuánto tiempo tarda en aprobar mi inscripción?</strong>
            <p class="mt-1 mb-0">Normalmente entre 24-48 horas después de confirmar tu pago.</p>
          </div>
          <div class="mb-3">
            <strong>¿Puedo cambiar de método de pago?</strong>
            <p class="mt-1 mb-0">Sí, puedes actualizar tu método en "Mis Matrículas".</p>
          </div>
          <div>
            <strong>¿Hay reembolsos disponibles?</strong>
            <p class="mt-1 mb-0">Sí, revisa nuestra política de reembolsos para más detalles.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .payment-method-card {
    background: #fff;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-xs);
    padding: 16px;
    cursor: pointer;
    transition: var(--transition);
  }
  .payment-method-card:hover {
    border-color: var(--primary-light);
    box-shadow: 0 0 0 3px rgba(225, 29, 72, 0.05);
  }
  .form-check-input:checked ~ .form-check-label strong {
    color: var(--primary);
  }
  .step-item {
    display: flex;
    gap: 12px;
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid rgba(59, 130, 246, 0.1);
  }
  .step-item:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
  }
</style>

<script>
function updatePaymentForm(radio) {
  // Ocultar todos los formularios de pago
  document.querySelectorAll('[id^="details_"]').forEach(el => el.style.display = 'none');
  
  // Mostrar el formulario del método seleccionado
  const selectedMethod = radio.value;
  const detailsElement = document.getElementById('details_' + selectedMethod);
  if (detailsElement) {
    detailsElement.style.display = 'block';
  }
}

// Validar que se seleccione un método de pago
document.getElementById('enrollmentForm')?.addEventListener('submit', function(e) {
  const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
  if (!selectedMethod) {
    e.preventDefault();
    alert('Por favor selecciona un método de pago');
  }
});
</script>
