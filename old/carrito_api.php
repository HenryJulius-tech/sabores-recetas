<?php
require_once 'config.php';
require_role('client');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['cart']) || empty($input['cart'])) {
    http_response_code(400);
    echo json_encode(['error' => 'El carrito está vacío']);
    exit;
}

$total = 0.0;
$detalles_para_crear = [];

try {
    $pdo->beginTransaction();
    
    foreach ($input['cart'] as $item) {
        $product_id = $item['id'] ?? null;
        $qty = (int)($item['quantity'] ?? 0);
        
        if ($qty <= 0) continue;
        
        $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ? FOR UPDATE");
        $stmt->execute([$product_id]);
        $prod = $stmt->fetch();
        
        if (!$prod) {
            throw new Exception("Producto no encontrado (ID: $product_id)");
        }
        
        if ($prod['stock'] < $qty) {
            throw new Exception("Stock insuficiente para \"{$prod['name']}\". Disponible: {$prod['stock']}");
        }
        
        $subtotal = $prod['price'] * $qty;
        $total += $subtotal;
        
        $detalles_para_crear[] = [
            'product_id' => $prod['id'],
            'quantity' => $qty,
            'price' => $prod['price']
        ];
    }
    
    if (empty($detalles_para_crear)) {
        throw new Exception("No se procesaron productos válidos");
    }
    
    $stmt = $pdo->prepare("INSERT INTO compras (user_id, total, status) VALUES (?, ?, 'pending')");
    $stmt->execute([$_SESSION['user_id'], $total]);
    $compra_id = $pdo->lastInsertId();
    
    foreach ($detalles_para_crear as $d) {
        $stmt = $pdo->prepare("INSERT INTO detalle_compras (compra_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$compra_id, $d['product_id'], $d['quantity'], $d['price']]);
    }
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Pedido confirmado. Por favor suba el comprobante de pago.',
        'compra_id' => $compra_id
    ]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
