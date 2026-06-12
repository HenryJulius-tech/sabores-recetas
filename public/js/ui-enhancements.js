// ============================================
// User Interface Enhancements
// ============================================

// Smooth scroll behavior
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth' });
        }
    });
});

// Add ripple effect to buttons
const buttons = document.querySelectorAll('button, a.btn, input[type="submit"]');
buttons.forEach(button => {
    button.addEventListener('click', function(e) {
        const ripple = document.createElement('span');
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        ripple.classList.add('ripple');
        
        this.appendChild(ripple);
        setTimeout(() => ripple.remove(), 600);
    });
});

// Toast notification system
function showToast(message, type = 'info', duration = 3000) {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <div class="toast-content">
            <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        </div>
        <button class="toast-close" onclick="this.parentElement.remove()">
            <i class="bi bi-x"></i>
        </button>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('show');
    }, 10);
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, duration);
}

// Smooth transitions for page loads
window.addEventListener('load', function() {
    document.body.classList.add('loaded');
    
    // Add animation to elements with delay
    const animatedElements = document.querySelectorAll('[data-animate]');
    animatedElements.forEach((el, index) => {
        setTimeout(() => {
            el.classList.add('animated');
        }, index * 100);
    });
});

// Enhance form validation with visual feedback
const forms = document.querySelectorAll('form');
forms.forEach(form => {
    form.addEventListener('submit', function(e) {
        const inputs = this.querySelectorAll('input[required], textarea[required], select[required]');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                input.classList.add('error-input');
                isValid = false;
            } else {
                input.classList.remove('error-input');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            showToast('Por favor completa todos los campos', 'error');
        }
    });
    
    // Remove error on input
    form.querySelectorAll('input, textarea, select').forEach(input => {
        input.addEventListener('input', function() {
            if (this.value.trim()) {
                this.classList.remove('error-input');
            }
        });
    });
});

// Mobile menu toggle enhancement
const mobileMenuButton = document.querySelector('[onclick*="classList.toggle"]');
if (mobileMenuButton) {
    document.addEventListener('click', function(e) {
        const sidebar = document.getElementById('sidebar');
        const button = document.querySelector('button[onclick*="sidebar"]');
        
        if (sidebar && !sidebar.contains(e.target) && !button.contains(e.target)) {
            if (window.innerWidth < 768 && sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
            }
        }
    });
}

// Debounce function for resize events
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Handle responsive sidebar
const handleResize = debounce(function() {
    if (window.innerWidth >= 768) {
        document.getElementById('sidebar').classList.remove('show');
    }
}, 250);

window.addEventListener('resize', handleResize);

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + K to focus search
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        const searchInput = document.querySelector('[data-shortcut="search"]');
        if (searchInput) searchInput.focus();
    }
    
    // Escape to close user menu
    if (e.key === 'Escape') {
        const userMenu = document.getElementById('userMenuDropdown');
        if (userMenu) userMenu.classList.remove('active');
    }
});

// Loading state management
function setLoading(element, isLoading) {
    if (isLoading) {
        element.disabled = true;
        element.classList.add('loading');
        element.innerHTML = '<i class="bi bi-hourglass-split"></i> Cargando...';
    } else {
        element.disabled = false;
        element.classList.remove('loading');
        element.innerHTML = element.dataset.originalText || 'Guardar';
    }
}

// Intersection Observer for lazy animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver(function(entries) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('fade-in');
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

document.querySelectorAll('[data-observe]').forEach(el => {
    observer.observe(el);
});

// Initialize tooltips and popovers
function initTooltips() {
    document.querySelectorAll('[data-tooltip]').forEach(el => {
        el.addEventListener('mouseenter', function() {
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = this.dataset.tooltip;
            document.body.appendChild(tooltip);
            
            const rect = this.getBoundingClientRect();
            tooltip.style.top = (rect.top - tooltip.offsetHeight - 10) + 'px';
            tooltip.style.left = (rect.left + rect.width / 2 - tooltip.offsetWidth / 2) + 'px';
            
            setTimeout(() => tooltip.classList.add('show'), 0);
            
            el.addEventListener('mouseleave', function() {
                tooltip.classList.remove('show');
                setTimeout(() => tooltip.remove(), 200);
            });
        });
    });
}

// Initialize on document ready
document.addEventListener('DOMContentLoaded', function() {
    initTooltips();
    
    // Prevent double submit
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('[type="submit"]');
            if (submitBtn) {
                submitBtn.dataset.originalText = submitBtn.innerHTML;
                setLoading(submitBtn, true);
            }
        });
    });
});

// Performance monitoring
if (window.performance && window.performance.timing) {
    window.addEventListener('load', function() {
        setTimeout(function() {
            const perfData = window.performance.timing;
            const pageLoadTime = perfData.loadEventEnd - perfData.navigationStart;
            console.log('Page load time:', pageLoadTime + 'ms');
        }, 0);
    });
}

// Export for use in modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        showToast,
        setLoading,
        debounce,
        handleResize
    };
}
