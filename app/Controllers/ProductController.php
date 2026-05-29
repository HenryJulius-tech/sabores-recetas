<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Session;
use App\Models\Product;
use App\Helpers\Security;
class ProductController extends Controller
{
    public function index()
    {
        $productos = Product::all();
        $this->view('products.index', ['title' => 'Productos', 'productos' => $productos]);
    }
    public function create()
    {
        $this->view('products.form', ['title' => 'Nuevo Producto', 'producto' => null]);
    }
    public function store()
    {
        $data = [
            'name' => $_POST['name'],
            'price' => (float)preg_replace('/[^0-9]/', '', $_POST['price']),
            'stock' => (int)$_POST['stock'],
            'description' => $_POST['description'] ?? '',
        ];
        if (!empty($_FILES['image']['name'])) {
            $upload = Security::validateUpload($_FILES['image']);
            if (!$upload['valid']) { Session::setFlash('error', $upload['error']); $this->redirectBack(); }
            move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../../uploads/' . $upload['name']);
            $data['image_url'] = $upload['name'];
        }
        Product::create($data);
        Session::setFlash('success', 'Producto creado');
        $this->redirect('productos');
    }
    public function edit($id)
    {
        $producto = Product::find($id);
        if (!$producto) { Session::setFlash('error', 'Producto no encontrado'); $this->redirect('productos'); }
        $this->view('products.form', ['title' => 'Editar Producto', 'producto' => $producto]);
    }
    public function update($id)
    {
        $data = [
            'name' => $_POST['name'],
            'price' => (float)preg_replace('/[^0-9]/', '', $_POST['price']),
            'stock' => (int)$_POST['stock'],
            'description' => $_POST['description'] ?? '',
        ];
        if (!empty($_FILES['image']['name'])) {
            $upload = Security::validateUpload($_FILES['image']);
            if (!$upload['valid']) { Session::setFlash('error', $upload['error']); $this->redirectBack(); }
            move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../../uploads/' . $upload['name']);
            $data['image_url'] = $upload['name'];
        }
        Product::updateProduct($id, $data);
        Session::setFlash('success', 'Producto actualizado');
        $this->redirect('productos');
    }
    public function destroy($id)
    {
        $producto = Product::find($id);
        if ($producto && !empty($producto['image_url'])) {
            $f = __DIR__ . '/../../uploads/' . $producto['image_url'];
            if (file_exists($f)) @unlink($f);
        }
        Product::delete($id);
        Session::setFlash('success', 'Producto eliminado');
        $this->redirect('productos');
    }
}
