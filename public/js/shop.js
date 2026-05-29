/**
 * Finca Bananera v2 - Shop Cart
 */
var cart = JSON.parse(localStorage.getItem('finca_karen_cart') || '[]');

function saveCart() { localStorage.setItem('finca_karen_cart', JSON.stringify(cart)); renderCart(); }

function addToCart(id, name, price, stock) {
    var existing = cart.find(function(i){return i.id===id});
    if (existing) {
        if (existing.quantity >= stock) { alert('Stock mÃ¡ximo alcanzado'); return; }
        existing.quantity++;
    } else { cart.push({id:id, name:name, price:price, stock:stock, quantity:1}); }
    saveCart();
    openCart();
}

function updateQuantity(id, qty) {
    var item = cart.find(function(i){return i.id===id});
    if (!item) return;
    if (qty <= 0) { cart = cart.filter(function(i){return i.id!==id}); }
    else if (qty > item.stock) { alert('Stock insuficiente'); return; }
    else { item.quantity = qty; }
    saveCart();
}

function renderCart() {
    var el = document.getElementById('cartItems');
    if (!el) return;
    if (cart.length === 0) {
        el.innerHTML = '<div class="text-center text-muted py-5"><i class="bi bi-cart-x" style="font-size:3rem"></i><p class="mt-2">Carrito vacÃ­o</p></div>';
        document.getElementById('cartTotal').textContent = '$0';
        return;
    }
    var html = '';
    var total = 0;
    cart.forEach(function(i){
        var sub = i.price * i.quantity;
        total += sub;
        html += '<div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">'+
            '<div class="flex-grow-1"><strong>'+i.name+'</strong><br><small class="text-muted">$'+i.price.toLocaleString('es-CO')+'</small></div>'+
            '<div class="d-flex align-items-center gap-2">'+
                '<button class="btn btn-sm btn-outline-secondary" onclick="updateQuantity('+i.id+','+(i.quantity-1)+')">-</button>'+
                '<span class="fw-bold">'+i.quantity+'</span>'+
                '<button class="btn btn-sm btn-outline-secondary" onclick="updateQuantity('+i.id+','+(i.quantity+1)+')">+</button>'+
            '</div></div>';
    });
    el.innerHTML = html;
    document.getElementById('cartTotal').textContent = '$'+total.toLocaleString('es-CO');
}

function openCart() {
    var p = document.getElementById('cartPanel');
    var o = document.getElementById('cartOverlay');
    if(p){p.classList.add('open')} if(o){o.classList.add('show')}
}

function toggleCart() {
    var p = document.getElementById('cartPanel');
    var o = document.getElementById('cartOverlay');
    if(p){p.classList.toggle('open')} if(o){o.classList.toggle('show')}
}

function confirmPurchase() {
    if (cart.length === 0) { alert('El carrito estÃ¡ vacÃ­o'); return; }
    if (!confirm('Â¿Confirmar compra por ' + document.getElementById('cartTotal').textContent + '?')) return;
    var btn = event.target;
    btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Procesando...';
    fetch(typeof API_CHECKOUT_URL!=='undefined'?API_CHECKOUT_URL:'/api/carrito/checkout', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
        body: JSON.stringify({cart: cart.map(function(i){return{id:i.id, quantity:i.quantity}})})
    }).then(function(r){return r.json()}).then(function(d){
        btn.disabled = false; btn.innerHTML = '<i class="bi bi-check2-circle me-1"></i>Confirmar Compra';
        if (d.success) {
            cart = []; saveCart();
            alert('Compra realizada con Ã©xito');
            window.location.reload();
        } else { alert('Error: '+(d.error||'Error desconocido')); }
    }).catch(function(){ btn.disabled = false; btn.innerHTML = '<i class="bi bi-check2-circle me-1"></i>Confirmar Compra'; alert('Error de conexiÃ³n'); });
}

renderCart();
