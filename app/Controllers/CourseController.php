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
            $subdir = 'courses';
            move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../../public/uploads/' . $subdir . '/' . $upload['name']);
            $data['image_url'] = $subdir . '/' . $upload['name'];
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
            $subdir = 'courses';
            move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../../public/uploads/' . $subdir . '/' . $upload['name']);
            $data['image_url'] = $subdir . '/' . $upload['name'];
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

    public function manageContent($id)
    {
        $curso = Course::findWithCategory($id);
        if (!$curso) { Session::setFlash('error', 'Curso no encontrado'); $this->redirect('cursos'); }
        $modulos = \App\Models\Module::getAllByCourse($id);
        $this->view('courses.manage_content', ['title' => 'Contenido: ' . $curso['title'], 'curso' => $curso, 'modulos' => $modulos]);
    }

    public function storeModule()
    {
        $data = ['curso_id' => (int)$_POST['curso_id'], 'title' => $_POST['title'], 'description' => $_POST['description'] ?? '', 'orden' => \App\Models\Module::getMaxOrden((int)$_POST['curso_id'])];
        \App\Models\Module::create($data);
        $this->json(['success' => true]);
    }

    public function updateModule($curso_id, $modId)
    {
        \App\Models\Module::update($modId, ['title' => $_POST['title'], 'description' => $_POST['description'] ?? '']);
        $this->json(['success' => true]);
    }

    public function deleteModule($curso_id, $modId)
    {
        \App\Models\Module::delete($modId);
        $this->json(['success' => true]);
    }

    public function storeClass()
    {
        $data = ['modulo_id' => (int)$_POST['modulo_id'], 'title' => $_POST['title'], 'description' => $_POST['description'] ?? '', 'video_url' => $_POST['video_url'], 'duration' => $_POST['duration'] ?? '', 'orden' => \App\Models\ClassModel::getMaxOrden((int)$_POST['modulo_id'])];
        \App\Models\ClassModel::create($data);
        $this->json(['success' => true]);
    }

    public function updateClass($curso_id, $claseId)
    {
        \App\Models\ClassModel::update($claseId, ['title' => $_POST['title'], 'description' => $_POST['description'] ?? '', 'video_url' => $_POST['video_url'], 'duration' => $_POST['duration'] ?? '']);
        $this->json(['success' => true]);
    }

    public function deleteClass($curso_id, $claseId)
    {
        \App\Models\ClassModel::delete($claseId);
        $this->json(['success' => true]);
    }

    public function storeExam()
    {
        $data = ['modulo_id' => (int)$_POST['modulo_id'], 'title' => $_POST['title'], 'description' => $_POST['description'] ?? '', 'passing_score' => (int)$_POST['passing_score'], 'max_attempts' => (int)$_POST['max_attempts'], 'time_limit_min' => (int)$_POST['time_limit_min']];
        \App\Models\Exam::create($data);
        $this->json(['success' => true]);
    }

    public function updateExam($curso_id, $examenId)
    {
        \App\Models\Exam::update($examenId, ['title' => $_POST['title'], 'description' => $_POST['description'] ?? '', 'passing_score' => (int)$_POST['passing_score'], 'max_attempts' => (int)$_POST['max_attempts'], 'time_limit_min' => (int)$_POST['time_limit_min']]);
        $this->json(['success' => true]);
    }

    public function storeQuestion()
    {
        $data = ['examen_id' => (int)$_POST['examen_id'], 'question' => $_POST['question'], 'options' => json_decode($_POST['options'], true) ?: explode("\n", $_POST['options']), 'correct_answer' => $_POST['correct_answer'], 'points' => (int)($_POST['points'] ?? 10)];
        \App\Models\Question::create($data);
        $this->json(['success' => true]);
    }

    public function deleteQuestion($pqId)
    {
        \App\Models\Question::delete($pqId);
        $this->json(['success' => true]);
    }

    public function progress()
    {
        $cursoId = (int)($_GET['curso'] ?? 0);
        $search = $_GET['search'] ?? '';
        $users = \App\Core\Database::fetchAll(
            "SELECT DISTINCT u.id, u.username, u.fullname, u.role FROM usuarios u
             INNER JOIN matriculas m ON u.id = m.user_id AND m.status = 'approved'
             WHERE u.role = 'client'" . ($search ? " AND (u.username LIKE ? OR u.fullname LIKE ?)" : "") . "
             ORDER BY u.username",
            $search ? ["%$search%", "%$search%"] : []
        );
        $courses = \App\Core\Database::fetchAll("SELECT id, title FROM cursos ORDER BY title");
        $students = [];
        foreach ($users as $u) {
            $userCursos = \App\Core\Database::fetchAll(
                "SELECT c.id, c.title FROM matriculas m JOIN cursos c ON m.curso_id = c.id
                 WHERE m.user_id = ? AND m.status = 'approved'" . ($cursoId ? " AND c.id = $cursoId" : "") . "
                 ORDER BY c.title",
                [$u['id']]
            );
            foreach ($userCursos as $c) {
                $totalClasses = (int)\App\Core\Database::fetchOne(
                    "SELECT COUNT(*) as cnt FROM clases cls JOIN modulos md ON cls.modulo_id = md.id WHERE md.curso_id = ?", [$c['id']]
                )['cnt'];
                $completedClasses = \App\Models\CompletedClass::countByUserAndCourse($u['id'], $c['id']);
                $totalExams = (int)\App\Core\Database::fetchOne(
                    "SELECT COUNT(*) as cnt FROM examenes ex JOIN modulos md ON ex.modulo_id = md.id WHERE md.curso_id = ?", [$c['id']]
                )['cnt'];
                $passedExams = 0;
                if ($totalExams > 0) {
                    $exams = \App\Core\Database::fetchAll(
                        "SELECT ex.id FROM examenes ex JOIN modulos md ON ex.modulo_id = md.id WHERE md.curso_id = ?", [$c['id']]
                    );
                    foreach ($exams as $e) {
                        if (\App\Models\ExamAttempt::hasPassed($u['id'], $e['id'])) $passedExams++;
                    }
                }
                $progress = $totalClasses > 0 ? round(($completedClasses / $totalClasses) * 100) : 0;
                $students[] = [
                    'username' => $u['username'],
                    'fullname' => $u['fullname'],
                    'course_title' => $c['title'],
                    'completed_classes' => $completedClasses,
                    'total_classes' => $totalClasses,
                    'passed_exams' => $passedExams,
                    'total_exams' => $totalExams,
                    'progress' => $progress,
                ];
            }
        }
        usort($students, function($a, $b) { return $b['progress'] - $a['progress']; });
        $this->view('admin.progress', ['title' => 'Progreso de Estudiantes', 'students' => $students, 'courses' => $courses]);
    }
}
