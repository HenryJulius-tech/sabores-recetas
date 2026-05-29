<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Session;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Payment;
use App\Helpers\Security;
class CartController extends Controller
{
    public function shop()
    {
        $role = Session::userRole();
        $productos = ($role === 'admin') ? Product::all() : Product::available();
        $this->view('products.shop', ['title' => 'Tienda', 'productos' => $productos]);
    }
    public function checkout()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input['cart'])) {
            $this->json(['error' => 'Carrito vacío'], 400);
        }
        try {
            \App\Core\Database::beginTransaction();
            $total = 0;
            $items = [];
            foreach ($input['cart'] as $item) {
                $product = \App\Core\Database::fetchOne("SELECT * FROM productos WHERE id=? FOR UPDATE", [(int)$item['id']]);
                if (!$product) throw new \Exception("Producto #{$item['id']} no encontrado");
                if ($product['stock'] < (int)$item['quantity']) throw new \Exception("Stock insuficiente para {$product['name']}");
                $price = (float)$product['price'];
                $total += $price * (int)$item['quantity'];
                $items[] = ['product' => $product, 'quantity' => (int)$item['quantity'], 'price' => $price];
            }
            $compraId = Purchase::create([
                'user_id' => Session::userId(),
                'total' => $total,
                'status' => 'pending',
            ]);
            foreach ($items as $it) {
                \App\Core\Database::insert(
                    "INSERT INTO detalle_compras (compra_id, product_id, quantity, price) VALUES (?,?,?,?)",
                    [$compraId, $it['product']['id'], $it['quantity'], $it['price']]
                );
                \App\Core\Database::execute("UPDATE productos SET stock=stock-? WHERE id=?", [$it['quantity'], $it['product']['id']]);
            }
            \App\Core\Database::commit();
            $this->json(['success' => true, 'compra_id' => $compraId]);
        } catch (\Exception $e) {
            \App\Core\Database::rollback();
            $this->json(['error' => $e->getMessage()], 400);
        }
    }
    public function myPurchases()
    {
        $compras = Purchase::byUser(Session::userId());
        $this->view('purchases.my_purchases', ['title' => 'Mis Compras', 'compras' => $compras]);
    }
    public function uploadPayment()
    {
        $compraId = (int)$_POST['compra_id'];
        $formaPago = $_POST['forma_pago'] ?? '';
        \App\Core\Database::execute("UPDATE compras SET forma_pago=? WHERE id=?", [$formaPago, $compraId]);
        $proofUrl = '';
        if (!empty($_FILES['proof']['name'])) {
            $upload = Security::validateUpload($_FILES['proof']);
            if (!$upload['valid']) { Session::setFlash('error', $upload['error']); $this->redirectBack(); }
            move_uploaded_file($_FILES['proof']['tmp_name'], __DIR__ . '/../../uploads/' . $upload['name']);
            $proofUrl = $upload['name'];
        }
        Payment::upsert($compraId, ['proof_image_url' => $proofUrl, 'status' => 'pending']);
        Session::setFlash('success', 'Comprobante enviado');
        $this->redirect('mis-compras');
    }
}
