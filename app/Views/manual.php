<h1 class="page-title mb-4">Manual de Usuario</h1>
<div class="row g-4">
    <div class="col-md-3">
        <div class="list-group shadow-sm">
            <a class="list-group-item list-group-item-action active" href="#intro" data-bs-toggle="list">IntroducciÃ³n</a>
            <?php if ($role === 'admin'): ?>
            <a class="list-group-item list-group-item-action" href="#dashboard" data-bs-toggle="list">Dashboard</a>
            <a class="list-group-item list-group-item-action" href="#productos" data-bs-toggle="list">Productos</a>
            <a class="list-group-item list-group-item-action" href="#compras" data-bs-toggle="list">Aprobar Compras</a>
            <a class="list-group-item list-group-item-action" href="#movimientos" data-bs-toggle="list">Movimientos</a>
            <a class="list-group-item list-group-item-action" href="#usuarios" data-bs-toggle="list">Usuarios</a>
            <?php elseif ($role === 'client'): ?>
            <a class="list-group-item list-group-item-action" href="#tienda" data-bs-toggle="list">Tienda</a>
            <a class="list-group-item list-group-item-action" href="#comprar" data-bs-toggle="list">CÃ³mo Comprar</a>
            <a class="list-group-item list-group-item-action" href="#pagos" data-bs-toggle="list">Pagos</a>
            <?php elseif ($role === 'worker'): ?>
            <a class="list-group-item list-group-item-action" href="#movimientos" data-bs-toggle="list">Movimientos</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-9">
        <div class="tab-content card-modern p-4">
            <div class="tab-pane fade show active" id="intro">
                <h5>Bienvenido a Finca Bananera</h5>
                <p>Sistema de gestiÃ³n integral para la finca. Permite administrar productos, ventas, movimientos financieros y usuarios.</p>
                <p>Tu rol actual es: <strong class="text-success"><?= ucfirst($role) ?></strong></p>
                <hr>
                <h6>Â¿Necesitas ayuda?</h6>
                <p class="text-muted">Contacta al administrador del sistema para cualquier inquietud.</p>
            </div>
            <?php if ($role === 'admin'): ?>
            <div class="tab-pane fade" id="dashboard">
                <h5>Dashboard</h5>
                <p>Panel principal con resumen financiero, grÃ¡fico de flujo de caja y estado del sistema.</p>
                <ul>
                    <li><strong>Ingresos/Gastos/Balance:</strong> Tarjetas con totales del perÃ­odo.</li>
                    <li><strong>GrÃ¡fico:</strong> Muestra ingresos vs gastos. Filtra por dÃ­a/semana/mes.</li>
                    <li><strong>Ãšltimos movimientos:</strong> Tabla con los 5 movimientos mÃ¡s recientes.</li>
                </ul>
            </div>
            <div class="tab-pane fade" id="productos">
                <h5>GestiÃ³n de Productos</h5>
                <p>CRUD completo de productos. Desde aquÃ­ puedes crear, editar y eliminar productos del catÃ¡logo.</p>
                <ul>
                    <li><strong>Crear:</strong> BotÃ³n "Nuevo Producto" â†’ formulario con nombre, precio, stock, descripciÃ³n e imagen.</li>
                    <li><strong>Editar:</strong> Icono de lÃ¡piz en cada fila de la tabla.</li>
                    <li><strong>Eliminar:</strong> Icono de papelera (pide confirmaciÃ³n).</li>
                    <li>El stock se actualiza automÃ¡ticamente al aprobar/rechazar compras.</li>
                </ul>
            </div>
            <div class="tab-pane fade" id="compras">
                <h5>Aprobar Compras</h5>
                <p>Revisa las compras realizadas por los clientes, visualiza los comprobantes de pago y aprueba o rechaza.</p>
                <ul>
                    <li><strong>Aprobar:</strong> Descarga el stock y registra el ingreso financiero automÃ¡ticamente.</li>
                    <li><strong>Rechazar:</strong> Devuelve el stock al inventario.</li>
                </ul>
            </div>
            <div class="tab-pane fade" id="movimientos">
                <h5>Movimientos Financieros</h5>
                <p>Registro de ingresos y gastos con filtros por tipo, categorÃ­a, estado y bÃºsqueda por texto.</p>
                <ul>
                    <li><strong>Crear:</strong> Los movimientos de admin se aprueban automÃ¡ticamente.</li>
                    <li><strong>Filtros:</strong> Busca por descripciÃ³n, tipo, estado y categorÃ­a.</li>
                    <li><strong>Exportar:</strong> Descarga los resultados filtrados a CSV.</li>
                </ul>
            </div>
            <div class="tab-pane fade" id="usuarios">
                <h5>GestiÃ³n de Usuarios</h5>
                <p>Administra los usuarios del sistema con tres roles: admin, worker (trabajador) y client (cliente).</p>
                <ul>
                    <li>No puedes eliminarte a ti mismo.</li>
                    <li>Debe haber al menos un administrador.</li>
                    <li>Los usuarios con compras o movimientos asociados no pueden eliminarse.</li>
                </ul>
            </div>
            <?php elseif ($role === 'client'): ?>
            <div class="tab-pane fade" id="tienda">
                <h5>Tienda</h5>
                <p>CatÃ¡logo de productos disponibles para la venta. Cada producto muestra nombre, descripciÃ³n, precio y stock.</p>
                <p>Haz clic en "Agregar" para aÃ±adir productos al carrito.</p>
            </div>
            <div class="tab-pane fade" id="comprar">
                <h5>CÃ³mo Comprar</h5>
                <ol>
                    <li>Navega por la tienda y agrega productos al carrito.</li>
                    <li>Ajusta las cantidades desde el panel del carrito (lado derecho).</li>
                    <li>Haz clic en "Confirmar Compra" para generar tu pedido.</li>
                    <li>Ve a "Mis Compras" para subir el comprobante de pago.</li>
                </ol>
            </div>
            <div class="tab-pane fade" id="pagos">
                <h5>Pagos</h5>
                <p>En "Mis Compras" verÃ¡s tus pedidos pendientes. Haz clic en "Subir Pago" para:</p>
                <ol>
                    <li>Seleccionar el mÃ©todo de pago (Efectivo, Tarjeta, Transferencia).</li>
                    <li>Subir una foto del comprobante de pago.</li>
                    <li>Esperar la aprobaciÃ³n del administrador.</li>
                </ol>
                <p>Una vez aprobado, podrÃ¡s descargar la factura en PDF.</p>
            </div>
            <?php elseif ($role === 'worker'): ?>
            <div class="tab-pane fade" id="movimientos">
                <h5>Movimientos Financieros</h5>
                <p>Puedes registrar movimientos (ingresos/gastos), pero quedarÃ¡n pendientes hasta que un admin los apruebe.</p>
                <p>Usa los filtros para buscar movimientos por tipo, categorÃ­a o texto.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
