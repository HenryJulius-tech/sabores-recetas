/* ════════════════════════════════════════════════════════
   main.js — Sabores & Recetas
   ════════════════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', function () {

    // ── Auto-dismiss de alertas flash ────────────────────────
    setTimeout(function () {
        document.querySelectorAll('.alert-dismissible').forEach(function (a) {
            var bs = bootstrap.Alert.getInstance(a);
            if (bs) bs.close();
            else a.remove();
        });
    }, 5000);

    // ── Helpers ──────────────────────────────────────────────
    function getCsrf() {
        var m = document.querySelector('meta[name="csrf-token"]');
        return m ? m.content : '';
    }
    function getBaseUrl() {
        return document.querySelector('meta[name="base-url"]')?.content || '/';
    }

    // ── Sidebar toggle móvil ─────────────────────────────────
    document.getElementById('menu-toggle')?.addEventListener('click', function () {
        document.getElementById('sidebar')?.classList.toggle('show');
    });
    // Cerrar sidebar al hacer clic fuera en móvil
    document.addEventListener('click', function (e) {
        var sidebar = document.getElementById('sidebar');
        var toggle  = document.getElementById('menu-toggle');
        if (sidebar && sidebar.classList.contains('show') &&
            !sidebar.contains(e.target) && !toggle?.contains(e.target)) {
            sidebar.classList.remove('show');
        }
    });

    // ════════════════════════════════════════════════════════
    //  SISTEMA DE NOTIFICACIONES
    // ════════════════════════════════════════════════════════
    var notifBtn    = document.getElementById('notifBtn');
    var notifPanel  = document.getElementById('notifPanel');
    var notifBadge  = document.getElementById('notifBadge');
    var notifList   = document.getElementById('notifList');
    var markAllBtn  = document.getElementById('markAllRead');

    var NOTIF_ICONS = {
        inscripcion: { cls: 'inscripcion', icon: 'bi-person-plus-fill' },
        pago:        { cls: 'pago',        icon: 'bi-receipt' },
        aprobacion:  { cls: 'aprobacion',  icon: 'bi-check-circle-fill' }
    };

    function renderNotifs(data) {
        if (data.count > 0) {
            notifBadge.textContent = data.count > 9 ? '9+' : data.count;
            notifBadge.style.display = 'flex';
        } else {
            notifBadge.style.display = 'none';
        }

        if (!data.items || data.items.length === 0) {
            notifList.innerHTML = '<div class="notif-empty"><i class="bi bi-bell-slash"></i><p>Sin notificaciones nuevas</p></div>';
            return;
        }

        notifList.innerHTML = data.items.map(function (n) {
            var cfg  = NOTIF_ICONS[n.tipo] || { cls: 'default', icon: 'bi-bell-fill' };
            var href = n.url ? 'href="' + n.url + '"' : '';
            return '<a class="notif-item" ' + href + ' data-id="' + n.id + '">' +
                '<div class="notif-icon ' + cfg.cls + '"><i class="bi ' + cfg.icon + '"></i></div>' +
                '<div class="notif-item-body">' +
                    '<p class="notif-item-msg">' + n.mensaje + '</p>' +
                    '<span class="notif-item-time">' + n.tiempo + '</span>' +
                '</div></a>';
        }).join('');

        // Marcar individual al hacer clic
        notifList.querySelectorAll('.notif-item').forEach(function (item) {
            item.addEventListener('click', function () {
                var id = this.dataset.id;
                fetch('api/marcar-leida.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: '_token=' + getCsrf() + '&id=' + id
                });
                this.style.opacity = '0.5';
            });
        });
    }

    function fetchNotifs() {
        fetch('api/notificaciones.php')
            .then(function (r) { return r.json(); })
            .then(renderNotifs)
            .catch(function () {});
    }

    if (notifBtn) {
        fetchNotifs();
        setInterval(fetchNotifs, 30000); // Polling cada 30 s

        notifBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            notifPanel.classList.toggle('open');
            if (profileDropdown) profileDropdown.classList.remove('open');
            if (profileBtn) profileBtn.classList.remove('open');
        });

        markAllBtn?.addEventListener('click', function () {
            fetch('api/marcar-leida.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: '_token=' + getCsrf() + '&id=all'
            }).then(fetchNotifs);
            notifList.innerHTML = '<div class="notif-empty"><i class="bi bi-bell-slash"></i><p>Sin notificaciones nuevas</p></div>';
            notifBadge.style.display = 'none';
            notifPanel.classList.remove('open');
        });
    }

    // ════════════════════════════════════════════════════════
    //  DROPDOWN DE PERFIL
    // ════════════════════════════════════════════════════════
    var profileBtn      = document.getElementById('profileBtn');
    var profileDropdown = document.getElementById('profileDropdown');

    profileBtn?.addEventListener('click', function (e) {
        e.stopPropagation();
        profileDropdown.classList.toggle('open');
        profileBtn.classList.toggle('open');
        if (notifPanel) notifPanel.classList.remove('open');
    });

    // Cerrar ambos paneles al hacer clic fuera
    document.addEventListener('click', function (e) {
        if (notifPanel && !notifPanel.contains(e.target) && !notifBtn?.contains(e.target)) {
            notifPanel.classList.remove('open');
        }
        if (profileDropdown && !profileDropdown.contains(e.target) && !profileBtn?.contains(e.target)) {
            profileDropdown.classList.remove('open');
            profileBtn?.classList.remove('open');
        }
    });

    // ════════════════════════════════════════════════════════
    //  MODAL DE PAGO UNIVERSAL (Fix Bug Parpadeo)
    //  Se usa un único modal que recibe datos por JS.
    //  Esto elimina la causa raíz: múltiples modales en el DOM.
    // ════════════════════════════════════════════════════════
    var pagoModal = document.getElementById('pagoModalUniversal');
    if (pagoModal) {
        var bsModal = new bootstrap.Modal(pagoModal, { backdrop: 'static' });

        // Los botones "Subir comprobante" populan el modal con data-*
        document.querySelectorAll('[data-open-pago]').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var inscId  = this.dataset.inscId;
                var precio  = this.dataset.precio;
                var titulo  = this.dataset.titulo;

                pagoModal.querySelector('#pagoInscripcionId').value   = inscId;
                pagoModal.querySelector('#pagoTituloCurso').textContent = titulo || 'Curso';
                pagoModal.querySelector('#pagoMonto').textContent      = precio || '';
                pagoModal.querySelector('#pagoComprobanteInput').value  = '';
                pagoModal.querySelector('.file-name').style.display     = 'none';
                pagoModal.querySelector('.file-name').textContent       = '';

                bsModal.show();
            });
        });

        // ── Drag & Drop en zona de comprobante ─────────────────
        var fileZone  = pagoModal.querySelector('.file-upload-zone');
        var fileInput = pagoModal.querySelector('#pagoComprobanteInput');

        if (fileZone && fileInput) {
            fileZone.addEventListener('click', function () {
                fileInput.click();
            });

            // Prevenir propagación del input — esto era la causa del parpadeo
            fileInput.addEventListener('click', function (e) { e.stopPropagation(); });

            fileInput.addEventListener('change', function (e) {
                e.stopPropagation();
                updateFileZone(this.files[0]);
            });

            fileZone.addEventListener('dragover', function (e) {
                e.preventDefault(); e.stopPropagation();
                this.classList.add('drag-over');
            });
            fileZone.addEventListener('dragleave', function (e) {
                e.stopPropagation();
                this.classList.remove('drag-over');
            });
            fileZone.addEventListener('drop', function (e) {
                e.preventDefault(); e.stopPropagation();
                this.classList.remove('drag-over');
                var file = e.dataTransfer.files[0];
                if (file && validateFile(file)) {
                    // Asignar al input via DataTransfer
                    var dt = new DataTransfer();
                    dt.items.add(file);
                    fileInput.files = dt.files;
                    updateFileZone(file);
                }
            });
        }

        function updateFileZone(file) {
            if (!file) return;
            if (!validateFile(file)) return;
            var nameEl = pagoModal.querySelector('.file-name');
            nameEl.textContent = '✓ ' + file.name + ' (' + (file.size / 1024).toFixed(0) + ' KB)';
            nameEl.style.display = 'block';
        }

        function validateFile(file) {
            var allowed = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];
            var maxSize = 2 * 1024 * 1024; // 2MB
            if (!allowed.includes(file.type)) {
                alert('Solo se permiten imágenes (JPG, PNG, WEBP) o PDF.');
                return false;
            }
            if (file.size > maxSize) {
                alert('El archivo no puede superar 2MB.');
                return false;
            }
            return true;
        }

        // ── Submit del formulario con Fetch API (Evita recarga y parpadeo) ──
        var form = pagoModal.querySelector('form');
        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                e.stopPropagation();

                var submitBtn = form.querySelector('[type="submit"]');
                var originalText = submitBtn.innerHTML;
                
                // Deshabilitar botón y mostrar spinner
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Enviando...';

                var formData = new FormData(form);
                
                // Resolver ruta relativa usando helper getBaseUrl()
                var submitUrl = getBaseUrl() + 'estudiante/procesar_pago.php';

                fetch(submitUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(function (r) {
                    if (!r.ok) {
                        return r.json().then(function (err) { throw new Error(err.message || 'Error en el servidor'); });
                    }
                    return r.json();
                })
                .then(function (res) {
                    if (res.success) {
                        // Ocultar modal y mostrar alerta de éxito
                        bsModal.hide();
                        alert(res.message || 'Comprobante enviado con éxito.');
                        // Recargar la página
                        window.location.reload();
                    } else {
                        alert(res.message || 'Error al enviar el comprobante.');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                })
                .catch(function (err) {
                    console.error(err);
                    alert(err.message || 'Error de conexión con el servidor.');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
            });
        }
    }

    // ════════════════════════════════════════════════════════
    //  DRAG & DROP FOTO DE PERFIL
    // ════════════════════════════════════════════════════════
    var avatarDropzone = document.querySelector('.avatar-dropzone');
    var avatarInput    = document.getElementById('avatarFileInput');
    var avatarPreview  = document.getElementById('avatarPreviewLarge');
    var avatarError    = document.querySelector('.avatar-upload-error');

    if (avatarDropzone && avatarInput) {
        avatarDropzone.addEventListener('click', function () { avatarInput.click(); });
        avatarInput.addEventListener('click', function (e) { e.stopPropagation(); });

        avatarInput.addEventListener('change', function (e) {
            e.stopPropagation();
            handleAvatarFile(this.files[0]);
        });

        avatarDropzone.addEventListener('dragover', function (e) {
            e.preventDefault(); e.stopPropagation();
            this.classList.add('drag-over');
        });
        avatarDropzone.addEventListener('dragleave', function (e) {
            e.stopPropagation(); this.classList.remove('drag-over');
        });
        avatarDropzone.addEventListener('drop', function (e) {
            e.preventDefault(); e.stopPropagation();
            this.classList.remove('drag-over');
            handleAvatarFile(e.dataTransfer.files[0]);
        });
    }

    function handleAvatarFile(file) {
        if (!file) return;
        var allowed = ['image/jpeg', 'image/png', 'image/webp'];
        var maxSize = 2 * 1024 * 1024;
        if (avatarError) avatarError.style.display = 'none';

        if (!allowed.includes(file.type)) {
            if (avatarError) { avatarError.textContent = 'Solo JPG, PNG o WEBP.'; avatarError.style.display = 'block'; }
            return;
        }
        if (file.size > maxSize) {
            if (avatarError) { avatarError.textContent = 'Máximo 2 MB.'; avatarError.style.display = 'block'; }
            return;
        }
        // Previsualización inmediata
        if (avatarPreview) {
            var reader = new FileReader();
            reader.onload = function (ev) {
                avatarPreview.src = ev.target.result;
                // Actualizar también el avatar del topbar (feedback instantáneo)
                var topbarAvatar = document.getElementById('topbarAvatar');
                var dropdownAvatar = document.getElementById('dropdownAvatar');
                if (topbarAvatar) topbarAvatar.src = ev.target.result;
                if (dropdownAvatar) dropdownAvatar.src = ev.target.result;
            };
            reader.readAsDataURL(file);
        }
        // Asignar al input real para el submit
        if (avatarInput) {
            var dt = new DataTransfer();
            dt.items.add(file);
            avatarInput.files = dt.files;
        }
    }

    // ── confirmAction (mantener compatibilidad) ───────────
    window.confirmAction = function (msg, formId) {
        if (confirm(msg || '¿Estás seguro?')) { document.getElementById(formId).submit(); }
    };
    window.formatCurrency = function (a) { return '$' + parseInt(a).toLocaleString('es-CO'); };

    // ── Preview de imágenes con data-preview ─────────────
    document.querySelectorAll('input[type="file"][data-preview]').forEach(function (el) {
        el.addEventListener('change', function (e) {
            e.stopPropagation();
            var p = document.getElementById(this.dataset.preview);
            if (p && this.files && this.files[0]) {
                p.style.display = 'block';
                var r = new FileReader();
                r.onload = function (ev) { p.src = ev.target.result; };
                r.readAsDataURL(this.files[0]);
            }
        });
    });

});
