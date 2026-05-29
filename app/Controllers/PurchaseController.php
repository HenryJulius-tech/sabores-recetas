<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Session;
use App\Models\Purchase;
use App\Models\Payment;
use App\Models\Movement;
class PurchaseController extends Controller
{
    public function index()
    {
        $compras = Purchase::pending();
        $this->view('purchases.admin_approval', ['title' => 'Aprobar Compras', 'compras' => $compras]);
    }
    public function approve($id)
    {
        $purchase = Purchase::withDetails($id);
        if (!$purchase) { Session::setFlash('error', 'Compra no encontrada'); $this->redirect('admin/compras'); }
        try {
            \App\Core\Database::beginTransaction();
            \App\Core\Database::execute("UPDATE compras SET status='approved' WHERE id=?", [$id]);
            \App\Core\Database::execute("UPDATE pagos SET status='approved' WHERE compra_id=?", [$id]);
            Movement::create([
                'compra_id' => $id,
                'type' => 'ingreso',
                'amount' => $purchase['total'],
                'forma_pago' => $purchase['payment']['forma_pago'] ?? '',
                'description' => 'Venta #' . $id . ' - ' . $purchase['username'],
                'category' => 'Ventas',
                'proveedor_beneficiario' => $purchase['username'],
                'date' => date('Y-m-d'),
                'status' => 'approved',
                'created_by_id' => Session::userId(),
            ]);
            \App\Core\Database::commit();
            Session::setFlash('success', 'Compra #' . $id . ' aprobada');
        } catch (\Exception $e) {
            \App\Core\Database::rollback();
            Session::setFlash('error', 'Error: ' . $e->getMessage());
        }
        $this->redirect('admin/compras');
    }
    public function reject($id)
    {
        $purchase = Purchase::withDetails($id);
        if (!$purchase) { Session::setFlash('error', 'Compra no encontrada'); $this->redirect('admin/compras'); }
        try {
            \App\Core\Database::beginTransaction();
            foreach ($purchase['items'] as $item) {
                \App\Core\Database::execute("UPDATE productos SET stock=stock+? WHERE id=?", [$item['quantity'], $item['product_id']]);
            }
            \App\Core\Database::execute("UPDATE compras SET status='rejected' WHERE id=?", [$id]);
            \App\Core\Database::execute("UPDATE pagos SET status='rejected' WHERE compra_id=?", [$id]);
            \App\Core\Database::commit();
            Session::setFlash('success', 'Compra #' . $id . ' rechazada');
        } catch (\Exception $e) {
            \App\Core\Database::rollback();
            Session::setFlash('error', 'Error: ' . $e->getMessage());
        }
        $this->redirect('admin/compras');
    }
}
