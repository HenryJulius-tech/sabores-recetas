document.addEventListener('DOMContentLoaded', () => {
    // Array para almacenar los elementos del carrito [{id, name, price, stock, quantity}]
    let cart = [];

    // Cargar carrito de localStorage si existe
    const storedCart = localStorage.getItem('finca_karen_cart');
    if (storedCart) {
        try {
            cart = JSON.parse(storedCart);
        } catch (e) {
            cart = [];
        }
    }

    // Exponer funciones globalmente
    window.addToCart = (id, name, price, stock) => {
        id = parseInt(id);
        const existingItem = cart.find(item => item.id === id);
        
        if (existingItem) {
            if (existingItem.quantity + 1 > stock) {
                if (window.showToast) {
                    window.showToast(`No hay suficiente stock para agregar más de "${name}".`, 'warning');
                }
                return;
            }
            existingItem.quantity += 1;
        } else {
            if (stock <= 0) {
                if (window.showToast) {
                    window.showToast(`Este producto está agotado.`, 'warning');
                }
                return;
            }
            cart.push({
                id: id,
                name: name,
                price: parseFloat(price),
                stock: parseInt(stock),
                quantity: 1
            });
        }
        
        saveCart();
        renderCart();
        if (window.showToast) {
            window.showToast(`"${name}" agregado al carrito.`, 'success');
        }
    };

    window.updateQuantity = (id, change) => {
        id = parseInt(id);
        const item = cart.find(item => item.id === id);
        if (!item) return;
        
        item.quantity += change;
        
        if (item.quantity <= 0) {
            cart = cart.filter(i => i.id !== id);
        } else if (item.quantity > item.stock) {
            item.quantity = item.stock;
            if (window.showToast) {
                window.showToast(`Se alcanzó el stock límite disponible para "${item.name}".`, 'warning');
            }
        }
        
        saveCart();
        renderCart();
    };

    const saveCart = () => {
        localStorage.setItem('finca_karen_cart', JSON.stringify(cart));
    };

    const renderCart = () => {
        const container = document.getElementById('cart-items-container');
        const totalSpan = document.getElementById('cart-total-amount');
        const checkoutBtn = document.getElementById('btn-checkout');
        
        if (!container || !totalSpan || !checkoutBtn) return;
        
        container.innerHTML = '';
        
        if (cart.length === 0) {
            container.innerHTML = `
                <div class="cart-empty-msg">
                    <i data-lucide="shopping-bag" class="cart-empty-icon"></i>
                    <p>Tu carrito está vacío</p>
                    <span style="font-size: 12px; opacity: 0.7;">Agrega productos del catálogo para comenzar.</span>
                </div>
            `;
            totalSpan.textContent = '$0.00';
            checkoutBtn.disabled = true;
            lucide.createIcons();
            return;
        }
        
        let total = 0.0;
        
        cart.forEach(item => {
            const subtotal = item.price * item.quantity;
            total += subtotal;
            
            const itemEl = document.createElement('div');
            itemEl.className = 'cart-item';
            itemEl.innerHTML = `
                <div class="cart-item-info">
                    <div class="cart-item-name" title="${item.name}">${item.name}</div>
                    <div class="cart-item-price">$${item.price.toFixed(2)}</div>
                </div>
                <div class="cart-item-qty">
                    <button class="cart-qty-btn" onclick="updateQuantity(${item.id}, -1)">-</button>
                    <span class="cart-qty-num">${item.quantity}</span>
                    <button class="cart-qty-btn" onclick="updateQuantity(${item.id}, 1)">+</button>
                </div>
            `;
            container.appendChild(itemEl);
        });
        
        totalSpan.textContent = `$${total.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        checkoutBtn.disabled = false;
        
        lucide.createIcons();
    };

    window.confirmPurchase = () => {
        if (cart.length === 0) return;
        
        const checkoutBtn = document.getElementById('btn-checkout');
        checkoutBtn.disabled = true;
        checkoutBtn.innerHTML = '<i data-lucide="loader" class="animate-spin"></i> Procesando...';
        lucide.createIcons();
        
        fetch('carrito_api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                cart: cart.map(item => ({
                    id: item.id,
                    quantity: item.quantity
                }))
            })
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(res => {
            if (res.status === 200) {
                cart = [];
                saveCart();
                renderCart();
                
                if (window.showToast) {
                    window.showToast('¡Pedido confirmado! Redirigiendo para subir tu pago...', 'success');
                }
                
                setTimeout(() => {
                    window.location.href = 'mis_compras.php';
                }, 2000);
            } else {
                checkoutBtn.disabled = false;
                checkoutBtn.innerHTML = '<i data-lucide="check-circle2"></i> Confirmar Pedido';
                lucide.createIcons();
                if (window.showToast) {
                    window.showToast(res.body.error || 'Ocurrió un error al procesar el pedido.', 'error');
                }
            }
        })
        .catch(error => {
            console.error(error);
            checkoutBtn.disabled = false;
            checkoutBtn.innerHTML = '<i data-lucide="check-circle2"></i> Confirmar Pedido';
            lucide.createIcons();
            if (window.showToast) {
                window.showToast('Error de red al procesar tu pedido.', 'error');
            }
        });
    };

    // Renderizar carrito inicial en la carga de la tienda
    renderCart();
});
