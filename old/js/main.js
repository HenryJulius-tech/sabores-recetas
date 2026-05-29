document.addEventListener('DOMContentLoaded', () => {
    // 1. COMPORTAMIENTO DE SIDEBAR MÓVIL
    const sidebar = document.querySelector('.sidebar');
    const toggleBtn = document.querySelector('.sidebar-toggle');
    const closeBtn = document.querySelector('.sidebar-close');

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.add('active');
        });
    }

    if (closeBtn && sidebar) {
        closeBtn.addEventListener('click', () => {
            sidebar.classList.remove('active');
        });
    }

    // Cerrar sidebar si se hace clic fuera en móvil
    document.addEventListener('click', (e) => {
        if (sidebar && sidebar.classList.contains('active')) {
            if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                sidebar.classList.remove('active');
            }
        }
    });

    // 2. SISTEMA GLOBAL DE NOTIFICACIONES TOAST
    const body = document.body;
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container';
        body.appendChild(toastContainer);
    }

    window.showToast = (message, type = 'success') => {
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        
        let icon = '💡';
        if (type === 'success') icon = '✅';
        if (type === 'error') icon = '❌';
        if (type === 'warning') icon = '⚠️';
        
        toast.innerHTML = `
            <span class="toast-icon">${icon}</span>
            <div class="toast-message">${message}</div>
            <button class="toast-close">&times;</button>
        `;
        
        toastContainer.appendChild(toast);
        
        // Agregar evento de cierre
        const closeBtn = toast.querySelector('.toast-close');
        closeBtn.addEventListener('click', () => {
            toast.style.transform = 'translateX(120%)';
            setTimeout(() => toast.remove(), 300);
        });
        
        // Auto-remover después de 4 segundos
        setTimeout(() => {
            if (toast.parentNode) {
                toast.style.transform = 'translateX(120%)';
                setTimeout(() => toast.remove(), 300);
            }
        }, 4000);
    };

    // Procesar mensajes Flash enviados desde Flask
    const flashMessages = document.querySelectorAll('.flash-data');
    flashMessages.forEach(msg => {
        const text = msg.getAttribute('data-message');
        const category = msg.getAttribute('data-category') || 'info';
        // Mapear categorías de Flask (error -> error, success -> success, warning -> warning, message -> info)
        let type = 'info';
        if (category === 'error' || category === 'danger') type = 'error';
        if (category === 'success') type = 'success';
        if (category === 'warning') type = 'warning';
        
        window.showToast(text, type);
        msg.remove(); // Limpiar del DOM
    });

    // 3. CONTROL DE MODALES GLOBAL
    window.openModal = (modalId) => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('active');
        }
    };

    window.closeModal = (modalId) => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('active');
        }
    };

    // Cerrar modal al hacer clic en el fondo overlay
    const modals = document.querySelectorAll('.modal-overlay');
    modals.forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('active');
            }
        });
    });

    // 4. PREVISUALIZADOR DE CARGA DE IMÁGENES
    window.initImagePreview = (fileInputId, previewContainerId, previewImgId) => {
        const fileInput = document.getElementById(fileInputId);
        const previewContainer = document.getElementById(previewContainerId);
        const previewImg = document.getElementById(previewImgId);
        
        if (fileInput && previewContainer && previewImg) {
            fileInput.addEventListener('change', () => {
                const file = fileInput.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        previewImg.src = e.target.result;
                        previewContainer.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    previewContainer.style.display = 'none';
                    previewImg.src = '';
                }
            });
        }
    };
});
