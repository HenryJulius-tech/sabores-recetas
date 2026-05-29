<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Session;
use App\Models\Category;
class CategoryController extends Controller
{
    public function index()
    {
        $categorias = Category::withCourseCount();
        $this->view('categories.index', ['title' => 'Categorías', 'categorias' => $categorias]);
    }
    public function store()
    {
        Category::create(['name' => $_POST['name'], 'description' => $_POST['description'] ?? '']);
        Session::setFlash('success', 'Categoría creada');
        $this->redirectBack();
    }
    public function destroy($id)
    {
        \App\Core\Database::execute("DELETE FROM categorias WHERE id=?", [$id]);
        Session::setFlash('success', 'Categoría eliminada');
        $this->redirectBack();
    }
}
