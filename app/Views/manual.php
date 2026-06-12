<h1 class="page-title mb-4">Manual de Usuario</h1>
<div class="row g-4">
    <div class="col-md-3">
        <div class="list-group shadow-sm">
            <a class="list-group-item list-group-item-action active" href="#intro" data-bs-toggle="list">Introducción</a>
            <?php if ($role === 'admin'): ?>
            <a class="list-group-item list-group-item-action" href="#dashboard" data-bs-toggle="list">Dashboard</a>
            <a class="list-group-item list-group-item-action" href="#cursos" data-bs-toggle="list">Cursos</a>
            <a class="list-group-item list-group-item-action" href="#matriculas" data-bs-toggle="list">Aprobar Matrículas</a>
            <a class="list-group-item list-group-item-action" href="#movimientos" data-bs-toggle="list">Movimientos</a>
            <a class="list-group-item list-group-item-action" href="#usuarios" data-bs-toggle="list">Usuarios</a>
            <a class="list-group-item list-group-item-action" href="#notificaciones" data-bs-toggle="list">Notificaciones</a>
            <a class="list-group-item list-group-item-action" href="#auditoria" data-bs-toggle="list">Auditoría</a>
            <a class="list-group-item list-group-item-action" href="#configuracion" data-bs-toggle="list">Configuración</a>
            <a class="list-group-item list-group-item-action" href="#contacto" data-bs-toggle="list">Contacto</a>
            <?php elseif ($role === 'client'): ?>
            <a class="list-group-item list-group-item-action" href="#catalogo" data-bs-toggle="list">Catálogo</a>
            <a class="list-group-item list-group-item-action" href="#matricularse" data-bs-toggle="list">Cómo Matricularse</a>
            <a class="list-group-item list-group-item-action" href="#pagos" data-bs-toggle="list">Pagos</a>
            <a class="list-group-item list-group-item-action" href="#factura" data-bs-toggle="list">Factura</a>
            <a class="list-group-item list-group-item-action" href="#notificaciones" data-bs-toggle="list">Notificaciones</a>
            <a class="list-group-item list-group-item-action" href="#configuracion" data-bs-toggle="list">Configuración</a>
            <a class="list-group-item list-group-item-action" href="#recuperar" data-bs-toggle="list">Recuperar Contraseña</a>
            <?php elseif ($role === 'worker'): ?>
            <a class="list-group-item list-group-item-action" href="#movimientos" data-bs-toggle="list">Movimientos</a>
            <a class="list-group-item list-group-item-action" href="#notificaciones" data-bs-toggle="list">Notificaciones</a>
            <a class="list-group-item list-group-item-action" href="#configuracion" data-bs-toggle="list">Configuración</a>
            <a class="list-group-item list-group-item-action" href="#recuperar" data-bs-toggle="list">Recuperar Contraseña</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-9">
        <div class="tab-content card-modern p-4">
            <div class="tab-pane fade show active" id="intro">
                <h5>Bienvenido a Sabores & Recetas</h5>
                <p>Plataforma de gestión académica y financiera para escuela de cocina. Administra cursos, matrículas, movimientos, usuarios y más.</p>
                <p>Tu rol actual es: <strong class="text-success"><?= ucfirst($role) ?></strong></p>
                <hr>
                <h6>Novedades</h6>
                <ul>
                    <li><strong>Notificaciones:</strong> Campana en la barra superior para ver eventos en tiempo real.</li>
                    <li><strong>Auditoría:</strong> Registro completo de todas las acciones del sistema (solo admin).</li>
                    <li><strong>Recuperación de contraseña:</strong> Enlace en la pantalla de login para restablecer acceso.</li>
                    <li><strong>Factura moderna:</strong> Vista previa en la misma página con opción de imprimir.</li>
                    <li><strong>Configuración:</strong> Preferencias de notificaciones por email y newsletter.</li>
                </ul>
            </div>

            <?php if ($role === 'admin'): ?>
            <div class="tab-pane fade" id="dashboard">
                <h5>Dashboard</h5>
                <p>Panel principal con resumen financiero, gráfico de flujo de caja y últimos movimientos.</p>
                <ul>
                    <li><strong>Tarjetas:</strong> Ingresos, Gastos y Balance del período.</li>
                    <li><strong>Gráfico:</strong> Ingresos vs Gastos. Filtra por día/semana/mes.</li>
                    <li><strong>Movimientos recientes:</strong> Últimos 5 movimientos del sistema.</li>
                </ul>
            </div>
            <div class="tab-pane fade" id="cursos">
                <h5>Gestión de Cursos</h5>
                <p>CRUD completo de cursos del catálogo.</p>
                <ul>
                    <li><strong>Crear:</strong> Botón "Nuevo Curso" → formulario con nombre, precio, cupo, descripción e imagen.</li>
                    <li><strong>Editar:</strong> Icono de lápiz en la tabla.</li>
                    <li><strong>Eliminar:</strong> Icono de papelera (pide confirmación).</li>
                    <li>El cupo disponible se actualiza automáticamente al aprobar/rechazar matrículas.</li>
                </ul>
            </div>
            <div class="tab-pane fade" id="matriculas">
                <h5>Aprobar Matrículas</h5>
                <p>Revisa las matrículas pendientes con sus comprobantes de pago.</p>
                <ul>
                    <li><strong>Columna "Pago":</strong> Muestra "Sin comprobante", "Ver comprobante" (enlace) o "Pagado".</li>
                    <li><strong>Aprobar:</strong> Aprueba la matrícula, el pago y registra el ingreso financiero automáticamente. El estudiante recibe notificación.</li>
                    <li><strong>Rechazar:</strong> Rechaza matrícula y pago. El estudiante recibe notificación.</li>
                </ul>
            </div>
            <div class="tab-pane fade" id="movimientos">
                <h5>Movimientos Financieros</h5>
                <p>Registro de ingresos y gastos.</p>
                <ul>
                    <li><strong>Crear:</strong> Admin puede crear movimientos que se aprueban automáticamente.</li>
                    <li><strong>Aprobar/Rechazar:</strong> Movimientos creados por workers requieren aprobación. Se notifica al worker.</li>
                    <li><strong>Filtros:</strong> Búsqueda por texto, tipo, estado y categoría.</li>
                    <li><strong>Exportar CSV:</strong> Descarga los resultados filtrados.</li>
                </ul>
            </div>
            <div class="tab-pane fade" id="usuarios">
                <h5>Gestión de Usuarios</h5>
                <p>Administra usuarios con roles: admin, worker y client.</p>
                <ul>
                    <li><strong>Resetear contraseña:</strong> Botón de llave 🔑 genera un enlace de restablecimiento que puedes compartir con el usuario.</li>
                    <li>No puedes eliminarte a ti mismo ni al último administrador.</li>
                </ul>
            </div>
            <?php endif; ?>

            <div class="tab-pane fade" id="notificaciones">
                <h5>Notificaciones</h5>
                <p>La campana en la barra superior muestra notificaciones en tiempo real.</p>
                <ul>
                    <li><strong>Badge rojo:</strong> Muestra la cantidad de notificaciones no leídas. Se actualiza automáticamente cada 30 segundos.</li>
                    <li><strong>Al hacer clic:</strong> Se abre el listado de notificaciones recientes.</li>
                    <li><strong>No leídas:</strong> Tienen un borde rojo a la izquierda.</li>
                    <li><strong>Al hacer clic en una:</strong> Marca como leída y navega a la página correspondiente.</li>
                    <li><strong>Eliminar:</strong> Botón ✕ en cada notificación para eliminarla individualmente.</li>
                    <li><strong>"Marcar todas leídas":</strong> Botón en el encabezado del menú.</li>
                </ul>
                <h6 class="mt-3">¿Qué eventos generan notificaciones?</h6>
                <table class="table table-sm table-bordered">
                    <tr><th>Evento</th><th>¿Quién lo recibe?</th></tr>
                    <tr><td>Nuevo registro de usuario</td><td>Admin</td></tr>
                    <tr><td>Nueva inscripción a curso</td><td>Admin</td></tr>
                    <tr><td>Comprobante de pago subido</td><td>Admin</td></tr>
                    <tr><td>Mensaje de contacto</td><td>Admin</td></tr>
                    <tr><td>Movimiento pendiente (worker)</td><td>Admin</td></tr>
                    <tr><td>Matrícula aprobada/rechazada</td><td>Estudiante</td></tr>
                    <tr><td>Pago aprobado/rechazado</td><td>Estudiante</td></tr>
                    <tr><td>Movimiento aprobado/rechazado</td><td>Worker</td></tr>
                </table>
            </div>

            <div class="tab-pane fade" id="configuracion">
                <h5>Configuración</h5>
                <p>Accede desde el menú de usuario o el sidebar. Personaliza tu experiencia:</p>
                <ul>
                    <li><strong>Cuenta:</strong> Enlace a editar perfil (nombre, email, bio, foto, contraseña).</li>
                    <li><strong>Notificaciones:</strong> Toggles para activar/desactivar notificaciones por email y newsletter. Los cambios se guardan automáticamente.</li>
                    <li><strong>Privacidad:</strong> Perfil público y mostrar progreso.</li>
                    <li><strong>Ayuda & Soporte:</strong> Enlace al formulario de contacto.</li>
                    <li><strong>Zona de Riesgo:</strong> Eliminación de cuenta.</li>
                </ul>
                <?php if ($role !== 'admin'): ?>
                <p><strong>Preferencias de Aprendizaje:</strong> Selecciona tu nivel (principiante, intermedio, avanzado).</p>
                <?php endif; ?>
            </div>

            <?php if ($role === 'admin'): ?>
            <div class="tab-pane fade" id="auditoria">
                <h5>Auditoría</h5>
                <p>Registro cronológico de todas las acciones importantes del sistema. Accede desde el sidebar.</p>
                <ul>
                    <li><strong>Columnas:</strong> Fecha, Usuario, Rol, Acción, Descripción, IP.</li>
                    <li><strong>Filtros:</strong> Por acción, usuario, rol y rango de fechas.</li>
                    <li><strong>Paginación:</strong> 50 registros por página.</li>
                </ul>
                <h6 class="mt-3">Acciones registradas</h6>
                <ul>
                    <li>Inicio/cierre de sesión (exitoso y fallido)</li>
                    <li>Registro de nuevos usuarios</li>
                    <li>Creación, aprobación y rechazo de matrículas</li>
                    <li>Subida de comprobantes de pago</li>
                    <li>Creación, aprobación y rechazo de movimientos</li>
                    <li>Actualización de perfil y cambio de foto</li>
                    <li>Mensajes de contacto</li>
                    <li>Solicitudes de recuperación de contraseña</li>
                    <li>Reset de contraseña por administrador</li>
                </ul>
            </div>
            <?php endif; ?>

            <?php if ($role !== 'admin'): ?>
            <div class="tab-pane fade" id="recuperar">
                <h5>Recuperar Contraseña</h5>
                <p>Si olvidaste tu contraseña:</p>
                <ol>
                    <li>En la pantalla de login, haz clic en "¿Olvidaste tu contraseña?".</li>
                    <li>Ingresa tu correo electrónico registrado.</li>
                    <li>Recibirás un enlace de restablecimiento (se muestra en pantalla).</li>
                    <li>Haz clic en el enlace y elige una nueva contraseña (mínimo 8 caracteres).</li>
                    <li>Inicia sesión con tu nueva contraseña.</li>
                </ol>
                <p>Si el admin te ha generado un enlace de restablecimiento, úsalo directamente.</p>
            </div>
            <?php endif; ?>

            <?php if ($role === 'client'): ?>
            <div class="tab-pane fade" id="catalogo">
                <h5>Catálogo de Cursos</h5>
                <p>Navega por todos los cursos disponibles. Cada curso muestra nombre, descripción, precio y cupo.</p>
                <p>Haz clic en "Matricularme" para iniciar el proceso de inscripción.</p>
            </div>
            <div class="tab-pane fade" id="matricularse">
                <h5>Cómo Matricularse</h5>
                <ol>
                    <li>Navega por el catálogo y elige un curso.</li>
                    <li>Haz clic en "Matricularme" para generar tu solicitud.</li>
                    <li>Selecciona método de pago y sube el comprobante si es necesario.</li>
                    <li>Revisa el estado desde "Mis Matrículas".</li>
                    <li>Espera la aprobación del administrador (recibirás notificación).</li>
                </ol>
            </div>
            <div class="tab-pane fade" id="pagos">
                <h5>Pagos</h5>
                <p>Desde "Mis Matrículas" puedes:</p>
                <ul>
                    <li><strong>Subir comprobante:</strong> Botón "Subir Pago" en matrículas pendientes.</li>
                    <li><strong>Ver estado:</strong> "Comprobante enviado", "Pendiente" o "Pagado".</li>
                    <li>Recibirás notificación cuando el admin apruebe o rechace tu pago.</li>
                </ul>
            </div>
            <div class="tab-pane fade" id="factura">
                <h5>Factura</h5>
                <p>Una vez aprobada tu matrícula, puedes ver la factura desde "Mis Matrículas".</p>
                <ul>
                    <li>La factura se abre en la misma página (no en nueva pestaña).</li>
                    <li>Incluye: datos del alumno, datos de la academia, detalle del curso, método de pago y total.</li>
                    <li><strong>Imprimir:</strong> Botón "Imprimir / Guardar PDF" para imprimir o guardar como PDF.</li>
                </ul>
            </div>
            <?php endif; ?>

            <?php if (in_array($role, ['admin','worker'])): ?>
            <div class="tab-pane fade" id="movimientos">
                <h5>Movimientos Financieros</h5>
                <p>Registro de ingresos y gastos.</p>
                <ul>
                    <li><strong>Crear:</strong> Formulario con tipo, monto, descripción, categoría, forma de pago y soporte.</li>
                    <?php if ($role === 'worker'): ?>
                    <li>Los movimientos creados por workers quedan <strong>pendientes</strong> hasta que un admin los apruebe.</li>
                    <li>Recibirás una notificación cuando aprueben o rechacen tu movimiento.</li>
                    <?php endif; ?>
                    <li><strong>Filtros:</strong> Búsqueda por texto, tipo, estado y categoría.</li>
                    <li><strong>Exportar CSV:</strong> Descarga los resultados.</li>
                </ul>
            </div>
            <?php endif; ?>

            <div class="tab-pane fade" id="contacto">
                <h5>Contacto & Soporte</h5>
                <p>¿Necesitas ayuda? Puedes contactar al soporte de dos formas:</p>
                <ul>
                    <li><strong>Configuración:</strong> Botón "Contactar Soporte" en la tarjeta de Ayuda & Soporte.</li>
                    <li><strong>Formulario directo:</strong> Navega a la página de contacto para enviar un mensaje.</li>
                </ul>
                <p>Tu mensaje se envía a los administradores, quienes recibirán una notificación.</p>
            </div>
        </div>
    </div>
</div>
