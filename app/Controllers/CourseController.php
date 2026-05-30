<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Session;
use App\Models\Course;
use App\Models\Category;
use App\Helpers\Security;
class CourseController extends Controller
{
    public function index()
    {
        $cursos = Course::all();
        $this->view('courses.index', ['title' => 'Cursos', 'cursos' => $cursos]);
    }
    public function create()
    {
        $categorias = Category::all();
        $this->view('courses.form', ['title' => 'Nuevo Curso', 'curso' => null, 'categorias' => $categorias]);
    }
    public function store()
    {
        $data = [
            'category_id' => (int)$_POST['category_id'],
            'title' => $_POST['title'],
            'description' => $_POST['description'] ?? '',
            'price' => preg_replace('/[^0-9]/', '', $_POST['price']),
            'duration' => $_POST['duration'] ?? '',
            'level' => $_POST['level'] ?? 'principiante',
            'instructor' => $_POST['instructor'] ?? '',
            'featured' => isset($_POST['featured']) ? 1 : 0,
            'status' => 'active',
        ];
        if (!empty($_FILES['image']['name'])) {
            $upload = Security::validateUpload($_FILES['image']);
            if (!$upload['valid']) { Session::setFlash('error', $upload['error']); $this->redirectBack(); }
            move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../../uploads/' . $upload['name']);
            $data['image_url'] = $upload['name'];
        }
        Course::create($data);
        Session::setFlash('success', 'Curso creado correctamente');
        $this->redirect('cursos');
    }
    public function edit($id)
    {
        $curso = Course::findWithCategory($id);
        if (!$curso) { Session::setFlash('error', 'Curso no encontrado'); $this->redirect('cursos'); }
        $categorias = Category::all();
        $this->view('courses.form', ['title' => 'Editar Curso', 'curso' => $curso, 'categorias' => $categorias]);
    }
    public function update($id)
    {
        $data = [
            'category_id' => (int)$_POST['category_id'],
            'title' => $_POST['title'],
            'description' => $_POST['description'] ?? '',
            'price' => preg_replace('/[^0-9]/', '', $_POST['price']),
            'duration' => $_POST['duration'] ?? '',
            'level' => $_POST['level'] ?? 'principiante',
            'instructor' => $_POST['instructor'] ?? '',
            'featured' => isset($_POST['featured']) ? 1 : 0,
        ];
        if (!empty($_FILES['image']['name'])) {
            $upload = Security::validateUpload($_FILES['image']);
            if (!$upload['valid']) { Session::setFlash('error', $upload['error']); $this->redirectBack(); }
            move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../../uploads/' . $upload['name']);
            $data['image_url'] = $upload['name'];
        }
        $sets = implode('=?,', array_keys($data)) . '=?';
        $vals = array_values($data);
        $vals[] = $id;
        \App\Core\Database::execute("UPDATE cursos SET {$sets} WHERE id=?", $vals);
        Session::setFlash('success', 'Curso actualizado');
        $this->redirect('cursos');
    }
    public function destroy($id)
    {
        \App\Core\Database::execute("DELETE FROM cursos WHERE id=?", [$id]);
        Session::setFlash('success', 'Curso eliminado');
        $this->redirect('cursos');
    }
}
